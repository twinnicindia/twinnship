<div class="card">
    <div class="card-body">
        <h3 class="h4">History
            <div class="float-right">
                <button type="button" class="btn btn-primary AddComment btn-sm mx-0"><i class="fa fa-upload"></i>&nbsp;&nbsp; Add Comment</button>
                <button type="button" class="btn btn-primary BackButton btn-sm mx-0"><i class="fa fa-arrow-alt-left"></i> Go Back</button>
            </div>
        </h3>
        <br>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="example1">
                    <thead>
                        <tr>
                            <th>Weight Discrepancy Date</th>
                            <th>Status</th>
                            <th>Charged Weight (KG)</th>
                            <th>Charged Dimension (CM)</th>
                            <th>Action Taken by</th>
                            <th>Applied Weight</th>
                            <th>Remark</th>
                            <th>Image</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{$weight_rec_data->created}}</td>
                            <td>{{$weight_rec_data->status}}</td>
                            <td>{{$weight_rec_data->c_weight}} </td>
                            <td>(L * B * H) : {{$weight_rec_data->c_length}} * {{$weight_rec_data->c_breadth}} * {{$weight_rec_data->c_height}} </td>
                            <td>{{$weight_rec_data->action_taken_by}}</td>
                            <td>{{$weight_rec_data->e_weight}}</td>
                            <td> - </td>
                            <td>None</td>
                        </tr>
                        @foreach($history as $h)
                        <?php
                        $history_image = DB::table('weight_reconciliation_images')->where('weight_reconciliation_history_id', $h->id)->get();
                        ?>
                        <td>{{$h->history_date}}</td>
                        <td>{{$h->status}}</td>
                        <td>{{$weight_rec_data->c_weigh}}</td>
                        <td>(L * B * H) : {{$weight_rec_data->c_length}} * {{$weight_rec_data->c_breadth}} * {{$weight_rec_data->c_height}} </td>
                        <td>{{$h->action_taken_by}}</td>
                        <td>{{$weight_rec_data->e_weight}}</td>
                        <td>{{$h->remark}}</td>
                        <td>
                            @forelse($history_image as $key=>$attechment)
                            <a href="{{asset($attechment->image)}}" target="_blank"><img src="{{asset($attechment->image)}}" style="height:100px;"></a>
                            @empty
                            <p></p>
                            @endforelse
                        </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="AddCommentWeight" tabindex="-1" role="dialog" aria-labelledby="AddCommentWeightTitle" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" id="fulfillment_info">
            <form method="post" action="{{route('administrator.addWeightRecComment')}}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="weight_rec_id" id="weight_rec_id" value="{{$weight_rec_data->id}}">
                <div class="modal-header">
                    <h5 class="modal-title" id="mySmallModalLabel">Add Comment</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Remark</label>
                                <input type="text" class="form-control" placeholder="Remark" id="remark" name="remark" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Images</label>
                                <input type="file" class="form-control" id="dispute_images" name="dispute_images[]" multiple required>
                            </div>
                        </div>

                    </div>
                    <br>
                    <br>
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <button type="submit" class="btn btn-info btn-sm">Add</button>
                            <button type="button" class="btn btn-secondary btn-sm closeHistoryBtn" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
