<div class="table-wrapper">
    @if ($table->isHasFilter())
        <div class="table-configuration-wrap" @if (request()->has('filter_table_id')) style="display: block;" @endif>
            <span class="configuration-close-btn btn-show-table-options"><i class="fa fa-times"></i></span>
            {!! $table->renderFilter() !!}
        </div>
    @endif
    <div class="portlet light bordered portlet-no-padding">
{{--        <div class="portlet-title">--}}
{{--            <div class="caption">--}}
{{--                <div class="wrapper-action">--}}
{{--                    @if ($actions)--}}
{{--                        <div class="btn-group">--}}
{{--                            <a class="btn btn-secondary dropdown-toggle" href="#" data-bs-toggle="dropdown">{{ trans('core/table::table.bulk_actions') }}--}}
{{--                            </a>--}}
{{--                            <ul class="dropdown-menu">--}}
{{--                                @foreach ($actions as $action)--}}
{{--                                    <li>--}}
{{--                                        {!! $action !!}--}}
{{--                                    </li>--}}
{{--                                @endforeach--}}
{{--                            </ul>--}}
{{--                        </div>--}}
{{--                    @endif--}}
{{--                    @if ($table->isHasFilter())--}}
{{--                        <button class="btn btn-primary btn-show-table-options">{{ trans('core/table::table.filters') }}</button>--}}
{{--                    @endif--}}
{{--                </div>--}}

{{--            </div>--}}
{{--        </div>--}}

        <div class="portlet-title">
            <div class="caption">
                <div class="wrapper-action" style="display: flex;justify-content: center;align-items: center;">

                    @if ($actions)
                        <div class="btn-group">
                            <a class="btn btn-secondary dropdown-toggle" href="#" data-bs-toggle="dropdown">{{ trans('core/table::table.bulk_actions') }}
                            </a>
                            <ul class="dropdown-menu">
                                @foreach ($actions as $action)
                                    <li>
                                        {!! $action !!}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if ($table->isHasFilter())
                        <button class="btn btn-primary btn-show-table-options">{{ trans('core/table::table.filters') }}</button>
                    @endif
{{--                        <div class="row">--}}
                            @php
                               $code =  \Botble\Ecommerce\Models\CorporateCode::pluck('list_of_corporate_code','list_of_corporate_code')->prepend('Please Select','')->toArray();
                            @endphp
{{--                </div>--}}
                        @if(Route::currentRouteName() == 'site.report')
                            <form action="{{route('site.report')}}">
                                <div class="row" style="margin-left: 270px;">
                                    <div class="col-md-7">
                                        {{ Form::select('code',$code, old('code'), ['class' => 'form-control']) }}
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-primary ">Filter</button>
                                    </div>
                                </div>
                            </form>
                        @endif
                </div>

            </div>

        </div>

        <div class="portlet-body">
            <div class="table-responsive @if ($actions) table-has-actions @endif @if ($table->isHasFilter()) table-has-filter @endif">
                @section('main-table')
                    {!! $dataTable->table(compact('id', 'class'), false) !!}
                @show
            </div>
        </div>
    </div>
</div>

@include('core/table::modal')


@push('footer')
    {!! $dataTable->scripts() !!}
    <script>
        function test(el) {
            $("#myModal").modal('show');
            var dis = el.data('id')
            $('#discount').val(dis.subcontractor_dis);

        }
    </script>
@endpush

