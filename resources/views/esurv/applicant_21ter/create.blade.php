@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">เพิ่ม คำขอการนำเข้าผลิตภัณฑ์เพื่อส่งออก (21 ตรี)</h3>
                    @can('view-'.str_slug('applicant-21ter'))
                        <a class="btn btn-success pull-right" href="{{url('/esurv/applicant_21ter')}}">
                            <i class="icon-arrow-left-circle"></i> กลับ
                        </a>
                    @endcan
                    <div class="clearfix"></div>
                    <hr>
                    @if ($errors->any())
                        <ul class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif

                    {!! Form::open(['url' => '/esurv/applicant_21ter', 'class' => 'form-horizontal', 'files' => true]) !!}

                    @include ('esurv.applicant_21ter.form')

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
@endsection
