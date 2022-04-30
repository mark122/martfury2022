<?php

namespace Botble\Ecommerce\Http\Controllers\Customers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Http\Requests\AddressRequest;
use Botble\Ecommerce\Http\Requests\AvatarRequest;
use Botble\Ecommerce\Http\Requests\CustomerCreateRequest;
use Botble\Ecommerce\Http\Requests\CustomerEditRequest;
use Botble\Ecommerce\Http\Requests\EditAccountRequest;
use Botble\Ecommerce\Http\Requests\UpdatePasswordRequest;
use Botble\Ecommerce\Models\CorporateCode;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Repositories\Interfaces\AddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Media\Services\ThumbnailService;
use Botble\Payment\Models\Payment;
use EcommerceHelper;
use Exception;
use File;
use Hash;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use OrderHelper;
use Response;
use RvMedia;
use SeoHelper;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Theme;
use Throwable;

class PublicController extends Controller
{
    /**
     * @var CustomerInterface
     */
    protected $customerRepository;

    /**
     * @var ProductInterface
     */
    protected $productRepository;

    /**
     * @var AddressInterface
     */
    protected $addressRepository;



    /**
     * @var OrderInterface
     */
    protected $orderRepository;

    /**
     * @var OrderHistoryInterface
     */
    protected $orderHistoryRepository;

    /**
     * PublicController constructor.
     * @param CustomerInterface $customerRepository
     * @param ProductInterface $productRepository
     * @param AddressInterface $addressRepository
     * @param OrderInterface $orderRepository
     * @param OrderHistoryInterface $orderHistoryRepository
     */
    public function __construct(
        CustomerInterface $customerRepository,
        ProductInterface $productRepository,
        AddressInterface $addressRepository,
        OrderInterface $orderRepository,
        OrderHistoryInterface $orderHistoryRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
        $this->addressRepository = $addressRepository;
        $this->orderRepository = $orderRepository;
        $this->orderHistoryRepository = $orderHistoryRepository;

        Theme::asset()
            ->add('customer-style', 'vendor/core/plugins/ecommerce/css/customer.css');

        Theme::asset()
            ->container('footer')
            ->add('ecommerce-utilities-js', 'vendor/core/plugins/ecommerce/js/utilities.js', ['jquery'])
            ->add('cropper-js', 'vendor/core/plugins/ecommerce/libraries/cropper.js', ['jquery'])
            ->add('avatar-js', 'vendor/core/plugins/ecommerce/js/avatar.js', ['jquery']);

        if (EcommerceHelper::loadCountriesStatesCitiesFromPluginLocation()) {
            Theme::asset()
                ->container('footer')
                ->add('location-js', 'vendor/core/plugins/location/js/location.js', ['jquery']);
        }
    }

    /**
     * @return Response
     */
    public function getOverview()
    {
        SeoHelper::setTitle(__('Account information'));

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Account information'), route('customer.overview'));

        return Theme::scope('ecommerce.customers.overview', [], 'plugins/ecommerce::themes.customers.overview')
            ->render();
    }

    /**
     * @return Response
     */
    public function getEditAccount()
    {
        SeoHelper::setTitle(__('Profile'));

        Theme::asset()
            ->add('datepicker-style',
                'vendor/core/core/base/libraries/bootstrap-datepicker/css/bootstrap-datepicker3.min.css',
                ['bootstrap']);
        Theme::asset()
            ->container('footer')
            ->add('datepicker-js',
                'vendor/core/core/base/libraries/bootstrap-datepicker/js/bootstrap-datepicker.min.js',
                ['jquery']);

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Profile'), route('customer.edit-account'));

        return Theme::scope('ecommerce.customers.edit-account', [], 'plugins/ecommerce::themes.customers.edit-account')
            ->render();
    }

