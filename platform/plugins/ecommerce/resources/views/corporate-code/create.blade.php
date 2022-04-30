@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <style>
        .form-group {
            margin-top: 20px
        }
    </style>
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet-body">
                    <div class="col-md-12">
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <b>Create Corporate Code</b>

                                </div>
                                <div class="tools">
                                    <a href="javascript:;" class="collapse"> </a>
                                    <a href="javascript:;" class="reload"> </a>
                                    <a href="javascript:;" class="remove"> </a>
                                </div>
                            </div>
                            <form action="{{route('corporate.code.store')}}" method="Post">
                                @csrf
                                <div class="main-form">
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                    <div class="form-group ">

                                        <label for="name" class="control-label required" aria-required="true">List Of Corporate Code</label>
                                        <input class="form-control" placeholder="List Of Corporate Code" data-counter="120" name="list_of_corporate_code" type="text" id="name">
                                    </div>
                                            @if ($errors->has('list_of_corporate_code'))
                                                <span class="text-danger">{{ $errors->first('list_of_corporate_code') }}</span>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group ">
                                                <label for="name" class="control-label required" aria-required="true">Max site rep and approval  admin allowed under this corporate code tree</label>
                                                <input class="form-control" placeholder="Max site rep and approval  admin allowed under this corporate code tree" data-counter="120" name="member_under" type="text" id="name">
                                            </div>
                                            @if ($errors->has('member_under'))
                                                <span class="text-danger">{{ $errors->first('member_under') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            <label for="name" class="control-label required" aria-required="true">Max Acct Allowed Under This Code:</label>
                                            <input class="form-control" placeholder="Max Acct Allowed Under This Code" data-counter="120" name="max_allow_to_be_user" type="text" id="name">
                                        </div>
                                        @if ($errors->has('max_allow_to_be_user'))
                                            <span class="text-danger">{{ $errors->first('max_allow_to_be_user') }}</span>
                                        @endif
                                    </div>
                                    </div>

                                    <div class="clearfix"></div>
                                </div>
                            </div>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
