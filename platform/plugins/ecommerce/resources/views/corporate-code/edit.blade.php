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
                                    <b>Update Corporate Code</b>

                                </div>
                                <div class="tools">
                                    <a href="javascript:;" class="collapse"> </a>
                                    <a href="javascript:;" class="reload"> </a>
                                    <a href="javascript:;" class="remove"> </a>
                                </div>
                            </div>
                            <form action="{{route('corporate.code.update',$corporate_code->id)}}" method="Post">
                                @csrf
                                <div class="main-form">
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-6">

                                    <div class="form-group ">

                                        <label for="name" class="control-label required" aria-required="true">List Of Corporate Code</label>
                                        <input class="form-control" placeholder="List Of Corporate Code" data-counter="120" name="list_of_corporate_code" type="text" id="name" value="{{$corporate_code->list_of_corporate_code}}">
                                    </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group ">
                                                <label for="name" class="control-label required" aria-required="true">Member Under</label>
                                                <input class="form-control" placeholder="Member Under" data-counter="120" name="member_under" type="text" id="name" value="{{$corporate_code->member_under}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            <label for="name" class="control-label required" aria-required="true">Max Allow To be User</label>
                                            <input class="form-control" placeholder="Max Allow To be User" data-counter="120" name="max_allow_to_be_user" type="text" id="name" value="{{$corporate_code->max_allow_to_be_user}}">
                                        </div>
                                    </div>
                                        <div class="col-md-6">
                                            <div class="form-group ">
                                            {!! Form::label('assign_corporate[]', 'Assign Corporate:') !!}
                                            {!! Form::select('assign_corporate[]',$corporator, json_decode($corporate_code->assign_corporate), ['class' => 'form-control multi-select','multiple' => 'multiple','value'=>"{{old('assign_corporate')}}"]) !!}
                                            <span class="text-red">{{ $errors->first('assign_corporate') }}</span>
                                        </div>
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
@push('footer')
<script>
    $(document).ready(function() {
        $('.multi-select').select2();
    });
</script>
    @endpush