    /**
     * @param EditAccountRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postEditAccount(EditAccountRequest $request, BaseHttpResponse $response)
    {
        $customer = $this->customerRepository->createOrUpdate(
            $request->input(),
            [
                'id' => auth('customer')->id(),
            ]);

        do_action(HANDLE_CUSTOMER_UPDATED_ECOMMERCE, $customer, $request);

        return $response
            ->setNextUrl(route('customer.edit-account'))
            ->setMessage(__('Update profile successfully!'));
    }

    /**
     * @return Response
     */
    public function getChangePassword()
    {
        SeoHelper::setTitle(__('Change Password'));

        Theme::breadcrumb()->add(__('Home'), route('public.index'))
            ->add(__('Change Password'), route('customer.change-password'));

        return Theme::scope('ecommerce.customers.change-password', [],
            'plugins/ecommerce::themes.customers.change-password')->render();
    }

    public function addUser()
    {
        SeoHelper::setTitle(__('Add User'));

//        Theme::breadcrumb()->add(__('Home'), route('public.index'))
//            ->add(__('Change Password'), route('customer.change-password'));

        return Theme::scope('ecommerce.customers.add-user', [],
            'plugins/ecommerce::themes.customers.add-user')->render();
    }

    public function saveUser(CustomerCreateRequest $request, BaseHttpResponse $response)
    {

       $input = $request->all();
       $code = auth('customer')->user()->corporate_code;
       $CorporateCode = CorporateCode::where('list_of_corporate_code',$code)->first();
       $underMember =  Customer::where('corporate_id',auth('customer')->id())->count();
       if ($underMember < $CorporateCode->max_allow_to_be_user){
       $input['corporate_id'] = auth('customer')->id();
       $input['password']= bcrypt($request->input('password'));
       $input['corporate_code']= $code;
       $customer = Customer::create($input);
       return redirect()->route('customer.show.user');
       }else{
           return $response->setError()->setMessage(trans('Adding Site A / R limit exceeded for this corporate code'));
       }
       // return $response->setMessage(trans('Add User SuccessFully'));
    }

    public function showUser(Request $request)
    {
        $customer = $this->customerRepository->advancedGet([
            'condition' => [
                'corporate_id' => auth('customer')->id(),
            ],
//            'order_by'  => [
//                'is_default' => 'DESC',
//            ],
            'paginate'  => [
                'per_page'      => 10,
                'current_paged' => (int)$request->input('page'),
            ],
        ]);
        SeoHelper::setTitle(__('List User'));

//        Theme::breadcrumb()->add(__('Home'), route('public.index'))
//            ->add(__('Change Password'), route('customer.change-password'));

        return Theme::scope('ecommerce.customers.list-user',compact('customer'),
            'plugins/ecommerce::themes.customers.list-user')->render();
    }

    public function editUser($id)
    {
        SeoHelper::setTitle(__('Edit User'));

//        Theme::breadcrumb()->add(__('Home'), route('public.index'))
//            ->add(__('Change Password'), route('customer.change-password'));
        $customer = Customer::findOrFail($id);
        return Theme::scope('ecommerce.customers.edit-user', compact('customer'),
            'plugins/ecommerce::themes.customers.edit-user')->render();
    }

    public function updateUser($id, CustomerEditRequest $request, BaseHttpResponse $response)
    {
        $customer = Customer::findOrFail($id);
        $input = $request->all();
        if (!empty($request->input('password'))){
            $input['password']= bcrypt($request->input('password'));
        }else{
            $input['password']=$customer->password;
        }
        $customer->update($input);
        return redirect()->route('customer.show.user');
    }

