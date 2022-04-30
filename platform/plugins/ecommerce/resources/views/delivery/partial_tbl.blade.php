<table class="table table-striped table-info" style="margin-top: 30px !important;">
    @foreach($s_data as $s)
        <tr class="rm_rw_{{$s->id}}">
            <td style="font-size: 15px; text-indent: 15px">{{$s->time}}</td>
            <td style="font-size: 15px; text-indent: 15px">{{$s->delivery_per_slot}} slots</td>
            <td style="text-align: right">
                <a href="javascript:;" class="btn btn-danger float-right" onclick="remove_slot('{{route('delivery.remove_slot')}}' , {{$s->id}}, '{{csrf_token()}}')">Delete</a>
            </td>
        </tr>
    @endforeach
</table>
