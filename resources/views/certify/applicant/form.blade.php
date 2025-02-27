@push('css')
    <link href="{{asset('plugins/components/bootstrap-datepicker-thai/css/datepicker.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('plugins/components/icheck/skins/all.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/components/summernote/summernote.css')}}" rel="stylesheet" type="text/css" />

    <style type="text/css">
        .input_background_color{
            background-color: rgb(232, 253, 246);
        }
        .input-show[disabled]{
            background-color: #FFFFFF;
        }

        .label-height{
            line-height: 16px;
        }

        .font_size{
            font-size: 10px;
        }

    </style>
@endpush


@php
    $attach_path = 'files/applicants/check_files/';

    if( !isset($certi_lab->id) ){

        $certi_lab = new stdClass;

        $certi_lab->applicanttype_id = $tis_data->applicanttype_id;
        $certi_lab->app_name = $tis_data->name;
        $certi_lab->tax_number = !empty($tis_data->tax_number) ? $tis_data->tax_number : null;

        $certi_lab->hq_subdistrict_id = !empty($tis_data->subdistrict_id)? $tis_data->subdistrict_id : null;
        $certi_lab->hq_district_id = !empty($tis_data->district_id)? $tis_data->district_id : null;
        $certi_lab->hq_province_id = !empty($tis_data->province_id)? $tis_data->province_id : null;

        $certi_lab->address_headquarters = !empty($tis_data->address_no)? $tis_data->address_no : null;
        $certi_lab->headquarters_alley = !empty($tis_data->soi)? $tis_data->soi : null;
        $certi_lab->headquarters_road = !empty($tis_data->street)? $tis_data->street : null;
        $certi_lab->headquarters_village_no = !empty($tis_data->moo)? $tis_data->moo : null;

        $certi_lab->headquarters_district = !empty($tis_data->subdistrict)? $tis_data->subdistrict : null;
        $certi_lab->headquarters_amphur = !empty($tis_data->district)? $tis_data->district : null;
        $certi_lab->headquarters_province = !empty($tis_data->province)? $tis_data->province : null;
        $certi_lab->headquarters_postcode = !empty($tis_data->zipcode)? $tis_data->zipcode : null;

        $certi_lab->headquarters_tel_fax = !empty($tis_data->fax)? $tis_data->fax : null;
        $certi_lab->headquarters_tel = !empty($tis_data->tel)? $tis_data->tel : null;
        $certi_lab->registerDate = !empty($tis_data->date_niti)? $tis_data->date_niti : null;

        $certi_lab->contactor_name = !empty($tis_data->contact_name)?$tis_data->contact_name:null;
        $certi_lab->email = !empty($tis_data->email)?$tis_data->email:null;
        $certi_lab->contact_tel = !empty($tis_data->contact_tel)?$tis_data->contact_tel:null; // โทรศัพท์ผู้ติดต่อ
        $certi_lab->telephone = !empty($tis_data->contact_phone_number)?$tis_data->contact_phone_number:null; // โทรศัพท์มือถือ

    }else{

        // $certi_information = App\Models\Certify\Applicant\Information::where('app_certi_lab_id',$certi_lab->id)->first();

        $certi_lab->app_name   = !empty($certi_lab->name)? $certi_lab->name : null;
        $certi_lab->tax_number = !empty($certi_lab->tax_id)? $certi_lab->tax_id : null;

        $certi_lab->address_headquarters    = !empty($certi_lab->hq_address)?$certi_lab->hq_address:null;
        $certi_lab->headquarters_alley      = !empty($certi_lab->hq_soi)?$certi_lab->hq_soi:null;
        $certi_lab->headquarters_road       = !empty($certi_lab->hq_road)?$certi_lab->hq_road:null;
        $certi_lab->headquarters_village_no = !empty($certi_lab->hq_moo)?$certi_lab->hq_moo:null;

        $certi_lab->headquarters_district = !empty($certi_lab->HqSubdistrictName)?$certi_lab->HqSubdistrictName:null;
        $certi_lab->headquarters_amphur   = !empty($certi_lab->HqDistrictName)?$certi_lab->HqDistrictName:null;
        $certi_lab->headquarters_province = !empty($certi_lab->HqProvinceName)?$certi_lab->HqProvinceName:null;
        $certi_lab->headquarters_postcode = !empty($certi_lab->hq_zipcode)?$certi_lab->hq_zipcode:null;
        
        $certi_lab->headquarters_tel_fax = !empty($certi_lab->hq_fax)?$certi_lab->hq_fax:null;
        $certi_lab->headquarters_tel     = !empty($certi_lab->hq_telephone)?$certi_lab->hq_telephone:null;
        $certi_lab->registerDate         = !empty($certi_lab->hq_date_registered)?$certi_lab->hq_date_registered:null;

    }   

    

