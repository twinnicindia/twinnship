@foreach($warehouse as $w)
    <div class="col-sm-6 col-md-4 mb-3 allWarehouseListData" data-text="{{$w->warehouse_name}}-{{$w->address_line1}}-{{$w->address_line2}}-{{$w->city}}-{{$w->state}}-{{$w->pincode}}">
        <div class="card">
            <div class="card-body">
                <input type="radio" id="warehouse_{{$w->id}}" name="warehouse"
                       data-id="" class="warehouse_select me-1"
                       value="{{$w->id}}" {{$w->default == 'y'? 'checked' : ''}}>
                <label for="warehouse"
                       class="h6 text-dark font-weight-bold">{{$w->warehouse_name}}</label><br>
                <div class="h6 mb-0 text-muted">{{$w->address_line1}} , {{$w->address_line2}}</div>
                <div class="h6 mb-0 text-muted">
                    {{$w->state}},{{$w->city}},{{$w->pincode}}</div>
            </div>
        </div>
    </div>
@endforeach
