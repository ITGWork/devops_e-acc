<fieldset class="white-box">
    <legend>
        <h4>
            <span class="text-danger">*</span>
            4.การปฏิบัติของห้องปฏิบัติการที่สอดคล้องตามข้อกำหนดมาตรฐานเลขที่ มอก. 17025 – 2561 (ISO/IEC 17025 : 2017)
        </h4>
    </legend>
    <div><h4>(Laboratory’s implementations which are conformed with TIS 17025 - 2561 (2018) (ISO/IEC 17025 : 2017))</h4></div>
    
    <div class="row repeater-form-file">
        <div class="col-md-12 box_section4" data-repeater-list="repeater-section4" >

            @php
                $section4_required = 'required';
            @endphp

            @if ( isset( $certi_lab->id ) && count( App\Models\Certify\Applicant\CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id )->where('file_section', '4')->get() ) > 0 )

                @php
                    $section4_required = '';
                    $file_sectionn4 = App\Models\Certify\Applicant\CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id )->where('file_section', '4')->get();
                @endphp

                @foreach ( $file_sectionn4 as $section4 )
                    <div class="form-group">
                        <div class="col-md-4 text-light"></div>
                        <div class="col-md-6">
                            <a href="{!! HP::getFileStorage($attach_path.$section4->file) !!}" target="_blank" class="view-attach btn btn-info btn-sm"> {!! HP::FileExtension($section4->file_client_name)  ?? '' !!}</a>
                            <a href="{{url('certify/applicant/delete/file_app_certi_lab_attach_all').'/'.$section4->id.'/'.$certi_lab->token}}" class="btn btn-danger btn-xs box_remove_file" onclick="return confirm('ต้องการลบไฟล์นี้ใช่หรือไม่ ?')">
                                <i class="fa fa-remove"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
                
            @endif

            <div class="form-group box_remove_file" data-repeater-item>
                <div class="col-md-4 text-light"></div>
                <div class="col-md-6">
                    <div class="fileinput fileinput-new input-group " data-provides="fileinput">
                        <div class="form-control" data-trigger="fileinput">
                            <i class="glyphicon glyphicon-file fileinput-exists"></i>
                            <span class="fileinput-filename"></span>
                        </div>
                        <span class="input-group-addon btn btn-default btn-file">
                            <span class="fileinput-new">เลือกไฟล์</span>
                            <span class="fileinput-exists">เปลี่ยน</span>
                            <input type="file" name="attachs_sec4" class="attachs_sec4 check_max_size_file" {!!  $section4_required !!}>
                        </span> 
                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                    </div>
                    {!! $errors->first('attachs_sec4', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="col-md-2">
                    <button class="btn btn-danger delete-sec4" type="button" data-repeater-delete>
                        <i class="fa fa-close"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <div class="col-md-10"></div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-success" data-repeater-create><i class="icon-plus"></i>เพิ่ม</button>
                </div>
            </div> 
        </div>
    </div>


    
</fieldset>

<fieldset class="white-box">
    <legend>
        <h4>
            <span class="text-danger">*</span> 
            5. รายชื่อคุณวุฒิประสบการณ์และขอบข่ายความรับผิดชอบของเจ้าหน้าที่ (List of relevant personnel providing name, qualification, experience and responsibility)
        </h4>
    </legend>

    <div class="row repeater-form-file">
        <div class="col-md-12 box_section5" data-repeater-list="repeater-section5" >
            @php
                $section5_required = 'required';
            @endphp
            @if ( isset( $certi_lab->id ) && count( App\Models\Certify\Applicant\CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id )->where('file_section', '5')->get() ) > 0 )

                @php
                    $section5_required = '';

                    $file_sectionn5 = App\Models\Certify\Applicant\CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id )->where('file_section', '5')->get();
                @endphp

                @foreach ( $file_sectionn5 as $section5 )
                    <div class="form-group">
                        <div class="col-md-4 text-light"></div>
                        <div class="col-md-6">
                            <a href="{!! HP::getFileStorage($attach_path.$section5->file) !!}" target="_blank" class="view-attach btn btn-info btn-sm"> {!! HP::FileExtension($section5->file_client_name)  ?? '' !!}</a>
                            <a href="{{url('certify/applicant/delete/file_app_certi_lab_attach_all').'/'.$section5->id.'/'.$certi_lab->token}}" class="btn btn-danger btn-xs box_remove_file" onclick="return confirm('ต้องการลบไฟล์นี้ใช่หรือไม่ ?')">
                                <i class="fa fa-remove"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            @endif

            <div class="form-group box_remove_file" data-repeater-item>
                <div class="col-md-4 text-light"></div>
                <div class="col-md-6">
                    <div class="fileinput fileinput-new input-group " data-provides="fileinput">
                        <div class="form-control" data-trigger="fileinput">
                            <i class="glyphicon glyphicon-file fileinput-exists"></i>
                            <span class="fileinput-filename"></span>
                        </div>
                        <span class="input-group-addon btn btn-default btn-file">
                            <span class="fileinput-new">เลือกไฟล์</span>
                            <span class="fileinput-exists">เปลี่ยน</span>
                            <input type="file" name="attachs_sec5" class="attachs_sec5 check_max_size_file" {!!  $section5_required !!}>
                        </span> 
                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                    </div>
                    {!! $errors->first('attachs_sec5', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="col-md-2">
                    <button class="btn btn-danger delete-sec5" type="button" data-repeater-delete>
                        <i class="fa fa-close"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <div class="col-md-10"></div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-success" data-repeater-create><i class="icon-plus"></i>เพิ่ม</button>
                </div>
            </div> 
        </div>
    </div>


</fieldset>


@include ('certify.applicant.forms.form_scope')

@include ('certify.applicant.forms.form_scope_test')

<fieldset class="white-box">
    <legend>
        <h4>
            <span class="text-danger">*</span> 
            7.เครื่องมือ (Equipment) 
        </h4>
    </legend>

    <div id="viewForm92" style="display: none">

        <div class="row repeater-form-file">
            <div class="col-md-12 box_section72"  >
                <div class="form-group">
                    <div class="col-md-4  text-light">
                        {!! Form::label('attachs_sec72', 'กรุณาแนบไฟล์เครื่องมือ', ['class' => 'col-md-12 label_attach text-light  control-label ']) !!}
                    </div>
                    <div class="col-md-6" data-repeater-list="repeater-section72">
                        @php
                            $section72_required = 'required';
                        @endphp
                        @if ( isset( $certi_lab->id ) && count( App\Models\Certify\Applicant\CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id )->where('file_section', '72')->get() ) > 0 )

                            @php
                                $section72_required = '';
                
                                $file_sectionn72 = App\Models\Certify\Applicant\CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id )->where('file_section', '72')->get();
                            @endphp
                
                            @foreach ( $file_sectionn72 as $section72 )
                                <div class="row form-group" >
                                    <div class="col-md-12">
                                        <a href="{!! HP::getFileStorage($attach_path.$section72->file) !!}" target="_blank" class="view_attach_sec72 btn btn-info btn-sm"> {!! HP::FileExtension($section72->file_client_name)  ?? '' !!}</a>
                                        <a href="{{url('certify/applicant/delete/file_app_certi_lab_attach_all').'/'.$section72->id.'/'.$certi_lab->token}}" class="btn btn-danger btn-xs box_remove_file" onclick="return confirm('ต้องการลบไฟล์นี้ใช่หรือไม่ ?')">
                                            <i class="fa fa-remove"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        <div class="row box_remove_file" data-repeater-item>
                            <div class="col-md-10">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                    <div class="form-control" data-trigger="fileinput">
                                        <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                        <span class="fileinput-filename"></span>
                                    </div>
                                    <span class="input-group-addon btn btn-default btn-file">
                                        <span class="fileinput-new">เลือกไฟล์</span> 
                                        <span class="fileinput-exists">เปลี่ยน</span>
                                        <input type="file" name="attachs_sec72" class="attachs_sec72 check_max_size_file">
                                    </span>
                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <button class="btn btn-danger  delete-sec72" type="button" data-repeater-delete>
                                    <i class="fa fa-close"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-md-9"></div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-success" data-repeater-create><i class="icon-plus"></i>เพิ่ม</button>
                    </div>
                </div> 
            </div>
        </div>

        <input type="hidden" name="calibrateAddSize" id="calibrateAddSize" value="1">
    </div>

    <div id="viewForm93" style="display: none;">

        <div class="row repeater-form-file">
            <div class="col-md-12 box_section71" >
                <div class="form-group">
                    <div class="col-md-4  text-light">
                        {!! Form::label('attachs_sec71', 'กรุณาแนบไฟล์เครื่องมือ', ['class' => 'col-md-12 label_attach text-light  control-label ']) !!}
                    </div>
                    <div class="col-md-6" data-repeater-list="repeater-section71" >
                        @php
                            $section71_required = 'required';
                        @endphp
                        @if ( isset( $certi_lab->id ) && count( App\Models\Certify\Applicant\CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id )->where('file_section', '71')->get() ) > 0 )

                            @php
                                $section71_required = '';
                
                                $file_sectionn71 = App\Models\Certify\Applicant\CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id )->where('file_section', '71')->get();
                            @endphp
                
                            @foreach ( $file_sectionn71 as $section71 )
                                <div class="row form-group">
                                    <div class="col-md-12">
                                        <a href="{!! HP::getFileStorage($attach_path.$section71->file) !!}" target="_blank" class="view_attach_sec71 btn btn-info btn-sm"> {!! HP::FileExtension($section71->file_client_name)  ?? '' !!}</a>
                                        <a href="{{url('certify/applicant/delete/file_app_certi_lab_attach_all').'/'.$section71->id.'/'.$certi_lab->token}}" class="btn btn-danger btn-xs box_remove_file" onclick="return confirm('ต้องการลบไฟล์นี้ใช่หรือไม่ ?')">
                                            <i class="fa fa-remove"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        <div class="row box_remove_file" data-repeater-item>
                            <div class="col-md-10">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                    <div class="form-control" data-trigger="fileinput">
                                        <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                        <span class="fileinput-filename"></span>
                                    </div>
                                    <span class="input-group-addon btn btn-default btn-file">
                                        <span class="fileinput-new">เลือกไฟล์</span> 
                                        <span class="fileinput-exists">เปลี่ยน</span>
                                        <input type="file" name="attachs_sec71" class="attachs_sec71 check_max_size_file">
                                    </span>
                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-danger  delete-sec71" type="button" data-repeater-delete>
                                    <i class="fa fa-close"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-md-9"></div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-success" data-repeater-create><i class="icon-plus"></i>เพิ่ม</button>
                    </div>
                </div> 
            </div>
        </div>


    </div>

