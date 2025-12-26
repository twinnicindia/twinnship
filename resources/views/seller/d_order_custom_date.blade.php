<table class="table table-hover">
    <thead>
        <tr>
            <th>Date 
                <div class="filter">
                    <a data-toggle="collapse" href="#NDRDateFilter" role="button" aria-expanded="false" aria-controls="NDRDateFilter"><i class="fa fa-calendar-alt fa-xs"></i></a>
                    <div class="collapse filter-collapse" id="NDRDateFilter">
                        <label>Filter by Date</label>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" name="custom_start_date" id="custom_start_date" value="{{$start_date}}" max="{{date('Y-m-d',strtotime('-1 days'))}}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" name="custom_end_date" id="custom_end_date" value="{{$end_date}}" max="{{date('Y-m-d',strtotime('-1 days'))}}">
                                </div>
                            </div>
                        </div>
                        <button type="button" data-modal="NDRDateFilter" class="custom_date_reset btn btn-primary btm-sm mt-2 ml-0">Reset</button>
                        <button type="button" id="get_report" data-modal="NDRDateFilter" class="btn btn-primary btm-sm mt-2 ml-0">Apply</button>
                    </div>
                </div>
            </th>
            <th>Total Shipment</th>
            <th>Yet to Pick</th>
            <th>Picked</th>
            <th>In-Transit</th>
            <th>Delivered</th>
            <th>NDR Pending</th>
            <th>NDR Delivered</th>
            <th>RTO</th>
            <th>Lost/Damaged</th>
        </tr>
    </thead>
    <tbody>
        @foreach($allDays as $p)
            <tr>
                <td>{{$p ?? ""}}</td>
                <td>{{$partner_total[$p] ?? 0}}</td>
                <td>{{$partner_unscheduled[$p] ?? 0}}</td>
                <td>{{$partner_scheduled[$p] ?? 0}}</td>
                <td>{{$partner_intransit[$p] ?? 0}}</td>
                <td>{{$partner_delivered[$p] ?? 0}}</td>
                <td>{{$partner_ndr_pending[$p] ?? 0}}</td>
                <td>{{$partner_ndr_delivered[$p] ?? 0}}</td>
                <td>{{$partner_ndr_rto[$p] ?? 0}}</td>
                <td>{{$partner_damaged[$p] ?? 0}}</td>
            </tr>
        @endforeach
    </tbody>
</table>