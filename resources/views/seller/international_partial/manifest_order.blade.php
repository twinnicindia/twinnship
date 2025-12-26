
        <input type="hidden" id="total_manifest_order" value="{{$total_manifest}}">
        <input type="hidden" id="total_ajax" value="{{$total_manifest}}">
        @if(count($manifest)!=0)
            @php($cnt=1)
            @foreach($manifest as $m)
                <tr id="row{{$m->id}}">
                    <td>{{$m->id}}</td>
                    <?php
                    $date = date('d/m/Y', strtotime($m->created));
                    $time = date('h:i A', strtotime($m->created_time));
                    ?>
                    <td>Date : {{$date}} <br> Time : {{$time}}</td>
                    <td class="text-capitalize">{{$m->type}}</td>
                    <td>{{$PartnerName[$m->courier] ?? ''}}</td>
                    <td>{{$m->number_of_order}}</td>
                    <td>{{$m->p_ref_no}}</td>
                    <td>
                        @if($m->status == 'manifest_generated')
                            <span class="text-info">Manifest Generated</span>
                        @else
                            <span class="text-success">Manifest Downloaded</span>
                        @endif
                    </td>
                    <td>{{$m->warehouse_name}}
                        <a href="javascript:;" class=" mx-0" data-placement="top" data-html="true" data-toggle="tooltip" data-original-title="{{$m->warehouse_address}}"><i class="fas fa-eye text-primary"></i></a>
                    </td>
                    <td>
                        <a title="Print Details" href='{{("order/invoice/pdf/$m->id")}}' class="btn btn-info btn-sm mx-0" data-placement="top" data-toggle="tooltip" data-original-title="Print Order"><i class="fa fa-print"></i> Invoice</a>
                        <a title="Print Label" href='{{("order/label/pdf/$m->id")}}' class="btn btn-info btn-sm mx-0" data-placement="top" data-toggle="tooltip" data-original-title="Print Label"><i class="fa fa-print"></i> Label</a>
                        <a title="Print Manifest" href='{{url("order/manifest/pdf/$m->id")}}' class="btn btn-info btn-sm mx-0" data-placement="top" data-toggle="tooltip" data-original-title="Print Manifest"><i class="fa fa-print"></i> Manifest</a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="11">No Manifest Generated</td>
            </tr>
        @endif
