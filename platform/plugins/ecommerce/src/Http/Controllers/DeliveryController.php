<?php

namespace Botble\Ecommerce\Http\Controllers;

use Assets;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Models\DeliveryTime;
use Botble\Ecommerce\Models\Timeslot;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use EcommerceHelper;
use Botble\Ecommerce\Tables\Reports\RecentOrdersTable;
use Botble\Ecommerce\Tables\Reports\TopSellingProductsTable;
use Botble\Payment\Enums\PaymentStatusEnum;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Ramsey\Uuid\Type\Time;
use Throwable;

class DeliveryController extends BaseController
{
    public function getIndex()
    {
        page_title()->setTitle(trans('Delivey Slot'));

        Assets::addScriptsDirectly([

            'vendor/core/core/base/js/bootstrap-timepicker.min.js',
            'vendor/core/plugins/ecommerce/libraries/apexcharts-bundle/dist/apexcharts.min.js',
            'vendor/core/plugins/ecommerce/libraries/moment.min.js',
           // 'vendor/core/plugins/ecommerce/libraries/delivery.js',
        ])
            ->addStylesDirectly([
                'vendor/core/core/base/css/bootstrap-timepicker.min.css',
            ]);

        $slot_data = Timeslot::where('week_day', 1)->first();

        return view('plugins/ecommerce::delivery.index', compact('slot_data'));
    }

    public function getTimeSlot(Request $request, BaseHttpResponse $response)
    {
        $slot_data = Timeslot::where('week_day', $request->week_day)->first();
        return view('plugins/ecommerce::delivery.partial', compact('slot_data'));
    }

    public function saveTimeSlot(Request $request, BaseHttpResponse $response)
    {
//        dd($request->all());
        if ($request->type == "day") {
            $ts = Timeslot::where('week_day', $request->week_day)->first();

//            if ($ts)
            $ts->week_day = $request->week_day;
            $ts->status = $request->status;
            $ts->save();
            if($request->status != 0) {
                DeliveryTime::where('week_day', $request->week_day)->delete();
            }
        }
        if ($request->type == "slot") {
            $start= 0;
            $ds = new DeliveryTime();
            if($request->slot_end_time < $request->slot_start_time){
                return $response->setError()
                    ->setMessage(trans('Please Select End Time should be greater than Start Time !'));
            }
            $dt = DeliveryTime::where('week_day',$request->week_day)->get();
            if (count($dt) >0){
            foreach ($dt as $d){
                $date = explode(' - ',$d->time);
                if ($d->time == $request->slot_start_time . " - " . $request->slot_end_time || $request->slot_start_time < $date[0] || $request->slot_start_time < $date[1]) {
                    $start++;
                }
            }
            }
            if (isset($dt)){
                if($start == 0){
                    $ds->week_day = $request->week_day;
                    $ds->timeslot_id = $request->timeslot_id;
                    $ds->time = $request->slot_start_time . " - " . $request->slot_end_time;
                    $ds->delivery_per_slot = $request->delivery_per_slot;
                    $ds->save();
                }else{
                    return $response->setError()
                        ->setMessage(trans('This Time slot already exists'));
                }
            }else{
                $ds->week_day = $request->week_day;
                $ds->timeslot_id = $request->timeslot_id;
                $ds->time = $request->slot_start_time . " - " . $request->slot_end_time;
                $ds->delivery_per_slot = $request->delivery_per_slot;
                $ds->save();
            }

        }
        return $response->setData([])->toApiResponse();
    }

    public function getSlotData(Request $request, BaseHttpResponse $response)
    {
        $s_data = DeliveryTime::where('week_day', $request->week_day)->get();
        return view('plugins/ecommerce::delivery.partial_tbl', compact('s_data'));
    }

    public function removeTimeSlot(Request $request, BaseHttpResponse $response)
    {
        return DeliveryTime::find($request->id)->delete();
    }



}
