<?php

namespace App\Http\Controllers\Certify;

use App\CertificateExport;
use App\Models\Bcertify\TestItem;
use App\Models\Basic\Staff;
use App\Models\Basic\Province;
use App\Models\Basic\Amphur;
use App\Models\Basic\District;
use App\Models\Certify\Applicant\Assessment;
use App\Models\Certify\Applicant\AssessmentGroup;
use App\Models\Certify\Applicant\AssessmentGroupFile;
use App\Models\Certify\Applicant\CertifyLabCalibrate;
use App\Models\Certify\Applicant\CertifyLabScope;
use App\Models\Certify\Applicant\CertifyTestScope;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

use App\Models\Certify\Applicant\CertiLab;
use App\Models\Certify\Applicant\CertiLabAttach;
use App\Models\Certify\Applicant\CertiLabAttachMore;
use App\Models\Certify\Applicant\CertiLabAttachAll;
use App\Models\Certify\Applicant\CertiLabCheckBox;
use App\Models\Certify\Applicant\CertiLabCheckBoxImage;
use App\Models\Certify\Applicant\CertiLabDeleteFile;
use App\Models\Certify\Applicant\CertiLabEmployee;
use App\Models\Certify\Applicant\CertiLabInfo;
use App\Models\Certify\Applicant\CertiLabPlace;
use App\Models\Certify\Applicant\CertiLabMaterialLef;
use App\Models\Certify\Applicant\CertiLabProgram;
use App\Models\Certify\Applicant\CertiLabFileAll;
use App\Models\Certify\Applicant\CertiToolsCalibrate;
use App\Models\Certify\Applicant\CertiToolsTest;
use App\Models\Certify\Applicant\Check;
use App\Models\Certify\Applicant\Cost;
use App\Models\Certify\Applicant\CostAssessment;
use App\Models\Certify\Applicant\CostCertificate;
use App\Models\Certify\Applicant\CostFile;
use App\Models\Certify\Applicant\Information;
use App\Models\Certify\Applicant\Notice;
use App\Models\Certify\Applicant\NoticeItem;
use App\Models\Certify\Applicant\NoticeFile;
use App\Models\Certify\Applicant\CostHistory;
use App\Models\Certify\Applicant\CostAssessmentHistory;
use App\Models\Certify\Applicant\Report;
use App\Models\Certify\Applicant\CertiLabExportMapreq;

use App\Models\Bcertify\Formula;

use App\Models\Certify\CertificateExportDesc;
use App\Models\Certify\CertificateExportFile;
use App\Models\Certify\CertificateHistory;
use App\Models\Certify\BoardAuditor;
use App\Models\Certify\BoardAuditorHistory;
use App\Models\Certify\SetStandardUser;
use App\Models\Certify\SetStandardUserSub;

use App\Models\Esurv\FollowUp;
use App\Models\Esurv\Trader;
use Carbon\Carbon;
use FontLib\Table\Type\post;
use function GuzzleHttp\Psr7\str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use HP;
use stdClass;
use App\User;
use Illuminate\Support\Facades\URL;


use Illuminate\Support\Facades\Mail;
use App\Mail\Lab\CertifyApplicant;
use App\Mail\Lab\LABRequestDocumentsMail;
use App\Mail\Lab\CertifyCost;
use App\Mail\Lab\CertifyBoardAuditor;
use App\Mail\Lab\CertifyConFirmAuditorsMail;
use App\Mail\Lab\CertifyPayIn1;
use App\Mail\Lab\CertifySaveAssessment;
use App\Mail\Lab\CertifyReport;
use App\Mail\Lab\CertifyCostCertificate;
use App\Mail\Lab\CertifyConfirmAssessment;