@endphp

@include ('certify.applicant.forms.form_infomation')
@include ('certify.applicant.forms.form_request')
@include ('certify.applicant.forms.form_model')

@include ('certify.applicant.forms.form_evidence')

@if( isset($certi_lab->desc_delete) && isset($certi_lab->id) && !is_null($certi_lab->desc_delete))

<fieldset class="white-box">
    <legend>
        <h4>
            ยกเลิกคำขอ  
        </h4>
    </legend>

    <div class="col-md-12">
        <div class="col-md-4 text-right"> สาเหตุ :</div>
        <div class="col-md-6 text-light">
            <p> {{  !empty($certi_lab->desc_delete)  ? $certi_lab->desc_delete : '-' }}</p>
        </div>
    </div>

    <div class="clearfix"></div>

    @if (isset($certi_lab->id) && $CertiLabDeleteFile->count() > 0)
        <div class="row">
            @foreach($CertiLabDeleteFile as $data)
                @if ($data->path)
                    <div class="col-md-12 form-group">
                        <div class="col-md-4 text-light"> </div>
                        <div class="col-md-6 text-light">
                            {{ @$data->name }}
                            <a href="{{url('certify/check/files/'.$data->path)}}" target="_blank">
                                {!! HP::FileExtension($data->path)  ?? '' !!}
                                    {{ basename($data->path) }}
                            </a>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

</fieldset>

@endif

{{-- <center> --}}
    <div class="row form-group">
        <div class="col-md-12">
            <div class="checkbox checkbox-success">
                <input id="checkbox_confirm" class="checkbox_confirm" type="checkbox" name="checkbox_confirm"  required value="1" 
                    {{ (isset($certi_lab->id) && $certi_lab->checkbox_confirm  == 1) ? 'checked': '' }}
                >
                <label for="checkbox_confirm"> &nbsp; ห้องปฏิบัติการทดสอบและสอบเทียบขอรับรองว่า (LAB hereby affirms certify that)
                    <button type="button" class="btn btn-link" data-toggle="modal" data-target="#myModal"><b>คลิก</b> </button>
                </label>
            </div>
        </div>
    </div>
{{-- </center> --}}


