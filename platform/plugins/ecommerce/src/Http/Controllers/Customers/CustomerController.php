<?php

namespace Botble\Ecommerce\Http\Controllers\Customers;

use Assets;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Forms\CustomerForm;
use Botble\Ecommerce\Http\Requests\AddCustomerWhenCreateOrderRequest;
use Botble\Ecommerce\Http\Requests\CustomerCreateRequest;
use Botble\Ecommerce\Http\Requests\CustomerEditRequest;
use Botble\Ecommerce\Http\Requests\CustomerUpdateEmailRequest;
use Botble\Ecommerce\Http\Resources\CustomerAddressResource;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Repositories\Interfaces\AddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Ecommerce\Tables\CustomerTable;
use Botble\Ecommerce\Tables\SiteReport;
use Botble\Ecommerce\Tables\SubContractorTable;
use Botble\Ecommerce\Tables\CorporateTable;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class CustomerController extends BaseController
{

    /**
     * @var CustomerInterface
     */
    protected $customerRepository;

    /**
     * @var AddressInterface
     */
    protected $addressRepository;

    /**
     * @param CustomerInterface $customerRepository
     * @param AddressInterface $addressRepository
     */
    public function __construct(CustomerInterface $customerRepository, AddressInterface $addressRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
    }

    /**
     * @param CustomerTable $dataTable
     * @return Factory|View
     * @throws Throwable
     */
    public function index(CustomerTable $dataTable)
    {
        page_title()->setTitle(trans('plugins/ecommerce::customer.name'));

        return $dataTable->renderTable();
    }
    public function subContractor(SubContractorTable $dataTable)
    {

        page_title()->setTitle(trans('plugins/ecommerce::customer.subcontractor'));
        return $dataTable->renderTable();
    }
    public function corporate(CorporateTable $dataTable)
    {

        page_title()->setTitle(trans('plugins/ecommerce::customer.corporate'));
        return $dataTable->renderTable();
    }

    public function SiteReport(SiteReport $dataTable)
    {
        page_title()->setTitle(trans('Site A/R Report'));
        return $dataTable->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/ecommerce::customer.create'));

        Assets::addScriptsDirectly('vendor/core/plugins/ecommerce/js/customer.js');

        return $formBuilder->create(CustomerForm::class)->remove('is_change_password')->renderForm();
    }

    /**
     * @param CustomerCreateRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(CustomerCreateRequest $request, BaseHttpResponse $response)
    {

        $customer = $this->customerRepository->getModel();
        $customer->fill($request->input());
        $customer->ranking = $request->input('subs_contractor');
        if($request->input('subs_contractor')==2){
            $customer->company = $request->input('company');
            $customer->biz_number = $request->input('biz_number');
            $customer->company_doc = $request->input('company_doc');
            $customer->representive_allowed = $request->input('representive_allowed');
            $customer->site_approval_allowed = $request->input('site_approval_allowed');

            if($request->file('company_doc')) {
                $request->file('company_doc');
                $file = $request->file('company_doc');
                $destinationPath = 'storage/company_doc';
                $filename = time().$file->getClientOriginalName();
                $filename = str_replace(' ', '-', $filename);
                $filename = preg_replace('/[^A-Za-z0-9\-]/', '', $filename).'.'.$file->getClientOriginalExtension();
                $file->move($destinationPath,$filename);
                $customer->company_doc = $filename;
            }
        }
        $customer->confirmed_at = now();
        $customer->password = bcrypt($request->input('password'));
        $customer->dob = Carbon::parse($request->input('dob'))->toDateString();
        $customer = $this->customerRepository->createOrUpdate($customer);

        event(new CreatedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $customer));

        return $response
            ->setPreviousUrl(route('customers.index'))
            ->setNextUrl(route('customers.edit', $customer->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder)
    {
        Assets::addScriptsDirectly('vendor/core/plugins/ecommerce/js/customer.js');

        $customer = $this->customerRepository->findOrFail($id);

        page_title()->setTitle(trans('plugins/ecommerce::customer.edit', ['name' => $customer->name]));

        $customer->password = null;

        return $formBuilder->create(CustomerForm::class, ['model' => $customer])->renderForm();
    }

    /**
     * @param int $id
     * @param CustomerEditRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, CustomerEditRequest $request, BaseHttpResponse $response)
    {
//        dd($request->input());
        $customer = $this->customerRepository->findOrFail($id);

        $customer->fill($request->except('password'));

        if ($request->input('is_change_password') == 1) {
            $customer->password = bcrypt($request->input('password'));
        }

        $customer->dob = Carbon::parse($request->input('dob'))->toDateString();
        $customer->ranking = $request->input('subs_contractor');
        if($request->input('subs_contractor')==2){
            $customer->company = $request->input('company');
            $customer->biz_number = $request->input('biz_number');
            $customer->company_doc = $request->input('company_doc');
            $customer->representive_allowed = $request->input('representive_allowed');
            $customer->site_approval_allowed = $request->input('site_approval_allowed');

            $customer->company_doc = $request->input('company_doc_url');
            if($request->file('company_doc')) {
                $request->file('company_doc');
                $file = $request->file('company_doc');
                $destinationPath = 'storage/company_doc';
                $filename = time().$file->getClientOriginalName();
                $filename = str_replace(' ', '-', $filename);
                $filename = preg_replace('/[^A-Za-z0-9\-]/', '', $filename).'.'.$file->getClientOriginalExtension();
                $file->move($destinationPath,$filename);
                $customer->company_doc = $filename;
            }
        }

        $customer = $this->customerRepository->createOrUpdate($customer);

        event(new UpdatedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $customer));

        return $response
            ->setPreviousUrl(route('customers.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param Request $request
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $customer = $this->customerRepository->findOrFail($id);
            $this->customerRepository->delete($customer);
            event(new DeletedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $customer));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $customer = $this->customerRepository->findOrFail($id);
            $this->customerRepository->delete($customer);
            event(new DeletedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $customer));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    /**
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function getListCustomerForSelect(BaseHttpResponse $response)
    {
        $customers = $this->customerRepository
            ->allBy([], [], ['id', 'name'])
            ->toArray();

        return $response->setData($customers);
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function getListCustomerForSearch(Request $request, BaseHttpResponse $response)
    {
        $customers = $this->customerRepository
            ->getModel()
            ->where('name', 'LIKE', '%' . $request->input('keyword') . '%')
            ->simplePaginate(5);

        foreach ($customers as &$customer) {
            $customer->avatar_url = (string)$customer->avatar_url;
        }

        return $response->setData($customers);
    }

    /**
     * @param int $id
     * @param CustomerUpdateEmailRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postUpdateEmail($id, CustomerUpdateEmailRequest $request, BaseHttpResponse $response)
    {
        $this->customerRepository->createOrUpdate(['email' => $request->input('email')], ['id' => $id]);

        return $response->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function getCustomerAddresses($id, BaseHttpResponse $response)
    {
        $addresses = $this->addressRepository->allBy(['customer_id' => $id]);

        return $response->setData(CustomerAddressResource::collection($addresses));
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function getCustomerOrderNumbers($id, BaseHttpResponse $response)
    {
        $customer = $this->customerRepository->findById($id);
        if (!$customer) {
            return $response->setData(0);
        }

        return $response->setData($customer->orders()->count());
    }

    /**
     * @param AddCustomerWhenCreateOrderRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postCreateCustomerWhenCreatingOrder(
        AddCustomerWhenCreateOrderRequest $request,
        BaseHttpResponse $response
    ) {
        $request->merge(['password' => bcrypt(time())]);
        $customer = $this->customerRepository->createOrUpdate($request->input());
        $customer->avatar = (string)$customer->avatar_url;

        event(new CreatedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $customer));

        $request->merge([
            'customer_id' => $customer->id,
            'is_default'  => true,
        ]);

        $address = $this->addressRepository->createOrUpdate($request->input());

        $address->country = $address->country_name;
        $address->state = $address->state_name;
        $address->city = $address->city_name;

        $address->country_name = $address->country;
        $address->state_name = $address->state;
        $address->city_name = $address->city;

        return $response
            ->setData(compact('address', 'customer'))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function saveDiscount()
    {

//      $cus  = ::findOrFail(\request()->sub_id);
        Customer::where('ranking',1)->update(['subcontractor_dis'=>\request()->subcontractor_dis]);
        return redirect()->back();
    }
}
