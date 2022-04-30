<?php

namespace Botble\Ecommerce\Http\Controllers\Customers;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Models\CorporateCode;
use Botble\Ecommerce\Models\Customer;
use Illuminate\Http\Request;

class CorporateCodeController extends BaseController
{
    public function index()
    {
        $corporate_code =   CorporateCode::all();
        return view('plugins/ecommerce::corporate-code.index',compact('corporate_code'));
    }

    public function create()
    {
        return view('plugins/ecommerce::corporate-code.create');
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'list_of_corporate_code'=>'required|unique:ec_corporate_code,list_of_corporate_code',
            'member_under'=>'required',
            'max_allow_to_be_user'=>'required'
        ]);
        $input = $request->all();
        $corporate_code = CorporateCode::create($input);
        return redirect()->route('corporate.code');
    }

    public function edit($id)
    {
        $corporate_code = CorporateCode::findOrFail($id);
        $corporator = Customer::Where('ranking',2)->pluck('name','id')->toArray();
        return view('plugins/ecommerce::corporate-code.edit',compact('corporate_code','corporator'));
    }

    public function update(Request $request,$id)
    {
        $corporate_code = CorporateCode::findOrFail($id);
        $input = $request->request->all();
        $corporate_code->update($input);
        return redirect()->route('corporate.code');
    }
}