<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog  modal-lg" role="document">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <p>
                    (1) ข้าพเจ้ารับทราบและให้คำมั่นจะปฏิบัติตามพระราชบัญญัติการมาตรฐานแห่งชาติ พ.ศ. 2551 รวมถึงกฎกระทรวง ประกาศ หลักเกณฑ์ วิธีการ และเงื่อนไข มาตรฐานข้อกำหนดสำหรับการรับรองระบบงาน ข้อกำหนดอื่น ๆ และ/หรือ ที่จะมีการกำหนด แก้ไขเพิ่มเติมในภายหลังด้วย 
                    <br>
                    I have acknowledged and committed to continually fulfil the requirements for accreditation and the other obligations of the conformity assessment body, and to comply with National Standardization Act, B.E.2551 (2008) including ministerial regulations, notification, criteria methods and conditions according to the act, standard requirement, conditions determined by TISI and/or any changes in future
                </p>
                <p>
                    (2) ข้าพเจ้าจะชำระค่าธรรมเนียมคำขอรับใบรับรองและใบรับรองทันทีที่ได้รับใบแจ้งการชำระเงินจากสำนักงานมาตรฐานผลิตภัณฑ์อุตสาหกรรม 
                    <br>
                    I will pay application fee, and certificate document fee upon receiving the Pay-in Slip from TISI without delays.
                </p>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script src="{{asset('plugins/components/icheck/icheck.min.js')}}"></script>
    <script src="{{asset('plugins/components/icheck/icheck.init.js')}}"></script>
    <script src="{{asset('js/mask/jquery.inputmask.bundle.min.js')}}"></script>
    <script src="{{asset('js/mask/mask.init.js')}}"></script>
    <!-- input calendar thai -->
    <script src="{{ asset('plugins/components/bootstrap-datepicker-thai/js/bootstrap-datepicker.js') }}"></script>
    <!-- thai extension -->
    <script src="{{ asset('plugins/components/bootstrap-datepicker-thai/js/bootstrap-datepicker-thai.js') }}"></script>
    <script src="{{ asset('plugins/components/bootstrap-datepicker-thai/js/locales/bootstrap-datepicker.th.js') }}"></script>

    <script src="{{ asset('plugins/components/summernote/summernote.js') }}"></script>
    <script src="{{ asset('plugins/components/summernote/summernote-ext-specialchars.js') }}"></script>

    <script src="{{asset('plugins/components/repeater/jquery.repeater.min.js')}}"></script>

    <script src="{{asset('js/jasny-bootstrap.js')}}"></script>

    <script>
        $(document).ready(function () {
            
            //ปฎิทิน
            $('.mydatepicker_th').datepicker({
                toggleActive: true,
                language:'th-th',
                format: 'dd/mm/yyyy'
            });
            $('.check-readonly').prop('disabled', true);//checkbox ความคิดเห็น
            $('.check-readonly').parent().removeClass('disabled');
            $('.check-readonly').css({"background-color": "rgb(238, 238, 238);","border-radius":"50%", "cursor": "not-allowed"});

        
            $('#checkbox_confirm').click(function(){
                button_save();
            });button_save();

            //Validate
            $('#app_certi_form').parsley().on('field:validated', function() {
                var ok = $('.parsley-error').length === 0;
                $('.bs-callout-info').toggleClass('hidden', !ok);
                $('.bs-callout-warning').toggleClass('hidden', ok);
            }).on('form:submit', function() {
                // Text
                $.LoadingOverlay("show", {
                    image       : "",
                    text        : "กำลังบันทึก กรุณารอสักครู่..."
                });
                return true; // Don't submit form for this demo
            });

            $('.repeater-form-file').repeater({
                show: function () {
                    $(this).slideDown();
                    box_section4_remove();
                    box_section5_remove();
                    
                    box_section61_remove();
                    box_section62_remove();

                    box_section71_remove();
                    box_section72_remove();
                    box_section8_remove();
                    box_section9_remove();
                    box_section10_remove();
                    box_more_remove();
                },
                hide: function (deleteElement) {
                    if (confirm('คุณต้องการลบแถวนี้ ?')) {
                        $(this).slideUp(deleteElement);
                       
                        setTimeout(function(){

                            box_section4_remove();
                            box_section5_remove();
                            
                            box_section61_remove();
                            box_section62_remove();

                            box_section71_remove();
                            box_section72_remove();

                            box_section8_remove();
                            box_section9_remove();
                            box_section10_remove();
                            box_more_remove();

                        }, 400);
                    }
                }
            });

            box_section4_remove();
            box_section5_remove();
            box_section71_remove();
            box_section72_remove();

            box_section61_remove();
            box_section62_remove();

            box_section8_remove();
            box_section9_remove();
            box_section10_remove();
            box_more_remove();

        });

        function box_section4_remove(){

            if($('.attachs_sec4').length > 1){
                $('button.delete-sec4').show();
            }else{
                $('button.delete-sec4').hide();
            }

        }

        function box_section5_remove(){

            if($('.attachs_sec5').length > 1){
                $('button.delete-sec5').show();
            }else{
                $('button.delete-sec5').hide();
            }

        }

        function box_section71_remove(){

            if($('.attachs_sec71').length > 1){
                $('button.delete-sec71').show();
            }else{
                $('button.delete-sec71').hide();
            }

        }

        function box_section72_remove(){

            if($('.attachs_sec72').length > 1){
                $('button.delete-sec72').show();
            }else{
                $('button.delete-sec72').hide();
            }

        }

        function box_section61_remove(){

            if($('.attachs_sec61').length > 1){
                $('button.delete-sec61').show();
            }else{
                $('button.delete-sec61').hide();
            }

        }

        function box_section62_remove(){

            if($('.attachs_sec62').length > 1){
                $('button.delete-sec62').show();
            }else{
                $('button.delete-sec62').hide();
            }

        }

        function box_section8_remove(){

            if($('.attachs_sec8').length > 1){
                $('button.delete-sec8').show();
            }else{
                $('button.delete-sec8').hide();
            }

        }

        function box_section9_remove(){

            if($('.attachs_sec9').length > 1){
                $('button.delete-sec9').show();
            }else{
                $('button.delete-sec9').hide();
            }

        }

           function box_section10_remove(){

            if($('.attachs_sec10').length > 1){
                $('button.delete-sec10').show();
            }else{
                $('button.delete-sec10').hide();
            }

        }

        function box_more_remove(){

            if($('.another_attach_files').length > 1){
                $('button.delete-more').show();
            }else{
                $('button.delete-more').hide();
            }

        }

        function checkNone(value) {
            return value !== '' && value !== null && value !== undefined;
        }

        function  CheckFile(){
            $('.check_file').bind('change', function() {
                var size =   this.files[0].size/1024/1024 ; // หน่วย MB
                if(size > 4){
                    Swal.fire(
                            'ขนาดไฟล์เกินกว่า 4 GB',
                            '',
                            'info'
                            )
                    this.value = '';
                    return false;
                } 
            });
        } 

        function submit_form(status) {

            var  number =  1;
            var max_size = "{{ ini_get('post_max_size') }}";
            var res = max_size.replace("M", "");
            $('#app_certi_form').find('input[type="file"]').each(function(index, el) {
                if(checkNone($(el).val()) && $(el).prop("tagName")=="INPUT" && $(el).prop("type")=="file"   ){
                    number +=  (el.files[0].size /1024/1024);
                }
            });

            var row = $("input[name=lab_ability]:checked").val();
            if(row == 'test'){ // ห้องปฏิบัติการ (ทดสอบ)
                var test_scope_branch_id = $(".test_scope_branch_id").length;
                if(test_scope_branch_id > 0){
                        if(number < res){
                            $('div#status_btn').html('<input type="text" name="save" value="' + status + '" hidden>');
                            setTimeout(function () {
                                $('#app_certi_form').submit();
                            }, 500);
                        }else{
                            Swal.fire(
                                'ขนาดไฟล์รวม '+number.toFixed(2)+' MB ไม่สามารถบันทึกได้ ต้องไม่เกิน ' + res + ' MB',
                                '',
                                'warning'
                            )
                        }
                }else{
                    Swal.fire(
                        'กรุณาเลือก ขอบข่ายที่ยื่นขอรับการรับรองห้องปฏิบัติการ (ทดสอบ)',
                        '',
                        'info'
                     )
                }
            }else{  //ห้องปฏิบัติการ (สอบเทียบ)
                var calibrate_branch_id = $(".calibrate_branch_id").length;
                if(calibrate_branch_id > 0){
                       if(number < res){
                            $('div#status_btn').html('<input type="text" name="save" value="' + status + '" hidden>');
                            setTimeout(function () {
                                $('#app_certi_form').submit();
                            }, 500);
                        }else{
                            Swal.fire(
                                'ขนาดไฟล์รวม '+number.toFixed(2)+' MB ไม่สามารถบันทึกได้ ต้องไม่เกิน ' + res + ' MB',
                                '',
                                'warning'
                            )
                         }
                }else{
                    Swal.fire(
                        'กรุณาเลือก ขอบข่ายที่ยื่นขอรับการรับรองห้องปฏิบัติการ (สอบเทียบ)',
                        '',
                        'info'
                    )
                }
            }
        }

        //ฉบับร่าง
        function  submit_form_draft(status){
            var  number =  1;
            var max_size = "{{ ini_get('post_max_size') }}";
            var res = max_size.replace("M", "");
            $('#app_certi_form').find('input[type="file"]').each(function(index, el) {
                if(checkNone($(el).val()) && $(el).prop("tagName")=="INPUT" && $(el).prop("type")=="file"   ){
                    number +=  (el.files[0].size /1024/1024);
                }
            });

            Swal.fire({
                title: 'ยืนยันการทำรายงาน ฉบับร่าง!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                    if (result.value) {
                        if(number < res){
                            $('#checkbox_confirm').attr('required',false);
                            $('div#status_btn').html('<input type="text" name="draft" value="' + status + '" hidden>');
                            setTimeout(function () {
                                $('#app_certi_form').submit();
                            }, 500);
                        }else{
                            Swal.fire(
                                        'ขนาดไฟล์รวม '+number.toFixed(2)+' MB ไม่สามารถบันทึกได้ ต้องไม่เกิน ' + res + ' MB',
                                        '',
                                        'warning'
                                    )
                        }
                    }
                })
        }

        function button_save(){
            if($('#checkbox_confirm').is(':checked')){
                $('button[name="save"]').attr('disabled', false);
                $('button[name="save"]').removeClass('btn-default').addClass('btn-primary');
            }else{
                $('button[name="save"]').attr('disabled', true);
                $('button[name="save"]').removeClass('btn-primary').addClass('btn-default');
            }
        }

        function filterEngAndNumberOnlyCustomForPage(obj){
            var orgi_text     = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ\/,.':;_()-&0123456789 ";
            var str           = $(obj).val();
            var str_length    = str.length;
            var Result = "";
            for(i=0;i<str_length;i++){
                var Char_At = str.charAt(i);
                if(orgi_text.indexOf(Char_At)!==-1){//อักษรเป็นภาษาไทย
                    Result += Char_At;
                }
            }
            $(obj).val(Result);
        }
    </script>
@endpush