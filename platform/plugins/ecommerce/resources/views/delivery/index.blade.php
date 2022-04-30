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
                                    <i class="fa fa-clock"></i> Set Time Slots
                                </div>
                                <div class="tools">
                                    <a href="javascript:;" class="collapse"> </a>
                                    <a href="javascript:;" class="reload"> </a>
                                    <a href="javascript:;" class="remove"> </a>
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <form action="{{route('delivery.save_time_slot')}}" class="form-horizontal"
                                      id="form_timeslot" method="POST">
                                    <input type="hidden" value="{{csrf_token()}}" id="_token">
                                    <div class="form-body" style="margin-top: 30px">
                                        <input hidden name="id" value="<?=isset($slot_data) ? $slot_data['id'] : '';?>"
                                               id="timeslot_id">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label col-md-12">Day of the week</label>
                                                    <div class="col-md-12">
                                                        <select onchange="get_day_data()" name="week_day" class="form-control" id="week_day">
                                                            <option value="1">Monday</option>
                                                            <option value="2">Tuesday</option>
                                                            <option value="3">Wednesday</option>
                                                            <option value="4">Thursday</option>
                                                            <option value="5">Friday</option>
                                                            <option value="6">Saturday</option>
                                                            <option value="7">Sunday</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="resp_data"></div>



                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="post_url" value="{{route('delivery.get_time_slot')}}">
        <input type="hidden" id="save_url" value="{{route('delivery.save_time_slot')}}">
        <input type="hidden" id="slot_url" value="{{route('delivery.get_slot_data')}}">
    </section>
@stop

@push('footer')
    <script>
        jQuery(document).ready(function () {
            $(".timepicker").timepicker({
                autoclose: true,
                minuteStep: 5,
                showSeconds: false,
                showMeridian: false
            });
        });
    </script>
<script>


$(document).ready(function () {

    $(".timepicker").timepicker({
    autoclose: true,
    minuteStep: 5,
    showSeconds: false,
    showMeridian: false
    });

    get_day_data();

    });

    function get_day_data(){
    $.ajax({
    url: $('#post_url').val(),
    method: "POST",
    data: {_token: $('#_token').val(), week_day: $("#week_day").val()},
    success: function (resp) {
    $("#resp_data").html(resp);
    get_slot($("#week_day").val());
    }
    });
    }

    function save_info(dis, type) {
    var data;
    if (type === 'day') {
    data = {
    type: type,
    _token: $('#_token').val(),
    week_day: $("#week_day").val(),
    status: parseInt($("#status").val()),
    delivery_per_slot: $("#delivery_per_slot").val()
    };
    } else {
    data = {
    type: type,
    _token: $('#_token').val(),
    week_day: $("#week_day").val(),
    timeslot_id: $("#timeslot_id").val(),
    slot_start_time: $("#slot_start_time").val(),
    slot_end_time: $("#slot_end_time").val(),
    delivery_per_slot: $("#delivery_per_slot").val()
    };
    }
    var prev = $(dis).html();
    console.log($('#save_url').val());
    $.ajax({
    url: $('#save_url').val(),
    method: "POST",
    data: data,
    beforeSend: function () {
    $(dis).html("Saving..");
    },
    success: function (resp) {
        $('.slot_error').text(' ');
    $(dis).html(prev);
    if(type == 'slot'){
    get_slot($("#week_day").val());
    }
    if(resp.message){
        // alert('ok');
        $('.slot_error').text(resp.message);
    }
    }
    });
    }

    function set_day_off(dis) {
    if (parseInt(dis.value) === 1) {
    dis.value = 0;
    $(".dayoff_class").removeClass("d-none");
    } else {
    dis.value = 1;
    $(".dayoff_class").addClass("d-none");
    }
    }

    function get_slot(){
    $.ajax({
    url: $('#slot_url').val(),
    method: "POST",
    data: {
    _token: $('#_token').val(),
    week_day: $("#week_day").val()
    },
    beforeSend: function () {
    $("#s_data").html("..");
    },
    success: function (resp) {
    $("#s_data").html(resp);
    }
    });
    }

    function remove_slot(url, id, token){
    $.ajax({
    url: url,
    method: "POST",
    data: {
    _token: token,
    id: id
    },
    success: function (resp) {
    $(".rm_rw_"+id).fadeOut(500, function(){
    $(".rm_rw_"+id).remove();
    });
    }
    });
    }
    </script>
    <!--  End Delivery js -->

@endpush
