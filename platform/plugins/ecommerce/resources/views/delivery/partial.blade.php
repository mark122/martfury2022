<?php $status_day = isset($slot_data) ? $slot_data['status'] : ''; ?>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label class="control-label col-md-12" for="status">Set as day off</label>
            <div class="col-md-12 text-left">
                <input type="checkbox" name="status" id="status"
                       class="form-control"
                       {{$status_day == 1 ? 'checked' : ''}}
                       value="{{$status_day}}"
                       onclick="set_day_off(this)"
                       style="width: 15px !important; height: 20px !important; border: none !important;">
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-offset-3 col-md-9">
                <a href="javascript:void(0)" class="btn btn-info btn-sm" onclick="save_info(this, 'day')">Save Day Info</a>
            </div>
        </div>
    </div>

    <div class="col-md-8 dayoff_class {{$status_day  == 1 ? 'd-none' : ''}}">
        <div class="form-group">
            <h4>Slot Setting</h4>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-3">
                    <label class="control-label col-md-12">Start Time</label>
                    <div class="col-md-12">
                        <input type="text" name="slot_start_time" id="slot_start_time"
                               class="form-control timepicker"
                               value="08:00">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="control-label col-md-12">End Time</label>
                    <div class="col-md-12">
                        <input type="text" name="slot_end_time" id="slot_end_time"
                               class="form-control timepicker"
                               value="10:00">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="control-label col-md-12">No. of delivery per slot</label>
                    <div class="col-md-12">
                        <input type="number" name="delivery_per_slot"
                               class="form-control" id="delivery_per_slot">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="control-label col-md-12">&nbsp;</label>
                    <a href="javascript:void(0)" class="btn btn-info btn-sm" onclick="save_info(this, 'slot')">
                        Save Slot
                    </a>
                </div>
            </div>
            <span class="slot_error text-danger"></span>
            <div class="row">
                <div class="col-md-12" id="s_data"></div>
            </div>
        </div>
    </div>



</div>