class ApplicantController extends Controller
{
    private $attach_path;//ที่เก็บไฟล์แนบ
    public function __construct()
    {
        // $this->middleware('auth');
        $this->attach_path = 'files/applicants/check_files/';
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $model = str_slug('view-applicant','-');
        $data_session     =    HP::CheckSession();
        if(!empty($data_session)){
        if(HP::CheckPermission($model)){

            $filter = [];
            $filter['filter_status'] = $request->get('filter_status', '');
            $filter['filter_search'] = $request->get('filter_search', '');
            $filter['filter_state'] = $request->get('filter_state', '');
            $filter['filter_branch'] = $request->get('filter_branch', '');
            $filter['filter_start_date'] = $request->get('filter_start_date', '');
            $filter['filter_end_date'] = $request->get('filter_end_date', '');
            $filter['perPage'] = $request->get('perPage', 10);

            $Query = CertiLab::with(['certificate_exports_to' => function($q){
                                $q->where('status', 4);
                            }]);
            if ($filter['filter_status']!='') {
                $Query = $Query->where('status', $filter['filter_status']);
            }
            if ($filter['filter_search'] != '') {
                $Query = $Query->where(function($Query) use ($filter){
                    return $Query->where('app_no','LIKE', '%'.$filter['filter_search'].'%')->Orwhere('name','LIKE', '%'.$filter['filter_search'].'%');
                });
            }
            if ($filter['filter_state']!='') {
                $Query = $Query->where('lab_type', $filter['filter_state']);
            }

            if ($filter['filter_state']!='' && $filter['filter_branch']!='' ) {

                $lab_ids = [];
                if($filter['filter_state'] == 3){
                    $lab_ids =   CertifyTestScope::select('app_certi_lab_id')->where('branch_id',$filter['filter_branch']);
                }else{
                    $lab_ids =   CertifyLabCalibrate::select('app_certi_lab_id')->where('branch_id',$filter['filter_branch']);
                }
                $Query = $Query->whereIn('id', $lab_ids);
            }

            if ($filter['filter_start_date']  != '' && $filter['filter_end_date'] != ''){
                $start = !empty($filter['filter_start_date'])?HP::convertDate($filter['filter_start_date'],true):null;
                $end = !empty($filter['filter_end_date'])?HP::convertDate($filter['filter_end_date'],true):null;
                $Query = $Query->whereBetween('created_at', [$start,$end]);

            } elseif ($filter['filter_start_date']  != '' && $filter['filter_end_date'] == ''){
                $start = !empty($filter['filter_start_date'])?HP::convertDate($filter['filter_start_date'],true):null;
                $Query = $Query->whereDate('created_at',$start);
            }

            if(!is_null($data_session->agent_id)){  // ตัวแทน
                $Query = $Query->where('agent_id',  $data_session->agent_id ) ;
            }else{
                if($data_session->branch_type == 1){  // สำนักงานใหญ่
                    $Query = $Query->where('tax_id',  $data_session->tax_number ) ;
                }else{   // ผู้บันทึก
                    $Query = $Query->where('created_by',   auth()->user()->getKey()) ;
                }
            }

            $applicants =  $Query->sortable()
                                ->orderby('id','desc')
                                ->paginate($filter['perPage']);

            return view('certify.applicant.index',[
                                                    'applicants' => $applicants,
                                                    'filter'     => $filter,
                                                    'attach_path' => $this->attach_path
                                                    ]);
        }
        abort(403);
        }else{
            return  redirect(HP::DomainTisiSso());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    $data_session     =    HP::CheckSession();
    if(!empty($data_session)){
        $Province =  Province::where('PROVINCE_NAME', 'LIKE', '%'.str_replace(" ","",$data_session->province).'%')->first();
        $contact_province =  Province::where('PROVINCE_NAME', 'LIKE', '%'.str_replace(" ","",$data_session->contact_province).'%')->first();

        $data_session->PROVINCE_ID  =    $Province->PROVINCE_ID ?? '';
        $data_session->contact_province_id  =    $contact_province->PROVINCE_ID ?? '';
        // $Amphur =  Amphur::where('AMPHUR_NAME', 'LIKE', '%'.str_replace(" ","",$user_tis->trader_address_amphur).'%')->first();
        $data_session->AMPHUR_ID    =    $data_session->district ?? '';
        // $District =  District::where('DISTRICT_NAME', 'LIKE', '%'.str_replace(" ","",$user_tis->trader_address_tumbol).'%')->first();
        $data_session->DISTRICT_ID  =     $data_session->subdistrict ?? '';



        $app_certi_labs = DB::table('app_certi_labs')->where('lab_type',3)->where('tax_id',$data_session->tax_number)->select('id');

        $certificate_exports = DB::table('certificate_exports')->whereIn('certificate_for',$app_certi_labs)->where('status',3)->pluck('certificate_no','id');

        $certificate_no = DB::table('certificate_exports')->select('id')->whereIn('certificate_for',$app_certi_labs)->where('status',3)->get();

        $branchs = DB::table('bcertify_test_branches')->where('state',1)->pluck('title','id');
        $attaches = DB::table('bcertify_config_attach_forms')->select('*')->where('form',1)->get();
        $data_session->branch_type    =   !empty($data_session->branch_type)?$data_session->branch_type:1;

        return view('certify.applicant.create',[
                                            'tis_data'  => $data_session,
                                            // 'formulas'  => $formulas,
                                            // 'province'  => $province,
                                            'attaches'  => $attaches,
                                            'branchs'   => $branchs,
                                            'certificate_exports'=> $certificate_exports,
                                            'certificate_no' => $certificate_no
            // ,
            // 'follow_up' => $follow_up
        ]);
    }
       abort(403);
    }



    // สำหรับเพิ่มรูปไปที่ store
    public function storeFile($files, $app_no = 'files_lab', $name = null)
    {
        $no  = str_replace("RQ-","",$app_no);
        $no  = str_replace("-","_",$no);

        if ($files) {

            $attach_path  =  $this->attach_path.$no;
            $file_extension = $files->getClientOriginalExtension();
            $fileClientOriginal   =  HP::ConvertCertifyFileName($files->getClientOriginalName());
            $filename = pathinfo($fileClientOriginal, PATHINFO_FILENAME);
            $fullFileName =  str_random(10).'-date_time'.date('Ymd_hms') . '.' . $files->getClientOriginalExtension();
            $storagePath = Storage::putFileAs($attach_path, $files,  $fullFileName );
            $storageName = basename($storagePath); // Extract the filename

            return  $no.'/'.$storageName;
        }else{
            return null;
        }
    }

    public function SaveCertiLab($request, $data_session , $token = null)
    {
        $requestData = $request->all();

        if( is_null($token) ){
            $id = "RQ-LAB-";
            $year = Carbon::now()->addYears(543)->format('y');
            $order = sprintf('%03d',CertiLab::whereYear('created_at',Carbon::now()->year)->count()+1);
            $genId = $id.$year."-".$order;

            $requestLab['app_no'] =  $genId;
        }


        if ($request->lab_ability == 'test'){
            // $requestLab['lab_ability'] = 3;
            $request->lab_ability = 3;

        }elseif ($request->lab_ability == 'calibrate'){
            // $requestLab['lab_ability'] = 4;
            $request->lab_ability = 4;
        }

        if ($request->address_same_headquarter == 'on'){
            $requestLab['address_same_headquarter'] = 1;
            $request->address_same_headquarter = 1;
        } else{
            $requestLab['address_same_headquarter'] = 2;
            $request->address_same_headquarter = 2;
        }

        $requestLab['checkbox_confirm']        = isset($request->checkbox_confirm) ? $request->checkbox_confirm : null;
        $requestLab['purpose_type']            = !empty($request->purpose)?$request->purpose:1;
        $requestLab['standard_id']             = !empty($request->according_formula)?$request->according_formula:null;
        $requestLab['lab_type']                = !empty($request->lab_ability)?$request->lab_ability:null ;
        $requestLab['branch']                  = !empty($request->branch)?$request->branch:null;

        $requestLab['certificate_exports_id']  = !empty($request->certificate_exports_id)?$request->certificate_exports_id:null;
        $requestLab['accereditation_no']       = !empty($request->accereditation_no)?$request->accereditation_no:null;
        $requestLab['lab_name']                = !empty($request->lab_name)?$request->lab_name:null;
        $requestLab['lab_name_en']             = !empty($request->lab_name_en)?$request->lab_name_en:null;
        $requestLab['lab_name_short']          = !empty($request->lab_name)?$request->lab_name_short:null;
        $requestLab['same_address']            = !empty($request->address_same_headquarter)?$request->address_same_headquarter:null;

        //ที่อยู่ ที่อยู่ห้องปฏิบัติการ
        $requestLab['address_no']              = !empty($request->address_number)?$request->address_number:null;
        $requestLab['allay']                   = !empty($request->village_no)?$request->village_no:null;
        $requestLab['village_no']              = !empty($request->address_soi)?$request->address_soi:null;
        $requestLab['road']                    = !empty($request->address_street)?$request->address_street:null;
        $requestLab['province']                = !empty($request->address_city)?$request->address_city:null;
        $requestLab['amphur']                  = !empty($request->address_district)?$request->address_district:null;
        $requestLab['district']                = !empty($request->sub_district)?$request->sub_district:null;
        $requestLab['postcode']                = !empty($request->postcode)?$request->postcode:null;
        $requestLab['tel']                     = !empty($request->address_tel)?$request->address_tel:null;
        $requestLab['tel_fax']                 = !empty($request->fax)?$request->fax:null;

        $requestLab['lab_latitude']            = !empty($request->lab_latitude)?$request->lab_latitude:null;
        $requestLab['lab_longitude']           = !empty($request->lab_longitude)?$request->lab_longitude:null;

        //ที่อยู่ ที่อยู่ห้องปฏิบัติการ EN
        $requestLab['lab_address_no_eng']      = !empty($request->lab_address_no_eng)?$request->lab_address_no_eng:null;
        $requestLab['lab_moo_eng']             = !empty($request->lab_moo_eng)?$request->lab_moo_eng:null;
        $requestLab['lab_soi_eng']             = !empty($request->lab_soi_eng)?$request->lab_soi_eng:null;
        $requestLab['lab_street_eng']          = !empty($request->lab_street_eng)?$request->lab_street_eng:null;
        $requestLab['lab_province_eng']        = !empty($request->lab_province_eng)?$request->lab_province_eng:null;
        $requestLab['lab_amphur_eng']          = !empty($request->lab_amphur_eng)?$request->lab_amphur_eng:null;
        $requestLab['lab_district_eng']        = !empty($request->lab_district_eng)?$request->lab_district_eng:null;
        $requestLab['lab_postcode_eng']        = !empty($request->lab_postcode_eng)?$request->lab_postcode_eng:null;

        //ข้อมูลสำหรับการติดต่อ
        $requestLab['contactor_name']          = !empty($request->contact)?$request->contact:null;
        $requestLab['email']                   = !empty($request->address_email)?$request->address_email:null;
        $requestLab['contact_tel']             = !empty($request->contact_tel)?$request->contact_tel:null; // โทรศัพท์ผู้ติดต่อ
        $requestLab['telephone']               = !empty($request->contact_mobile)?$request->contact_mobile:null; // ทรศัพท์มือถือ

        $requestLab['management_lab']          = isset($request->mn_3_1)?$request->mn_3_1:null;

        //ข้อมูลสำนักงานใหญ่
        $requestLab['hq_address']              = !empty($request->head_num)?$request->head_num:null;
        $requestLab['hq_moo']                  = !empty($request->head_moo)?$request->head_moo:null;
        $requestLab['hq_soi']                  = !empty($request->head_soi)?$request->head_soi:null;
        $requestLab['hq_road']                 = !empty($request->head_street)?$request->head_street:null;
        $requestLab['hq_subdistrict_id']       = !empty($request->hq_subdistrict_id)?$request->hq_subdistrict_id:null;
        $requestLab['hq_district_id']          = !empty($request->hq_district_id)?$request->hq_district_id:null;
        $requestLab['hq_province_id']          = !empty($request->hq_province_id)?$request->hq_province_id:null;
        $requestLab['hq_zipcode']              = !empty($request->head_post)?$request->head_post:null;
        $requestLab['hq_date_registered']      = Carbon::hasFormat($request->entity_date, 'd/m/Y')?Carbon::createFromFormat("d/m/Y", $request->entity_date)->addYear(-543)->format('Y-m-d'):null;
        $requestLab['hq_telephone']            = !empty($request->head_tel)?$request->head_tel:null;
        $requestLab['hq_fax']                  = !empty($request->head_fax)?$request->head_fax:null;

        // กลุ่มงานตามมาตรฐานและสาขา
        if($requestLab['lab_type'] == 3){
            //   6. ขอบข่ายที่ยื่นขอรับการรับรอง (ทดสอบ)
            if(isset($requestData['test_scope'])){
                $set_standard  =  SetStandardUserSub::select('standard_user_id')
                                                    ->whereIn('test_branch_id',(array)$requestData['test_scope']['branch_id'][0])
                                                    ->first() ;
                if(!is_null($set_standard)){
                    $requestLab['subgroup'] =  $set_standard->set_standard_user->sub_department_id ?? 1804;
                }
            }

        }else if($requestLab['lab_type'] == 4){
            //   6. ขอบข่ายที่ยื่นขอรับการรับรอง (สอบเทียบ)
            if(isset($requestData['calibrate'])){
                $set_standard  =  SetStandardUserSub::select('standard_user_id')
                                                    ->whereIn('items_id',(array)$requestData['calibrate']['branch_id'][0])
                                                    ->first() ;
                if(!is_null($set_standard)){
                    $requestLab['subgroup'] =  $set_standard->set_standard_user->sub_department_id ?? 1806;
                }
            }
        }

        if(  is_null($token) ){

            if ($request->save){
                $requestLab['status'] = 1;
                $requestLab['start_date'] =  date('Y-m-d');
            }else{
                $requestLab['status'] = 0;
            }
            if($request->status == 1){}

            $requestLab['applicanttype_id']   =   !empty($data_session->applicanttype_id) ? $data_session->applicanttype_id : null;
            $requestLab['tax_id']             =   !empty($data_session->tax_number) ? $data_session->tax_number : null;
            $requestLab['created_by']         =   !empty($data_session->id) ? $data_session->id : null;
            $requestLab['name']               =   !empty($data_session->name) ? $data_session->name : null;
            $requestLab['agent_id']           =   !empty($data_session->agent_id) ? $data_session->agent_id : null;
            $requestLab['branch_type']        =   !empty($data_session->branch_type)?$data_session->branch_type:1;
            $requestLab['token']              =   str_random(16);

            $certi_lab = CertiLab::create($requestLab);
        }else{

            $certi_lab = CertiLab::where('token',$token)->first();
            // วันที่เปลี่ยนสถานะฉบับร่างเป็นรอดำเนินการตรวจ
            if($request->save == 1 && $certi_lab->status == 0 ){
                $requestLab['created_at'] = date('Y-m-d h:m:s');
                $requestLab['start_date'] = !empty( $certi_lab->start_date)?$certi_lab->start_date: date('Y-m-d');
            }

            // if($request->save == 1 ){
            //     $requestLab['start_date'] = date('Y-m-d');
            // }

            if ($certi_lab->status == 3){  // ขอเอกสารเพิ่มเติม
                $status = 3;  //Mail
                $requestLab['status'] = 2;
            }else{
                $status = 1;  //Mail
                if ($request->save){
                    $requestLab['status'] =  1;
                }
                if ($request->draft){
                    $requestLab['status'] = 0;
                }
            }

            $certi_lab->update( $requestLab );
        }

        return $certi_lab;

    }

    public function SaveInformation($request, $certilab)
    {

        $certi_information =  Information::where('app_certi_lab_id',$certilab->id)->first();
        if( is_null($certi_information) ){
            $certi_information = new Information();
            $certi_information->token = str_random(16);
        }
        $certi_information->app_certi_lab_id            = $certilab->id;
        $certi_information->name                        = !empty($request->app_name)?$request->app_name:null;
        $certi_information->ages                        = !empty($request->app_old)?$request->app_old:null;
        $certi_information->nationality                 = !empty($request->app_nation)?$request->app_nation:null;
        $certi_information->tax_indentification_number  = !empty($request->id_tax)?$request->id_tax:null;
        $certi_information->address_headquarters        = !empty($request->head_num)?$request->head_num:null;
        $certi_information->headquarters_alley          = !empty($request->head_soi)?$request->head_soi:null;
        $certi_information->headquarters_road           = !empty($request->head_street)?$request->head_street:null;
        $certi_information->headquarters_village_no     = !empty($request->head_moo)?$request->head_moo:null;
        $certi_information->headquarters_district       = !empty($request->head_tumbon)?$request->head_tumbon:null;
        $certi_information->headquarters_amphur         = !empty($request->head_area)?$request->head_area:null;
        $certi_information->headquarters_province       = !empty($request->head_province)?$request->head_province:null;
        $certi_information->headquarters_postcode       = !empty($request->head_post)?$request->head_post:null;
        $certi_information->headquarters_tel            = !empty($request->head_tel)?$request->head_tel:null;
        $certi_information->headquarters_tel_fax        = !empty($request->head_fax)?$request->head_fax:null;
        $certi_information->date_regis_juristic_person  = !empty($request->entity_date)?Carbon::createFromFormat("d/m/Y",$request->entity_date)->addYear(-543)->formatLocalized('%Y-%m-%d'):null;

        $certi_information->save();
    }

    public function SaveCertiLabPlace($request, $certilab)
    {
        $certi_lab_place =  CertiLabPlace::where('app_certi_lab_id',$certilab->id)->first();
        if( is_null($certi_lab_place) ){
            $certi_lab_place = new CertiLabPlace();
            $certi_lab_place->token = str_random(16);
        }
        $certi_lab_place->app_certi_lab_id          = $certilab->id;
        $certi_lab_place->permanent_operating_site  = isset($request->pl_2_1)?$request->pl_2_1:null;
        $certi_lab_place->off_site_operations       = isset($request->pl_2_2)?$request->pl_2_2:null;
        $certi_lab_place->temporary_operating_site  = isset($request->pl_2_4)?$request->pl_2_4:null;
        $certi_lab_place->mobile_operating_facility = isset($request->pl_2_3)?$request->pl_2_3:null;
        $certi_lab_place->multi_site_facility       = isset($request->pl_2_5)?$request->pl_2_5:null;

        $certi_lab_place->save();
    }

    public function SaveCertiLabInfo($request, $certilab)
    {
        $certi_lab_info =  CertiLabInfo::where('app_certi_lab_id',$certilab->id)->first();
        if( is_null($certi_lab_info) ){
            $certi_lab_info = new CertiLabInfo();
            $certi_lab_info->token = str_random(16);
        }
        $certi_lab_info->app_certi_lab_id           = $certilab->id;
        $certi_lab_info->petitioner                 = !empty($request->man_applicant)?$request->man_applicant:null;
        $certi_lab_info->lab_type_other             = isset($request->at_1_1_1)?$request->at_1_1_1:null;

        if($request->hasFile('activity_file')){
            $certi_lab_info->desc_main_file         = $this->storeFile($request->activity_file,$certilab->app_no) ?? null;
            $certi_lab_info->activity_client_name   = HP::ConvertCertifyFileName($request->activity_file->getClientOriginalName());
        }
        if($request->hasFile('file_section')){
            $certi_lab_info->file_section           = $this->storeFile($request->file_section,$certilab->app_no) ?? null;
            $certi_lab_info->file_client_name       = HP::ConvertCertifyFileName($request->file_section->getClientOriginalName());
        }
        $certi_lab_info->only_own_depart               = isset($request->at_1_1_3)?$request->at_1_1_3:null;
        $certi_lab_info->depart_other                  = isset($request->at_1_1_4)?$request->at_1_1_4:null;
        $certi_lab_info->over_twenty                   = isset($request->at_1_1_5)?$request->at_1_1_5:null;
        $certi_lab_info->not_bankrupt                  = isset($request->at_1_1_6)?$request->at_1_1_6:null;
        $certi_lab_info->not_being_incompetent         = isset($request->at_1_1_7)?$request->at_1_1_7:null;
        $certi_lab_info->suspended_using_a_certificate = isset($request->at_1_1_8)?$request->at_1_1_8:null;
        $certi_lab_info->never_revoke_a_certificate    = isset($request->at_1_1_9)?$request->at_1_1_9:null;
        $certi_lab_info->save();
    }

    public function SaveFileSection($request, $name, $input_name, $section, $certilab )
    {
        $requestData = $request->all();
        if( isset($requestData[ $name ]) ){
            $repeater_list = $requestData[ $name ];

            foreach( $repeater_list AS $item ){

                if( isset($item[ $input_name ]) ){

                    $certi_lab_attach                   = new CertiLabAttachAll();
                    $certi_lab_attach->app_certi_lab_id = $certilab->id;
                    $certi_lab_attach->file_section     = (string)$section;
                    $certi_lab_attach->file             = $this->storeFile( $item[ $input_name ] ,$certilab->app_no);
                    $certi_lab_attach->file_client_name = HP::ConvertCertifyFileName( $item[ $input_name ]->getClientOriginalName());
                    $certi_lab_attach->token            = str_random(16);
                    $certi_lab_attach->save();

                }

            }

        }
    }

    public function SaveFileMore($request, $certilab )
    {

        $requestData = $request->all();
        if( isset($requestData[ 'repeater-section-other' ]) ){
            $repeater_list = $requestData[ 'repeater-section-other' ];

            foreach( $repeater_list AS $item ){
                if( isset($item[ 'another_attach_files' ]) ){

                    $certi_lab_attach_more                   = new CertiLabAttachMore();
                    $certi_lab_attach_more->app_certi_lab_id = $certilab->id;
                    $certi_lab_attach_more->file_desc        = !empty($item[ 'another_attach_files_desc' ])?$item[ 'another_attach_files_desc' ]:null;
                    $certi_lab_attach_more->file             = $this->storeFile( $item[ 'another_attach_files' ] ,$certilab->app_no);
                    $certi_lab_attach_more->file_client_name = HP::ConvertCertifyFileName( $item[ 'another_attach_files' ]->getClientOriginalName());
                    $certi_lab_attach_more->token            = str_random(16);
                    $certi_lab_attach_more->save();

                }
            }

        }

    }

    private function save_certify_test_scope($main, $requestData){
        CertifyTestScope::where('app_certi_lab_id', $main->id)->delete();

        if( isset($requestData['test_scope']) ){
            /* ขอบข่ายที่ยื่นขอรับการรับรอง (ทดสอบ) */
            $test_scope = (array)@$requestData['test_scope'];

            foreach($test_scope['branch_id'] as $key => $itme) {
                $input = [];
                $input['app_certi_lab_id'] = $main->id;
                //   $input['category_product_id'] = $itme;
                $input['branch_id'] = $itme;
                $input['token'] = str_random(16);
                CertifyTestScope::create($input);
            }
        }

    }
    private function save_certifyLab_calibrate($main, $requestData){
        CertifyLabCalibrate::where('app_certi_lab_id', $main->id)->delete();

        if( isset($requestData['calibrate']) ){
            /*  ขอบข่ายที่ยื่นขอรับการรับรอง (ทดสอบ) */
            $calibrate = (array)@$requestData['calibrate'];
            foreach($calibrate['branch_id'] as $key => $itme) {
                $input = [];
                $input['app_certi_lab_id'] = $main->id;
                //   $input['group_id'] = $itme;
                $input['branch_id'] = $itme;
                $input['token'] = str_random(16);
                CertifyLabCalibrate::create($input);
            }
        }

    }


    public function store(Request $request)
    {
        $model = str_slug('applicant','-');
        $data_session     =    HP::CheckSession();

        if(!empty($data_session)){
            if(HP::CheckPermission('add-'.$model)){
                // try {

                    $requestData = $request->all();

                    // add ceti lab
                    $certilab = $this->SaveCertiLab($request, $data_session , null );

                    // Save information
                    $this->SaveInformation($request, $certilab);

                    // Save Place
                    $this->SaveCertiLabPlace($request, $certilab);

                    //Save lab info
                    $this->SaveCertiLabInfo($request, $certilab);

                    // ตรวจสอบคำขอใบรับรองห้องปฏิบัติการ
                    $check = new Check;
                    $check->app_certi_lab_id = $certilab->id;
                    $check->save();

                    if($certilab->lab_type == 3){
                        //   6. ขอบข่ายที่ยื่นขอรับการรับรอง (ทดสอบ)
                        if(isset($requestData['test_scope'])){
                            $this->save_certify_test_scope($certilab,$requestData);
                        }
                    }else if($certilab->lab_type == 4){
                        //   6. ขอบข่ายที่ยื่นขอรับการรับรอง (สอบเทียบ)
                        if(isset($requestData['calibrate'])){
                            $this->save_certifyLab_calibrate($certilab,$requestData);
                        }
                    }

                    //ไฟล์แนบ

                    if ( isset($requestData['repeater-section4'] ) ){
                        $this->SaveFileSection($request, 'repeater-section4', 'attachs_sec4', 4 , $certilab );
                    }

                    if ( isset($requestData['repeater-section5'] ) ){
                        $this->SaveFileSection($request, 'repeater-section5', 'attachs_sec5', 5 , $certilab );
                    }

                    if ( isset($requestData['repeater-section61'] ) ){
                        $this->SaveFileSection($request, 'repeater-section61', 'attachs_sec61', 61 , $certilab );
                    }

                    if ( isset($requestData['repeater-section62'] ) ){
                        $this->SaveFileSection($request, 'repeater-section62', 'attachs_sec62', 62 , $certilab );
                    }

                    if ( isset($requestData['repeater-section71'] ) ){
                        $this->SaveFileSection($request, 'repeater-section71', 'attachs_sec71', 71 , $certilab );
                    }

                    if ( isset($requestData['repeater-section72'] ) ){
                        $this->SaveFileSection($request, 'repeater-section72', 'attachs_sec72', 72 , $certilab );
                    }

                    if ( isset($requestData['repeater-section8'] ) ){
                        $this->SaveFileSection($request, 'repeater-section8', 'attachs_sec8', 8 , $certilab );
                    }

                    if ( isset($requestData['repeater-section9'] ) ){
                        $this->SaveFileSection($request, 'repeater-section9', 'attachs_sec9', 9 , $certilab );
                    }

                    if ( isset($requestData['repeater-section10'] ) ){
                        $this->SaveFileSection($request, 'repeater-section10', 'attachs_sec10', 10 , $certilab );
                    }

                    if ( isset($requestData['repeater-section-other'] ) ){
                        $this->SaveFileMore($request, $certilab );
                    }


                    // เงื่อนไขเช็คมีใบรับรอง
                    $this->save_certilab_export_mapreq( $certilab );

                    ////////////////////////////////////////////////////////////////////////////////////////
                    // add certi lab check box image
                    // if ($request->attachs_sec4  && $request->hasFile('attachs_sec4')){   // ถ้ามีการแนบ ไฟล์ มา
                    //     $certi_lab_check_box = CertiLabCheckBox::where('app_certi_lab_id',$certilab->id)->first();
                    //     if(is_null($certi_lab_check_box)){
                    //         $certi_lab_check_box = new CertiLabCheckBox;
                    //         $certi_lab_check_box->app_certi_lab_id = $certilab->id;
                    //         $certi_lab_check_box->token = str_random(20);
                    //         $certi_lab_check_box->save();
                    //     }
                    //     foreach ($request->attachs_sec4 as $index => $attachs_sec4){
                    //         $certi_lab_check_box_image = new CertiLabCheckBoxImage();
                    //         $certi_lab_check_box_image->app_certi_lab_check_box_id = $certi_lab_check_box->id;
                    //         $certi_lab_check_box_image->path_image = $this->storeFile($attachs_sec4,$certilab->app_no);
                    //         $certi_lab_check_box_image->file_client_name = HP::ConvertCertifyFileName($attachs_sec4->getClientOriginalName());
                    //         $certi_lab_check_box_image->token = str_random(16);
                    //         $certi_lab_check_box_image->save();
                    //     }
                    // }


                    // ส่ง Email
                    if($certilab->status == 1){
                        $this->SET_EMAIL($certilab,1);
                    }

                    return redirect('certify/applicant')->with('message', 'เรียบร้อยแล้ว!');
                // } catch (\Exception $e) {
                //     return redirect('certify/applicant')->with('message_error', 'เกิดข้อผิดพลาดกรุณาบันทึกใหม่');
                // }

                abort(403);
            }else{
                return  redirect(HP::DomainTisiSso());
            }
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($token)
    {
        // = CertiLab::findOrFail($token);
        $certi_lab = CertiLab::where('token',$token)->first();
        $certi_information = Information::where('app_certi_lab_id',$certi_lab->id)->first();
        $certi_lab['app_name'] = $certi_information->name ?? '-' ;
        $certi_lab['app_old'] = $certi_information->ages ?? '-' ;
        $certi_lab['app_nation'] = $certi_information->nationality ?? '-' ;
        $certi_lab['home_num'] = $certi_information->address_no ?? '-' ;
        $certi_lab['home_soi'] = $certi_information->alley ?? '-' ;
        $certi_lab['home_street'] = $certi_information->road ?? '-' ;
        $certi_lab['home_moo'] = $certi_information->village_no ?? '-' ;
        $certi_lab['home_province'] = $certi_information->province ?? '-' ;
        $certi_lab['home_area'] = $certi_information->amphur ?? '-' ;
        $certi_lab['home_tumbon'] = $certi_information->district ?? '-' ;
        $certi_lab['home_post'] = $certi_information->postcode ?? '-' ;
        $certi_lab['home_phone'] = $certi_information->tel ?? '-' ;

    //    return $certi_lab;

        // add certi lab info
        $certi_lab_info =  CertiLabInfo::where('app_certi_lab_id',$certi_lab->id)->first();
        if(is_null($certi_lab_info)){
            $certi_lab_info = new CertiLabInfo;
        }
       // add certi lab place
         $certi_lab_place =  CertiLabPlace::where('app_certi_lab_id',$certi_lab->id)->first();
         if(is_null($certi_lab_place)){
            $certi_lab_place = new CertiLabPlace;
        }
        $branchs = DB::table('bcertify_test_branches')->select('*')->where('state',1)->pluck('title','id');
        $calibration_branchs = DB::table('bcertify_calibration_branches')->select('*')->where('state',1)->pluck('title','id');
        $province = DB::table('province')->select('*')->get();

        $attaches = DB::table('bcertify_config_attach_forms')->select('*')->where('form',1)->get();
        $certi_lab_attach_more = CertiLabAttachMore::where('app_certi_lab_id',$certi_lab->id)->get();
        $certi_lab_attach_all5 = CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id)
                                                  ->where('file_section', '5')
                                                  ->get();
        $certi_lab_attach_all61 = CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id)
                                                  ->where('file_section', '61')
                                                  ->get();
        $certi_lab_attach_all62 = CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id)
                                                  ->where('file_section', '62')
                                                  ->get();
        $certi_lab_attach_all71 = CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id)
                                                  ->where('file_section', '71')
                                                  ->get();
        $certi_lab_attach_all72 = CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id)
                                                  ->where('file_section', '72')
                                                  ->get();
        $certi_lab_attach_all8 = CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id)
                                                  ->where('file_section', '8')
                                                  ->get();
        $certi_lab_attach_all9 = CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id)
                                                  ->where('file_section', '9')
                                                  ->get();
         $certi_lab_check_box = CertiLabCheckBox::where('app_certi_lab_id',$certi_lab->id)->first();
         if(is_null($certi_lab_check_box)){
            $certi_lab_check_box = new CertiLabCheckBox;
         }
        $certi_lab_check_box_image = !is_null($certi_lab_check_box) ? CertiLabCheckBoxImage::where('app_certi_lab_check_box_id', $certi_lab_check_box->id)->get() : null;
        $CertiLabDeleteFile = CertiLabDeleteFile::where('app_certi_lab_id', $certi_lab->id) ->get();

        $app_certi_labs = DB::table('app_certi_labs')->where('lab_type',$certi_lab->lab_type)->where('tax_id',$certi_lab->tax_id)->select('id');

        $certificate_exports = DB::table('certificate_exports')->whereIn('certificate_for',$app_certi_labs)->where('status',3)->pluck('certificate_no','id');
        $certificate_no = DB::table('certificate_exports')->select('id')->whereIn('certificate_for',$app_certi_labs)->where('status',3)->get();

        return view('certify.applicant.show',[
                                            'certi_lab'=>$certi_lab,
                                            'attaches' => $attaches,
                                            'certi_information' => $certi_information,
                                            'certi_lab_info'=> $certi_lab_info,
                                            'certi_lab_place'=>$certi_lab_place,
                                            'branchs' =>$branchs,
                                            'calibration_branchs'=> $calibration_branchs,
                                            'certi_lab_attach_more'=> $certi_lab_attach_more,
                                            'certi_lab_attach_all5' => $certi_lab_attach_all5,
                                            'certi_lab_attach_all61' => $certi_lab_attach_all61,
                                            'certi_lab_attach_all62' => $certi_lab_attach_all62,
                                            'certi_lab_attach_all71' => $certi_lab_attach_all71,
                                            'certi_lab_attach_all72' => $certi_lab_attach_all72,
                                            'certi_lab_attach_all8' => $certi_lab_attach_all8,
                                            'certi_lab_attach_all9' => $certi_lab_attach_all9,
                                            'certi_lab_check_box_image'=> $certi_lab_check_box_image,
                                            'CertiLabDeleteFile' => $CertiLabDeleteFile,
                                            'certificate_no' => $certificate_no,
                                            'certificate_exports' => $certificate_exports
                                             ]);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($token)
    {
        $model = str_slug('applicant','-');
        $data_session     =    HP::CheckSession();
if(!empty($data_session)){
    if(HP::CheckPermission('edit-'.$model)){

        $certi_lab = CertiLab::where('token',$token)->first();
        // $certi_information = Information::where('app_certi_lab_id',$certi_lab->id)->first();
        // dd( $certi_lab);
        // $certi_lab['app_name'] = $certi_information->name ?? '-' ;
        // $certi_lab['app_old'] = $certi_information->ages ?? '-' ;
        // $certi_lab['app_nation'] = $certi_information->nationality ?? '-' ;
        // $certi_lab['home_num'] = $certi_information->address_no ?? '-' ;
        // $certi_lab['home_soi'] = $certi_information->alley ?? '-' ;
        // $certi_lab['home_street'] = $certi_information->road ?? '-' ;
        // $certi_lab['home_moo'] = $certi_information->village_no ?? '-' ;
        // $certi_lab['home_province'] = $certi_information->province ?? '-' ;
        // $certi_lab['home_area'] = $certi_information->amphur ?? '-' ;
        // $certi_lab['home_tumbon'] = $certi_information->district ?? '-' ;
        // $certi_lab['home_post'] = $certi_information->postcode ?? '-' ;
        // $certi_lab['home_phone'] = $certi_information->tel ?? '-' ;
    //    return $certi_lab;
        // add certi lab info
        $certi_lab_info =  CertiLabInfo::where('app_certi_lab_id',$certi_lab->id)->first();
        if(is_null($certi_lab_info)){
            $certi_lab_info = new CertiLabInfo;
        }
       // add certi lab place
         $certi_lab_place =  CertiLabPlace::where('app_certi_lab_id',$certi_lab->id)->first();
         if(is_null($certi_lab_place)){
            $certi_lab_place = new CertiLabPlace;
        }
        $branchs = DB::table('bcertify_test_branches')->select('*')->where('state',1)->pluck('title','id');
        $calibration_branchs = DB::table('bcertify_calibration_branches')->select('*')->where('state',1)->pluck('title','id');
        $province = DB::table('province')->select('*')->get();

        $attaches = DB::table('bcertify_config_attach_forms')->select('*')->where('form',1)->get();
        $certi_lab_attach_more = CertiLabAttachMore::where('app_certi_lab_id',$certi_lab->id)->get();
        $certi_lab_attach_all5 = CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id)
                                                  ->where('file_section', '5')
                                                  ->get();
        $certi_lab_attach_all61 = CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id)
                                                  ->where('file_section', '61')
                                                  ->get();
        $certi_lab_attach_all62 = CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id)
                                                  ->where('file_section', '62')
                                                  ->get();
        $certi_lab_attach_all71 = CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id)
                                                  ->where('file_section', '71')
                                                  ->get();
        $certi_lab_attach_all72 = CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id)
                                                  ->where('file_section', '72')
                                                  ->get();
        $certi_lab_attach_all8 = CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id)
                                                  ->where('file_section', '8')
                                                  ->get();
        $certi_lab_attach_all9 = CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id)
                                                  ->where('file_section', '9')
                                                  ->get();
         $certi_lab_check_box = CertiLabCheckBox::where('app_certi_lab_id',$certi_lab->id)->first();
         if(is_null($certi_lab_check_box)){
             $certi_lab_check_box = new CertiLabCheckBox;
          }
        $certi_lab_check_box_image = !is_null($certi_lab_check_box) ? CertiLabCheckBoxImage::where('app_certi_lab_check_box_id', $certi_lab_check_box->id)->get() : null;
        $CertiLabDeleteFile = CertiLabDeleteFile::where('app_certi_lab_id', $certi_lab->id) ->get();

        // $follow_up = FollowUp::where('trader_autonumber',Auth::user()->getKey())->first() ;
        $user_tis = $data_session;
        $Province =  Province::where('PROVINCE_NAME', 'LIKE', '%'.$user_tis->province.'%')->first();
        $contact_province =  Province::where('PROVINCE_NAME', 'LIKE', '%'.str_replace(" ","",$data_session->contact_province).'%')->first();

        $user_tis->PROVINCE_ID          =    $Province->PROVINCE_ID ?? '';
        $user_tis->contact_province_id  =    $contact_province->PROVINCE_ID ?? '';

        $app_certi_labs = DB::table('app_certi_labs')->where('lab_type',$certi_lab->lab_type)->where('tax_id',$data_session->tax_number)->select('id');
        $certificate_exports = DB::table('certificate_exports')->whereIn('certificate_for',$app_certi_labs)->where('status',3)->pluck('request_number','id');
        $certificate_no = DB::table('certificate_exports')->select('id')->whereIn('certificate_for',$app_certi_labs)->where('status',3)->get();

        return view('certify.applicant.edit',[
                                            'certi_lab'=>$certi_lab,
                                            'attaches' => $attaches,
                                            // 'certi_information' => $certi_information,
                                            'certi_lab_info'=> $certi_lab_info,
                                            'certi_lab_place'=>$certi_lab_place,
                                            'branchs' =>$branchs,
                                            'calibration_branchs'=> $calibration_branchs,
                                            'certi_lab_attach_more'=> $certi_lab_attach_more,
                                            'certi_lab_attach_all5' => $certi_lab_attach_all5,
                                            'certi_lab_attach_all61' => $certi_lab_attach_all61,
                                            'certi_lab_attach_all62' => $certi_lab_attach_all62,
                                            'certi_lab_attach_all71' => $certi_lab_attach_all71,
                                            'certi_lab_attach_all72' => $certi_lab_attach_all72,
                                            'certi_lab_attach_all8' => $certi_lab_attach_all8,
                                            'certi_lab_attach_all9' => $certi_lab_attach_all9,
                                            'certi_lab_check_box_image'=> $certi_lab_check_box_image,
                                            'CertiLabDeleteFile'=> $CertiLabDeleteFile,
                                            // 'follow_up' => $follow_up,
                                            'user_tis' => $user_tis,
                                            'certificate_exports' => $certificate_exports,
                                            'certificate_no' => $certificate_no
                                             ]);
    }
       abort(403);

    }else{
        return  redirect(HP::DomainTisiSso());
     }


    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   public function update(Request $request, $token)
    {
        $model = str_slug('applicant','-');
        $data_session     =    HP::CheckSession();

        if(!empty($data_session)){
            if(HP::CheckPermission('edit-'.$model)){

                try {

                    $requestData = $request->all();

                    $certi_lab = CertiLab::where('token',$token)->first();

                    if (!is_null($certi_lab)){

                        $certi_lab = $this->SaveCertiLab($request, $data_session , $token );

                        // Save information
                        $this->SaveInformation($request, $certi_lab);

                        // Save Place
                        $this->SaveCertiLabPlace($request, $certi_lab);

                        //Save lab info
                        $this->SaveCertiLabInfo($request, $certi_lab);

                        if($certi_lab->lab_type == 3){
                            //   6. ขอบข่ายที่ยื่นขอรับการรับรอง (ทดสอบ)
                            if(isset($requestData['test_scope'])){
                                $this->save_certify_test_scope($certi_lab,$requestData);
                            }
                        }else if($certi_lab->lab_type == 4){
                            //   6. ขอบข่ายที่ยื่นขอรับการรับรอง (สอบเทียบ)
                            if(isset($requestData['calibrate'])){
                                $this->save_certifyLab_calibrate($certi_lab,$requestData);
                            }
                        }

                        //ไฟล์แนบ

                        if ( isset($requestData['repeater-section4'] ) ){
                            $this->SaveFileSection($request, 'repeater-section4', 'attachs_sec4', 4 , $certi_lab );
                        }

                        if ( isset($requestData['repeater-section5'] ) ){
                            $this->SaveFileSection($request, 'repeater-section5', 'attachs_sec5', 5 , $certi_lab );
                        }

                        if ( isset($requestData['repeater-section61'] ) ){
                            $this->SaveFileSection($request, 'repeater-section61', 'attachs_sec61', 61 , $certi_lab );
                        }

                        if ( isset($requestData['repeater-section62'] ) ){
                            $this->SaveFileSection($request, 'repeater-section62', 'attachs_sec62', 62 , $certi_lab );
                        }

                        if ( isset($requestData['repeater-section71'] ) ){
                            $this->SaveFileSection($request, 'repeater-section71', 'attachs_sec71', 71 , $certi_lab );
                        }

                        if ( isset($requestData['repeater-section72'] ) ){
                            $this->SaveFileSection($request, 'repeater-section72', 'attachs_sec72', 72 , $certi_lab );
                        }

                        if ( isset($requestData['repeater-section8'] ) ){
                            $this->SaveFileSection($request, 'repeater-section8', 'attachs_sec8', 8 , $certi_lab );
                        }

                        if ( isset($requestData['repeater-section9'] ) ){
                            $this->SaveFileSection($request, 'repeater-section9', 'attachs_sec9', 9 , $certi_lab );
                        }

                        if ( isset($requestData['repeater-section10'] ) ){
                            $this->SaveFileSection($request, 'repeater-section10', 'attachs_sec10', 10 , $certi_lab );
                        }

                        if ( isset($requestData['repeater-section-other'] ) ){
                            $this->SaveFileMore($request, $certi_lab );
                        }

                        // เงื่อนไขเช็คมีใบรับรอง
                        $this->save_certilab_export_mapreq( $certi_lab );

                        if ($certi_lab->status == 3){  // ขอเอกสารเพิ่มเติม
                            $status = 3;  //Mail
                            $requestLab['status'] = 2;
                        }else{
                            $status = 1;  //Mail
                        }

                        // ส่ง Email
                        if($status == 3){
                            $this->SET_EMAIL_Request_Documents($certi_lab);
                        }else{
                            if($certi_lab->status == 1 || $certi_lab->status == 2){
                                $this->SET_EMAIL($certi_lab,$status);
                            }
                        }

                                                ////////////////////////////////////////////////////////////////////////////////////////
                        // add certi lab check box image
                        // if (isset($requestData['attachs_sec4'] )  && $request->hasFile('attachs_sec4')){   // ถ้ามีการแนบ ไฟล์ มา
                        //     $certi_lab_check_box = CertiLabCheckBox::where('app_certi_lab_id',$certi_lab->id)->first();
                        //     if(is_null($certi_lab_check_box)){
                        //     $certi_lab_check_box =  CertiLabCheckBox::create(['app_certi_lab_id' => $certi_lab->id,
                        //                                                         'token'=>str_random(20)
                        //                                                         ]);
                        //     }
                        //     foreach ($request->attachs_sec4 as $index => $attachs_sec4){
                        //         $certi_lab_check_box_image = new CertiLabCheckBoxImage();
                        //         $certi_lab_check_box_image->app_certi_lab_check_box_id = $certi_lab_check_box->id;
                        //         $certi_lab_check_box_image->path_image = $this->storeFile($attachs_sec4,$certi_lab->app_no);
                        //         $certi_lab_check_box_image->file_client_name = HP::ConvertCertifyFileName($attachs_sec4->getClientOriginalName());
                        //         $certi_lab_check_box_image->token = str_random(16);
                        //         $certi_lab_check_box_image->save();
                        //     }
                        // }
                    }


                    return redirect('certify/applicant')->with('message', 'เรียบร้อยแล้ว!');
                } catch (\Exception $e) {

                    echo $e->getMessage();
                    exit;
                    return redirect('certify/applicant')->with('message_error', 'เกิดข้อผิดพลาดกรุณาบันทึกใหม่');
                }
            }else{
                return  redirect(HP::DomainTisiSso());
            }
        }

    }


       // ส่ง Email
        public function SET_EMAIL($certilab,$status = null)
        {

          if(count($certilab->DataEmailDirectorLAB) > 0){

            $config = HP::getConfig();
            $url  =   !empty($config->url_center) ? $config->url_center : url('');

            $request = '';
            if($status == 3){
                $request = 'ได้แก้ไข';
            }else{
                $request = 'ได้ดำเนิน';
            }
            $data_app =  ['email'=>  $certilab->email ?? '-',
                         'certilab'=>  $certilab,
                         'request' => $request,
                         'url'=>  $url.'/certify/check_certificate/'.$certilab->check->id .'/show' ,
                         'email_cc'=>  (count($certilab->DataEmailDirectorLABCC) > 0 ) ? $certilab->DataEmailDirectorLABCC : 'lab1@tisi.mail.go.th'
                        ];


            if(count($certilab->DataEmailDirectorLABCC) > 0){
                $email_cc =  implode(',', $certilab->DataEmailDirectorLABCC);
            }else{
                $email_cc =  'lab1@tisi.mail.go.th';
            }

            $log_email =  HP::getInsertCertifyLogEmail( $certilab->app_no,
                                                    $certilab->id,
                                                    (new CertiLab)->getTable(),
                                                    $certilab->id,
                                                    (new CertiLab)->getTable(),
                                                    1,
                                                    'คำขอรับบริการยืนยันความสามารถห้องปฏิบัติการ',
                                                    view('mail.lab.applicant', $data_app),
                                                    $certilab->created_by,
                                                    $certilab->agent_id,
                                                    null,
                                                    $certilab->email,
                                                    implode(',',(array)$certilab->DataEmailDirectorLAB),
                                                    $email_cc,
                                                    null,
                                                    null
                                                 );

               $html = new CertifyApplicant($data_app);
               $mail =  Mail::to($certilab->DataEmailDirectorLAB)->send($html);

                if(is_null($mail) && !empty($log_email)){
                    HP::getUpdateCertifyLogEmail($log_email->id);
                }

          }

        }
        public function SET_EMAIL_Request_Documents($certilab)
        {
          if(count($certilab->DataEmailDirectorLAB) > 0){

            $config = HP::getConfig();
            $url  =   !empty($config->url_center) ? $config->url_center : url('');

           $data_app = ['email'     =>  $certilab->email,
                        'certilab'  =>  $certilab,
                        'url'       =>  $url.'/certify/check_certificate/'.$certilab->check->id .'/show' ,
                        'email_cc'  =>  (count($certilab->DataEmailDirectorLABCC) > 0 ) ? $certilab->DataEmailDirectorLABCC : 'lab1@tisi.mail.go.th'
                      ];

            $log_email =  HP::getInsertCertifyLogEmail( $certilab->app_no,
                                                    $certilab->id,
                                                    (new CertiLab)->getTable(),
                                                    $certilab->id,
                                                    (new CertiLab)->getTable(),
                                                    1,
                                                    'ขอส่งเอกสารเพิ่มเติม',
                                                    view('mail.lab.request_documents', $data_app),
                                                    $certilab->created_by,
                                                    $certilab->agent_id,
                                                    null,
                                                    $certilab->email,
                                                    implode(',',(array)$certilab->DataEmailDirectorLAB),
                                                    (count($certilab->DataEmailDirectorLABCC) > 0 ) ?   implode(',',(array)$certilab->DataEmailDirectorLABCC) : 'lab1@tisi.mail.go.th',
                                                    null,
                                                    null
                                                 );


             $html = new LABRequestDocumentsMail($data_app);
             $mail =    Mail::to($certilab->DataEmailDirectorLAB)->send($html);

            if(is_null($mail) && !empty($log_email)){
               HP::getUpdateCertifyLogEmail($log_email->id);
            }

          }

        }




    public function updateToEight(Request $request)
    {
        $certi_lab = CertiLab::where('token',$request->findCertiLab)->first();
        $certi_lab->status = 8 ;
        $certi_lab->save();
        if(  $request->hasFile('activity_file')){
            $certi_lab_check = Check::where('app_certi_lab_id',$certi_lab->id)->first();
            $certi_lab_check->invoice = $this->storeFile($request->activity_file,$certi_lab->app_no);
            $certi_lab_check->save();
        }
        // return redirect(route('applicant.index'));
        return redirect('certify/applicant')->with('message', 'เรียบร้อยแล้ว!');
    }

    public function GetPayInOne($id = null,$token = null)
    {
           $previousUrl = app('url')->previous();
           $pay_in  =  CostAssessment::findOrFail($id);

           return view('certify/applicant/form_status.form_pay_in_one',  compact('previousUrl',
                                                                                    'pay_in'
                                                                                  ));
    }
    // Pay-IN ครั้งที่ 1
    public function updateStatusCostAssessment(Request $request ,$id)
    {
             $data_session     =    HP::CheckSession();
    //    try {

            $find_cost_assessment  =  CostAssessment::findOrFail($id);
            if ($request->activity_file15  &&  $request->hasFile('activity_file15')){


            $find_certi_lab = CertiLab::findOrFail($find_cost_assessment->app_certi_lab_id);

            $find_cost_assessment->state = 2;
            $find_cost_assessment->remark = null;
            $find_cost_assessment->status_confirmed = null;
            $find_cost_assessment->invoice =  $this->storeFile($request->activity_file15,$find_certi_lab->app_no) ;
            $find_cost_assessment->invoice_client_name = HP::ConvertCertifyFileName($request->activity_file15->getClientOriginalName()) ;
            $find_cost_assessment->save();



            if(!empty($find_cost_assessment->assessment->auditor_id)){
                // สถานะ แต่งตั้งคณะกรรมการ
                $auditor = BoardAuditor::findOrFail($find_cost_assessment->assessment->auditor_id);
                if(!is_null($auditor)){
                    $auditor->step_id = 5; // แจ้งหลักฐานการชำระเงิน
                    $auditor->save();

                }
            }

            // Log
             $ao = new CostAssessment;
             $history = CertificateHistory::where('table_name',$ao->getTable())
                                                ->where('ref_id',$find_cost_assessment->id)
                                                ->where('system',3)
                                                ->orderby('id','desc')
                                                ->first();
             if(!is_null($history)){
                 $history->update([
                                      'attachs_file'    =>  $find_cost_assessment->invoice ?? null,
                                      'evidence'        =>  $find_cost_assessment->invoice_client_name ?? null,
                                      'updated_by'      =>  auth()->user()->getKey() ,
                                      'date'            =>  date('Y-m-d')
                                    ]);

             }

             // Mail ลท.
            $config = HP::getConfig();
            $url  =   !empty($config->url_center) ? $config->url_center : url('');

             if($find_certi_lab && !is_null($find_certi_lab->email)  && count($find_certi_lab->CertiEmailLt) > 0 ){


                       $data_app = ['certi_lab'      => $find_certi_lab,
                                    'assessment'    => $find_cost_assessment,
                                    'email'         => $find_certi_lab->email,
                                    'url'           =>  $url.'/certify/check_certificate/'.$find_certi_lab->check->id .'/show' ,
                                    'email_cc'      =>  (count($find_certi_lab->DataEmailAndLtLABCC) > 0 ) ? $find_certi_lab->DataEmailAndLtLABCC : 'lab1@tisi.mail.go.th'
                                  ];

                        $log_email =  HP::getInsertCertifyLogEmail( $find_certi_lab->app_no,
                                                                    $find_certi_lab->id,
                                                                    (new CertiLab)->getTable(),
                                                                    $find_cost_assessment->id,
                                                                    (new CostAssessment)->getTable(),
                                                                    1,
                                                                    'แจ้งหลักฐานการชำระค่าบริการในการตรวจประเมิน',
                                                                    view('mail.lab.pay_in1', $data_app),
                                                                    $find_certi_lab->created_by,
                                                                    $find_certi_lab->agent_id,
                                                                    null,
                                                                    $find_certi_lab->email,
                                                                    implode(',',(array)$find_certi_lab->CertiEmailLt),
                                                                    (count($find_certi_lab->DataEmailAndLtLABCC) > 0 ) ?   implode(',',(array)$find_certi_lab->DataEmailAndLtLABCC) : 'lab1@tisi.mail.go.th',
                                                                    null,
                                                                    null
                                                                 );

                            $html = new CertifyPayIn1($data_app);
                            $mail =    Mail::to($find_certi_lab->CertiEmailLt)->send($html);

                            if(is_null($mail) && !empty($log_email)){
                                HP::getUpdateCertifyLogEmail($log_email->id);
                            }


            }

            if(!empty($find_certi_lab->app_no) && !empty($find_cost_assessment->app_certi_assessment_id)){
                        //  เช็คการชำระ
                    $arrContextOptions=array();
                    if(strpos($url, 'https')===0){//ถ้าเป็น https
                        $arrContextOptions["ssl"] = array(
                                                        "verify_peer" => false,
                                                        "verify_peer_name" => false,
                                                    );
                    }
                    file_get_contents($url.'api/v1/checkbill?ref1='.$find_certi_lab->app_no.'-'.$find_cost_assessment->app_certi_assessment_id, false, stream_context_create($arrContextOptions));
            }

        }
            return redirect('certify/applicant')->with('message', 'เรียบร้อยแล้ว!');
    //   } catch (\Exception $e) {
    //         return redirect('certify/applicant')->with('message_error', 'เกิดข้อผิดพลาดกรุณาบันทึกใหม่');
    //   }
    }


    public function updateStatusCostCertificate(Request $request,$id)
    {
        $data_session     =    HP::CheckSession();
        // try {

        $attach_path   = $this->attach_path;
        $find_cost_certificate = CostCertificate::findOrFail($id);
        if(!is_null($find_cost_certificate) &&  isset($request->activity_file19)  &&  $request->hasFile('activity_file19') ){
                //สถานะ
                $find_certi_lab = CertiLab::findOrFail($find_cost_certificate->app_certi_lab_id);
                $find_certi_lab->status = 24; //แจ้งหลักฐานการชำระค่าใบรับรอง
                $find_certi_lab->save();

                $find_cost_certificate->invoice              = $this->storeFile($request->activity_file19,$find_certi_lab->app_no);
                $find_cost_certificate->invoice_client_name = HP::ConvertCertifyFileName($request->activity_file19->getClientOriginalName()) ;
                $find_cost_certificate->save();

                $file = $find_cost_certificate->invoice;
                if( HP::checkFileStorage($attach_path.$file)){
                    HP::getFileStorage($attach_path.$file);
                }

            // Log
              $ao = new CostCertificate;
             $CertificateHistory = CertificateHistory::where('table_name',$ao->getTable())
                                                        ->where('ref_id',$id)
                                                        ->where('system',6)
                                                        ->orderby('id','desc')
                                                        ->first();
            if(!is_null($CertificateHistory)){
                $CertificateHistory->update([
                                                'attachs_file'     =>  $file ?? null,
                                                'evidence'         =>  $find_cost_certificate->invoice_client_name ?? null,
                                                'updated_by'       => auth()->user()->getKey() ,
                                                'date'             => date('Y-m-d')
                                             ]);
            }


        $config = HP::getConfig();
        $url  =   !empty($config->url_center) ? $config->url_center : url('');

            // ส่ง Email เจ้าหน้าที่มอบหมาย
        if(!is_null($find_certi_lab->email) && count($find_certi_lab->CertiEmailLt) > 0){
                    $data_app = [
                                'PayIn'         => $find_cost_certificate,
                                'find_certi'    => $find_certi_lab ,
                                'file'          => $file ,
                                'email'         => $find_certi_lab->email,
                                'url'           =>  $url.'/certify/check_certificate/'.$find_certi_lab->check->id .'/show' ,
                                'email_cc'      =>  (count($find_certi_lab->DataEmailAndLtLABCC) > 0 ) ? $find_certi_lab->DataEmailAndLtLABCC : 'lab1@tisi.mail.go.th'
                               ];

                        $log_email =  HP::getInsertCertifyLogEmail( $find_certi_lab->app_no,
                                                                    $find_certi_lab->id,
                                                                    (new CertiLab)->getTable(),
                                                                    $find_cost_certificate->id,
                                                                    (new CostCertificate)->getTable(),
                                                                    1,
                                                                    'แจ้งหลักฐานการชำระค่าธรรมเนียมคำขอ และค่าธรรมเนียมใบรับรอง',
                                                                    view('mail.lab.pay_in_two', $data_app),
                                                                    $find_certi_lab->created_by,
                                                                    $find_certi_lab->agent_id,
                                                                    null,
                                                                    $find_certi_lab->email,
                                                                    implode(',',(array)$find_certi_lab->CertiEmailLt),
                                                                    (count($find_certi_lab->DataEmailAndLtLABCC) > 0 ) ?   implode(',',(array)$find_certi_lab->DataEmailAndLtLABCC) : 'lab1@tisi.mail.go.th',
                                                                    null,
                                                                    'certify/check/file_client/'.$file.'/'.$find_cost_certificate->invoice_client_name
                                                                 );

                            $html = new CertifyCostCertificate($data_app);
                            $mail =    Mail::to($find_certi_lab->CertiEmailLt)->send($html);

                            if(is_null($mail) && !empty($log_email)){
                                HP::getUpdateCertifyLogEmail($log_email->id);
                            }


           }

           if(!empty($find_certi_lab->app_no)){
                        //  เช็คการชำระ
                    $arrContextOptions=array();
                    if(strpos($url, 'https')===0){//ถ้าเป็น https
                        $arrContextOptions["ssl"] = array(
                                                        "verify_peer" => false,
                                                        "verify_peer_name" => false,
                                                    );
                    }
                    file_get_contents($url.'api/v1/checkbill?ref1='.$find_certi_lab->app_no, false, stream_context_create($arrContextOptions));
            }

          }
            return redirect('certify/applicant')->with('message', 'เรียบร้อยแล้ว!');
    //  } catch (\Exception $e) {
    //         return redirect('certify/applicant')->with('message_error', 'เกิดข้อผิดพลาดกรุณาบันทึกใหม่');
    // }
 }

    public function updateStatusStatusCertificate(Request $request)
    {
    // try {
        $find_certi_lab = CertiLab::where('token',$request->findCertiLab23)->first();
        $find_certificate_export = CertificateExport::where('request_number',$find_certi_lab->app_no)->first();

        if ($request->checkStatusCertificate == 1){
            $find_certi_lab->status = 25 ;
            $find_certi_lab->save();

            $find_certificate_export->status = 2;
            $find_certificate_export->save();
        }

        else{
            $find_certi_lab->status = 24 ;
            $find_certi_lab->save();

            $find_certificate_export->status = 0;
            $find_certificate_export->save();

            $find_desc = new CertificateExportDesc();
            $find_desc->certificate_exports_id = $find_certificate_export->id;
            $find_desc->description_detail = $request->remarkCertificate;
            $find_desc->description_date = Carbon::now();
            $find_desc->save();

            if ($request->another_attach_files_del23 &&  $request->hasFile('another_attach_files_del23')){
                foreach ($request->another_attach_files_del23 as $data){
                    $certificate_export_file = new CertificateExportFile();
                    $certificate_export_file->certificate_exports_id = $find_certificate_export->id;
                    $certificate_export_file->file_path = $this->storeFile($data,$find_certi_lab->app_no);
                    $certificate_export_file->save();
                }
            }

        }

        // return redirect(route('applicant.index'));
        return redirect('certify/applicant')->with('message', 'เรียบร้อยแล้ว!');

        // } catch (\Exception $e) {
        //     return redirect('certify/applicant')->with('message_error', 'เกิดข้อผิดพลาดกรุณาบันทึกใหม่');
        // }
  }



    public function updateStatusNotice(Request $request)
    {
        // try {
        $certi_lab = CertiLab::where('token',$request->certiLab17)->first();
        $find_notice = Notice::where('app_certi_lab_id',$certi_lab->id)->get();

//        dd($request);

        $count_index = 1;
        $number = 0;


        foreach ($request->descNotice as $data){
            $find_notice[$number]->desc = $request->descNotice[$count_index];
            $find_notice[$number]->status_edit = 1;
            $find_notice[$number]->save();


            // add file
            $nameFile = "another_modal_attach_files17".$count_index;
            if ($request->$nameFile  &&  $request->hasFile('nameFile'))
            {
                foreach ($request->$nameFile as $file){
                    $notice_file = new NoticeFile();
                    $notice_file->app_certi_lab_notice_id = $find_notice[$number]->id;
                    $notice_file->file = $this->storeFile($file,$certi_lab->app_no);
                    $notice_file->save();


                }
                $count_index++;
                $number++;
            }

        }
        // return redirect(route('applicant.index'));
            return redirect('certify/applicant')->with('message', 'เรียบร้อยแล้ว!');
        // } catch (\Exception $e) {
        //     return redirect('certify/applicant')->with('message_error', 'เกิดข้อผิดพลาดกรุณาบันทึกใหม่');
        // }
    }

    public function removeFiles($path){
//        $model = str_slug('certificate','-');
//        if(auth()->user()->can('edit-'.$model)) {
            try{
                $file = storage_path().'/files/applicants/check_files/'.$path;
                if (!File::exists($file)) {
                    return Response::make("File does not exist.", 404);
                }
                if(is_file($file)){
                    File::delete($file);
                }else {
                    echo "File does not exist";
                }
                return true;
            }catch (\Exception $x){
                return false;
            }
//        }
//        abort(403);
    }

    public function removeFilesWithMessage($path,$token){
//        $model = str_slug('certificate','-');
//        if(auth()->user()->can('edit-'.$model)) {
            $obj = CertiLabCheckBoxImage::whereToken($token)->first() ?? CertiLabAttachMore::whereToken($token)->first() ?? null;

            try{
                $obj->delete();
                $file = storage_path().'/files/applicants/check_files/'.$path;
                if (!File::exists($file)) {
                    return Response::make("File does not exist.", 404);
                }
                if(is_file($file)){
                    File::delete($file);
                }else {
                    echo "File does not exist";
                }
                return redirect()->back()->with('message', 'ลบไฟล์แล้ว!');
            }catch (\Exception $x){
                echo "เกิดข้อผิดพลาด";
            }
//        }
//        abort(403);
    }

    public function removeFilesCertiLabAttachAll($path,$token){

               $certi_lab_attach = CertiLabAttachAll::where('token',$token)->first();

                try{
                    $certi_lab_attach->delete();
                    $file = storage_path().'/files/applicants/check_files/'.$path;
                    if(is_file($file)){
                        File::delete($file);
                    }
                    return redirect()->back()->with('message', 'ลบไฟล์แล้ว!');
                }catch (\Exception $x){
                    echo "เกิดข้อผิดพลาด";
                }
   }
   public function removeFilesCertiLabAttachhMoreAll($path,$token){
    $certi_lab_attach = CertiLabAttachMore::where('token',$token)->first();
     try{
         $certi_lab_attach->delete();
         $file = storage_path().'/files/applicants/check_files/'.$path;
         if(is_file($file)){
             File::delete($file);
         }
         return redirect()->back()->with('message', 'ลบไฟล์แล้ว!');
     }catch (\Exception $x){
         echo "เกิดข้อผิดพลาด";
     }
}
    public function removeFilesCertiLabCheckBoxImage($path,$token){
           $certi_lab_attach = CertiLabCheckBoxImage::where('token',$token)->first();
            try{
                     $certi_lab_attach->delete();
                     $file = storage_path().'/files/applicants/check_files/'.$path;
                     if(is_file($file)){
                         File::delete($file);
                     }
                return redirect()->back()->with('message', 'ลบไฟล์แล้ว!');
            }catch (\Exception $x){
             echo "เกิดข้อผิดพลาด";
           }
    }
    public function UpdateFileLabInfo($id,$token){
            $certi_lab_info =  CertiLabInfo::findOrFail($id);
        if(!is_null($certi_lab_info)){
            $certi_lab_info->desc_main_file = null;
            $certi_lab_info->save();
        }
        return redirect()->back()->with('message', 'ลบไฟล์แล้ว!');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    //แต่งตั้งคณะผู้ตรวจประเมิน
    public function updateStatusCommentOpinion(Request $request)
    {
    //    try{
        $certi_lab = CertiLab::where('token',$request->certiLab13)  ->orderby('id','desc')->first();
        $assessment = Assessment::where('app_certi_lab_id',$certi_lab->id)->first();
        $assessment_group = AssessmentGroup::where('app_certi_lab_id',$certi_lab->id)->where('app_certi_assessment_id',$assessment->id)->get();

        $count = 1;
        foreach ($assessment_group as $group){
            $status = "status13".$count;
            $file = "another_modal_attach_files13".$count;
            if ($request->$status == 1){
                $group->status = 1 ;
                $group->save();
            }
            else{
                $group->status = 2;
                $group->remark = $request->reason13[$count];
                $group->save();
                if ($request->$file &&  $request->hasFile('file')){
                    $countFile = 0;
                    foreach ($request->$file as $dataFile){
                        $add_file = new AssessmentGroupFile();
                        $add_file->app_certi_assessment_group_id = $group->id;
                        $add_file->file = $this->storeFile($request->$file[$countFile],$certi_lab->app_no);
                        $add_file->save();
                        $countFile++ ;
                    }
                }
            }
            $count++;
        }

        if(count($assessment_group) > 0){
           $assessment =  $assessment_group->last();
           $certi_lab->status =  !empty($assessment->status)? 22 :  20;
        }else{
            $certi_lab->status = 20;
        }
        $certi_lab->save();

        // return redirect(route('applicant.index'));
           return redirect('certify/applicant')->with('message', 'เรียบร้อยแล้ว!');
        // } catch (\Exception $e) {
        //     return redirect('certify/applicant')->with('message_error', 'เกิดข้อผิดพลาดกรุณาบันทึกใหม่');
        // }
    }


    public function deleteApplicant(Request $request)
    {

        $certi_lab = CertiLab::where('id',$request->find_certi_lab)->first();
        $certi_lab->desc_delete = $request->reason;
        $certi_lab->status = 4;
        $certi_lab->save();

        $number = 0 ;
        foreach ($request->another_attach_name as $data){
            $file_delete = new CertiLabDeleteFile();
            $file_delete->app_certi_lab_id = $certi_lab->id;
            $file_delete->name = $request->another_attach_name[$number];
            $file_delete->path = $this->storeFile($request->another_attach_files_del[$number],$certi_lab->app_no);
            $file_delete->save();
            $number ++ ;
        }

        // return redirect(route('applicant.index'));
        return redirect('certify/applicant')->with('message', 'เรียบร้อยแล้ว!');
    }


    public function apiTest(Request $request)
    {
        $branchs = DB::table('bcertify_test_branches')->select('*')->where('state',1)->get();
        return $branchs;
    }

    public function apiCalibrate(Request $request)
    {
        $branchs = DB::table('bcertify_calibration_branches')->select('*')->where('state',1)->get();
        return $branchs;
    }


    public function apiCertificateExports(Request $request)
    {
        $data_session           = HP::CheckSession();

        if ($request->select == 'test'){
            $lab_ability = 3;
        }else{
            $lab_ability = 4;
        }

        $app_certi_labs         = CertiLab::where('lab_type',$lab_ability)->where('tax_id',$data_session->tax_number)->select('id');
        $certificate_exports    = DB::table('certificate_exports')->whereIn('certificate_for',$app_certi_labs)->where('status',3)->get();
        return response()->json($certificate_exports);
    }

    public function get_app_no_and_certificate_exports_no(Request $request)
    {
        $data_session           = HP::CheckSession();
        $standard_id = $request->input('std_id');
        $lab_type_check = $request->input('lab_type');
        if($lab_type_check == 'test'){
            $lab_type = 3;
        }else if($lab_type_check == 'calibrate'){
            $lab_type = 4;
        }

        try {
            $app_certi_lab         = CertiLab::with([
                                                    'certificate_exports_to' => function($q){
                                                        // $q->where('status', 3);
                                                        $q->whereIn('status',['0','1','2','3','4']);
                                                    }
                                                ])
                                                ->where( function($Query) use($data_session){
                                                    if(!is_null($data_session->agent_id)){  // ตัวแทน
                                                        $Query->where('agent_id',  $data_session->agent_id ) ;
                                                    }else{
                                                        if($data_session->branch_type == 1){  // สำนักงานใหญ่
                                                            $Query->where('tax_id',  $data_session->tax_number ) ;
                                                        }else{   // ผู้บันทึก
                                                            $Query->where('created_by', auth()->user()->getKey()) ;
                                                        }
                                                    }
                                                })
                                                ->whereNotIn('status', [0, 4])
                                                ->where('standard_id', $standard_id)
                                                ->where('lab_type', $lab_type)
                                                ->first();
            $data = array(
                'status' => true,
                'app_no' => $app_certi_lab->app_no ?? null,
                'certificate_exports_no' => $app_certi_lab->certificate_exports_to->certificate_no ?? null
            );
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'satus' => false,
                'error_message' => $e
            ]);
        }
    }

    private function save_certilab_export_mapreq($certi_lab)
    {
          $app_certi_lab         = CertiLab::with([
                                                    'certificate_exports_to' => function($q){
                                                        $q->whereIn('status',['0','1','2','3','4']);
                                                    }
                                                ])
                                                ->where('created_by', $certi_lab->created_by)
                                                ->whereNotIn('status', ['0','4'])
                                                ->where('standard_id', $certi_lab->standard_id)
                                                ->where('lab_type', $certi_lab->lab_type)
                                                ->first();
         if(!Is_null($app_certi_lab)){
             $certificate_exports_id = !empty($app_certi_lab->certificate_exports_to->id) ? $app_certi_lab->certificate_exports_to->id : null;
              if(!Is_null($certificate_exports_id)){
                  $mapreq =  CertiLabExportMapreq::where('app_certi_lab_id',$certi_lab->id)->where('certificate_exports_id', $certificate_exports_id)->first();
                  if(Is_null($mapreq)){
                      $mapreq = new  CertiLabExportMapreq;
                  }
                  $mapreq->app_certi_lab_id       = $certi_lab->id;
                  $mapreq->certificate_exports_id = $certificate_exports_id;
                  $mapreq->save();
              }
         }
    }



    public function apiTestItem(Request $request)
    {
        $arr_data = array();
        $test_item = DB::table('bcertify_test_items')->select('*')->where('test_branch_id',$request->select)->get();
        $product_categories = DB::table('bcertify_product_categories')->select('*')->where('test_branch_id',$request->select)->get();
        array_push($arr_data,$test_item,$product_categories);
        return $arr_data;
    }

    function dataApiTestItem($test_branch_id) {
        $arr_data = array();
        // ทดสอบ
        $test_item = DB::table('bcertify_test_items')->select(DB::raw("CONCAT(bcertify_test_branches.title,' - ',bcertify_test_items.title) AS titles"),'bcertify_test_items.id')
                            ->Join('bcertify_test_branches','bcertify_test_branches.id','=','bcertify_test_items.test_branch_id')
                            ->whereIn('bcertify_test_items.test_branch_id',explode(",",$test_branch_id))
                            ->get();
        //  สอบเทียบ
        $product_categories = DB::table('bcertify_product_categories')->select(DB::raw("CONCAT(bcertify_test_branches.title,' - ',bcertify_product_categories.title) AS titles"),'bcertify_product_categories.id')
                            ->Join('bcertify_test_branches','bcertify_test_branches.id','=','bcertify_product_categories.test_branch_id')
                            ->whereIn('bcertify_product_categories.test_branch_id',explode(",",$test_branch_id))
                            ->get();
        array_push($arr_data,$test_item,$product_categories);
        return response()->json($arr_data);
    }

    public function apiTestProduct(Request $request)
    {
        $product = DB::table('bcertify_product_items')->select('*')->where('product_category_id',$request->select)->get();
        return $product;
    }

    public function apiCalibrateItem(Request $request)
    {
        $calibration_groups = DB::table('bcertify_calibration_groups')->select('*')->where('calibration_branch_id',$request->select)->get();
        return $calibration_groups;
    }

    public function apiCalibrateList(Request $request)
    {
        $calibration_list = DB::table('bcertify_calibration_items')->select('*')->where('calibration_group_id',$request->select)->get();
        return $calibration_list;
    }

    public function apiAmphur(Request $request)
    {
        $amphur = DB::table('amphur')->select('AMPHUR_ID','AMPHUR_NAME')->where('PROVINCE_ID',$request->select)->orderbyRaw('CONVERT(AMPHUR_NAME USING tis620)')->get();
        return $amphur;
    }

    public function apiDistrict(Request $request)
    {
        $district = DB::table('district')->select('DISTRICT_ID','DISTRICT_NAME')->where('AMPHUR_ID',$request->select)->orderbyRaw('CONVERT(DISTRICT_NAME USING tis620)')->get();
        return $district;
    }



    public function assess($token)
    {
        $certi_lab = CertiLab::where('token',$token)->first();
        $certi_lab->name = $certi_lab->information->name ?? null;

        $find_notice = Notice::where('app_certi_lab_id',$certi_lab->id)->orderby('id','desc') ->first();
        $certi_lab->assessment_date = HP::revertDate($find_notice->assessment_date->format('Y-m-d'),true) ?? null;
        $certi_lab->DataGroupeTitle = $find_notice->DataGroupeTitle ?? null;
       return view('certify/applicant/form_status.form_status19',[
                                                                   'certi_lab'=>$certi_lab,
                                                                   'find_notice'=>$find_notice
                                                                   ]);
    }

    public function storeFileCut($files, $app_no = 'files_lab', $name = null)
    {
        $no  = str_replace("RQ-","",$app_no);
        $no  = str_replace("-","_",$no);

        if ($files) {
             $attach_path  =  $this->attach_path.$no;
             $file_extension = $files->getClientOriginalExtension();
             $fileClientOriginal   =  HP::ConvertCertifyFileName($files->getClientOriginalName());
             $filename = pathinfo($fileClientOriginal, PATHINFO_FILENAME);
             $fullFileName =  mb_substr(str_replace(" ","",$filename),-20).'-date_time'.date('Ymd_hms') . '.' . $files->getClientOriginalExtension();
             $fileData = mb_convert_encoding($fullFileName, "UTF-8", "auto");

             $storagePath = Storage::putFileAs($attach_path, $files, $fileData );
            $storageName = basename($storagePath); // Extract the filename
            return  $no.'/'.$storageName;
        }else{
            return null;
        }
    }



    public function PassInspection($id,$token)
    {
        $certi_lab = CertiLab::where('token',$token)->first();
        $certi_lab->name = $certi_lab->name ?? null;


        $find_notice = Notice::where('id',base64_decode($id))->first();

        $certi_lab->assessment_date = HP::revertDate($find_notice->assessment_date->format('Y-m-d'),true) ?? null;
        $certi_lab->DataGroupeTitle = $find_notice->DataGroupeTitle ?? null;

       return view('certify.applicant.form_status.form_status15',[
                                                                   'certi_lab'=>$certi_lab,
                                                                   'find_notice'=>$find_notice
                                                                   ]);
    }

    public function inspection_update(Request $request, $id)
    {


    //   try{
         $data_session     =    HP::CheckSession();
         $notice           =    Notice::findOrFail($id);
         $certi_lab        =    CertiLab::findOrFail($notice->app_certi_lab_id);
         $committee        =    BoardAuditor::findOrFail($notice->auditor_id);
         $ao = new Notice;
          if(!is_null($notice)){

             if($request->status_scope == 1){  // update สถานะ



                 $notice->status = 1;
                 $notice->degree = 7;
                 $notice->save();

                // สถานะ แต่งตั้งคณะกรรมการ
                 $committee->step_id = 10; //ยืนยัน Scope
                 $committee->save();

              // สถานะ แต่งตั้งคณะกรรมการ
              $board_auditor = BoardAuditor::where('app_certi_lab_id',$certi_lab->id)
                                                ->whereIn('step_id',[9,10])
                                                ->whereNull('status_cancel')
                                                ->get();
               if(count($board_auditor) == count($certi_lab->certi_auditors_many)){

                  //  สรุปรายงานและเสนออนุกรรมการฯ
                  $report = new Report;
                  $report->app_certi_assessment_id = $notice->app_certi_assessment_id;
                  $report->app_certi_lab_id        = $certi_lab->id;
                  $report->save();
                     // ทบทวน
                //   $certi_lab->review   = 1;
                //   $certi_lab->status   = 12;
                  $certi_lab->status   = 20;
                  $certi_lab->save();

                }

             }else{

            //    $notice->date_scope_edit  = date('Y-m-d h:m:s');
               $notice->status         = 2;
               $notice->degree         = 5;
               $notice->file_scope     =  null;

               $notice->save();


                    if($request->attach_files &&  $request->hasFile('attach_files')){   //  ไฟล์แนบ
                        foreach ($request->attach_files as $key => $itme) {
                        $list  = new  stdClass;
                        $list->attachs =   $this->storeFile($itme,$certi_lab->app_no);
                        $list->attachs_client_name =  HP::ConvertCertifyFileName($itme->getClientOriginalName());
                        $list->file_desc_text =   $request->file_desc_text[$key]  ?? null;
                        $evidence[] = $list;
                        }
                    }


               $committee->step_id = 11; // ขอแก้ไข Scope
               $committee->save();
             }
                $history = CertificateHistory::where('table_name',$ao->getTable())
                                                        ->where('ref_id',$notice->id)
                                                        ->where('system',11)
                                                        ->orderby('id','desc')
                                                        ->first();
                if(!is_null($history)){
                    $history->update([
                                            'status_scope'=> $notice->status ?? null,
                                            'remark'=> $request->remark_scope ?? null,
                                            'evidence'=>  isset($evidence)  ? json_encode($evidence) : null,
                                            'updated_by' =>  auth()->user()->getKey() ,
                                            'date' => date('Y-m-d')
                                            ]);
                }




             //Mail
            if(!is_null($certi_lab->email) && count($certi_lab->DataEmailDirectorLAB) > 0){
                    $config = HP::getConfig();
                    $url  =   !empty($config->url_center) ? $config->url_center : url('');
                   $status_scope = ['1'=>'เห็นชอบกับ Scope','2'=>'ไม่เห็นชอบกับ Scope'];


                    $config = HP::getConfig();
                    $url  =   !empty($config->url_center) ? $config->url_center : url('');

                    $data_app = [
                                'certi_lab'    => $certi_lab,
                                'email'         => $certi_lab->email,
                                'status_scope'  => $request->status_scope ??  '',
                                'remark'        => $request->remark_scope ?? null,
                                'evidence'      =>  isset($evidence)  ?  $evidence : null,
                                'url'           => $url.'/certify/check_certificate/'.$certi_lab->id.'/show' ,
                                'email_cc'      =>  (count($certi_lab->DataEmailDirectorLABCC) > 0 ) ? $certi_lab->DataEmailDirectorLABCC : 'lab1@tisi.mail.go.th'
                                ];

                    $log_email =  HP::getInsertCertifyLogEmail( $certi_lab->app_no,
                                                                $certi_lab->id,
                                                                (new CertiLab)->getTable(),
                                                                $notice->id,
                                                                (new Notice)->getTable(),
                                                                1,
                                                                'ยืนยันขอบข่ายการรับรองหน่วยตรวจ',
                                                                view('mail.lab.certify_confirm_assessment', $data_app),
                                                                $certi_lab->created_by,
                                                                $certi_lab->agent_id,
                                                                null,
                                                                $certi_lab->email,
                                                                implode(',',(array)$certi_lab->DataEmailDirectorLAB),
                                                                (count($certi_lab->DataEmailAndLtLABCC) > 0 ) ?   implode(',',(array)$certi_lab->DataEmailAndLtLABCC) : 'lab1@tisi.mail.go.th',
                                                                null,
                                                                null
                                                                );

                        $html = new CertifyConfirmAssessment($data_app);
                        $mail =    Mail::to($certi_lab->DataEmailDirectorLAB)->send($html);

                        if(is_null($mail) && !empty($log_email)){
                            HP::getUpdateCertifyLogEmail($log_email->id);
                        }

           }

        }


          return redirect('certify/applicant')->with('message', 'เรียบร้อยแล้ว!');
        // } catch (\Exception $e) {
        //     return redirect('certify/applicant')->with('message_error', 'เกิดข้อผิดพลาดกรุณาบันทึกใหม่');
        // }
    }


    public function updateStatusReport(Request $request, $id)
    {
        $data_session     =    HP::CheckSession();
    //  try{

        $Report = Report::findOrFail($id);
        if(!is_null($Report) &&  isset($request->status_confirm)){
               $certi_lab = CertiLab::findOrFail($Report->app_certi_lab_id);
            if(!is_null($Report)){
                $certi_lab->update(['status'=>22]); // ยืนยันจัดทำใบรับรอง
                $Report->update([
                                  'start_date' => date('Y-m-d'),
                                  'updated_by' => auth()->user()->getKey()
                             ]);
                             if($request->cf_cer==1){
                                $Report->update(['cf_cer'=>1]);
                            }
                  $ao = new Report;
                 $CertificateHistory = CertificateHistory::where('table_name',$ao->getTable())
                                                    ->where('ref_id',$Report->id)
                                                    ->where('system',5)
                                                    ->orderby('id','desc')
                                                    ->first();
             if(!is_null($CertificateHistory)){
                $CertificateHistory->update([
                                            'updated_by' =>  auth()->user()->getKey() ,
                                            'date' => date('Y-m-d')
                                           ]);
              }


                //เจ้าหน้าที่มอบหมาย
                if(count($certi_lab->CertiEmailLt) > 0 ){
                    // ส่ง E-mail
                    $config = HP::getConfig();
                    $url  =   !empty($config->url_center) ? $config->url_center : url('');

                    $dataMail = ['1804'=> 'lab1@tisi.mail.go.th','1805'=> 'lab2@tisi.mail.go.th','1806'=> 'lab3@tisi.mail.go.th'];
                    $EMail =  array_key_exists($certi_lab->subgroup,$dataMail)  ? $dataMail[$certi_lab->subgroup] :'admin@admin.com';

                    $data_app =         [
                                            'email'     => $certi_lab->email,
                                            'certi_lab' => $certi_lab,
                                            'url'       => $url.'/certify/check_certificate/'.$certi_lab->id.'/show' ?? '-',
                                            'email_cc'  =>  (count($certi_lab->DataEmailAndLtLABCC) > 0 ) ? $certi_lab->DataEmailAndLtLABCC : 'lab1@tisi.mail.go.th'
                                        ];

                    $log_email =  HP::getInsertCertifyLogEmail($certi_lab->app_no,
                                                            $certi_lab->id,
                                                            (new CertiLab)->getTable(),
                                                            $Report->id,
                                                            (new Report)->getTable(),
                                                            1,
                                                            'ยืนยันสรุปรายงานเสนอคณะกรรมการ/คณะอนุกรรมการ',
                                                            view('mail.lab.report', $data_app),
                                                            $certi_lab->created_by,
                                                            $certi_lab->agent_id,
                                                            null,
                                                            $certi_lab->email,
                                                            implode(',',(array)$certi_lab->CertiEmailLt),
                                                            (count($certi_lab->DataEmailAndLtLABCC) > 0 ) ? implode(',',$certi_lab->DataEmailAndLtLABCC) : $EMail,
                                                            null,
                                                            null
                                                        );

                        $html = new CertifyReport($data_app);
                        $mail =  Mail::to($certi_lab->CertiEmailLt)->send($html);

                        if(is_null($mail) && !empty($log_email)){
                            HP::getUpdateCertifyLogEmail($log_email->id);
                        }

                    }
            }
          }
           return redirect('certify/applicant')->with('message', 'เรียบร้อยแล้ว!');
        // } catch (\Exception $e) {
        //     return redirect('certify/applicant')->with('message_error', 'เกิดข้อผิดพลาดกรุณาบันทึกใหม่');
        // }
    }


      //การประมาณค่าใช้จ่าย
      public function EditCost($token)
      {
        $previousUrl = app('url')->previous();
        $certi_lab = CertiLab::where('token',$token)->first();
        $find_certi_lab_cost =Cost::where('app_certi_lab_id',$certi_lab->id)->get();
        return view('certify/applicant/form_status.form_status11',[
                                                                    'certi_lab'=>$certi_lab,
                                                                    'find_certi_lab_cost'=>$find_certi_lab_cost,
                                                                    'previousUrl' => $previousUrl
                                                                 ]);
      }
      public function updateStatusCost(Request $request,$id)
      {

//   try{
        //   $certiLab = CertiLab::where('token',$request->token)->first();
          $certiLab = CertiLab::findOrFail($id);
          $certi_cost = Cost::where('app_certi_lab_id',$certiLab->id) ->orderby('id','desc')->first();
          $certi_cost->vehicle =  null;
          $certi_cost->status_scope = $request->status_scope ?? null;
          $certi_cost->check_status = $request->check_status ?? null;
          $certi_cost->date =    date('Y-m-d'); //วันที่บันทึก

          $attachs = null;
          $attachs_scope = null;

    if ($request->check_status == 1){
              $certi_cost->agree = 1;
    } else{

              $certi_cost->agree = 2;
              $certi_cost->remark =  $request->remark ?? null;
              if ($request->another_modal_attach_files &&  $request->hasFile('another_modal_attach_files')){  // หลักฐาน เห็นชอบกับค่าใช่จ่ายที่เสนอมา
                foreach ($request->another_modal_attach_files as $key => $dataaa){
                    $certi_cost_find = new CostFile();
                    $certi_cost_find->app_certi_cost_id     = $certi_cost->id;
                    $certi_cost_find->file_desc             = !empty($request->file_desc[$key]) ?  $request->file_desc[$key]:'' ;
                    $certi_cost_find->file                  = $this->storeFile($dataaa,$certiLab->app_no);
                    $certi_cost_find->file_client_name      = HP::ConvertCertifyFileName($dataaa->getClientOriginalName()) ;
                    $certi_cost_find->save();

                    $cost_find = new stdClass();
                    $cost_find->file_desc             = $certi_cost_find->file_desc;
                    $cost_find->file                  =  $certi_cost_find->file ;
                    $cost_find->file_client_name      =   $certi_cost_find->file_client_name  ;
                    $attachs[]                        = $cost_find;
                }
            }
    }

     if ($request->status_scope == 1){
             $certi_cost->agree = 1;
     } else{
             $certi_cost->agree = 2;
             $certi_cost->remark_scope = $request->remark_scope ?? null;
             if ($request->attach_files &&  $request->hasFile('attach_files')){  //หลักฐาน Scope
                foreach ($request->attach_files as $key => $item){
                    $find = new stdClass();
                    $find->file_desc_text        = !empty($request->file_desc_text[$key]) ?  $request->file_desc_text[$key]:'' ;
                    $find->attach_files          = $this->storeFile($item,$certiLab->app_no);
                    $find->file_client_name      = HP::ConvertCertifyFileName($item->getClientOriginalName()) ;
                    $attachs_scope[]             = $find;
                }
             }
     }


    if($request->status_scope == 1 && $request->check_status == 1){

            $certiLab->status = 12; //อยู่ระหว่างแต่งตั้งคณะผู้ตรวจประเมิน
            // $certiLab->status           = 9; // อยู่ระหว่างแต่งตั้งคณะผู้ตรวจประเมิน
            $certi_cost->remark         =   null;  //
            $certi_cost->remark_scope   =  null;  //
            $certi_cost->amount         = $certi_cost->SumAmountTotal ?? null;  //จำนวนเงินที่อนุมัติ

     }else{
            //  $certiLab->status = 7; // ประมาณการค่าใช้จ่าย
            $certiLab->status = 10;//ประมาณการค่าใช้จ่าย


     }
            $certi_cost->save();
            $certiLab->save(); // ตารางหลัก


       $config = HP::getConfig();
        $url  =   !empty($config->url_center) ? $config->url_center : url('');
     $check_status = ['1'=>'ยืนยัน','2'=>'แก้ไข'];
     $status_scope = ['1'=>'ยื่นยัน Scope','2'=>'ขอแก้ไข Scope'];
         $title = '';
     if($request->status_scope == 2 && $request->check_status == 2){
         $title = 'ขอแก้เห็นชอบกับค่าใช่จ่ายที่เสนอมา/เห็นชอบกับ Scope (การประมาณค่าใช้จ่าย)' ;
     }elseif($request->check_status == 2){
         $title = 'ขอแก้เห็นชอบกับค่าใช่จ่ายที่เสนอมา (การประมาณค่าใช้จ่าย)' ;
     }elseif($request->status_scope == 2){
         $title = 'ขอแก้เห็นชอบกับ Scope (การประมาณค่าใช้จ่าย)' ;
     }else{
         $title = 'การประมาณค่าใช้จ่าย' ;
     }


         $this->CertificateHistory($certi_cost,$attachs,$attachs_scope);


     if(!is_null($certiLab->email) && count($certiLab->DataEmailDirectorLAB) > 0){  //  ส่ง E-mail


            $dataMail = ['1804'=> 'lab1@tisi.mail.go.th','1805'=> 'lab2@tisi.mail.go.th','1806'=> 'lab3@tisi.mail.go.th'];
            $EMail =  array_key_exists($certiLab->subgroup,$dataMail)  ? $dataMail[$certiLab->subgroup] :'admin@admin.com';
            $data_app =         ['email'             => $certiLab->email,
                                  'certiLab'         => $certiLab,
                                  'certi_cost'       =>  $certi_cost,
                                  'check_status'     => array_key_exists($certi_cost->check_status,$check_status)   ? $check_status[$certi_cost->check_status]   :  '-',
                                  'status_scope'     => array_key_exists($certi_cost->status_scope,$status_scope)   ? $status_scope[$certi_cost->status_scope]   :  '-',
                                  'attachs'          => !is_null($attachs) ? $attachs : '-',
                                  'attachs_scope'    => !is_null($attachs_scope) ? $attachs_scope : '-',
                                  'url'              =>  $url.'/certify/estimated_cost/'. $certi_cost->id .'/edit' ,
                                  'email_cc'         =>  (count($certiLab->DataEmailDirectorLABCC) > 0 ) ? $certiLab->DataEmailDirectorLABCC : $EMail
                                ];

            $log_email =  HP::getInsertCertifyLogEmail($certiLab->app_no,
                                                    $certiLab->id,
                                                    (new CertiLab)->getTable(),
                                                    $certi_cost->id,
                                                    (new Cost)->getTable(),
                                                    1,
                                                    'การประมาณการค่าใช้จ่าย',
                                                    view('mail.lab.certify_cost', $data_app),
                                                    $certiLab->created_by,
                                                    $certiLab->agent_id,
                                                    null,
                                                    $certiLab->email,
                                                    implode(',',(array)$certiLab->DataEmailDirectorLAB),
                                                    (count($certiLab->DataEmailDirectorLABCC) > 0 ) ? implode(',',$certiLab->DataEmailDirectorLABCC) : $EMail,
                                                    null,
                                                    null
                                                 );

               $html = new CertifyCost($data_app);
               $mail =  Mail::to($certiLab->DataEmailDirectorLAB)->send($html);

                if(is_null($mail) && !empty($log_email)){
                    HP::getUpdateCertifyLogEmail($log_email->id);
                }

      }




        if($request->previousUrl){
            return redirect("$request->previousUrl")->with('message', 'เรียบร้อยแล้ว!');
        }else{
            return redirect('certify/applicant')->with('message', 'เรียบร้อยแล้ว!');
        }

    // } catch (\Exception $e) {
    //     return redirect('certify/applicant')->with('message_error', 'เกิดข้อผิดพลาดกรุณาบันทึกใหม่');
    // }
  }

       //   คณะผู้ตรวจประเมิน
      public function EditAuditor($token)
      {
        $model = str_slug('applicant','-');
   
         if(!is_null(auth()->user()) &&   HP::CheckPermission('edit-'.$model)){ 
            $previousUrl = app('url')->previous();
            $certi_lab = CertiLab::where('token',$token)->first();
            $auditor = BoardAuditor::where('certi_no',$certi_lab->app_no)->get();

            return view('certify.applicant.form_status.form_status13',[
                                                                    'certi_lab'=>$certi_lab,
                                                                    'auditor'=>$auditor,
                                                                    'previousUrl' => $previousUrl
                                                                 ]);
        }else{
              return  redirect(HP::DomainTisiSso());
         }

      }
      public function updateStatusAuditor(Request $request ,$token)
      {
         $data_session     =    HP::CheckSession();
  $model = str_slug('applicant','-');

 if(!empty(auth()->user()) &&  HP::CheckPermission('edit-'.$model)){ 
//    try{     
        $certiLab = CertiLab::where('token',$token)->first();

        if(!is_null($certiLab)){
            $authorities = [];
            $data = [];


            foreach ($request->auditors_id as $key => $item){
                         $auditors = BoardAuditor::where('id',$item)->orderby('id','desc')->first();
                 if(!is_null($auditors)){
                        $auditors->status = $request->status[$item] ?? 2;
                    if($request->status == 2){
                        $auditors->remark =  $request->remark[$item] ?? null;
                        $auditors->vehicle =  2;
                        $auditors->step_id =  1; //อยู่ระหว่างแต่งตั้งคณะผู้ตรวจประเมิน
                    }else{
                        $auditors->remark = null;
                        $auditors->step_id =  3; //เห็นชอบการแต่งตั้งคณะผู้ตรวจประเมิน
                    }
                        $auditors->save();


                    // ไฟล์แนบ
                    $attachs = [];
                    if (isset($request->another_modal_attach_files[$item])){
                    foreach ($request->another_modal_attach_files[$item] as $key => $attach){
                            $find = new stdClass();
                            $find->file_desc             = $request->file_desc[$key] ?? null;
                            $find->file                  = $this->storeFile($attach,$certiLab->app_no);
                            $find->file_client_name      = HP::ConvertCertifyFileName($attach->getClientOriginalName());
                            $attachs[]                   =  $find;
                        }
                    }


                    $ao = new BoardAuditor;
                    $history = CertificateHistory::where('table_name',$ao->getTable())
                                                            ->where('ref_id',$item)
                                                            ->where('system',2)
                                                            ->orderby('id','desc')
                                                            ->first();

                    if(!is_null($history)){
                         $history->status        =  $auditors->status;
                         $history->attachs_file  = (count($attachs) > 0) ?  json_encode($attachs) : null;
                         $history->updated_by    =   auth()->user()->getKey();
                         $history->date          =  date('Y-m-d') ;
                         $history->save();
                    }



                    // pay in ครั้งที่ 1
                   if($auditors->status == 1){
                            // $assessment = Assessment::where('app_certi_lab_id',$certiLab->id)->where('auditor_id',$auditors->id)->first();
                            $assessment = Assessment::where('app_certi_lab_id',$certiLab->id)->where('auditor_id',$item)->first();
                            if(!is_null($assessment)){
                                    $cost_ass = CostAssessment::where('app_certi_lab_id',$certiLab->id)->where('app_certi_assessment_id',$assessment->id)->first();
                                if(is_null($cost_ass)){
                                    $cost_ass = new CostAssessment;
                                }
                                    $cost_ass->app_certi_assessment_id  = $assessment->id  ?? null;
                                    $cost_ass->app_certi_lab_id         = $certiLab->id;
                                    $cost_ass->save();
                            }
                    }

                    $list = new stdClass();  // หมายเลขตำขอไม่เห็นชอบ
                    $list->auditor      =    $auditors->auditor  ?? null;
                    $list->status       =    $auditors->status  ?? null;
                    $list->created_at   =    $auditors->created_at  ?? null;
                    $list->updated_at   =    $auditors->updated_at  ?? null;
                    $list->remark       =    $auditors->remark ?? null;
                    $list->attachs      =    (count($attachs) > 0) ?  json_encode($attachs) : null;
                    $authorities[]      =    $list;

               }

          }

                    $dataMail = ['1804'=> 'lab1@tisi.mail.go.th','1805'=> 'lab2@tisi.mail.go.th','1806'=> 'lab3@tisi.mail.go.th'];
                    $EMail =  array_key_exists($certiLab->subgroup,$dataMail)  ? $dataMail[$certiLab->subgroup] :'admin@admin.com';

                    // ส่ง E-mail
                        $config = HP::getConfig();
                        $url  =   !empty($config->url_center) ? $config->url_center : url('');
                    if(!is_null($certiLab->email) && count($authorities) > 0){  //  ส่ง E-mail  เจ้าหน้าที่รับผิดชอบ

                       $data_app = ['email'         => $certiLab->email,
                                    'certi_Lab'     => $certiLab,
                                    'authorities'   => count($authorities) > 0 ?  $authorities : '-',
                                    'url'           => $url.'/certify/check_certificate/'.$certiLab->check->id .'/show' ,
                                    'email_cc'      => (count($certiLab->DataEmailDirectorLABCC) > 0 ) ? $certiLab->DataEmailDirectorLABCC : $EMail
                                    ];

                        $log_email =  HP::getInsertCertifyLogEmail( $certiLab->app_no,
                                                                    $certiLab->id,
                                                                    (new CertiLab)->getTable(),
                                                                    $auditors->id,
                                                                    (new BoardAuditor)->getTable(),
                                                                    1,
                                                                    'การแต่งตั้งคณะผู้ตรวจประเมิน',
                                                                    view('mail.lab.auditors', $data_app),
                                                                    $certiLab->created_by,
                                                                    $certiLab->agent_id,
                                                                    null,
                                                                    $certiLab->email,
                                                                    implode(',',(array)$certiLab->DataEmailDirectorLAB),
                                                                    (count($certiLab->DataEmailDirectorLABCC) > 0 ) ?   implode(',',(array)$certiLab->DataEmailDirectorLABCC) : 'lab1@tisi.mail.go.th',
                                                                    null,
                                                                    null
                                                                 );

                            $html = new CertifyBoardAuditor($data_app);
                            $mail =    Mail::to($certiLab->DataEmailDirectorLAB)->send($html);

                            if(is_null($mail) && !empty($log_email)){
                                HP::getUpdateCertifyLogEmail($log_email->id);
                            }


                    }

                    if(!is_null($certiLab->email)  && count($certiLab->CertiEmailLt) > 0){  //  ส่ง E-mail เจ้าหน้าที ลท. CB

                      $data_app = ['email'          => $certiLab->email,
                                    'certi_Lab'     => $certiLab,
                                    'authorities'   => count($authorities) > 0 ?  $authorities : '-',
                                    'url'           => $url.'/certify/check_certificate/'.$certiLab->check->id .'/show' ,
                                    'email_cc'      => (count($certiLab->DataEmailDirectorLABCC) > 0 ) ? $certiLab->DataEmailDirectorLABCC : $EMail
                                  ];

                        $log_email =  HP::getInsertCertifyLogEmail( $certiLab->app_no,
                                                                    $certiLab->id,
                                                                    (new CertiLab)->getTable(),
                                                                    $auditors->id,
                                                                    (new BoardAuditor)->getTable(),
                                                                    1,
                                                                    'การแต่งตั้งคณะผู้ตรวจประเมิน',
                                                                    view('mail.lab.con_firm_auditors', $data_app),
                                                                    $certiLab->created_by,
                                                                    $certiLab->agent_id,
                                                                    null,
                                                                    $certiLab->email,
                                                                    implode(',',(array)$certiLab->CertiEmailLt),
                                                                    (count($certiLab->DataEmailDirectorLABCC) > 0 ) ?   implode(',',(array)$certiLab->DataEmailDirectorLABCC) : 'lab1@tisi.mail.go.th',
                                                                    null,
                                                                    null
                                                                 );

                            $html = new CertifyConFirmAuditorsMail($data_app);
                            $mail =    Mail::to($certiLab->CertiEmailLt)->send($html);

                            if(is_null($mail) && !empty($log_email)){
                                HP::getUpdateCertifyLogEmail($log_email->id);
                            }
                    }



                if($request->previousUrl){
                    return redirect("$request->previousUrl")->with('message', 'เรียบร้อยแล้ว!');
                }else{
                    return redirect('certify/applicant-cb')->with('message', 'เรียบร้อยแล้ว!');
                }



         }else{
            return redirect('certify/applicant')->with('message', 'เรียบร้อยแล้ว!');
         }

    // } catch (\Exception $e) {
    //     return redirect('certify/applicant')->with('message_error', 'เกิดข้อผิดพลาดกรุณาบันทึกใหม่');
    // }
}else{
    return  redirect(HP::DomainTisiSso());
}
 }


      //บันทึกการแก้ไขข้อบกพร่อง / ข้อสังเกต
      public function EditAssessment($id,$token)
      {
        $previousUrl = app('url')->previous();
        $certi_lab = CertiLab::where('token',$token)->first();
        $assessment = Notice::where('id',base64_decode($id))->first();

        return view('certify/applicant/form_status.form_status16',[
                                                                    'certi_lab'=>$certi_lab,
                                                                    'assessment'=>$assessment,
                                                                    'previousUrl' => $previousUrl
                                                                 ]);
      }

     public function UpdateAssessment(Request $request, $id){


//   try {
            $data_session     =    HP::CheckSession();
            $tb               =    new Notice;

            $assessment       =    Notice::findOrFail($id);
            $assessment->degree = 2;
            $assessment->save();

            $certi_lab        =    $assessment->applicant;

            $requestData = $request->all();
            if(isset($requestData["detail"]) ){
                $detail = (array)$requestData["detail"];
                foreach ($detail['id'] as $key => $item) {
                        $notice_itme = NoticeItem::where('id',$item)->first();
                        $notice_itme->details =  $detail["details"][$key] ?? $notice_itme->details;
                                $assessment->check_file = 'false';
                            if($request->attachs  && $request->hasFile('attachs')){
                                $notice_itme->attachs             =  array_key_exists($key, $request->attachs) ?  $this->storeFile($request->attachs[$key],$certi_lab->app_no): @$notice_itme->attachs;
                                $notice_itme->attachs_client_name =  array_key_exists($key, $request->attachs) ?  HP::ConvertCertifyFileName($request->attachs[$key]->getClientOriginalName()) : @$notice_itme->attachs_client_name;
                                $assessment->check_file  = 'true';
                            }
                        $notice_itme->save();

                    }
            }

            $ao = new Notice;
           $CertificateHistory = CertificateHistory::where('table_name',$ao->getTable())
                                                   ->where('ref_id',$assessment->id)
                                                   ->where('system',4)
                                                   ->orderby('id','desc')
                                                   ->first();

          $NoticeItem = NoticeItem::select('remark','report','no','type','status','file_status','reporter','reporter_id','attachs','attachs','comment','comment_file','details','attachs_client_name')
                                        ->where('app_certi_lab_notice_id',$assessment->id)
                                        ->get()
                                        ->toArray();
            if(!is_null($CertificateHistory)){
               $CertificateHistory->update([
                                         'details_table'=>  (count($NoticeItem) > 0) ? json_encode($NoticeItem) : null,
                                         'updated_by' =>  auth()->user()->getKey() ,
                                         'date' => date('Y-m-d')
                                       ]);
             }


             if(!is_null($certi_lab) && count($certi_lab->DataEmailDirectorLAB) > 0 ){
                        // ส่ง E-mail
                        $config = HP::getConfig();
                        $url  =   !empty($config->url_center) ? $config->url_center : url('');
                        $data_app = [
                                        'email'     => $certi_lab->email,
                                        'assessment'=>$assessment,
                                        'certi_lab' =>$certi_lab,
                                        'url'       =>$url.'certify/check_certificate/'.$certi_lab->id.'/show',
                                        'email_cc'  =>  (count($certi_lab->DataEmailDirectorLABCC) > 0 ) ? $certi_lab->DataEmailDirectorLABCC : 'lab1@tisi.mail.go.th'
                                   ];

                        $log_email =  HP::getInsertCertifyLogEmail( $certi_lab->app_no,
                                                                    $certi_lab->id,
                                                                    (new CertiLab)->getTable(),
                                                                    $assessment->id,
                                                                    (new Notice)->getTable(),
                                                                    1,
                                                                    'แจ้งแนวทางแก้ไข/ส่งหลักฐานการแก้ไขข้อบกพร่อง',
                                                                    view('mail.lab.assessment', $data_app),
                                                                    $certi_lab->created_by,
                                                                    $certi_lab->agent_id,
                                                                    null,
                                                                    $certi_lab->email,
                                                                    implode(',',(array)$certi_lab->DataEmailDirectorLAB),
                                                                    (count($certi_lab->DataEmailDirectorLABCC) > 0 ) ?   implode(',',(array)$certi_lab->DataEmailDirectorLABCC) : 'lab1@tisi.mail.go.th',
                                                                    null,
                                                                    null
                                                                 );

                            $html = new CertifySaveAssessment($data_app);
                            $mail =    Mail::to($certi_lab->DataEmailDirectorLAB)->send($html);

                            if(is_null($mail) && !empty($log_email)){
                                HP::getUpdateCertifyLogEmail($log_email->id);
                            }


              }


       return redirect('certify/applicant')->with('message', 'เรียบร้อยแล้ว!');

    // } catch (\Exception $e) {
    //    return redirect('certify/applicant')->with('message_error', 'เกิดข้อผิดพลาดกรุณาบันทึกใหม่');
    // }
  }


      private function save_certi_tools_calibrate($main, $requestData){
        CertiToolsCalibrate::where('app_certi_lab_id', $main->id)->delete();
        foreach($requestData as $key => $itme) {
            if($itme != null || $itme != ''){
                $input = [];
                $input['app_certi_lab_id'] = $main->id;
                $input['name'] = $itme;
                $input['token'] = $main->token;
                CertiToolsCalibrate::create($input);
            }

        }
      }

      private function save_Certi_Tools_Test($main, $requestData){
        CertiToolsTest::where('app_certi_lab_id', $main->id)->delete();
        foreach($requestData as $key => $itme) {
          if($itme != null || $itme != ''){
            $input = [];
            $input['app_certi_lab_id'] = $main->id;
            $input['name'] = $itme;
            $input['token'] = $main->token;
            CertiToolsTest::create($input);
          }
        }
      }
      public function CertificateHistory($data,$file1 = null,$file2 = null)
      {
        $data_session     =    HP::CheckSession();
          $ao = new Cost;
          $Cost = Cost::select('app_certi_assessment_id', 'app_certi_lab_id', 'checker_id', 'draft', 'remark', 'agree', 'status_scope','check_status','remark_scope','amount','date','created_at')
                            ->where('id',$data->id)
                            ->first();
            if($file1 != null){
                $attachs_file  =  $file1;
            }
            if($file2 != null){
                $evidence  =  $file2;
            }
        $CertificateHistory = CertificateHistory::where('table_name',$ao->getTable())
                                                ->where('ref_id',$data->id)
                                                ->where('system',1)
                                                ->orderby('id','desc')
                                                ->first();
         if(!is_null($CertificateHistory)){
            $CertificateHistory->update([
                                      'details'=>   !is_null($Cost) ? json_encode($Cost) : null,
                                      'check_status'=> $data->check_status ?? null ,
                                      'status_scope'=> $data->status_scope ?? null ,
                                      'attachs_file'=>  isset($attachs_file) ?  json_encode($attachs_file) : null,
                                      'evidence'=>  isset($evidence) ?  json_encode($evidence) : null,
                                      'updated_by' => auth()->user()->getKey() ,
                                      'date' => date('Y-m-d')
                                    ]);


          }
      }

      //log
      public function DataLogLab($token)
      {
        $previousUrl = app('url')->previous();
        $certi_lab = CertiLab::where('token',$token)->first();

        // ประวัติคำขอ
         $history  =  CertificateHistory::where('app_no',$certi_lab->app_no)
                                        ->orwhere('app_no',"RQ-".$certi_lab->app_no)
                                        ->orderby('id','desc')
                                        ->get();

        return view('certify/applicant.log',['certi_lab'=>$certi_lab,
                                                     'history' => $history,
                                                      'previousUrl' => $previousUrl
                                                     ]);
      }
      public function DataShow($token = null,$id = null)
      {
          $previousUrl = app('url')->previous();
          $history  =  CertificateHistory::findOrFail($id);

          return view('certify/applicant/history.history_detail',  compact('previousUrl',
                                                                    'history'
                                                                  ));
      }

    public function get_certificate(Request $request)
    {
        $id = $request->get('id');


        $certificate_no = DB::table('certificate_exports AS exports')
                                ->leftJoin('app_certi_labs AS lab', 'lab.id', '=', 'exports.certificate_for')
                                ->select('exports.*', 'lab.app_no')
                                ->where('exports.id',$id)
                                ->first();

        return response()->json($certificate_no);

    }

    public function draft_pdf($certilab_id = null)
    {

        if(!is_null($certilab_id)){

                $CertiLab = CertiLab::findOrFail($certilab_id);


                if($certilab_id == 21){
                    $certilab_id = 7;
                }

                // return $certilab_id;
                 $formula = Formula::where('id', $CertiLab->type_standard)
                                        ->whereState(1)->first();

                // if(!is_null($file) && !is_null($file->attach_pdf) ){

                     $url  =   url('/certify/check_files_lab/'. rtrim(strtr(base64_encode($certilab_id), '+/', '-_'), '=') );
                    //ข้อมูลภาพ QR Code
                     $image_qr = QrCode::format('png')->merge('plugins/images/tisi.png', 0.2, true)
                                  ->size(500)->errorCorrection('H')
                                  ->generate($url);

                // }

            // $last   = CertificateExport::where('certificate_for',$CertiLab->id)->whereYear('created_at',Carbon::now())->count() + 1;

            // $lab_type = ['1'=>'Testing','2'=>'Cal','3'=>'IB','4'=>'CB'];
            // $accreditation_no = '';
            // if(array_key_exists("1",$lab_type)){
            //     $accreditation_no .=  $lab_type[1].'-';
            // }
            // if(!is_null($CertiLab->app_no)){
            //     $app_no = explode('-', $CertiLab->app_no);
            //     $accreditation_no .= $app_no[2].'-';
            // }
            // if(!is_null($last)){
            //     $accreditation_no .=  str_pad($last, 3, '0', STR_PAD_LEFT).'-'.(date('Y') +543);
            // }

            //     $CertiLab->accreditation_no  =   $accreditation_no ? $accreditation_no : '&emsp;' ;

                $data_export = [
                    'app_no'             => $CertiLab->app_no,
                    'name'               => !empty($CertiLab->lab_name) ? $CertiLab->lab_name : '&emsp;' ,
                    'name_en'            =>  isset($CertiLab->lab_name_en) ?  '('.$CertiLab->lab_name_en.')' : '&emsp;',
                    'lab_name_font_size' => $this->CalFontSize($this->FormatAddress($CertiLab)),
                    'address'            => $this->FormatAddress($CertiLab),
                    'lab_name_font_size_address' => $this->CalFontSize($this->FormatAddress($CertiLab)),
                    'address_en'         => !empty($CertiLab->lab_address_no_eng) ? '('.$this->FormatAddressEn($CertiLab).')'  : '&emsp;',
                    // 'accreditation_no'   => $CertiLab->accreditation_no,
                    // 'accreditation_no_en'   => $CertiLab->accreditation_no_en,
                    'formula_title'      => !empty($CertiLab->get_standard->title) ? $CertiLab->get_standard->title : null,
                    'formula_title_en'      => !empty($CertiLab->get_standard->title_en) ? $CertiLab->get_standard->title_en : '&emsp;' ,
                    'image_qr'           => isset($image_qr) ? $image_qr : null,
                    'url'                => isset($url) ? $url : null,

                   ];

                    $config = ['instanceConfigurator' => function ($mpdf) {
                        $mpdf->SetWatermarkText('DRAFT');
                        $mpdf->watermark_font = 'DejaVuSansCondensed';
                        $mpdf->showWatermarkText = true;
                        $mpdf->watermarkTextAlpha = 0.12;
                    }];

              $pdf = Pdf::loadView('certify/applicant/pdf/draft-thai', $data_export, [], $config);
              return $pdf->stream("certificate-thai.pdf");

        }

        abort(403);

    }

 //คำนวนขนาดฟอนต์ของชื่อหน่วยงานผู้ได้รับรอง
 private function CalFontSize($certificate_for){
    $alphas = array_combine(range('A', 'Z'), range('a', 'z'));
    $thais = array('ก','ข', 'ฃ', 'ค', 'ฅ', 'ฆ','ง','จ','ฉ','ช','ซ','ฌ','ญ', 'ฎ', 'ฏ', 'ฐ','ฑ','ฒ'
    ,'ณ','ด','ต','ถ','ท','ธ','น','บ','ป','ผ','ฝ','พ','ฟ','ภ','ม','ย','ร','ล'
    ,'ว','ศ','ษ','ส','ห','ฬ','อ','ฮ', 'ำ', 'า', 'แ');

            if(function_exists('mb_str_split')){
            $chars = mb_str_split($certificate_for);
            }else if(function_exists('preg_split')){
            $chars = preg_split('/(?<!^)(?!$)/u', $certificate_for);
            }

            $i = 0;
            foreach ($chars as $char) {
                if(in_array($char, $alphas) || in_array($char, $thais)){
                    $i++;
                }
            }

            // if($i>40 && $i<50){
            //     $font = 12;
            // }  else if($i>50 && $i<60){
            //     $font = 11;
            // }  else if($i>60 && $i<70){
            //     $font = 10;
            // }  else if($i>70 && $i<80){
            //     $font = 9;
            // }  else if($i>80 && $i<90){
            //     $font = 8;
            // }  else if($i>90 && $i<100){
            //     $font = 7;
            // }  else if($i>100 && $i<120){
            //     $font = 6;
            // }  else if($i>120 && $i<130){
            //     $font = 5;
            // }  else if($i>130){
            //     $font = 4;
            // }   else{
            //     $font = 12;
            // }

            if($i>60 && $i<70){
                $font = 10;
            }  else if($i>70 && $i<80){
                $font = 9;
            }  else if($i>80 && $i<90){
                $font = 8;
            }  else if($i>90 && $i<100){
                $font = 7;
            }  else if($i>100 && $i<120){
                $font = 6;
            }  else if($i>120){
                $font = 5;
            }  else{
                $font = 11;
            }


            return $font;

        }

 private function CalFontSizeCondition($certificate_for){
    $alphas = array_combine(range('A', 'Z'), range('a', 'z'));
    $thais = array('ก','ข', 'ฃ', 'ค', 'ฅ', 'ฆ','ง','จ','ฉ','ช','ซ','ฌ','ญ', 'ฎ', 'ฏ', 'ฐ','ฑ','ฒ'
    ,'ณ','ด','ต','ถ','ท','ธ','น','บ','ป','ผ','ฝ','พ','ฟ','ภ','ม','ย','ร','ล'
    ,'ว','ศ','ษ','ส','ห','ฬ','อ','ฮ', 'ำ', 'า', 'แ');

            if(function_exists('mb_str_split')){
            $chars = mb_str_split($certificate_for);
            }else if(function_exists('preg_split')){
            $chars = preg_split('/(?<!^)(?!$)/u', $certificate_for);
            }

            $i = 0;
            foreach ($chars as $char) {
                if(in_array($char, $alphas) || in_array($char, $thais)){
                    $i++;
                }
            }

            if($i>60 && $i<70){
                $font = 10;
            }  else if($i>70 && $i<80){
                $font = 9;
            }  else if($i>80 && $i<90){
                $font = 8;
            }  else if($i>90 && $i<100){
                $font = 7;
            }  else if($i>100 && $i<120){
                $font = 6;
            }  else if($i>120 && $i<130){
                $font = 5;
            }  else if($i>130){
                $font = 4;
            }  else{
                $font = 11;
            }
            return $font;

     }

        //ที่อยู่ ที่อยู่ห้องปฏิบัติการ
