<fieldset class="white-box">
    <legend><h4><span class="text-danger">*</span> 2. คู่มือคุณภาพและขั้นตอนการดำเนินงานของหน่วยงานตรวจสอบที่สอดคล้องตามข้อกำหนดมาตรฐานเลขที่ มอก.17020 (Inspection body implementations which are conformed with TIS 17020)</h4></legend>

    <div class="row repeater-form-file">
        <div class="col-md-12 box_section1" data-repeater-list="repeater-section1" >

            @php
                $section1_required = 'required';
            @endphp

            @if ( isset( $certi_ib->id ) && count( App\Models\Certify\ApplicantIB\CertiIBAttachAll::where('app_certi_ib_id', $certi_ib->id )->where('file_section', '1')->get() ) > 0 )

                @php
                    $section1_required = '';
                    $file_sectionn1 = App\Models\Certify\ApplicantIB\CertiIBAttachAll::where('app_certi_ib_id', $certi_ib->id )->where('file_section', '1')->get();
                @endphp

                @foreach ( $file_sectionn1 as $section1 )
                    <div class="form-group">
                        <div class="col-md-4 text-light"></div>
                        <div class="col-md-6">
                            <a href="{!! HP::getFileStorage($attach_path.$section1->file) !!}" target="_blank" class="view-attach btn btn-info btn-sm"> {!! HP::FileExtension($section1->file_client_name)  ?? '' !!}</a>
                            <a href="{{url('certify/applicant-ib/delete/file_app_certi_ib_attach_all').'/'.$section1->id.'/'.$certi_ib->token}}" class="btn btn-danger btn-xs box_remove_file" onclick="return confirm('ต้องการลบไฟล์นี้ใช่หรือไม่ ?')">
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
                            <input type="file" name="attachs_sec1" class="attachs_sec1 check_max_size_file" {!!  $section1_required !!}>
                        </span> 
                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                    </div>
                    {!! $errors->first('attachs_sec1', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="col-md-2">
                    <button class="btn btn-danger delete-sec1" type="button" data-repeater-delete>
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
    <legend><h4>3. รายชื่อคุณวุฒิประสบการณ์และขอบข่ายความรับผิดชอบของเจ้าหน้าที่ (List of relevant personnel providing name, qualification, experience and responsibility)</h4></legend>
    <div class="row repeater-form-file">
        <div class="col-md-12 box_section2" data-repeater-list="repeater-section2" >

            @if ( isset( $certi_ib->id ) && count( App\Models\Certify\ApplicantIB\CertiIBAttachAll::where('app_certi_ib_id', $certi_ib->id )->where('file_section', '2')->get() ) > 0 )

                @php
                    $file_sectionn2 = App\Models\Certify\ApplicantIB\CertiIBAttachAll::where('app_certi_ib_id', $certi_ib->id )->where('file_section', '2')->get();
                @endphp
        
                @foreach ( $file_sectionn2 as $section2 )
                    <div class="form-group">
                        <div class="col-md-4 text-light"></div>
                        <div class="col-md-6">
                            <a href="{!! HP::getFileStorage($attach_path.$section2->file) !!}" target="_blank" class="view-attach btn btn-info btn-sm"> {!! HP::FileExtension($section2->file_client_name)  ?? '' !!}</a>
                            <a href="{{url('certify/applicant-ib/delete/file_app_certi_ib_attach_all').'/'.$section2->id.'/'.$certi_ib->token}}" class="btn btn-danger btn-xs box_remove_file" onclick="return confirm('ต้องการลบไฟล์นี้ใช่หรือไม่ ?')">
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
                            <input type="file" name="attachs_sec2" class="attachs_sec2 check_max_size_file">
                        </span> 
                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                    </div>
                    {!! $errors->first('attachs_sec2', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="col-md-2">
                    <button class="btn btn-danger delete-sec2" type="button" data-repeater-delete>
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
            <span class="text-danger">*</span> 4. ขอบข่ายที่ยื่นขอรับการรับรอง (Scope of Accreditation Sought)
            <span class="text-danger">ไฟล์แนบ Word</span><span class="text-danger" style="font-size: 13px;"> (doc,docx)</span>
        </h4>
    </legend>
    <div class="row repeater-form-file">
        <div class="col-md-12 box_section3" data-repeater-list="repeater-section3" >

            @php
                $section3_required = 'required';
            @endphp

            @if ( isset( $certi_ib->id ) && count( App\Models\Certify\ApplicantIB\CertiIBAttachAll::where('app_certi_ib_id', $certi_ib->id )->where('file_section', '3')->get() ) > 0 )

                @php
                    $section3_required = '';
                    $file_sectionn3 = App\Models\Certify\ApplicantIB\CertiIBAttachAll::where('app_certi_ib_id', $certi_ib->id )->where('file_section', '3')->get();
                @endphp
        
                @foreach ( $file_sectionn3 as $section3 )
                    <div class="form-group">
                        <div class="col-md-4 text-light"></div>
                        <div class="col-md-6">
                            <a href="{!! HP::getFileStorage($attach_path.$section3->file) !!}" target="_blank" class="view-attach btn btn-info btn-sm"> {!! HP::FileExtension($section3->file_client_name)  ?? '' !!}</a>
                            <a href="{{url('certify/applicant-ib/delete/file_app_certi_ib_attach_all').'/'.$section3->id.'/'.$certi_ib->token}}" class="btn btn-danger btn-xs box_remove_file" onclick="return confirm('ต้องการลบไฟล์นี้ใช่หรือไม่ ?')">
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
                            <input type="file" name="attachs_sec3" class="attachs_sec3 check_max_size_file" {!! $section3_required !!}>
                        </span> 
                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                    </div>
                    {!! $errors->first('attachs_sec3', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="col-md-2">
                    <button class="btn btn-danger delete-sec3" type="button" data-repeater-delete>
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
            <span class="text-danger">*</span> 5. เครื่องมือ (Equipment)   
        </h4>
    </legend>

    <div class="row repeater-form-file">
        <div class="col-md-12 box_section4" >
            <div class="form-group">
                <div class="col-md-4  text-light">
                    {!! Form::label('attachs_sec4', 'กรุณาแนบไฟล์เครื่องมือ', ['class' => 'col-md-12 label_attach text-light  control-label ']) !!}
                </div>
                <div class="col-md-6" data-repeater-list="repeater-section4" >
                    @php
                        $section4_required = 'required';
                    @endphp
                    @if ( isset( $certi_ib->id ) && count( App\Models\Certify\ApplicantIB\CertiIBAttachAll::where('app_certi_ib_id', $certi_ib->id )->where('file_section', '4')->get() ) > 0 )

                        @php
                            $section4_required = '';
            
                            $file_sectionn4 = App\Models\Certify\ApplicantIB\CertiIBAttachAll::where('app_certi_ib_id', $certi_ib->id )->where('file_section', '4')->get();
                        @endphp
            
                        @foreach ( $file_sectionn4 as $section4 )
                            <div class="row form-group">
                                <div class="col-md-12">
                                    <a href="{!! HP::getFileStorage($attach_path.$section4->file) !!}" target="_blank" class="btn btn-info btn-sm"> {!! HP::FileExtension($section4->file_client_name)  ?? '' !!}</a>
                                    <a href="{{url('certify/applicant-ib/delete/file_app_certi_ib_attach_all').'/'.$section4->id.'/'.$certi_ib->token}}" class="btn btn-danger btn-xs box_remove_file" onclick="return confirm('ต้องการลบไฟล์นี้ใช่หรือไม่ ?')">
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
                                    <input type="file" name="attachs_sec4" class="attachs_sec4 check_max_size_file" {!! $section4_required !!}>
                                </span>
                                <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-danger  delete-sec4" type="button" data-repeater-delete>
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

</fieldset>

<fieldset class="white-box">
    <legend>
        <h4>
            6. วัสดุอ้างอิง/มาตรฐานอ้างอิง (Reference material / Reference TIS)  
        </h4>
    </legend>

    <div class="row repeater-form-file">
        <div class="col-md-12 box_section5" data-repeater-list="repeater-section5" >

            @if ( isset( $certi_ib->id ) && count( App\Models\Certify\ApplicantIB\CertiIBAttachAll::where('app_certi_ib_id', $certi_ib->id )->where('file_section', '5')->get() ) > 0 )

                @php
                    $file_sectionn5 = App\Models\Certify\ApplicantIB\CertiIBAttachAll::where('app_certi_ib_id', $certi_ib->id )->where('file_section', '5')->get();
                @endphp
        
                @foreach ( $file_sectionn5 as $section5 )
                    <div class="form-group">
                        <div class="col-md-4 text-light"></div>
                        <div class="col-md-6">
                            <a href="{!! HP::getFileStorage($attach_path.$section5->file) !!}" target="_blank" class="view-attach btn btn-info btn-sm"> {!! HP::FileExtension($section5->file_client_name)  ?? '' !!}</a>
                            <a href="{{url('certify/applicant-ib/delete/file_app_certi_ib_attach_all').'/'.$section5->id.'/'.$certi_ib->token}}" class="btn btn-danger btn-xs box_remove_file" onclick="return confirm('ต้องการลบไฟล์นี้ใช่หรือไม่ ?')">
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
                            <input type="file" name="attachs_sec5" class="attachs_sec5 check_max_size_file">
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

<fieldset class="white-box">
    <legend>
        <h4>    
            7. การเข้าร่วมการทดสอบความชำนาญ / การเปรียบเทียบผลระหว่างห้องปฏิบัติการ (Participation in Proficiency testing program / Interlaboratory comparison)   
        </h4>
    </legend>

    <div class="row repeater-form-file">
        <div class="col-md-12 box_section6" data-repeater-list="repeater-sectio6" >
            @if ( isset( $certi_ib->id ) && count( App\Models\Certify\ApplicantIB\CertiIBAttachAll::where('app_certi_ib_id', $certi_ib->id )->where('file_section', '6')->get() ) > 0 )

                @php
                    $file_sectionn6 = App\Models\Certify\ApplicantIB\CertiIBAttachAll::where('app_certi_ib_id', $certi_ib->id )->where('file_section', '6')->get();
                @endphp
        
                @foreach ( $file_sectionn6 as $section6 )
                    <div class="form-group">
                        <div class="col-md-4 text-light"></div>
                        <div class="col-md-6">
                            <a href="{!! HP::getFileStorage($attach_path.$section6->file) !!}" target="_blank" class="view-attach btn btn-info btn-sm"> {!! HP::FileExtension($section6->file_client_name)  ?? '' !!}</a>
                            <a href="{{url('certify/applicant-ib/delete/file_app_certi_ib_attach_all').'/'.$section6->id.'/'.$certi_ib->token}}" class="btn btn-danger btn-xs box_remove_file" onclick="return confirm('ต้องการลบไฟล์นี้ใช่หรือไม่ ?')">
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
                            <input type="file" name="attachs_sec6" class="attachs_sec6 check_max_size_file">
                        </span> 
                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                    </div>
                    {!! $errors->first('attachs_sec6', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="col-md-2">
                    <button class="btn btn-danger delete-sec6" type="button" data-repeater-delete>
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
            8. เอกสารอ้างอิง ชื่อย่อ
        </h4>
    </legend>

    <div class="row repeater-form-file">
        <div class="col-md-12 box_section8" data-repeater-list="repeater-section8" >
            @if ( isset( $certi_ib->id ) && count( App\Models\Certify\ApplicantIB\CertiIBAttachAll::where('app_certi_ib_id', $certi_ib->id )->where('file_section', '8')->get() ) > 0 )

                @php
                    $file_sectionn8 = App\Models\Certify\ApplicantIB\CertiIBAttachAll::where('app_certi_ib_id', $certi_ib->id )->where('file_section', '8')->get();
                @endphp
        
                @foreach ( $file_sectionn8 as $section8 )
                    <div class="form-group">
                        <div class="col-md-4 text-light"></div>
                        <div class="col-md-6">
                            <a href="{!! HP::getFileStorage($attach_path.$section8->file) !!}" target="_blank" class="view-attach btn btn-info btn-sm"> {!! HP::FileExtension($section8->file_client_name)  ?? '' !!}</a>
                            <a href="{{url('certify/applicant-ib/delete/file_app_certi_ib_attach_all').'/'.$section8->id.'/'.$certi_ib->token}}" class="btn btn-danger btn-xs box_remove_file" onclick="return confirm('ต้องการลบไฟล์นี้ใช่หรือไม่ ?')">
                                <i class="fa fa-remove"></i>
                            </a>
                        </div>
                    </div>
                @endforeach

            @else
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
                        {!! $errors->first('attachs_sec6', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                
            @endif
        </div>
    </div>


</fieldset>

<fieldset class="white-box">
    <legend>
        <h4>    
            เอกสารอื่นๆ (Others)  
        </h4>
    </legend>

    <div class="row repeater-form-file">
        <div class="col-md-12 box_section_other" data-repeater-list="repeater-section7" >

            @if ( isset( $certi_ib->id ) && count( App\Models\Certify\ApplicantIB\CertiIBAttachAll::where('app_certi_ib_id', $certi_ib->id )->where('file_section', '7')->get() ) > 0 )

                @php
                    $file_sectionn7 = App\Models\Certify\ApplicantIB\CertiIBAttachAll::where('app_certi_ib_id', $certi_ib->id )->where('file_section', '7')->get();
                @endphp
        
                @foreach ( $file_sectionn7 as $section7 )
                    <div class="form-group">
                        <div class="col-md-4 text-light"><input type="text" class='form-control' value="{!! !empty( $section7->file_desc )?$section7->file_desc:null !!}" disabled></div>
                        <div class="col-md-6">
                            <a href="{!! HP::getFileStorage($attach_path.$section7->file) !!}" target="_blank" class="view-attach btn btn-info btn-sm"> {!! HP::FileExtension($section7->file_client_name)  ?? '' !!}</a>
                            <a href="{{url('certify/applicant-ib/delete/file_app_certi_ib_attach_all').'/'.$section7->id.'/'.$certi_ib->token}}" class="btn btn-danger btn-xs box_remove_file" onclick="return confirm('ต้องการลบไฟล์นี้ใช่หรือไม่ ?')">
                                <i class="fa fa-remove"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
                
            @endif

            <div class="form-group box_remove_file" data-repeater-item>
                <div class="col-md-4 text-light">
                    {!! Form::text('attachs_txt', null, ['class' => 'form-control', 'placeholder' => 'กรุณากรอกชื่อไฟล์']) !!}
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
                            <input type="file" name="attachs_sec7" class="attachs_sec7 check_max_size_file">
                        </span> 
                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                    </div>
                    {!! $errors->first('attachs_sec7', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="col-md-2">
                    <button class="btn btn-danger delete-sec7" type="button" data-repeater-delete>
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