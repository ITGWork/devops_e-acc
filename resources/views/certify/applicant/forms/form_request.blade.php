<fieldset class="white-box">
    <legend><h4>ข้อมูลขอรับบริการ</h4></legend>

    <input type="hidden" name="_token" value="{{ csrf_token() }}"/>

    @php
        $Formula_Arr = App\Models\Bcertify\Formula::where('applicant_type',3)->orderbyRaw('CONVERT(title USING tis620)')->pluck('title','id');

        // $province = DB::table('province')->select('*')->get();

        $province = DB::table('province')->select('PROVINCE_ID','PROVINCE_NAME', 'PROVINCE_NAME_EN')->orderbyRaw('CONVERT(PROVINCE_NAME USING tis620)')->get();

        $formulas = DB::table('bcertify_formulas')->select('*')->where('applicant_type',3)->orWhere('applicant_type',4)->get();
        
        $app_check = [];
        if( !isset($certi_lab->id) ){

            $app_check = App\Models\Certify\Applicant\CertiLab::where( function($Query) use($tis_data){
                                                                    if(!is_null($tis_data->agent_id)){  // ตัวแทน
                                                                        $Query->where('agent_id',  $tis_data->agent_id ) ;
                                                                    }else{
                                                                        if($tis_data->branch_type == 1){  // สำนักงานใหญ่
                                                                            $Query->where('tax_id',  $tis_data->tax_number ) ;
                                                                        }else{   // ผู้บันทึก
                                                                            $Query->where('created_by',   auth()->user()->getKey()) ;
                                                                        }
                                                                    }
                                                                })
                                                                ->get();

  
        }else{
            $tis_data = DB::table('sso_users')->where('id', $certi_lab->created_by )->first();


        }

   
    @endphp

    @if (count($formulas)==1)
        <div class="form-group {{ $errors->has('according_formula') ? 'has-error' : ''}}">
            {!! HTML::decode(Form::label('according_formula', '<span class="text-danger">*</span> ตามมาตรฐานเลข'.':'.'<br/><span class="  font_size">(According to TIS)</span>', ['class' => 'col-md-3 control-label label-height'])) !!}
            <div class="col-md-4" >
                {!! Form::select('according_formula',$Formula_Arr, !empty( $certi_lab->standard_id )?$certi_lab->standard_id:$formulas[0]->id, ['class' => 'form-control', 'id'=>'according_formula','readonly' => 'readonly','required' => true]) !!}
                {!! $errors->first('according_formula', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    @else
        <div class="form-group {{ $errors->has('according_formula') ? 'has-error' : ''}}">
            {!! HTML::decode(Form::label('according_formula', '<span class="text-danger">*</span> ตามมาตรฐานเลข'.':'.'<br/><span class="  font_size">(According to TIS)</span>', ['class' => 'col-md-3 control-label label-height'])) !!}
            <div class="col-md-4" >
                {!! Form::select('according_formula',$Formula_Arr, !empty( $certi_lab->standard_id )?$certi_lab->standard_id:null, ['class' => 'form-control', 'id'=>'according_formula','required' => true, 'placeholder' =>'- เลือกตามมาตรฐานเลข -']) !!}
                {!! $errors->first('according_formula', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    @endif

    
    <div class="form-group {{ $errors->has('lab_ability') ? 'has-error' : ''}}">
        {!! HTML::decode(Form::label('lab_ability', 'ความสามารถห้องปฏิบัติการ'.':'.'<br/><span class="  font_size">(Laboratory)</span>', ['class' => 'col-md-3 control-label label-height'])) !!}
        <label class="col-md-2  label-height" >
            {!! Form::radio('lab_ability', 'test', !empty( $certi_lab->lab_type ) && $certi_lab->lab_type == '3' ?true:true, ['class'=>'check checkLab', 'data-radio'=>'iradio_square-green','id'=>'lab_ability_test']) !!}
            &nbsp;ทดสอบ  <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span class=" font_size">(Testing)</span>
        </label>
        <label class="col-md-2  label-height" >
            {!! Form::radio('lab_ability', 'calibrate', !empty( $certi_lab->lab_type ) && $certi_lab->lab_type == '4' ?true:false, ['class'=>'check checkLab', 'data-radio'=>'iradio_square-red','id'=>'lab_ability_calibrate']) !!}
            &nbsp;สอบเทียบ <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span class=" font_size">(Calibration)</span>
        </label>
        {!! $errors->first('lab_ability', '<p class="help-block">:message</p>') !!}
    </div>

    @if( isset($certi_lab->id) && !empty($certi_lab->purpose_type) )

        <div class="form-group {{ $errors->has('purpose') ? 'has-error' : ''}}">
            {!! HTML::decode(Form::label('lab_name', 'วัตถุประสงค์ในการยื่นคำขอ'.':'.'<br/><span class=" font_size">(Apply to NSC for)</span>', ['class' => 'col-md-3 control-label label-height'])) !!}
            <label  class="col-md-2 label-height">
                {!! Form::radio('purpose', '1', $certi_lab->purpose_type == 1 ?true:false, ['class'=>'check', 'data-radio'=>'iradio_square-green','id'=>'purpose1']) !!}
                &nbsp;ยื่นขอครั้งแรก <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span class="font_size">(initial assessment)</span>
            </label>
            <label  class="col-md-2 label-height">
                {!! Form::radio('purpose', '2', $certi_lab->purpose_type == 2 ?true:false, ['class'=>'check', 'data-radio'=>'iradio_square-green','id'=>'purpose2']) !!}
                &nbsp;ต่ออายุใบรับรอง <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span class="font_size">(renewal)</span>
            </label>
            <label  class="col-md-2 label-height">
                {!! Form::radio('purpose', '3', $certi_lab->purpose_type == 3 ?true:false, ['class'=>'check', 'data-radio'=>'iradio_square-green','id'=>'purpose3']) !!}
                &nbsp;ขยายขอบข่าย <br> &nbsp;&nbsp;&nbsp;
                <span class="font_size">(extending accreditation)</span>
            </label>
            <label  class="col-md-3 label-height">
                {!! Form::radio('purpose', '4', $certi_lab->purpose_type == 4 ?true:false, ['class'=>'check', 'data-radio'=>'iradio_square-green','id'=>'purpose4']) !!}
                &nbsp;การเปลี่ยนแปลงมาตรฐาน <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span class="font_size">(standard change)</span>
            </label>
            {!! $errors->first('purpose', '<p class="help-block">:message</p>') !!}
        </div>
        
    @else
        <div class="form-group {{ $errors->has('purpose') ? 'has-error' : ''}}">
            {!! HTML::decode(Form::label('lab_name', 'วัตถุประสงค์ในการยื่นคำขอ'.':'.'<br/><span class=" font_size">(Apply to NSC for)</span>', ['class' => 'col-md-3 control-label label-height'])) !!}
            <label  class="col-md-2 label-height" >
                {!! Form::radio('purpose', '1', false, ['class'=>'check', 'data-radio'=>'iradio_square-green','id'=>'purpose1']) !!}
                &nbsp;ยื่นขอครั้งแรก <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span class="font_size">(initial assessment)</span>
            </label>
            <label  class="col-md-2 label-height">
                {!! Form::radio('purpose', '2', false, ['class'=>'check', 'data-radio'=>'iradio_square-green','id'=>'purpose2']) !!}
                &nbsp;ต่ออายุใบรับรอง <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span class="font_size">(renewal)</span>
            </label>
            <label  class="col-md-2 label-height">
                {!! Form::radio('purpose', '3', false, ['class'=>'check', 'data-radio'=>'iradio_square-green','id'=>'purpose3']) !!}
                &nbsp;ขยายขอบข่าย <br> &nbsp;&nbsp;&nbsp;
                <span class="font_size">(extending accreditation)</span>
            </label>
            <label  class="col-md-3 label-height">
                {!! Form::radio('purpose', '4', false, ['class'=>'check', 'data-radio'=>'iradio_square-green','id'=>'purpose4']) !!}
                &nbsp;การเปลี่ยนแปลงมาตรฐาน <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span class="font_size">(standard change)</span>
            </label>
            {!! $errors->first('purpose', '<p class="help-block">:message</p>') !!}
        </div>

    @endif
    <div id="box_ref_application_no" style="display: none;">
        <div class="form-group {{ $errors->has('ref_application_no') ? 'has-error' : ''}}">
            {!! HTML::decode(Form::label('ref_application_no', 'อ้างอิงเลขที่คำขอ'.':'.'<br/><span class=" font_size">(Application No.)</span>', ['class' => 'col-md-3 control-label  label-height'])) !!}
            <div class="col-md-4">
                {!! Form::text('ref_application_no', !empty($certi_lab->app_no)?$certi_lab->app_no:null, ['class' => 'form-control', 'id' => 'ref_application_no']) !!}
                {!! $errors->first('ref_application_no', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
        <div class="form-group {{ $errors->has('certificate_exports_id') ? 'has-error' : ''}}">
            {!! HTML::decode(Form::label('certificate_exports_id', 'ใบรับรองเลขที่'.':'.'<br/><span class="  font_size">(Certificate No)</span>', ['class' => 'col-md-3 control-label label-height'])) !!}
            <div class="col-md-4">
                {!! Form::text('certificate_exports_id', !empty($certi_lab->certificate_exports_id)?$certi_lab->certificate_exports_id:null, ['class' => 'form-control', 'id' => 'certificate_exports_id']) !!}
                {!! $errors->first('certificate_exports_id', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
        <div class="form-group {{ $errors->has('accereditation_no') ? 'has-error' : ''}}">
            {!! HTML::decode(Form::label('accereditation_no', '<span class="text-danger">*</span> หมายเลขการรับรองที่'.':'.'<br/><span class="  font_size">(Accreditation No. Calibration)</span>', ['class' => 'col-md-3 control-label label-height'])) !!}
            <div class="col-md-4">
                {!! Form::text('accereditation_no', !empty($certi_lab->accereditation_no)?$certi_lab->accereditation_no:null, ['class' => 'form-control', 'id' => 'accereditation_no']) !!}
                {!! $errors->first('accereditation_no', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>

    {{-- @if( count($app_check) > 0 && count($certificate_exports) == 0)
        @php 
            $app_first = $app_check->first();
        @endphp
        <div class="form-group {{ $errors->has('ref_application_no') ? 'has-error' : ''}}">
            {!! HTML::decode(Form::label('ref_application_no', 'เลขที่คำขอ'.':'.'<br/><span class=" font_size">(Application No.)</span>', ['class' => 'col-md-3 control-label  label-height'])) !!}
            <div class="col-md-4">
                {!! Form::text('ref_application_no', !empty( $app_first )?$app_first->app_no:null, ['class' => 'form-control','required' => true, 'readonly' => true]) !!}
                {!! $errors->first('ref_application_no', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    @endif
    @if (count($certificate_exports) > 0)
          
    @if (count($certificate_no)==1)
        <div class="form-group div_certificate_exports_id{{ $errors->has('certificate_exports_id') ? 'has-error' : ''}}">
            {!! HTML::decode(Form::label('certificate_exports_id', '<span class="text-danger">*</span> ใบรับรองเลขที่'.':'.'<br/><span class="  font_size">(Certificate No)</span>', ['class' => 'col-md-3 control-label label-height'])) !!}
            <div class="col-md-4" >
                {!! Form::select('certificate_exports_id', 
                   $certificate_exports,
                 !empty($certi_lab->certificate_exports_id)?$certi_lab->certificate_exports_id: $certificate_no[0]->id,
                  ['class' => 'form-control',
                   'id'=>'certificate_exports_id',
                    'required' => true,
                    'placeholder' =>'- ใบรับรองเลขที่ -'
                    ]) !!}
                {!! $errors->first('certificate_exports_id', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    @elseif (count($certificate_no) > 0)
        <div class="form-group div_certificate_exports_id{{ $errors->has('certificate_exports_id') ? 'has-error' : ''}}">
            {!! HTML::decode(Form::label('certificate_exports_id', '<span class="text-danger">*</span> ใบรับรองเลขที่'.':'.'<br/><span class="  font_size">(Certificate No)</span>', ['class' => 'col-md-3 control-label label-height'])) !!}
            <div class="col-md-4" >
                {!! Form::select('certificate_exports_id',
                 $certificate_exports,
                 !empty($certi_lab->certificate_exports_id)?$certi_lab->certificate_exports_id:null,
                 ['class' => 'form-control',
                 'id'=>'certificate_exports_id',
                 'required' => true,
                  'placeholder' =>'- ใบรับรองเลขที่ -']) !!}
                {!! $errors->first('certificate_exports_id', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    @endif

    @if (count($certificate_no) > 0)
        <div class="form-group div_certificate_exports_id{{ $errors->has('ref_application_no') ? 'has-error' : ''}}">
            {!! HTML::decode(Form::label('ref_application_no', 'เลขที่คำขอ'.':'.'<br/><span class=" font_size">(Application No.)</span>', ['class' => 'col-md-3 control-label  label-height'])) !!}
            <div class="col-md-4">
                {!! Form::text('ref_application_no', null, ['class' => 'form-control','required' => true, 'readonly' => true]) !!}
                {!! $errors->first('ref_application_no', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    @endif   
@endif --}}


    <div class="form-group {{ $errors->has('branch_type') ? 'has-error' : ''}}">
        {!! HTML::decode(Form::label('branch_type', '<span class="text-danger">*</span> ประเภทสาขา'.':'.'<br/><span class=" font_size">(Branch Type)</span>', ['class' => 'col-md-3 control-label  label-height'])) !!}
        <div class="col-md-4" >
             <div class="row">
            
                    {!! Form::radio('branch_type', '1',  !empty( $certi_lab->branch_type ) && $certi_lab->branch_type == '1' ?true:(!empty($tis_data->branch_type ) && $tis_data->branch_type == '1' ?true:false), ['class'=>'check check-readonly', 'data-radio'=>'iradio_square-blue','id'=>'branch_type1']) !!}
                    <label   for="branch_type1">   &nbsp;สำนักงานใหญ่&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;    </label>
             
                    {!! Form::radio('branch_type', '2',  !empty( $certi_lab->branch_type ) && $certi_lab->branch_type != '1' ?true:(!empty($tis_data->branch_type ) && $tis_data->branch_type != '1' ?true:false), ['class'=>'check check-readonly', 'data-radio'=>'iradio_square-blue','id'=>'branch_type2']) !!}
                    <label for="branch_type2">  &nbsp;สาขา     </label>
            </div>
        </div>
    </div>

    <div class="form-group{{ $errors->has('lab_name') ? 'has-error' : ''}}">
        {!! HTML::decode(Form::label('lab_name', '<span class="text-danger">*</span> ชื่อห้องปฏิบัติการ (TH)'.':'.'<br/><span class=" font_size">(Name laboratory)</span>', ['class' => 'col-md-3 control-label  label-height'])) !!}
        <div class="col-md-7">
            {!! Form::text('lab_name', !empty($certi_lab->lab_name)?$certi_lab->lab_name:null, ['class' => 'form-control','required' => true]) !!}
            {!! $errors->first('lab_name', '<p class="help-block">:message</p>') !!}
        </div>
    </div>

    <div class="form-group {{ $errors->has('lab_name_en') ? 'has-error' : ''}}">
        {!! HTML::decode(Form::label('lab_name_en', '<span class="text-danger">*</span> ชื่อห้องปฏิบัติการ (EN)'.':'.'<br/><span class=" font_size">(Name laboratory)</span>', ['class' => 'col-md-3 control-label  label-height'])) !!}
        <div class="col-md-7">
            {!! Form::text('lab_name_en', !empty($certi_lab->lab_name_en)?$certi_lab->lab_name_en:null, ['class' => 'form-control input_address_eng','required' => true]) !!}
            {!! $errors->first('lab_name_en', '<p class="help-block">:message</p>') !!}
        </div>
    </div>

    <div class="form-group {{ $errors->has('lab_name_short') ? 'has-error' : ''}}">
        {!! HTML::decode(Form::label('lab_name_short', 'ชื่อย่อห้องปฏิบัติการ'.':'.'<br/><span class=" font_size">(Name laboratory)</span>', ['class' => 'col-md-3 control-label  label-height'])) !!}
        <div class="col-md-4">
            {!! Form::text('lab_name_short', !empty($certi_lab->lab_name_short)?$certi_lab->lab_name_short:null, ['class' => 'form-control']) !!}
            {!! $errors->first('lab_name_short', '<p class="help-block">:message</p>') !!}
        </div>
    </div>

    <hr>

    <div class="form-group {{ $errors->has('use_address_office') ? 'has-error' : ''}}">
        {!! HTML::decode(Form::label('use_address_office', 'ที่อยู่ห้องปฏิบัติการ'.':'.'<br/><span class=" font_size">(Address laboratory)</span>', ['class' => 'col-md-3 control-label  label-height'])) !!}
        <div class="col-md-9">
            <div class="col-md-5">
                {!! Form::radio('use_address_office', '1',!empty( $certi_lab->branch_type ) && $certi_lab->branch_type == '1' ?true:(!empty($tis_data->branch_type ) && $tis_data->branch_type == '1' ?true:false), ['class' => 'form-control check', 'data-radio' => 'iradio_square-blue', 'id'=>'use_address_office-1']) !!}
                {!! Form::label('use_address_office-1', 'ที่อยู่เดียวกับที่อยู่สำนักงานใหญ่', ['class' => 'control-label font-medium-1 text-capitalize']) !!}
            </div>
            <div class="col-md-4">
                {!! Form::radio('use_address_office', '2',!empty( $certi_lab->branch_type ) && $certi_lab->branch_type != '1' ?true:(!empty($tis_data->branch_type ) && $tis_data->branch_type != '1' ?true:false), ['class' => 'form-control check', 'data-radio' => 'iradio_square-blue', 'id'=>'use_address_office-2']) !!}
                {!! Form::label('use_address_office-2', 'ที่อยู่เดียวกับที่อยู่ติดต่อได้', ['class' => 'control-label font-medium-1 text-capitalize']) !!}
            </div>
            <div class="col-md-3">
                {!! Form::radio('use_address_office', '3',null, ['class' => 'form-control check', 'data-radio' => 'iradio_square-blue', 'id'=>'use_address_office-3']) !!}
                {!! Form::label('use_address_office-3', 'ระบุที่ตั้งใหม่', ['class' => 'control-label font-medium-1 text-capitalize']) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('address_number') ? 'has-error' : ''}}">
                {!! HTML::decode(Form::label('address_number', '<span class="text-danger">*</span> เลขที่'.':'.'<br/><span class=" font_size">(Address)</span>', ['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7">
                    {!! Form::text('address_number', !empty($certi_lab->address_no)?$certi_lab->address_no:null, ['class' => 'form-control input_address', 'required' => 'required']) !!}
                    {!! $errors->first('address_number', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('village_no') ? 'has-error' : ''}}">
                {!! HTML::decode(Form::label('village_no', 'หมู่ที่'.':'.'<br/><span class=" font_size">(Mool)</span>', ['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7">
                    {!! Form::text('village_no', !empty($certi_lab->allay)?$certi_lab->allay:null, ['class' => 'form-control input_address']) !!}
                    {!! $errors->first('village_no', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('address_soi') ? 'has-error' : ''}}">
                {!! HTML::decode(Form::label('address_soi', 'ตรอก/ซอย'.':'.'<br/><span class=" font_size">(Trok/Sol)</span>', ['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7">
                    {!! Form::text('address_soi', !empty($certi_lab->village_no)?$certi_lab->village_no:null, ['class' => 'form-control input_address']) !!}
                    {!! $errors->first('address_soi', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('address_street') ? 'has-error' : ''}}">
                {!! HTML::decode(Form::label('address_street', 'ถนน'.':'.'<br/><span class=" font_size">(Street/Road)</span>',['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7">
                    {!! Form::text('address_street', !empty($certi_lab->road)?$certi_lab->road:null, ['class' => 'form-control input_address']) !!}
                    {!! $errors->first('address_street', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group ">
                {!! Form::label('authorized_address_seach', 'ค้นหาที่อยู่'.' :', ['class' => 'col-md-5 control-label']) !!}
                <div class="col-md-7">
                    {!! Form::text('authorized_address_seach', null,  ['class' => 'form-control authorized_address_seach', 'autocomplete' => 'off', 'data-provide' => 'typeahead', 'placeholder' => 'ค้นหาที่อยู่' ]) !!}
                    {!! $errors->first('authorized_address_seach', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('address_city') ? 'has-error' : ''}}">
                {!! HTML::decode(Form::label('address_city', '<span class="text-danger">*</span> จังหวัด'.':'.'<br/><span class=" font_size">(Province)</span>',['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7">
                    {!! Form::select('address_city', $province->pluck('PROVINCE_NAME', 'PROVINCE_ID' ), !empty($certi_lab->province)?$certi_lab->province: null , ['class' => 'form-control select_address', 'id'=>'address_city', 'required' => true, 'placeholder' =>'- จังหวัด -']) !!}
                    {!! $errors->first('address_city', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('address_district') ? 'has-error' : ''}}">
                {!! HTML::decode(Form::label('address_district', 'เขต/อำเภอ'.':'.'<br/><span class=" font_size">(Arnphoe/Khet)</span>',['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7">
                    <input type="text" name="address_district" id="address_district" class="form-control input_address" value="{!! !empty($certi_lab->amphur)?$certi_lab->amphur: null !!}">
                    {!! $errors->first('according_district', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('sub_district') ? 'has-error' : ''}}">
                {!! HTML::decode(Form::label('sub_district', 'แขวง/ตำบล'.':'.'<br/><span class=" font_size">(Tambon/Khwaeng)</span>',['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7">
                    <input type="text" name="sub_district" id="sub_district" class="form-control input_address" value="{!! !empty($certi_lab->district)?$certi_lab->district: null !!}">
                    {!! $errors->first('sub_district', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('postcode') ? 'has-error' : ''}}">
                {!! HTML::decode(Form::label('postcode', '<span class="text-danger">*</span> รหัสไปรษณีย์'.':'.'<br/><span class=" font_size">(Zip code)</span>',['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7">
                    <input type="text" name="postcode" id="postcode" class="form-control input_address" required value="{!! !empty($certi_lab->postcode)?$certi_lab->postcode: null !!}">
                    {!! $errors->first('postcode', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! HTML::decode(Form::label('', 'ที่อยู่ห้องปฏิบัติการ (EN)',['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('lab_address_no_eng') ? 'has-error' : ''}}">
                {!! HTML::decode(Form::label('lab_address_no_eng', '<span class="text-danger">*</span> เลขที่'.':'.'<br/><span class=" font_size">(Address)</span>', ['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7">
                    {!! Form::text('lab_address_no_eng', !empty($certi_lab->lab_address_no_eng)?$certi_lab->lab_address_no_eng: null , ['class' => 'form-control input_address_eng', 'required' => 'required']) !!}
                    {!! $errors->first('lab_address_no_eng', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('lab_moo_eng') ? 'has-error' : ''}}">
                {!! HTML::decode(Form::label('lab_moo_eng', 'หมู่ที่'.':'.'<br/><span class=" font_size">(Moo)</span>', ['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7">
                    {!! Form::text('lab_moo_eng', !empty($certi_lab->lab_moo_eng)?$certi_lab->lab_moo_eng: null , ['class' => 'form-control input_address_eng']) !!}
                    {!! $errors->first('lab_moo_eng', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('lab_soi_eng') ? 'has-error' : ''}}">
                {!! HTML::decode(Form::label('lab_soi_eng', 'ตรอก/ซอย'.':'.'<br/><span class=" font_size">(Trok/Sol)</span>', ['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7">
                    {!! Form::text('lab_soi_eng', !empty($certi_lab->lab_soi_eng)?$certi_lab->lab_soi_eng: null , ['class' => 'form-control input_address_eng']) !!}
                    {!! $errors->first('lab_soi_eng', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('lab_street_eng') ? 'has-error' : ''}}">
                {!! HTML::decode(Form::label('lab_street_eng', 'ถนน'.':'.'<br/><span class=" font_size">(Street/Road)</span>',['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7">
                    {!! Form::text('lab_street_eng', !empty($certi_lab->lab_street_eng)?$certi_lab->lab_street_eng: null , ['class' => 'form-control input_address_eng']) !!}
                    {!! $errors->first('lab_street_eng', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group  {{ $errors->has('address_city') ? 'has-error' : ''}}">
                {!! HTML::decode(Form::label('lab_province_eng', '<span class="text-danger">*</span> จังหวัด'.':'.'<br/><span class=" font_size">(Province)</span>',['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7">
                    {!! Form::select('lab_province_eng', $province->where('PROVINCE_NAME_EN', '!=', null)->pluck('PROVINCE_NAME_EN', 'PROVINCE_ID' ), !empty($certi_lab->lab_province_eng)?$certi_lab->lab_province_eng: null , ['class' => 'form-control', 'id'=>'lab_province_eng', 'required' => true, 'placeholder' =>'- PROVINCE -']) !!}
                    {!! $errors->first('lab_province_eng', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('lab_amphur_eng') ? 'has-error' : ''}}">
                {!! HTML::decode(Form::label('lab_amphur_eng', 'เขต/อำเภอ'.':'.'<br/><span class=" font_size">(Arnphoe/Khet)</span>',['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7">
                    <input type="text" name="lab_amphur_eng" id="lab_amphur_eng" class="form-control input_address_eng" value="{!! !empty($certi_lab->lab_amphur_eng)?$certi_lab->lab_amphur_eng: null !!}">
                    {!! $errors->first('lab_amphur_eng', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('lab_district_eng') ? 'has-error' : ''}}">
                {!! HTML::decode(Form::label('lab_district_eng', 'แขวง/ตำบล'.':'.'<br/><span class=" font_size">(Tambon/Khwaeng)</span>',['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7">
                    <input type="text" name="lab_district_eng" id="lab_district_eng" class="form-control input_address_eng" value="{!! !empty($certi_lab->lab_district_eng)?$certi_lab->lab_district_eng: null !!}">
                    {!! $errors->first('lab_district_eng', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        {{-- <div class="col-md-6">
            <div class="form-group  {{ $errors->has('lab_postcode_eng') ? 'has-error' : ''}}">
                {!! HTML::decode(Form::label('lab_postcode_eng', '<span class="text-danger">*</span> รหัสไปรษณีย์'.':'.'<br/><span class=" font_size">(Zip code)</span>',['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7">
                    <input type="text" name="lab_postcode_eng" id="lab_postcode_eng" class="form-control input_address_eng" required value="{!! !empty($certi_lab->lab_postcode_eng)?$certi_lab->lab_postcode_eng: null !!}">
                    {!! $errors->first('lab_postcode_eng', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div> --}}
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! HTML::decode(Form::label('', '',['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7">
                    <a class="btn btn-default pull-left" id="show_map" onclick="return false">
                        ค้นหาจากแผนที่
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('lab_latitude') ? 'has-error' : ''}}">
                {!! HTML::decode(Form::label('lab_latitude', '<span class="text-danger">*</span> พิกัดที่ตั้ง (ละติจูด)'.':'.'<br/><span class=" font_size">(latitude)</span>',['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7">
                    <input type="text" name="lab_latitude" id="lab_latitude" class="form-control input_address" value="{!! !empty($certi_lab->lab_latitude)?$certi_lab->lab_latitude: null !!}" required>
                    {!! $errors->first('lab_latitude', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('lab_longitude') ? 'has-error' : ''}}">
                {!! HTML::decode(Form::label('lab_longitude', '<span class="text-danger">*</span> พิกัดที่ตั้ง (ลองจิจูด)'.':'.'<br/><span class=" font_size">(longitude)</span>',['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7">
                    <input type="text" name="lab_longitude" id="lab_longitude" class="form-control input_address" value="{!! !empty($certi_lab->lab_longitude)?$certi_lab->lab_longitude: null !!}" required>
                    {!! $errors->first('lab_longitude', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! HTML::decode(Form::label('lab_district_eng', 'ข้อมูลสำหรับการติดต่อ'.'<br/><span class=" font_size">(Contact information)</span>',['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-6"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('contact') ? 'has-error' : ''}}">
                {!! HTML::decode(Form::label('contact', '<span class="text-danger">*</span> ชื่อบุคคลที่ติดต่อ'.':'.'<br/><span class=" font_size">(Contact Person)</span>',['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7">
                    <input type="text" name="contact"   value="{!! !empty($certi_lab->contactor_name)?$certi_lab->contactor_name: null !!}"  id="contactor_name" class="form-control" readonly >
                    {!! $errors->first('contact', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('address_tel') ? 'has-error' : ''}}">
                {!! HTML::decode(Form::label('address_tel', '<span class="text-danger">*</span> Email'.':'.'<br/><span class=" font_size text-danger">*หากต้องการเปลี่ยน e-mail กรุณาติดต่อเจ้าหน้าที่</span>',['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7">
                    <input type="email"  value="{!! !empty($certi_lab->email)?$certi_lab->email: null !!}"  name="address_email" id="address_email" class="form-control" required placeholder="Email@gmail.com" readonly>
                    {!! $errors->first('address_email', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('contact_tel') ? 'has-error' : ''}}">
                {!! HTML::decode(Form::label('contact_tel', '<span class="text-danger">*</span> โทรศัพท์ผู้ติดต่อ'.':'.'<br/><span class=" font_size">(Telephone)</span>',['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7">
                    <input type="text" name="contact_tel"  value="{!! !empty($certi_lab->contact_tel)?$certi_lab->contact_tel: null !!}"   id="contact_tel" class="form-control"   readonly>
                    {!! $errors->first('contact_tel', '<p class="help-block">:message</p>') !!}
                </div>

            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('contact_mobile') ? 'has-error' : ''}}">
                {!! HTML::decode(Form::label('contact_mobile', '<span class="text-danger">*</span> โทรศัพท์มือถือ'.':'.'<br/><span class=" font_size">(Mobile)</span>',['class' => 'col-md-5 control-label label-height'])) !!}
                <div class="col-md-7">
                    <input type="text" name="contact_mobile" id="contact_mobile" class="form-control"  readonly   value="{!! !empty($certi_lab->telephone)?$certi_lab->telephone: null !!}" >
                    {!! $errors->first('contact_mobile', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div> 
    </div>

</fieldset>

<fieldset class="white-box">
    <legend><h4>1. ข้อมูลทั่วไป (General information)</h4></legend>

    @php
        $man_applicant_arr[ 1 ] = 'เป็นนิติบุคคล (A legal Entity)';
        $man_applicant_arr[ 2 ] = 'เป็นนิติบุคคลที่มีกิจกรรมอื่นนอกเหนือจากกิจกรรม ทดสอบ/สอบเทียบ (A legal entity having other types of business apart from testing / calibration)';
        $man_applicant_arr[ 3 ] = 'เป็นหน่วยงานของรัฐ (A Government Agency)';
        $man_applicant_arr[ 4 ] = 'เป็นรัฐวิสาหกิจ (A State Enterprise)';
        $man_applicant_arr[ 5 ] = 'เป็นสถาบันการศึกษา (An Academic Institution)';
        $man_applicant_arr[ 6 ] = 'เป็นสถาบันวิชาชีพ (A Professional Institution)';
        $man_applicant_arr[ 7 ] = 'อื่นๆ (Others)';
    @endphp

    @php

        $certi_lab_info = null;
        if( isset($certi_lab->id) ){
            $certi_lab_info = App\Models\Certify\Applicant\CertiLabInfo::Where('app_certi_lab_id',$certi_lab->id)->first();
        }
    @endphp


    <div class="row">
        <div class="m-l-10 form-group {{ $errors->has('man_applicant') ? 'has-error' : ''}}">
            <label for="man_applicant" class="col-md-12" style="padding-top: 7px;margin-bottom: 5px;font-size: 16px"><span class="text-danger">*</span> ผู้ยื่นคำขอ (Qualifications of Applicant)</label>
            <div class="col-md-6 ">
                {!! Form::select('man_applicant', $man_applicant_arr,  !empty($certi_lab_info->petitioner)?$certi_lab_info->petitioner: null , ['class' => 'form-control', 'id'=>'man_applicant', 'placeholder' =>'- ผู้ยื่นคำขอ -', 'required' => true]) !!}
                {!! $errors->first('man_applicant', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div id="extra_value_two" style="display: none;">
            <div class="m-l-15 form-group {{ $errors->has('at_1_1_1') ? 'has-error' : ''}}">
                <label for="at_1_1_1" class="col-md-12" style="padding-top: 7px;margin-bottom: 5px">(1) มีกิจกรรมที่นอกเหนือจากกิจกรรมทดสอบ/สอบเทียบ เป็นกิจกรรมหลัก (major type of business apart from testing / calibration)</label>
                <div class="col-md-12 m-t-5 m-l-15">
                    <label>{!! Form::radio('at_1_1_1', '0', isset( $certi_lab_info->lab_type_other ) && $certi_lab_info->lab_type_other == '0' ?true:false, ['class'=>'check input_extra_value_two', 'data-radio'=>'iradio_square-green']) !!} &nbsp;มี &nbsp;</label>
                    <label>{!! Form::radio('at_1_1_1', '1', isset( $certi_lab_info->lab_type_other ) && $certi_lab_info->lab_type_other == '1' ?true:false, ['class'=>'check input_extra_value_two', 'data-radio'=>'iradio_square-red']) !!} &nbsp;ไม่มี &nbsp;</label>
                    {!! $errors->first('at_1_1_1', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="m-l-15 form-group {{ $errors->has('activity_file') ? 'has-error' : ''}}">
                <label for="    " class="col-md-12" style="padding-top: 7px;margin-bottom: 13px">(2) อธิบายรายละเอียดกิจกรรมหลัก  (please specify major type of business)</label>
                <div class="col-md-6">

                    @if( !empty($certi_lab_info->desc_main_file) && HP::checkFileStorage($attach_path.$certi_lab_info->desc_main_file)  )
                        
                        <a href="{!! HP::getFileStorage($attach_path.$certi_lab_info->desc_main_file) !!}" target="_blank" class="view-attach btn btn-info btn-sm"> {!! HP::FileExtension($certi_lab_info->activity_client_name)  ?? '' !!}</a>

                        <a href="{{url('certify/applicant/delete/file_app_certi_lab_info').'/'.$certi_lab_info->id.'/'.'desc_main_file'.'/'.$certi_lab->token}}" class="btn btn-danger btn-xs" onclick="return confirm('ต้องการลบไฟล์นี้ใช่หรือไม่ ?')">
                            <i class="fa fa-remove"></i>
                        </a>

                    @else
                        <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                            <div class="form-control" data-trigger="fileinput">
                                <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                <span class="fileinput-filename"></span>
                            </div>
                            <span class="input-group-addon btn btn-default btn-file">
                                <span class="fileinput-new">เลือกไฟล์</span> 
                                <span class="fileinput-exists">เปลี่ยน</span>
                                <input type="file" name="activity_file" class="  check_max_size_file" >
                            </span>
                            <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                        </div>
                        {!! $errors->first('activity_file', '<p class="help-block">:message</p>') !!}

                    @endif

                </div>
            </div>
        
            <div class="m-l-15 form-group {{ $errors->has('at_1_1_3') ? 'has-error' : ''}}">
                <label for="at_1_1_3" class="col-md-12" style="padding-top: 7px;margin-bottom: 5px">(3) ทดสอบ/สอบเทียบให้หน่วยงานของตนเองเท่านั้น (Testing / Calibration services are restricted to own use )</label>
                <div class="col-md-12 m-t-5 m-l-15">
                    <label>{!! Form::radio('at_1_1_3', '0', isset( $certi_lab_info->only_own_depart ) && $certi_lab_info->only_own_depart == '0' ?true:false, ['class'=>'check input_extra_value_two', 'data-radio'=>'iradio_square-green']) !!} &nbsp;ใช่ &nbsp;</label>
                    <label>{!! Form::radio('at_1_1_3', '1', isset( $certi_lab_info->only_own_depart ) && $certi_lab_info->only_own_depart == '1' ?true:false, ['class'=>'check input_extra_value_two', 'data-radio'=>'iradio_square-red']) !!} &nbsp;ไม่ใช่ &nbsp;</label>
                    {!! $errors->first('at_1_1_3', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        
            <div class="m-l-15 form-group {{ $errors->has('at_1_1_4') ? 'has-error' : ''}}">
                <label for="at_1_1_4" class="col-md-12" style="padding-top: 7px;margin-bottom: 5px">(4) ทดสอบ/สอบเทียบให้หน่วยงานอื่นด้วย (Testing / Calibration services are open for public)</label>
                <div class="col-md-12 m-t-5 m-l-15">
                    <label>{!! Form::radio('at_1_1_4', '0', isset( $certi_lab_info->depart_other ) && $certi_lab_info->depart_other == '0' ?true:false, ['class'=>'check input_extra_value_two', 'data-radio'=>'iradio_square-green']) !!} &nbsp;ใช่ &nbsp;</label>
                    <label>{!! Form::radio('at_1_1_4', '1', isset( $certi_lab_info->depart_other ) && $certi_lab_info->depart_other == '1' ?true:false, ['class'=>'check input_extra_value_two', 'data-radio'=>'iradio_square-red']) !!} &nbsp;ไม่ใช่ &nbsp;</label>
                    {!! $errors->first('at_1_1_4', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div id="extra_value_three" style="display: none;">
            <div class="m-l-15 form-group {{ $errors->has('file_section') ? 'has-error' : ''}}">
                <div class="col-md-6">

                    @if( !empty($certi_lab_info->file_section) && HP::checkFileStorage($attach_path.$certi_lab_info->file_section)  )
                        <a href="{!! HP::getFileStorage($attach_path.$certi_lab_info->file_section) !!}" target="_blank" class="view-attach btn btn-info btn-sm"> {!! HP::FileExtension($certi_lab_info->file_client_name)  ?? '' !!}</a>

                        <a href="{{url('certify/applicant/delete/file_app_certi_lab_info').'/'.$certi_lab_info->id.'/'.'file_section'.'/'.$certi_lab->token}}" class="btn btn-danger btn-xs" onclick="return confirm('ต้องการลบไฟล์นี้ใช่หรือไม่ ?')">
                            <i class="fa fa-remove"></i>
                        </a>
                    @else
                        <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                            <div class="form-control" data-trigger="fileinput">
                                <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                <span class="fileinput-filename"></span>
                            </div>
                            <span class="input-group-addon btn btn-default btn-file">
                                <span class="fileinput-new">เลือกไฟล์</span>
                                <span class="fileinput-exists">เปลี่ยน</span>
                                <input type="file" name="file_section" accept=".doc,.docx" class="file_section check_max_size_file" >
                                </span>
                            <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                        </div>
                        {!! $errors->first('file_section', '<p class="help-block">:message</p>') !!}
                    @endif

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div id="extra_value_other" style="display: none;">
            <div class="m-l-15 form-group {{ $errors->has('at_1_1_5') ? 'has-error' : ''}}">
                <div class="col-md-12 m-l-15">
                    <label>{!! Form::checkbox('at_1_1_5', '0', isset( $certi_lab_info->over_twenty ) && $certi_lab_info->over_twenty == '0' ?true:false, ['class'=>'check input_extra_value_other','data-checkbox'=>"icheckbox_flat-red"]) !!} &nbsp;มีอายุไม่ต่ำกว่ายี่สิบปีบริบูรณ์ (being not less than twenty years of age) &nbsp;</label>
                    {!! $errors->first('at_1_1_5', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="m-l-15 form-group {{ $errors->has('at_1_1_6') ? 'has-error' : ''}}">
                <div class="col-md-12 m-l-15">
                    <label>{!! Form::checkbox('at_1_1_6', '0', isset( $certi_lab_info->not_bankrupt ) && $certi_lab_info->not_bankrupt == '0' ?true:false, ['class'=>'check input_extra_value_other', 'data-checkbox'=>"icheckbox_flat-red"]) !!} &nbsp;ไม่เป็นบุคคลล้มละลาย (not being bankrupt) &nbsp;</label>
                    {!! $errors->first('at_1_1_6', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="m-l-15 form-group {{ $errors->has('at_1_1_7') ? 'has-error' : ''}}">
                <div class="col-md-12 m-l-15">
                    <label>{!! Form::checkbox('at_1_1_7', '0', isset( $certi_lab_info->not_being_incompetent ) && $certi_lab_info->not_being_incompetent == '0' ?true:false, ['class'=>'check input_extra_value_other','data-checkbox'=>"icheckbox_flat-red"]) !!} &nbsp;ไม่เป็นคนไร้ความสามารถหรือคนเสมือนไร้ความสามารถ (not being an incompetent or quasi-incompetent person) &nbsp;</label>
                    {!! $errors->first('at_1_1_7', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="m-l-15 form-group {{ $errors->has('at_1_1_8') ? 'has-error' : ''}}">
                <div class="col-md-12 m-l-15">
                    <label>{!! Form::checkbox('at_1_1_8', '0', isset( $certi_lab_info->suspended_using_a_certificate ) && $certi_lab_info->suspended_using_a_certificate == '0' ?true:false, ['class'=>'check input_extra_value_other','data-checkbox'=>"icheckbox_flat-red"]) !!}&nbsp;ไม่เป็นผู้อยู่ในระหว่างถูกสั่งพักใช้ใบรับรอง (not being a person whose Certificate is suspended) &nbsp;</label>
                    {!! $errors->first('at_1_1_8', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="m-l-15 form-group {{ $errors->has('at_1_1_9') ? 'has-error' : ''}}">
                <div class="col-md-12 m-l-15">
                    <label>{!! Form::checkbox('at_1_1_9', '0', isset( $certi_lab_info->never_revoke_a_certificate ) && $certi_lab_info->never_revoke_a_certificate == '0' ?true:false, ['class'=>'check input_extra_value_other','data-checkbox'=>"icheckbox_flat-red"]) !!}&nbsp;ไม่เคยถูกเพิกถอนใบรับรองหรือเคยถูกเพิกถอนใบรับรอง แต่เวลาได้ล่วงพ้นมาแล้วไม่น้อยกว่า 6 เดือน (not being subjected to Certificate withdrawal or in case of having been subjected to Certificate withdrawal, not less than six month shall have elapsed since the date of Certificate withdrawal)&nbsp;</label>
                    {!! $errors->first('at_1_1_9', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

</fieldset>

<fieldset class="white-box">
    <legend><h4>2. ประเภทสถานปฏิบัติการของห้องปฏิบัติการ (Types of laboratory’s facilities)</h4></legend>

    @php
        $certi_lab_place = null;
        if( isset($certi_lab->id) ){
            $certi_lab_place = App\Models\Certify\Applicant\CertiLabPlace::Where('app_certi_lab_id',$certi_lab->id)->first();
        }
    @endphp
    <div class="row">
        <div class="m-l-15 form-group {{ $errors->has('pl_2_1') ? 'has-error' : ''}}">
            <div class="col-md-12 m-l-15">
                {!! Form::checkbox('pl_2_1', '0', isset( $certi_lab_place->permanent_operating_site ) && $certi_lab_place->permanent_operating_site == '0' ?true:false, ['class'=>'check pl_2_1','data-checkbox'=>"icheckbox_flat-red"]) !!}
                <label for="pl_2_1"> &nbsp;สถานปฏิบัติการถาวร  (Permanent facilities) &nbsp; </label>
                {!! $errors->first('pl_2_1', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
        <div class="m-l-15 form-group {{ $errors->has('pl_2_2') ? 'has-error' : ''}}">
            <div class="col-md-12 m-l-15">
                {!! Form::checkbox('pl_2_2', '0', isset( $certi_lab_place->off_site_operations ) && $certi_lab_place->off_site_operations == '0' ?true:false, ['class'=>'check pl_2_2','data-checkbox'=>"icheckbox_flat-red"]) !!}
                <label for="pl_2_2"> &nbsp;สถานปฏิบัติการนอกสถานที่ (Sites away from its permanent facilities) &nbsp; </label>
                {!! $errors->first('pl_2_2', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
        <div class="m-l-15 form-group {{ $errors->has('pl_2_4') ? 'has-error' : ''}}">
            <div class="col-md-12 m-l-15">
                {!! Form::checkbox('pl_2_4', '0', isset( $certi_lab_place->temporary_operating_site ) && $certi_lab_place->temporary_operating_site == '0' ?true:false, ['class'=>'check pl_2_4','data-checkbox'=>"icheckbox_flat-red"]) !!}
                <label for="pl_2_4"> &nbsp;สถานปฏิบัติการชั่วคราว (Temporary facilities) &nbsp; </label>
                {!! $errors->first('pl_2_4', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
        <div class="m-l-15 form-group {{ $errors->has('pl_2_3') ? 'has-error' : ''}}">
            <div class="col-md-12 m-l-15">
                {!! Form::checkbox('pl_2_3', '0', isset( $certi_lab_place->mobile_operating_facility ) && $certi_lab_place->mobile_operating_facility == '0' ?true:false, ['class'=>'check pl_2_3','data-checkbox'=>"icheckbox_flat-red"]) !!}
                <label for="pl_2_3"> &nbsp;สถานปฏิบัติการเคลื่อนที่ (Mobile facilities) &nbsp; </label>
                {!! $errors->first('pl_2_3', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
        <div class="m-l-15 form-group {{ $errors->has('pl_2_5') ? 'has-error' : ''}}">
            <div class="col-md-12 m-l-15">
                {!! Form::checkbox('pl_2_5', '0', isset( $certi_lab_place->multi_site_facility ) && $certi_lab_place->multi_site_facility == '0' ?true:false, ['class'=>'check pl_2_5','data-checkbox'=>"icheckbox_flat-red"]) !!}
                <label for="pl_2_5"> &nbsp;สถานปฏิบัติการหลายสถานะที่ (Multi-site facilities) &nbsp; </label>
                {!! $errors->first('pl_2_5', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
</fieldset>

<fieldset class="white-box">
    <legend><h4>3. ระบบบริหารงานของห้องปฏิบัติการ (Management System of Laboratory)</h4></legend>
    <div class="row">
        <div class="m-l-15 form-group">
            <div class="col-md-12 m-l-15">
                <label>{!! Form::radio('mn_3_1', '0', isset($certi_lab->management_lab) && $certi_lab->management_lab== '0'?true: false, ['class'=>'check', 'data-radio'=>'iradio_square-green']) !!} &nbsp;ทางเลือก ก -- ระบบบริหารงานตามข้อกำหนดมาตรฐานเลขที่ มอก. 17025 - 2561 (ISO/IEC 17025 : 2017)<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Option A – a management system in accordance with requirements of TIS 17025 – 2561(2018) (ISO/IEC 17025 : 2017))  &nbsp;</label>
                {!! $errors->first('mn_3_1', '<p class="help-block">:message</p>') !!}
            </div>
            <div class="col-md-12 m-l-15">
                <label>{!! Form::radio('mn_3_1', '1', isset($certi_lab->management_lab) && $certi_lab->management_lab == '1'?true: false, ['class'=>'check', 'data-radio'=>'iradio_square-green']) !!} &nbsp;ทางเลือก ข – ระบบบริหารงานตามข้อกำหนดมาตรฐานเลขที่ มอก. 9001 – 2559 หรือ ISO 9001 : 2015 <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Option B – a management system in accordance with requirements of TIS 9001 – 2559(2016) or ISO 9001 : 2015) &nbsp;</label>
                {!! $errors->first('mn_3_1', '<p class="help-block">:message</p>') !!}
            </div>
       </div> 
    </div>
</fieldset>

@push('js')
<script>
    const app_lab_id = '{{ @$certi_lab->id }}';
    
    $(document).ready(function () {


 
        //เมื่อกรอกภาษาอังกฤษ
        $('.input_address_eng').keyup(function(event) {
            filterEngAndNumberOnlyCustomForPage(this);
        });

        $('.input_address_eng').change(function(event) {
            filterEngAndNumberOnlyCustomForPage(this);
        });

        // change   สาขาที่ขอรับการรับรอง
        $("input[name=lab_ability]").on("ifChanged",function(){
            status_show_lab_ability();
            // certificate_exports();
        });
        status_show_lab_ability();
        // certificate_exports();

            if(app_lab_id == ''){
                use_address_offices();
            }

        // $('#lab_ability_test').on('ifChecked', function(event){
        //     get_app_no(3);
        // });
        // $('#lab_ability_calibrate').on('ifChecked', function(event){
        //     get_app_no(4);
        // });
      
        $('#use_address_office-1').on('ifChecked', function(event){
            use_address_offices();
        });

        $('#use_address_office-2').on('ifChecked', function(event){
            use_address_offices();
        });

        $('#use_address_office-3').on('ifChecked', function(event){
            use_address_offices();
        });

        
        $('#show_map').click(function(){
            $('#modal-default').modal('show');
        });

        
        $('#button-modal-default').click(function(){

            if( $('#lat1').val() != ""){
                $('#lab_latitude').val( $('#lat1').val());
            }else{
                $('#lab_latitude').val('');
            }

            if( $('#lng1').val() != ""){
                $('#lab_longitude').val( $('#lng1').val());
            }else{
                $('#lab_longitude').val('');
            }

            $('#modal-default').modal('hide');
        });

        file_section();

        $('#man_applicant').on('change',function () {
            if ($(this).val() === "2"){

                $('#extra_value_two').fadeIn();
                $('#extra_value_other').fadeOut();
                $('#extra_value_three').fadeOut();

                $('#extra_value_two').find('input, select, textarea').prop('disabled', false);
                $('#extra_value_other').find('input, select, textarea').prop('disabled', true);
                $('#extra_value_three').find('input, select, textarea').prop('disabled', true);

                // $('#extra_value_two').find('.input_extra_value_two').iCheck();

                $('#extra_value_two').find('.iradio_square-red').removeClass('disabled');
                $('#extra_value_two').find('.iradio_square-green').removeClass('disabled');


            }
            else if ($(this).val() === "3") {

                $('#extra_value_three').fadeIn();
                $('#extra_value_two').fadeOut();
                $('#extra_value_other').fadeOut();

                $('#extra_value_two').find('input, select, textarea').prop('disabled', true);
                $('#extra_value_other').find('input, select, textarea').prop('disabled', true);
                $('#extra_value_three').find('input, select, textarea').prop('disabled', false);
            }
            else if ($(this).val() === "7") {

                $('#extra_value_other').fadeIn();
                $('#extra_value_two').fadeOut();
                $('#extra_value_three').fadeOut();

                $('#extra_value_two').find('input, select, textarea').prop('disabled', true);
                $('#extra_value_other').find('input, select, textarea').prop('disabled', false);
                $('#extra_value_three').find('input, select, textarea').prop('disabled', true);

                $('#extra_value_other').find('.icheckbox_flat-red').removeClass('disabled');
            }else {
                $('#extra_value_two').fadeOut();
                $('#extra_value_other').fadeOut();
                $('#extra_value_three').fadeOut();

                $('#extra_value_two').find('input, select, textarea').prop('disabled', true);
                $('#extra_value_other').find('input, select, textarea').prop('disabled', true);
                $('#extra_value_three').find('input, select, textarea').prop('disabled', true);
            }
        });
        $('#man_applicant').change();

        // $('#certificate_exports_id').change(function (e) { 

        //     console.log($(this).val());

        //     $('#ref_application_no').val('');
           
        //     if( $(this).val() != '' ){
        //         $.ajax({
        //             url: "{!! url('/certify/applicant/get_certificate?id=') !!}" +  $(this).val()
        //         }).done(function( object ) {

        //             if( object != '' ){
        //                 $('#ref_application_no').val( object.request_number);
        //             }

        //         });
        //     }
        // });
        // $('#certificate_exports_id').change();

        $("#authorized_address_seach").select2({
                dropdownAutoWidth: true,
                width: '100%',
                ajax: {
                    url: "{{ url('/funtions/search-addreess') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchTerm: params // search term
                        };
                    },
                    results: function (response) {
                        return {
                            results: response
                        };
                    },
                    cache: true,
                },
                placeholder: 'คำค้นหา',
                minimumInputLength: 1,
            });

            $("#authorized_address_seach").on('change', function () {
                $.ajax({
                    url: "{!! url('/funtions/get-addreess/') !!}" + "/" + $(this).val()
                }).done(function( jsondata ) {
                    if(jsondata != ''){

                        $('#address_city').val(jsondata.pro_id).select2();
                        $('#address_district').val(jsondata.dis_title);
                        $('#sub_district').val(jsondata.sub_title);
                        $('#postcode').val(jsondata.zip_code);

                        
                        $('#lab_province_eng').val(jsondata.pro_id).select2();
                        $('#lab_amphur_eng').val(jsondata.dis_title_en);
                        $('#lab_district_eng').val(jsondata.sub_title_en);
                        $('#lab_postcode_eng').val(jsondata.zip_code);

                    }
                });
            });

            
        // change   สาขาที่ขอรับการรับรอง
        $("input[name=purpose], input[name=lab_ability]").on("ifChanged",function(){
            box_ref_application_no();
            get_app_no_and_certificate_exports_no();
        });
            
        // change   สาขาที่ขอรับการรับรอง
        $("#according_formula").change(function(){
            box_ref_application_no();
            get_app_no_and_certificate_exports_no();
        });
        box_ref_application_no();


    });

    //  Attach File
    function  file_section(){
        $('.file_section').change( function () {
            var fileExtension = ['docx','doc'];
            if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                Swal.fire(
                    'ไม่ใช่หลักฐานประเภทไฟล์ที่อนุญาต .doc หรือ .docx',
                    '',
                    'info'
                );
                this.value = '';
                return false;
            }
        }); 
    }

    function status_show_lab_ability(){
        var select = $("input[name=lab_ability]:checked").val();
        const _token = $('input[name="_token"]').val();
        $('#branch_lab_test').html('<option>- สาขาการทดสอบ -</option>').select2();
        $('#branch_lab_calibrate').html('<option>- สาขาสอบเทียบ -</option>').select2();

        if (select === 'test') {
            var    certi_lab        =   '{!!  !empty($certi_lab->id) ? $certi_lab->id  :'' !!}'; 
            // ไฟล์แนบ
            if(certi_lab == ""){
                $('.attachs_sec61').prop('required', true);
                $('.attachs_sec62').prop('required', false);
            }


            $('.attachs_sec72').prop('required', false);
            if($('.view_attach_sec71').length == 0){
                $('input.attachs_sec71').prop('required', true);  
            }else{
                $('input.attachs_sec71').prop('required', false);  
            }

            $('#viewForm93').fadeIn();
            $('#viewForm92').hide();

            $('#viewForm93').find('input, select, textarea').prop('disabled', false);
            $('#viewForm92').find('input, select, textarea').prop('disabled', true);

            $.ajax({
                url:"{{route('api.test')}}",
                method:"POST",
                data:{select:select,_token:_token},
                success:function (result){
                    $('#viewForm90').fadeIn();
                    $('#viewForm91').hide();

                    $('#viewForm90').find('input, select, textarea').prop('disabled', false);
                    $('#viewForm91').find('input, select, textarea').prop('disabled', true);

                    $('#type_product').val('').change();

                    $.each(result,function (index,value) {
                        $('#branch_lab_test').append('<option value='+value.id+' >'+value.title+'</option>')
                    });
                }
            });
        }else if (select === 'calibrate') {
 
            var    certi_lab        =   '{!!  !empty($certi_lab->id) ? $certi_lab->id  :'' !!}'; 
            // ไฟล์แนบ
            if(certi_lab == ""){
                $('.attachs_sec61').prop('required', false);
                $('.attachs_sec62').prop('required', true);
            }




            $('.attachs_sec71').prop('required', false);

            if($('.view_attach_sec72').length == 0){
                $('input.attachs_sec72').prop('required', true);  
            }else{
                $('input.attachs_sec72').prop('required', false);  
            }

            $('#viewForm92').fadeIn();
            $('#viewForm93').hide();

            $('#viewForm92').find('input, select, textarea').prop('disabled', false);
            $('#viewForm93').find('input, select, textarea').prop('disabled', true);

            $.ajax({
                url:"{{route('api.calibrate')}}",
                method:"POST",
                data:{select:select,_token:_token},
                success:function (result){
                    $('#viewForm91').fadeIn();
                    $('#viewForm90').hide();

                    $('#viewForm91').find('input, select, textarea').prop('disabled', false);
                    $('#viewForm90').find('input, select, textarea').prop('disabled', true);

                    $('#type_calibrate').val('').change();
                    $.each(result,function (index,value) {
                        $('#branch_lab_calibrate').append('<option value='+value.id+' >'+value.title+'</option>')
                    });
                }
            });
        }
    }

    function certificate_exports(){
 
    var app_check           = '{!! count($app_check) !!}';
    var select              = $("input[name=lab_ability]:checked").val();
    const _token            = $('input[name="_token"]').val();
    var    exports_id       =   '{!!  !empty($certi_lab->certificate_exports_id) ?$certi_lab->certificate_exports_id:'' !!}'; 
    var    certi_lab        =   '{!!  !empty($certi_lab->id) ? $certi_lab->id  :'' !!}'; 
    console.log(certi_lab);
       if(app_check  == 0 && certi_lab == ""){
 
            $('#certificate_exports_id').html('<option>- ใบรับรองเลขที่ -</option>').select2();
            // $('#ref_application_no').val('');   
            $('.div_certificate_exports_id').hide();
            $.ajax({
                url:"{{route('api.certificate_exports')}}",
                method:"get",
                data:{select:select,_token:_token}
            }).done(function( result ) {
                console.log(result);
                if(result.length > 0){
                    $('.div_certificate_exports_id').show();
                    $('#certificate_exports_id').prop('required', true);
                        $.each(result,function (index,value) {
                            let selected = (value.id == exports_id)?'selected="selected"':'';
                           $('#certificate_exports_id').append('<option value='+value.id+' '+selected+'  >'+value.certificate_no+'</option>')
                        }); 
                        $('#certificate_exports_id').select2();
              
                                $('#purpose1').prop('disabled', true);
                                $('#purpose2').prop('disabled', false);
                                $('#purpose3').prop('disabled', false);
                                $('#purpose4').prop('disabled', false);
                                $('#purpose1').prop('checked', false);
                                $('#purpose2').prop('checked', true);
                                $('#purpose1,#purpose2,#purpose3,#purpose4').iCheck('update');
                                // $('#ref_application_no').prop('required', true);
                         
                }else{
                    $('.div_certificate_exports_id').hide();
                    $('#certificate_exports_id').prop('required', false);
            
                                $('#purpose1').prop('disabled', false);
                                $('#purpose2').prop('disabled', true);
                                $('#purpose3').prop('disabled', true);
                                $('#purpose4').prop('disabled', true);
                                $('#purpose1').prop('checked', true);
                                $('#purpose2,#purpose3,#purpose4').prop('checked',false);
                                $('#purpose1,#purpose2,#purpose3,#purpose4').iCheck('update');
                                // $('#ref_application_no').prop('required', false);
                  
                }
            });

        }
    }
    
    function use_address_offices(){

        $('.input_address').val('');
        $('#address_city').val('').select2();

        if( $('#use_address_office-1').is(':checked',true) ){


            var address =  `{!! isset($tis_data) && !empty($tis_data->address_no) ?$tis_data->address_no:'' !!}`;
            var moo =  `{!! isset($tis_data) && !empty($tis_data->moo) ?$tis_data->moo:'' !!}`;
            var soi =  `{!! isset($tis_data) && !empty($tis_data->soi) ?$tis_data->soi:'' !!}`;
            var road =  `{!! isset($tis_data) && !empty($tis_data->street) ?$tis_data->street:'' !!}`;
            var building =  `{!! isset($tis_data) && !empty($tis_data->building) ?$tis_data->building:'' !!}`;

            var subdistrict_txt =  `{!! isset($tis_data) && !empty($tis_data->subdistrict) ?$tis_data->subdistrict:'' !!}`;
            var district_txt = `{!! isset($tis_data) && !empty($tis_data->district) ?$tis_data->district:'' !!}`;
            var province_txt = `{!! isset($tis_data) && !empty($tis_data->province_id) ?$tis_data->province_id:'' !!}`;
            var postcode_txt = `{!! isset($tis_data) && !empty($tis_data->zipcode) ?$tis_data->zipcode:'' !!}`;

            var longitude =  `{!! isset($tis_data) && !empty($tis_data->longitude) ?$tis_data->longitude:'' !!}`;
            var latitude =  `{!! isset($tis_data) && !empty($tis_data->latitude) ?$tis_data->latitude:'' !!}`;

            $('#address_number').val(address);
            $('#village_no').val(moo);
            $('#address_soi').val(soi);
            $('#address_street').val(road);

            $('#address_city').val(province_txt).select2();
            $('#address_district').val(district_txt);
            $('#sub_district').val(subdistrict_txt);
            $('#postcode').val(postcode_txt);

            $('#lab_latitude').val(latitude);
            $('#lab_longitude').val(longitude);

        }else if( $('#use_address_office-2').is(':checked',true) ){

            var address =  '{!! isset($tis_data) && !empty($tis_data->contact_address_no) ?$tis_data->contact_address_no:'' !!}';
            var moo =  '{!! isset($tis_data) && !empty($tis_data->contact_moo) ?$tis_data->contact_moo:'' !!}';
            var soi =  '{!! isset($tis_data) && !empty($tis_data->contact_soi) ?$tis_data->contact_soi:'' !!}';
            var road =  '{!! isset($tis_data) && !empty($tis_data->contact_street) ?$tis_data->contact_street:'' !!}';
            var building =  '{!! isset($tis_data) && !empty($tis_data->contact_building) ?$tis_data->contact_building:'' !!}';

            var subdistrict_txt =  '{!! isset($tis_data) && !empty($tis_data->contact_subdistrict) ?$tis_data->contact_subdistrict:'' !!}';
            var district_txt = '{!! isset($tis_data) && !empty($tis_data->contact_district) ?$tis_data->contact_district:'' !!}';
            var province_txt = '{!! isset($tis_data) && !empty($tis_data->contact_province_id) ?$tis_data->contact_province_id:'' !!}';
            var postcode_txt = '{!! isset($tis_data) && !empty($tis_data->contact_zipcode) ?$tis_data->contact_zipcode:'' !!}';

            $('#address_number').val(address);
            $('#village_no').val(moo);
            $('#address_soi').val(soi);
            $('#address_street').val(road);

            $('#address_city').val(province_txt).select2();
            $('#address_district').val(district_txt);
            $('#sub_district').val(subdistrict_txt);
            $('#postcode').val(postcode_txt);

        }
   
    }

    // function get_app_no(lab_type_val){

    //     let tax_id           = '{!! isset($tis_data) && !empty($tis_data->tax_number) ?$tis_data->tax_number:'' !!}';
    //     let standard_id       =  $('#according_formula').val();
    //     let lab_type              = lab_type_val;

    //         $('#ref_application_no').val('');
            
    //         $.get("{{ url('/certify/applicant/get_appno') }}", { 
    //             tax_id: tax_id,
    //             lab_type: lab_type,
    //             standard_id: standard_id
    //         }).done(function( data ) {
    //             console.log(data);
    //             if(data.app_no){
    //                 $('#ref_application_no').parent().parent().show(300);
    //                 $('#ref_application_no').val(data.app_no);
    //             }else{
    //                 $('#ref_application_no').parent().parent().hide(300);
    //             }
             
    //         });
  
    // }

    function box_ref_application_no(){
        let purpose = $('input[name="purpose"]:checked').val();
        if(purpose >= 2){
            $('#box_ref_application_no').show();
            $('#box_ref_application_no').find('input').prop('disabled', false);
            $('#accereditation_no').prop('required', true);
        }else{
            $('#box_ref_application_no').hide();
            $('#box_ref_application_no').find('input').prop('disabled', true);
            $('#accereditation_no').prop('required', false);
        }
    }

    function get_app_no_and_certificate_exports_no(){
        let std_id = $('#according_formula').val();
        let lab_type = $('input[name="lab_ability"]:checked').val();
        let purpose = $('input[name="purpose"]:checked').val();
        $('#ref_application_no').val(null);
        $('#certificate_exports_id').val(null);
        if(app_lab_id == '' && !!std_id && !!lab_type && purpose >= 2){
            $.get("{{ url('/certify/applicant/get_app_no_and_certificate_exports_no') }}", { 
                std_id: std_id,
                lab_type: lab_type
            }).done(function( data ) {
                if(data.status){
                    $('#ref_application_no').val(data.app_no);
                    $('#certificate_exports_id').val(data.certificate_exports_no);
                }
            });
        }
    }

</script>
@endpush