private function FormatAddress($request){

    $address   = '';
    $address .= $request->address_no;

    if($request->allay!=''){
      $address .=  " หมู่ที่ " . $request->allay;
    }

    if($request->village_no!='' && $request->village_no !='-'  && $request->village_no !='--'){
      $address .=  " ซอย "  . $request->village_no;
    }

    if($request->road!='' && $request->road !='-'  && $request->road !='--'){
      $address .=  " ถนน ".$request->road;
    }

    if($request->district!=''){
        if(trim($request->province_to->PROVINCE_NAME)=='กรุงเทพมหานคร'){
            $address .= " แขวง".$request->district;
        }else{
            $address .= " ตำบล".$request->district;

        }
    }

    if($request->amphur!=''){
        if(trim($request->province_to->PROVINCE_NAME)=='กรุงเทพมหานคร'){
            $address .= " เขต".$request->amphur;
        }else{
            $address .= " อำเภอ".$request->amphur;
        }
    }

    if($request->province!=''){
        if(trim($request->province_to->PROVINCE_NAME)=='กรุงเทพมหานคร'){
            $address .=  " ".trim($request->province_to->PROVINCE_NAME);
        }else{
            $address .=  " จังหวัด".trim($request->province_to->PROVINCE_NAME);
        }
    }

    return $address;

}




 //ที่อยู่ ที่อยู่ห้องปฏิบัติการ EN
