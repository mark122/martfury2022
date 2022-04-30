@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')

@section('content')
    <div class="ps-section__header">
        <h3></h3>
        <div class="float-left">
            <h3>{{ SeoHelper::getTitle() }}</h3>
        </div>
{{--        <div class="float-right">--}}
{{--            <a class="add-address ps-btn ps-btn--sm ps-btn--small" href="{{ route('customer.address.create') }}">--}}
{{--                <span>{{ __('Add a new address') }}</span>--}}
{{--            </a>--}}
{{--        </div>--}}
    </div>
    <div class="ps-section__content">
        <div class="table-responsive">
            <table class="table ps-table--wishlist">
                <thead>
                <tr>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Role') }}</th>
                    <th>{{ __('Created At') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @if (count($customer) > 0)
                    @foreach($customer as $cust)
                        <tr class="dashboard-address-item">
                            <td style="white-space: inherit;">
                                <center><p>{{ $cust->name }}</p></center>
                            </td>
                            <td style="width: 120px;">
                                <center><p>{{ $cust->email }}</p> </center>
                            </td>
                            <td style="width: 120px;">
                                <p>@if($cust->ranking == 3) Site Admin @elseif($cust->ranking == 4) Site Representative @else - @endif</p>
                            </td>
                            <td style="width: 120px;">
                                <center> <p>{{ BaseHelper::formatDate($cust->created_at) }}</p> </center>
                            </td>
                            <td style="width: 120px;" >
                               <center> <p ><span class="badge @if($cust->status == 'activated')badge-info @else badge-danger @endif">{{ $cust->status }}</span></p></center>
                            </td>
                            <td style="width: 140px;">
                                <a href="{{ route('customer.change-status', $cust->id) }}" class='ps-btn  ps-btn--small @if($cust->status=="activated") btn-warning @else btn-success @endif action-btn btn-sm'>
                                    <i class="fa @if($cust->status =="activated") fa-ban @else fa-check @endif"></i>
                                </a>
                                <a class="ps-btn ps-btn--sm ps-btn--small" href="{{ route('customer.edit.user', $cust->id) }}">{{ __('Edit') }}</a>

                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="text-center">{{ __('No user!') }}</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
        <div class="mt-3 justify-content-center pagination_style1">
            {!! $customer->links() !!}
        </div>
    </div>

    <div class="modal fade" id="confirm-delete-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xs">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><strong>{{ __('Confirm delete') }}</strong></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Do you really want to delete this address?') }}</p>
                </div>
                <div class="modal-footer">
                    <button class="ps-btn ps-btn--sm ps-btn--gray" type="button" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button class="ps-btn ps-btn--sm avatar-save btn-confirm-delete" type="submit">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    </div><!-- /.modal -->
@endsection
