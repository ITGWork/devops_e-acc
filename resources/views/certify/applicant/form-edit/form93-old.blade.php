<?php $key93=0?>
<div id="viewForm93" class="{{$certi_lab->lab_type == 3 ? 'show':'hide'}}">
    <div class="m-l-10 m-t-20 form-group" style="margin-bottom: 0">
        <h4 class="m-l-5">7. เครื่องมือ (ทดสอบ)</h4>
    </div>
    @if ($certi_lab)
        @if($certi_lab->certi_tools_test->count() > 0)
            <button class="btn btn-success btn-sm pull-right m-b-10 m-l-5" id="test_tools_add" type="button">
                <i class="icon-plus"></i> เพิ่ม
            </button>
            <div id="tools-test-container">
                @foreach ($certi_lab->certi_tools_test as $tools_test)
                    <?php $key93++ ?>
                    <div class="test_tools_box">
                        <div class="row test_tools_item">
                            <div class="col-md-12 m-b-10">
                                <div class="row">
                                    <div class="col-md-8"></div>
                                    {!! $errors->first('all_test_tools[]', '<p class="help-block">:message</p>') !!}
                                    <div class="col-md-4">
                                        @if($key93==0)
                                            <button class="btn btn-success pull-right btn-sm" id="test_tools_add" type="button">
                                                <i class="icon-plus"></i> เพิ่ม
                                            </button>
                                        @else
                                            <button class="btn btn-danger pull-right btn-sm test_tools_remove" type="button">
                                                <i class="icon-close"></i> &nbsp;ลบ
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="white-box" style="border: 2px solid #e5ebec;">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group {{ $errors->has('test_tools_no[]') ? 'has-error' : ''}}">
                                                {!! Form::label('test_tools_no', 'ลำดับที่: ', ['class' => ' control-label']) !!}
                                                <input type="number" name="test_tools_no[]" class="form-control text-center" readonly value="{{$tools_test->no ?? ''}}">
                                                {!! $errors->first('test_tools_no[]', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group {{ $errors->has('test_license_number[]') ? 'has-error' : ''}}">
                                                {!! Form::label('test_license_number[]', 'หมายเลขทะเบียน: ', ['class' => ' control-label']) !!}
                                                {!! Form::text('test_license_number[]',$tools_test->regis_no ?? '', ['class' => 'form-control']) !!}
                                                {!! $errors->first('test_license_number[]', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group {{ $errors->has('test_name_trader[]') ? 'has-error' : ''}}">
                                                {!! Form::label('test_name_trader[]', 'รายการ (ชื่อและเครื่องหมายการค้า): ', ['class' => ' control-label']) !!}
                                                {!! Form::text('test_name_trader[]',$tools_test->name ?? '', ['class' => 'form-control']) !!}
                                                {!! $errors->first('test_name_trader[]', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {{ $errors->has('test_type_model_layout[]') ? 'has-error' : ''}}">
                                                {!! Form::label('test_type_model_layout[]', 'ประเภท/รุ่น/แบบ: ', ['class' => ' control-label']) !!}
                                                {!! Form::text('test_type_model_layout[]',$tools_test->type ?? '', ['class' => 'form-control']) !!}
                                                {!! $errors->first('test_type_model_layout[]', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {{ $errors->has('test_number_code[]') ? 'has-error' : ''}}">
                                                {!! Form::label('test_number_code[]', 'เลขที่/รหัส: ', ['class' => ' control-label']) !!}
                                                {!! Form::text('test_number_code[]',$tools_test->code_no ?? '', ['class' => 'form-control']) !!}
                                                {!! $errors->first('test_number_code[]', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {{ $errors->has('test_limit_line[]') ? 'has-error' : ''}}">
                                                {!! Form::label('test_limit_line[]', 'ขีดความสามารถ: ', ['class' => ' control-label']) !!}
                                                {!! Form::text('test_limit_line[]',$tools_test->capability ?? '', ['class' => 'form-control']) !!}
                                                {!! $errors->first('test_limit_line[]', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {{ $errors->has('test_use_range[]') ? 'has-error' : ''}}">
                                                {!! Form::label('test_use_range[]', 'ช่วงการใช้งาน: ', ['class' => ' control-label']) !!}
                                                {!! Form::text('test_use_range[]',$tools_test->usage_time ?? '', ['class' => 'form-control']) !!}
                                                {!! $errors->first('test_use_range[]', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {{ $errors->has('test_standard_accept[]') ? 'has-error' : ''}}">
                                                {!! Form::label('test_standard_accept[]', 'เกณฑ์การยอมรับ: ', ['class' => ' control-label']) !!}
                                                {!! Form::text('test_standard_accept[]',$tools_test->standard ?? '', ['class' => 'form-control']) !!}
                                                {!! $errors->first('test_standard_accept[]', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {{ $errors->has('test_calibrate_freq[]') ? 'has-error' : ''}}">
                                                {!! Form::label('test_calibrate_freq[]', 'ความถี่ของการสอบเทียบ: ', ['class' => ' control-label']) !!}
                                                {!! Form::text('test_calibrate_freq[]',$tools_test->cali_times ?? '', ['class' => 'form-control']) !!}
                                                {!! $errors->first('test_calibrate_freq[]', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {{ $errors->has('test_last_calibrate[]') ? 'has-error' : ''}}">
                                                {!! Form::label('test_last_calibrate[]', 'สอบเทียบล่าสุด: ', ['class' => ' control-label']) !!}
                                                {!! Form::text('test_last_calibrate[]',$tools_test->cali_latest_date ? \Carbon\Carbon::parse($tools_test->cali_latest_date)->format('d/m/Y'):'', ['class' => 'form-control mydatepicker']) !!}
                                                {!! $errors->first('test_last_calibrate[]', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {{ $errors->has('test_matured_calibrate[]') ? 'has-error' : ''}}">
                                                {!! Form::label('test_matured_calibrate[]', 'วันที่ครบอายุสอบเทียบ: ', ['class' => ' control-label']) !!}
                                                {!! Form::text('test_matured_calibrate[]',$tools_test->cali_anni_date ? \Carbon\Carbon::parse($tools_test->cali_anni_date)->format('d/m/Y'):'', ['class' => 'form-control mydatepicker']) !!}
                                                {!! $errors->first('test_matured_calibrate[]', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {{ $errors->has('test_calibrate_department[]') ? 'has-error' : ''}}">
                                                {!! Form::label('test_calibrate_department[]', 'หน่วยงานที่สอบเทียบ: ', ['class' => ' control-label']) !!}
                                                {!! Form::text('test_calibrate_department[]',$tools_test->cali_depart ?? '', ['class' => 'form-control']) !!}
                                                {!! $errors->first('test_calibrate_department[]', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div id="tools-test-container">
                <div class="test_tools_box">
                    <div class="row test_tools_item">
                        <div class="col-md-12 m-b-10">
                            <div class="row">
                                <div class="col-md-8"></div>
                                {!! $errors->first('all_test_tools[]', '<p class="help-block">:message</p>') !!}
                                <div class="col-md-4">
                                    @if($key93==0)
                                        <button class="btn btn-success pull-right btn-sm" id="test_tools_add" type="button">
                                            <i class="icon-plus"></i> เพิ่ม
                                        </button>
                                    @else
                                        <button class="btn btn-danger pull-right btn-sm test_tools_remove" type="button">
                                            <i class="icon-close"></i> &nbsp;ลบ
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="white-box" style="border: 2px solid #e5ebec;">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group {{ $errors->has('test_tools_no[]') ? 'has-error' : ''}}">
                                            {!! Form::label('test_tools_no', 'ลำดับที่: ', ['class' => ' control-label']) !!}
                                            <input type="number" name="test_tools_no[]" class="form-control text-center" readonly value="1">
                                            {!! $errors->first('test_tools_no[]', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group {{ $errors->has('test_license_number[]') ? 'has-error' : ''}}">
                                            {!! Form::label('test_license_number[]', 'หมายเลขทะเบียน: ', ['class' => ' control-label']) !!}
                                            {!! Form::text('test_license_number[]',null, ['class' => 'form-control']) !!}
                                            {!! $errors->first('test_license_number[]', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group {{ $errors->has('test_name_trader[]') ? 'has-error' : ''}}">
                                            {!! Form::label('test_name_trader[]', 'รายการ (ชื่อและเครื่องหมายการค้า): ', ['class' => ' control-label']) !!}
                                            {!! Form::text('test_name_trader[]',null, ['class' => 'form-control']) !!}
                                            {!! $errors->first('test_name_trader[]', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {{ $errors->has('test_type_model_layout[]') ? 'has-error' : ''}}">
                                            {!! Form::label('test_type_model_layout[]', 'ประเภท/รุ่น/แบบ: ', ['class' => ' control-label']) !!}
                                            {!! Form::text('test_type_model_layout[]',null, ['class' => 'form-control']) !!}
                                            {!! $errors->first('test_type_model_layout[]', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {{ $errors->has('test_number_code[]') ? 'has-error' : ''}}">
                                            {!! Form::label('test_number_code[]', 'เลขที่/รหัส: ', ['class' => ' control-label']) !!}
                                            {!! Form::text('test_number_code[]',null, ['class' => 'form-control']) !!}
                                            {!! $errors->first('test_number_code[]', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {{ $errors->has('test_limit_line[]') ? 'has-error' : ''}}">
                                            {!! Form::label('test_limit_line[]', 'ขีดความสามารถ: ', ['class' => ' control-label']) !!}
                                            {!! Form::text('test_limit_line[]',null, ['class' => 'form-control']) !!}
                                            {!! $errors->first('test_limit_line[]', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {{ $errors->has('test_use_range[]') ? 'has-error' : ''}}">
                                            {!! Form::label('test_use_range[]', 'ช่วงการใช้งาน: ', ['class' => ' control-label']) !!}
                                            {!! Form::text('test_use_range[]',null, ['class' => 'form-control']) !!}
                                            {!! $errors->first('test_use_range[]', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {{ $errors->has('test_standard_accept[]') ? 'has-error' : ''}}">
                                            {!! Form::label('test_standard_accept[]', 'เกณฑ์การยอมรับ: ', ['class' => ' control-label']) !!}
                                            {!! Form::text('test_standard_accept[]',null, ['class' => 'form-control']) !!}
                                            {!! $errors->first('test_standard_accept[]', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {{ $errors->has('test_calibrate_freq[]') ? 'has-error' : ''}}">
                                            {!! Form::label('test_calibrate_freq[]', 'ความถี่ของการสอบเทียบ: ', ['class' => ' control-label']) !!}
                                            {!! Form::text('test_calibrate_freq[]',null, ['class' => 'form-control']) !!}
                                            {!! $errors->first('test_calibrate_freq[]', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {{ $errors->has('test_last_calibrate[]') ? 'has-error' : ''}}">
                                            {!! Form::label('test_last_calibrate[]', 'สอบเทียบล่าสุด: ', ['class' => ' control-label']) !!}
                                            {!! Form::text('test_last_calibrate[]',null, ['class' => 'form-control mydatepicker']) !!}
                                            {!! $errors->first('test_last_calibrate[]', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {{ $errors->has('test_matured_calibrate[]') ? 'has-error' : ''}}">
                                            {!! Form::label('test_matured_calibrate[]', 'วันที่ครบอายุสอบเทียบ: ', ['class' => ' control-label']) !!}
                                            {!! Form::text('test_matured_calibrate[]',null, ['class' => 'form-control mydatepicker']) !!}
                                            {!! $errors->first('test_matured_calibrate[]', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {{ $errors->has('test_calibrate_department[]') ? 'has-error' : ''}}">
                                            {!! Form::label('test_calibrate_department[]', 'หน่วยงานที่สอบเทียบ: ', ['class' => ' control-label']) !!}
                                            {!! Form::text('test_calibrate_department[]',null, ['class' => 'form-control']) !!}
                                            {!! $errors->first('test_calibrate_department[]', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $key93++ ?>
            </div>
        @endif
    @endif
</div>
@push('js')
    <script src="{{ asset('plugins/components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            let box_test = $('.test_tools_box:first').clone();
            //ปฎิทิน
            $('.mydatepicker').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd/mm/yyyy',
                orientation: 'bottom'
            });

            //เพิ่มตำแหน่งงาน
            $('#test_tools_add').click(function() {

                let newBox = box_test.clone(); //Clone Element
                newBox.find('input.mydatepicker').datepicker({
                    autoclose: true,
                    todayHighlight: true,
                    format: 'dd/mm/yyyy',
                    orientation: 'bottom'
                });
                newBox.find('input').val('');
                newBox.appendTo('#tools-test-container');

                var last_new = $('.test_tools_box').children(':last');
                {!! $key93+=1 !!}
                //Change Button
                $(last_new).find('button').removeClass('btn-success');
                $(last_new).find('button').addClass('btn-danger test_tools_remove');
                $(last_new).find('button').html('<i class="icon-close"></i> &nbsp;ลบ');
                if (reOrderLabTest() === 1){
                    last_new.find('button').remove();
                }

            });

            //ลบตำแหน่ง
            $('body').on('click', '.test_tools_remove', function() {

                $(this).parent().parent().parent().parent().parent().remove();

                if (reOrderLabTest() === 0){
                    $('#test_tools_add').trigger('click');
                }

            });


        });

        function reOrderLabTest(){//รีเซตลำดับของตำแหน่ง
            let new_val = 0;
            $('.test_tools_box').children().each(function(index, el) {
                $(el).find('input[name="test_tools_no[]"]').val(index+1);
                new_val++;
            });
            return new_val;
        }
    </script>
@endpush