private function FormatAddressEn($request){
    $address   = [];
    $address[] = $request->lab_address_no_eng;

    if($request->lab_moo_eng!=''){
      $address[] =    'Moo '.$request->lab_moo_eng;
    }

    if($request->lab_soi_eng!='' && $request->lab_soi_eng !='-'  && $request->lab_soi_eng !='--'){
      $address[] =   $request->lab_soi_eng;
    }
    if($request->lab_street_eng!='' && $request->lab_street_eng !='-'  && $request->lab_street_eng !='--'){
        $address[] =   $request->lab_street_eng.',';
    }
    if($request->lab_district_eng!='' && $request->lab_district_eng !='-'  && $request->lab_district_eng !='--'){
        $address[] =   $request->lab_district_eng.',';
    }
    if($request->lab_amphur_eng!='' && $request->lab_amphur_eng !='-'  && $request->lab_amphur_eng !='--'){
        $address[] =   $request->lab_amphur_eng.',';
    }
    if(!empty($request->province_eng_to->PROVINCE_NAME_EN)){
        $address[] =   $request->province_eng_to->PROVINCE_NAME_EN;
    }
    if($request->lab_postcode_eng!='' && $request->lab_postcode_eng !='-'  && $request->lab_postcode_eng !='--'){
        $address[] =   $request->lab_postcode_eng;
    }
    return implode(' ', $address);
}


    public function get_appno(Request $request)
    {
            /*
            select * from app_certi_labs
            WHERE tax_id = '0185558000227' AND lab_type = 3 AND standard_id = 1 AND status NOT IN (4)

            โดยที่
            tax_id //ตามผู้ที่ดำเนินการ
            lab_type //ดึงจากตามที่เลือก
            standard_id //ตามที่เลือก
            */

        $tax_id = $request->get('tax_id');
        $lab_type = $request->get('lab_type');
        $standard_id = $request->get('standard_id');

        $where_condition = ['tax_id'=>$tax_id,'lab_type'=>$lab_type,'standard_id'=>$standard_id];
        $result = CertiLab::where($where_condition)->whereNotIn('status', [4])->select('app_no')->first();

        return response()->json($result);

    }



}
