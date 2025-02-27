@extends('layouts.master')
@push('css')

    <style>
        .table>tbody>tr>td ,label{
            line-height: 1.7;
            color: #5f5f5f;
        }
         .input_text_color {
            background-color:#ccffcc;
            /* color: white; */
         }

        .input_text_color[readonly]{
            background-color: #ccffcc;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">คำขอรับบริการห้องปฏิบัติการ (LAB)</h3>

                    <a class="btn btn-danger text-white pull-right" href="{{url('certify/applicant')}}">
                        <i class="icon-arrow-left-circle"></i> กลับ
                    </a>
                    <div class="clearfix"></div>
                    <hr>

                    {!! Form::open(['url' => 'certify/applicant/store', 'class' => 'form-horizontal','id'=>'app_certi_form', 'files' => true]) !!}
{{--                    @can('view-'.str_slug('board'))--}}
        
{{--                    @endcan--}}
          
                   
                        @if ($errors->any())
                            <ul class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    {{-- @include ('certify.applicant.form.form84')
                    @include ('certify.applicant.form.form85')
                    <hr>
                    @include ('certify.applicant.form.form86')
                    @include ('certify.applicant.form.form87')
                    @include ('certify.applicant.form.form88')
                    @include ('certify.applicant.form.form89')
                    @include ('certify.applicant.form.form90')
                    @include ('certify.applicant.form.form91')
                    @include ('certify.applicant.form.form92')
                    @include ('certify.applicant.form.form93')
                    @include ('certify.applicant.form.form94')
                    @include ('certify.applicant.form.form95')
                    @include ('certify.applicant.form.form96') --}}


                        @include ('certify.applicant.form')

                        <center>
                            <div class="col-md-12 text-center">
                                <div id="status_btn"></div>
                                <button type="button"class="btn btn-default m-l-5" value="ส่งข้อมูล"  name="save" onclick="submit_form('1');return false" disabled>ส่งข้อมูล</button>
                                <button type="button" class="btn btn-warning text-white m-l-5" id="draft" name="draft" value="ฉบับร่าง" onclick="submit_form_draft('0');return false">ฉบับร่าง</button>
                                <a href="{{url('certify/applicant')}}" class="btn btn-danger text-white m-l-5" id="cancel_edit_calibrate">ยกเลิก</a>
                            </div>
                        </center>
                        
                    <div class="clearfix"></div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{asset('plugins/components/toast-master/js/jquery.toast.js')}}"></script>
    <script src="{{ asset('js/jasny-bootstrap.js') }}"></script>
    <script>
        $(document).ready(function () {
            @if(\Session::has('flash_message'))
            $.toast({
                heading: 'Success!',
                position: 'top-center',
                text: '{{session()->get('flash_message')}}',
                loaderBg: '#ff6849',
                icon: 'success',
                hideAfter: 3000,
                stack: 6
            });
            @endif

        });


    </script>

@stop