    public function changeStatus($id,BaseHttpResponse $response)
    {
      $customer=  Customer::findOrFail($id);
        if ($customer->status == "activated"){
            $up_status="locked";
        }
        else{
            $up_status="activated";
        }
        $customer->update(['status'=>$up_status]);
        return $response->setMessage(trans('Update status SuccessFully'));
    }
//    protected function validator(array $data)
//    {
//        $rules = [
//            'name' => 'required|max:255',
//            'email' => 'required|email|max:255|unique:ec_customers',
//            'password' => 'required|min:6|confirmed',
//        ];
//    }
    /**
     * @param UpdatePasswordRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postChangePassword(UpdatePasswordRequest $request, BaseHttpResponse $response)
    {
        $currentUser = auth('customer')->user();

        if (!Hash::check($request->input('old_password'), $currentUser->getAuthPassword())) {
            return $response
                ->setError()
                ->setMessage(trans('acl::users.current_password_not_valid'));
        }

        $this->customerRepository->update(['id' => $currentUser->getAuthIdentifier()], [
            'password' => bcrypt($request->input('password')),
        ]);

        return $response->setMessage(trans('acl::users.password_update_success'));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getListOrders(Request $request)
    {
        SeoHelper::setTitle(__('Orders'));
//        dd(auth('customer')->user()->ranking);
        if ((auth('customer')->user()->ranking) == 3){

            $corporate_id = auth('customer')->user()->corporate_id;
            $allcus = Customer::where('corporate_id',$corporate_id)->pluck('id')->toArray();
            $orders =  Order::whereIn('user_id',$allcus)->paginate(10);
        }elseif ((auth('customer')->user()->ranking) == 2){

            $corporate_id = auth('customer')->id();
            $allcus = Customer::where('corporate_id',$corporate_id)->pluck('id')->toArray();
            $orders =  Order::whereIn('user_id',$allcus)->paginate(10);
        }
        else{
            $orders = $this->orderRepository->advancedGet([
            'condition' => [
                'user_id'     => auth('customer')->id(),
                'is_finished' => 1,
            ],
            'paginate'  => [
                'per_page'      => 10,
                'current_paged' => (int)$request->input('page'),
            ],
            'withCount' => ['products'],
        ]);
        }
        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Orders'), route('customer.orders'));

        return Theme::scope('ecommerce.customers.orders.list', compact('orders'),
            'plugins/ecommerce::themes.customers.orders.list')->render();
    }

    public function orderApprove($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status'=>'shipping']);
        Payment::where('id',$order->payment_id)->update(['payment_channel'=>"Pay Later"]);
        return redirect()->back();
      }

    public function orderCancel($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status'=>'cancel']);
        return redirect()->back();
      }
    /**
     * @param int $id
     * @return Response
     */
    public function getViewOrder($id)
    {
        SeoHelper::setTitle(__('Order detail :id', ['id' => get_order_code($id)]));

        $order = $this->orderRepository->getFirstBy(
            [
                'id'      => $id,
                //'user_id' => auth('customer')->id(),
            ],
            ['ec_orders.*'],
            ['address', 'products']
        );

        if (!$order) {
            abort(404);
        }

        Theme::breadcrumb()->add(__('Home'), route('public.index'))
            ->add(__('Order detail :id', ['id' => get_order_code($id)]),
                route('customer.orders.view', $id));

        return Theme::scope('ecommerce.customers.orders.view', compact('order'),
            'plugins/ecommerce::themes.customers.orders.view')->render();
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws FileNotFoundException
     * @throws Throwable
     */
    public function getCancelOder($id, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->getFirstBy([
            'id'      => $id,
            'user_id' => auth('customer')->id(),
        ], ['*']);

        if (!$order) {
            abort(404);
        }

        if (!$order->canBeCanceled()) {
            return $response->setError()
                ->setMessage(trans('plugins/ecommerce::order.cancel_error'));
        }

        OrderHelper::cancelOrder($order);

        $this->orderHistoryRepository->createOrUpdate([
            'action'      => 'cancel_order',
            'description' => __('Order is cancelled by custom :customer', ['customer' => $order->address->name]),
            'order_id'    => $order->id,
        ]);

        return $response->setMessage(trans('plugins/ecommerce::order.cancel_success'));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getListAddresses(Request $request)
    {
        SeoHelper::setTitle(__('Address books'));

        $addresses = $this->addressRepository->advancedGet([
            'condition' => [
                'customer_id' => auth('customer')->id(),
            ],
            'order_by'  => [
                'is_default' => 'DESC',
            ],
            'paginate'  => [
                'per_page'      => 10,
                'current_paged' => (int)$request->input('page'),
            ],
        ]);

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Address books'), route('customer.address'));

        return Theme::scope('ecommerce.customers.address.list', compact('addresses'),
            'plugins/ecommerce::themes.customers.address.list')->render();
    }

    /**
     * @return Response
     */
    public function getCreateAddress()
    {
        SeoHelper::setTitle(__('Create Address'));

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Address books'), route('customer.address'))
            ->add(__('Create Address'), route('customer.address.create'));

        return Theme::scope('ecommerce.customers.address.create', [],
            'plugins/ecommerce::themes.customers.address.create')->render();
    }

    /**
     * @param AddressRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function postCreateAddress(AddressRequest $request, BaseHttpResponse $response)
    {
        if ($request->input('is_default') == 1) {
            $this->addressRepository->update([
                'is_default'  => 1,
                'customer_id' => auth('customer')->id(),
            ], ['is_default' => 0]);
        }

        $request->merge([
            'customer_id' => auth('customer')->id(),
            'is_default'  => $request->input('is_default', 0),
        ]);

        $address = $this->addressRepository->createOrUpdate($request->input());

        return $response
            ->setData([
                'id'   => $address->id,
                'html' => view('plugins/ecommerce::orders.partials.address-item',
                    compact('address'))->render(),
            ])
            ->setNextUrl(route('customer.address'))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @return Response
     */
    public function getEditAddress($id)
    {
        SeoHelper::setTitle(__('Edit Address #:id', ['id' => $id]));

        $address = $this->addressRepository->getFirstBy([
            'id'          => $id,
            'customer_id' => auth('customer')->id(),
        ]);

        if (!$address) {
            abort(404);
        }

        Theme::breadcrumb()->add(__('Home'), route('public.index'))
            ->add(__('Edit Address #:id', ['id' => $id]), route('customer.address.edit', $id));

        return Theme::scope('ecommerce.customers.address.edit', compact('address'),
            'plugins/ecommerce::themes.customers.address.edit')->render();
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function getDeleteAddress($id, BaseHttpResponse $response)
    {
        $this->addressRepository->deleteBy([
            'id'          => $id,
            'customer_id' => auth('customer')->id(),
        ]);
        return $response->setNextUrl(route('customer.address'))
            ->setMessage(trans('core/base::notices.delete_success_message'));
    }

    /**
     * @param int $id
     * @param AddressRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function postEditAddress($id, AddressRequest $request, BaseHttpResponse $response)
    {
        if ($request->input('is_default')) {
            $this->addressRepository->update([
                'is_default'  => 1,
                'customer_id' => auth('customer')->id(),
            ], ['is_default' => 0]);
        }

        $address = $this->addressRepository->createOrUpdate($request->input(), [
            'id'          => $id,
            'customer_id' => auth('customer')->id(),
        ]);

        return $response
            ->setData([
                'id'   => $address->id,
                'html' => view('plugins/ecommerce::orders.partials.address-item', compact('address'))
                    ->render(),
            ])
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getPrintOrder($id, Request $request)
    {
        $order = $this->orderRepository->getFirstBy([
            'id'      => $id,
            'user_id' => auth('customer')->id(),
        ]);

        if (!$order) {
            abort(404);
        }

        if ($request->input('type') == 'print') {
            return OrderHelper::streamInvoice($order);
        }

        return OrderHelper::downloadInvoice($order);
    }

    /**
     * @param AvatarRequest $request
     * @param ThumbnailService $thumbnailService
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postAvatar(AvatarRequest $request, ThumbnailService $thumbnailService, BaseHttpResponse $response)
    {
        try {
            $account = auth('customer')->user();

            $result = RvMedia::handleUpload($request->file('avatar_file'), 0, 'customers');

            if ($result['error'] != false) {
                return $response->setError()->setMessage($result['message']);
            }

            $avatarData = json_decode($request->input('avatar_data'));

            $file = $result['data'];

            $thumbnailService
                ->setImage(RvMedia::getRealPath($file->url))
                ->setSize((int)$avatarData->width, (int)$avatarData->height)
                ->setCoordinates((int)$avatarData->x, (int)$avatarData->y)
                ->setDestinationPath(File::dirname($file->url))
                ->setFileName(File::name($file->url) . '.' . File::extension($file->url))
                ->save('crop');

            $account->avatar = $file->url;

            $this->customerRepository->createOrUpdate($account);

            return $response
                ->setMessage(trans('plugins/customer::dashboard.update_avatar_success'))
                ->setData(['url' => RvMedia::url($file->url)]);
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
