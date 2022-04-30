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
    $.ajax({
        url: $('#save_url').val(),
        method: "POST",
        data: data,
        beforeSend: function () {
            $(dis).html("Saving..");
        },
        success: function (resp) {
            $(dis).html(prev);
            if(type == 'slot'){
                get_slot($("#week_day").val());
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