</fieldset>

<fieldset class="white-box">
    <legend>
        <h4>
            8. วัสดุอ้างอิง/วัสดุอ้างอิงรับรอง (Reference material / certified reference material) 
        </h4>
    </legend>

    <div class="row repeater-form-file">
        <div class="col-md-12 box_section8" data-repeater-list="repeater-section8" >

            @if ( isset( $certi_lab->id ) && count( App\Models\Certify\Applicant\CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id )->where('file_section', '8')->get() ) > 0 )

                @php
                    $file_sectionn8 = App\Models\Certify\Applicant\CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id )->where('file_section', '8')->get();
                @endphp

                @foreach ( $file_sectionn8 as $section8 )
                    <div class="form-group">
                        <div class="col-md-4 text-light"></div>
                        <div class="col-md-6">
                            <a href="{!! HP::getFileStorage($attach_path.$section8->file) !!}" target="_blank" class="view-attach btn btn-info btn-sm"> {!! HP::FileExtension($section8->file_client_name)  ?? '' !!}</a>
                            <a href="{{url('certify/applicant/delete/file_app_certi_lab_attach_all').'/'.$section8->id.'/'.$certi_lab->token}}" class="btn btn-danger btn-xs box_remove_file" onclick="return confirm('ต้องการลบไฟล์นี้ใช่หรือไม่ ?')">
                                <i class="fa fa-remove"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            @endif

            <div class="form-group box_remove_file" data-repeater-item>
                <div class="col-md-4 text-light"></div>
                <div class="col-md-6">
                    <div class="fileinput fileinput-new input-group " data-provides="fileinput">
                        <div class="form-control" data-trigger="fileinput">
                            <i class="glyphicon glyphicon-file fileinput-exists"></i>
                            <span class="fileinput-filename"></span>
                        </div>
                        <span class="input-group-addon btn btn-default btn-file">
                            <span class="fileinput-new">เลือกไฟล์</span>
                            <span class="fileinput-exists">เปลี่ยน</span>
                            <input type="file" name="attachs_sec8" class="attachs_sec8 check_max_size_file">
                        </span> 
                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                    </div>
                    {!! $errors->first('attachs_sec8', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="col-md-2">
                    <button class="btn btn-danger delete-sec8" type="button" data-repeater-delete>
                        <i class="fa fa-close"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <div class="col-md-10"></div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-success" data-repeater-create><i class="icon-plus"></i>เพิ่ม</button>
                </div>
            </div> 
        </div>
    </div>

</fieldset>

<fieldset class="white-box">
    <legend>
        <h4>
            <span class="text-danger">*</span> 
            9. การเข้าร่วมการทดสอบความชำนาญ / การเปรียบเทียบผลระหว่างห้องปฏิบัติการ (Participation in Proficiency testing program / Interlaboratory comparison)  
        </h4>
    </legend>

    <div class="row repeater-form-file">
        <div class="col-md-12 box_section9" data-repeater-list="repeater-section9" >

            @php
                $section9_required = 'required';
            @endphp

            @if ( isset( $certi_lab->id ) && count( App\Models\Certify\Applicant\CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id )->where('file_section', '9')->get() ) > 0 )

                @php
                    $section9_required = '';
                    $file_sectionn9 = App\Models\Certify\Applicant\CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id )->where('file_section', '9')->get();
                @endphp

                @foreach ( $file_sectionn9 as $section9 )
                    <div class="form-group">
                        <div class="col-md-4 text-light"></div>
                        <div class="col-md-6">
                            <a href="{!! HP::getFileStorage($attach_path.$section9->file) !!}" target="_blank" class="view-attach btn btn-info btn-sm"> {!! HP::FileExtension($section9->file_client_name)  ?? '' !!}</a>
                            <a href="{{url('certify/applicant/delete/file_app_certi_lab_attach_all').'/'.$section9->id.'/'.$certi_lab->token}}" class="btn btn-danger btn-xs box_remove_file" onclick="return confirm('ต้องการลบไฟล์นี้ใช่หรือไม่ ?')">
                                <i class="fa fa-remove"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
                
            @endif

            <div class="form-group box_remove_file" data-repeater-item>
                <div class="col-md-4 text-light"></div>
                <div class="col-md-6">
                    <div class="fileinput fileinput-new input-group " data-provides="fileinput">
                        <div class="form-control" data-trigger="fileinput">
                            <i class="glyphicon glyphicon-file fileinput-exists"></i>
                            <span class="fileinput-filename"></span>
                        </div>
                        <span class="input-group-addon btn btn-default btn-file">
                            <span class="fileinput-new">เลือกไฟล์</span>
                            <span class="fileinput-exists">เปลี่ยน</span>
                            <input type="file" name="attachs_sec9" class="attachs_sec9 check_max_size_file" {!!  $section9_required !!}>
                        </span> 
                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                    </div>
                    {!! $errors->first('attachs_sec9', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="col-md-2">
                    <button class="btn btn-danger delete-sec9" type="button" data-repeater-delete>
                        <i class="fa fa-close"></i>
                    </button>
                </div>
            </div>

        </div>
        <div class="col-md-12">
            <div class="form-group">
                <div class="col-md-10"></div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-success" data-repeater-create><i class="icon-plus"></i>เพิ่ม</button>
                </div>
            </div> 
        </div>
    </div>

</fieldset>

<fieldset class="white-box">
    <legend>
        <h4>
            10. เอกสารอ้างอิง ชื่อย่อ
        </h4>
    </legend>

    <div class="row repeater-form-file">
        <div class="col-md-12 box_section10" data-repeater-list="repeater-section10" >
            @if ( isset( $certi_lab->id ) && count( App\Models\Certify\Applicant\CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id )->where('file_section', '10')->get() ) > 0 )

                @php
                    $file_sectionn10 = App\Models\Certify\Applicant\CertiLabAttachAll::where('app_certi_lab_id', $certi_lab->id )->where('file_section', '10')->get();
                @endphp

                @foreach ( $file_sectionn10 as $section10 )
                    <div class="form-group">
                        <div class="col-md-4 text-light"></div>
                        <div class="col-md-6">
                            <a href="{!! HP::getFileStorage($attach_path.$section10->file) !!}" target="_blank" class="view-attach btn btn-info btn-sm"> {!! HP::FileExtension($section10->file_client_name)  ?? '' !!}</a>
                            <a href="{{url('certify/applicant/delete/file_app_certi_lab_attach_all').'/'.$section10->id.'/'.$certi_lab->token}}" class="btn btn-danger btn-xs box_remove_file" onclick="return confirm('ต้องการลบไฟล์นี้ใช่หรือไม่ ?')">
                                <i class="fa fa-remove"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
             @endif
            <div class="form-group box_remove_file" data-repeater-item>
                <div class="col-md-4 text-light"></div>
                <div class="col-md-6">
                    <div class="fileinput fileinput-new input-group " data-provides="fileinput">
                        <div class="form-control" data-trigger="fileinput">
                            <i class="glyphicon glyphicon-file fileinput-exists"></i>
                            <span class="fileinput-filename"></span>
                        </div>
                        <span class="input-group-addon btn btn-default btn-file">
                            <span class="fileinput-new">เลือกไฟล์</span>
                            <span class="fileinput-exists">เปลี่ยน</span>
                            <input type="file" name="attachs_sec10" class="attachs_sec10 check_max_size_file" >
                        </span> 
                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                    </div>
                    {!! $errors->first('attachs_sec10', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="col-md-2">
                    <button class="btn btn-danger delete-sec10" type="button" data-repeater-delete>
                        <i class="fa fa-close"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <div class="col-md-10"></div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-success" data-repeater-create><i class="icon-plus"></i>เพิ่ม</button>
                </div>
            </div> 
        </div>
    </div>

</fieldset>

<fieldset class="white-box">
    <legend>
        <h4>
         11.   เอกสารอื่นๆ (Others)  
        </h4>
    </legend>

    <div class="row repeater-form-file">
        <div class="col-md-12 box_section_other" data-repeater-list="repeater-section-other" >

            @if ( isset( $certi_lab->id ) && count( App\Models\Certify\Applicant\CertiLabAttachMore::where('app_certi_lab_id', $certi_lab->id )->get() ) > 0 )

                @php
                    $file_more = App\Models\Certify\Applicant\CertiLabAttachMore::where('app_certi_lab_id', $certi_lab->id )->get();
                @endphp
                @foreach ( $file_more as $more )
                    <div class="form-group">
                        <div class="col-md-4 text-light">
                            <input type="text" class='form-control' value="{!! !empty( $more->file_desc )?$more->file_desc:null !!}" disabled>
                        </div>
                        <div class="col-md-6">
                            <a href="{!! HP::getFileStorage($attach_path.$more->file) !!}" target="_blank" class="view-attach btn btn-info btn-sm"> {!! HP::FileExtension($more->file_client_name)  ?? '' !!}</a>
                            <a href="{{url('certify/applicant/delete/file_app_certi_lab_attach_more').'/'.$more->id.'/'.$certi_lab->token}}" class="btn btn-danger btn-xs box_remove_file" onclick="return confirm('ต้องการลบไฟล์นี้ใช่หรือไม่ ?')">
                                <i class="fa fa-remove"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            @endif

            <div class="form-group box_remove_file" data-repeater-item>
                <div class="col-md-4 text-light">
                    {!! Form::text('another_attach_files_desc', null, ['class' => 'form-control', 'placeholder' => 'กรุณากรอกชื่อไฟล์']) !!}
                </div>
                <div class="col-md-6">
                    <div class="fileinput fileinput-new input-group " data-provides="fileinput">
                        <div class="form-control" data-trigger="fileinput">
                            <i class="glyphicon glyphicon-file fileinput-exists"></i>
                            <span class="fileinput-filename"></span>
                        </div>
                        <span class="input-group-addon btn btn-default btn-file">
                            <span class="fileinput-new">เลือกไฟล์</span>
                            <span class="fileinput-exists">เปลี่ยน</span>
                            <input type="file" name="another_attach_files" class="another_attach_files check_max_size_file" >
                        </span> 
                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                    </div>
                    {!! $errors->first('another_attach_files', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="col-md-2">
                    <button class="btn btn-danger delete-more" type="button" data-repeater-delete>
                        <i class="fa fa-close"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <div class="col-md-10"></div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-success" data-repeater-create><i class="icon-plus"></i>เพิ่ม</button>
                </div>
            </div> 
        </div>
    </div>

</fieldset>


@push('js')
    <script src="{{asset('js/jasny-bootstrap.js')}}"></script>
    <script>
        $(document).ready(function () {


            //เพิ่มตำแหน่งงาน
            $('#test_tools_add').click(function() {

                $('.div_test_name_trader:first').clone().insertAfter(".div_test_name_trader:last");
                var last_new = $(".div_test_name_trader:last");
                $('.div_test_name_trader:last > label').text(''); 
                  $(last_new).find('input[type="text"]').val('');
                // resetOrder();
                ShowHideTestNameTrader();
            });

            //ลบตำแหน่ง
            $('body').on('click', '.test_tools_remove', function() {

                $(this).parent().parent().parent().parent().parent().remove();

                // reOrderLabTest();
                ShowHideTestNameTrader();
            });
            ShowHideTestNameTrader();

            //เพิ่มตำแหน่งงาน
            $('#test_program_add').click(function() {

                let newBox = $('#test_program_box').children(':first').clone(); //Clone Element
                newBox.find('input.mydatepicker').datepicker({
                    autoclose: true,
                    todayHighlight: true,
                    format: 'dd/mm/yyyy',
                    orientation: 'bottom'
                });
                newBox.find('input').val('');
                newBox.appendTo('#test_program_box');

                var last_new = $('#test_program_box').children(':last');
                //Change Button
                $(last_new).find('button').removeClass('btn-success');
                $(last_new).find('button').addClass('btn-danger test_program_remove');
                $(last_new).find('button').html('<i class="icon-close"></i> ลบ');
                reOrderTestProgram();

            });

            //ลบตำแหน่ง
            $('body').on('click', '.test_program_remove', function() {

                $(this).parent().parent().parent().parent().remove();

                reOrderTestProgram();

            });

            check_max_size_file();
            //เพิ่มไฟล์แนบ
            $('#another_attach-add').click(function(event) {
                $('.another_attach_files:first').clone().appendTo('#another_attach_files-box');
                $('.another_attach_files:last').find('input').val('');
                $('.another_attach_files:last').find('a.fileinput-exists').click();
                $('.another_attach_files:last').find('a.view-attach').remove();
                $('.another_attach_files:last').find('.label_attach').remove();
                $('.another_attach_files:last').find('button.another_attach-add').remove();
                $('.another_attach_files:last').find('.button_remove_files').html('<button class="btn btn-danger btn-sm another_attach_remove" type="button"> <i class="icon-close"></i>  </button>');
                // ShowHideRemoveBtn();
                check_max_size_file();
            });

            //ลบไฟล์แนบ
            $('body').on('click', '.another_attach_remove', function(event) {
                $(this).parent().parent().parent().remove();
                // ShowHideRemoveBtn();
            });
        });

        function ShowHideTestNameTrader() { //ซ่อน-แสดงปุ่มลบ
            var rows = $('div.div_test_name_trader').children(); //แถวทั้งหมด
            rows.each(function(index, el) {
                 if(index > 0){
                    $(el).find('.test_tools_remove').show();
                 }else{
                     $(el).find('.test_tools_remove').hide();
                 }
                $(el).find('label.label_last_new').first().html((index+1)+'.  รายการ (ชื่อและเครื่องหมายการค้า): ');
                // $(el).find('.test_tools_no').val((index+1));
            });
        }

        function reOrderTestProgram(){//รีเซตลำดับของตำแหน่ง
            let new_val = 0;
            $('.test_program_box').children().each(function(index, el) {
                $(el).find('input[name="test_program_no[]"]').val(index+1);
                new_val++;
            });
            return new_val;
        }
    </script>   
@endpush