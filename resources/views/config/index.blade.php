@extends('layouts.master')
@section('content')
    <section class="content container-fluid">
        <div class="content-header">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <H2>Settings</H2>
                </div>
                <div class="col-md-5 text-right">
                    <button class="btn btn-default" id="discard">Discard</button>
                    <button class="btn btn-primary" id="save">Save</button>
                </div>
            </div>
        </div>
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        <div class="row">
            <div class="col-md-4">
                <div class="title">
                    <h3 class="mt-0">General setting</h3>
                    <span>Enter a generic message to display if multiple offers are triggered</span>
                </div>
            </div>
            <div class="col-md-8">
                <form action="/setting/save" method="post" id="config_form">
                    @csrf
                    <div class="box box-primary form-trigger-event">
                        <div class="box-body general-setting">
                            <div class="form-group">
                                <div class="data-box">
                                    <label for="offer_title">Offer Title</label>
                                    <input type="text" name="offer_title" value="{{$title}}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="offer_title">Description</label>
                                <textarea name="offer_description" rows="4" cols="50" class="form-control">{{$description}}</textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
@section('page-script')
    <script type="text/javascript">
        $('#save').click(function () {
            $('#config_form').submit();
        });
    </script>
@stop
