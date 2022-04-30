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
                                    <b>Corporate Code</b>

                                </div>
                                <div class="dt-buttons btn-group flex-wrap">   <a class="btn btn-secondary action-item" href="{{route('corporate.code.create')}}" tabindex="0" aria-controls="botble-page-tables-page-table" ><span><i class="fa fa-plus"></i> Create</span></a></div>
{{--                                <div class="tools">--}}
{{--                                    <a href="javascript:;" class="collapse"> </a>--}}
{{--                                    <a href="javascript:;" class="reload"> </a>--}}
{{--                                    <a href="javascript:;" class="remove"> </a>--}}
{{--                                </div>--}}

                            </div>
                            <table class="table" style="margin-top: 30px !important;">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">List Of Corporate Code</th>
                                    <th scope="col">Max Allow to be Corporate User</th>
                                    <th scope="col">Max Allow to be A/R</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($corporate_code  as $cc)
                                    <tr>
                                        <td>{{$cc->id}}</td>
                                        <td style="font-size: 15px; text-indent: 15px">{{$cc->list_of_corporate_code}}</td>
                                        <td style="font-size: 15px; text-indent: 15px"><a href="{{route('corporate')}}?corporate-user={{$cc->list_of_corporate_code}}">{{$cc->member_under}}</td>
                                        <td style="font-size: 15px; text-indent: 15px"><a href="{{route('corporate')}}?a-r={{$cc->list_of_corporate_code}}">{{$cc->max_allow_to_be_user}}</a></td>
                                        <td style="text-align: right">
                                            <a href="{{route('corporate')}}?code={{$cc->list_of_corporate_code}}" class="btn btn-warning float-right"
                                               >Show users </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
