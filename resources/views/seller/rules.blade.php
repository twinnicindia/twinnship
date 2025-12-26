<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Courier Preferences Rules | {{$config->title}} </title>
    @include('seller.pages.styles')
</head>

<body>

@php
    $name=array(
     'payment_type' => "Payment Mode",
    'order_amount' => "Order Amount",
    'pickup_pincode' => "Pickup Pincode",
    'delivery_pincode' => "Delivery Pincode",
    'zone' => "Zone",
    'weight' => "Weight",
    'product_name' => "Product Name",
    'product_sku' => "Product SKU",
    'order_type' => "Order Type",
    'is' => "Is",
    'is_not' => "Is not",
    'starts_with' => "Starts with",
    'greater_than' => "GT - Greater than",
    'less_than' => "LE - Less than Equal to",
    'any_of' => "Any Of",
    'contain' => "Contain Word",
    'reverse_qc' => "Reverse with QC",
    'reverse' => "Reverse",
    );
@endphp

@include('seller.pages.header')

@include('seller.pages.side_links')


<div class="container-fluid">
    <div class="main-content d-flex flex-column">


        <div class="row ">
            <div class="col-xxl-12 mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="card-body mt-3">
                        <div class="row">
                            <div class="content-inner" id="data_div">
                                <div class="card" id="display_row">
                                    <div class="card-body col-lg-4">
                                        <h3 class="h4 mb-4">Courier Preferences Rules</h3>
                                        <a class="btn btn-primary addInfoButton w-50"><i class="fa fa-plus"></i>
                                            Add new Rule</a>
                                        <br><br>
                                        @foreach($preferences as $key => $p)
                                            <div class="col-sm-6"  id="row{{$p->id}}">
                                                <div class="list-group mb-3">
                                                    <div class="list-group-item list-group-item-action flex-column align-items-start ">
                                                        <div class="row border-bottom p-all-10 m-b-10">
                                                            <div class="col-sm-6 d-flex w-100   justify-content-between ">
                                                                <h5 class="mb-1">{{$key+1}}. {{$p->rule_name}}</h5>
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <i class="mdi mdi-arrow-up"></i> #{{$p->priority}}
                                                            </div>
                                                            <div class="col-sm-2 text-right">
                                                                <div class="custom-control custom-switch">
                                                                    <input type="checkbox" data-id="{{$p->id}}" class="custom-control-input change_status" {{$p->status=="y"?"checked":""}} id="customSwitch{{$p->id}}">
                                                                    <label class="custom-control-label" for="customSwitch{{$p->id}}"></label>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-2 text-right">
                                                                <i class="fa fa-pencil modify_data text-success" title="Edit Information" data-id="{{$p->id}}"></i>
                                                                <i class="fa fa-trash remove_data text-danger" title="Remove Information" data-id="{{$p->id}}"></i>
                                                            </div>

                                                        </div>
                                                        @foreach($p->rules as $r)
                                                            <p class="mb-1">{{$name[$r->criteria]}} {{$name[$r->match_type]}} {{$r->criteria == 'weight' ? ($r->match_value /1000) : ($name[$r->match_value] ?? $r->match_value)}} {{$r->criteria == 'weight' ? 'KG' : ''}}</p>
                                                        @endforeach
                                                        <div class="row">
                                                            <div class="col-sm-6"><b>Priority 1</b>: {{$allPartners[$p->priority1] ?? "NA"}}</div>
                                                            <div class="col-sm-6"><b>Priority 2</b>: {{$allPartners[$p->priority2] ?? "NA"}}</div>
                                                            <div class="col-sm-6"><b>Priority 3</b>: {{$allPartners[$p->priority3] ?? "NA"}}</div>
                                                            <div class="col-sm-6"><b>Priority 4</b>: {{$allPartners[$p->priority4] ?? "NA"}}</div>
                                                        </div>

                                                        <p></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                        @endforeach


                                    </div>
                                </div>
                            </div>
                            <div class="content-inner card-row" id="form_div" style="display: none;">
                                <form method="post" action="{{route('seller.add_rule')}}" id="quickForm">
                                    @csrf
                                <div class="row">
                                    <h3 class="h4 mb-4">Shipping Rule</h3>
                                    <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <div class="form-group">
                                                        <label for="rule_name" class="col-form-label">Rule Name</label>
                                                        <input type="text" class="form-control" name="name" value="" id="rule_name">
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="priority" class="col-form-label">Set Priority</label>
                                                        <input type="number" class="form-control" name="priority" id="priority" value="">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-row pl-4 border-top pt-3 d-flex justify-content-between">
                                                <div class="col-sm-6">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="any" name="filter_type" class="custom-control-input courier_priority" checked="checked" value="any">
                                                        <label class="custom-control-label" for="any">Match Any of the Below</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="all" name="filter_type" class="custom-control-input courier_priority" value="all">
                                                        <label class="custom-control-label" for="all">Match All of the Below</label>
                                                    </div>
                                                </div>
                                            </div>



                                            <input type="hidden" name="count_row" id="count_total_rules" value="1">

                                            <div class="card-body filter-body">
                                                <div class="row pt-2 mb-1" id="filter_number_0">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <select name="filter[0][field]" class="custom-select" required="" onchange="on_field_change(0,this.value)" id="filter_0_type">
                                                                <option value="payment_type">Payment Mode</option>
                                                                <option value="order_amount">Order Amount</option>
                                                                <option value="pickup_pincode">Pickup Pincode</option>
                                                                <option value="delivery_pincode">Delivery Pincode</option>
                                                                <option value="weight">Weight (In Kg.)</option>
                                                                <option value="product_name">Product Name</option>
                                                                <option value="product_sku">Product SKU</option>
                                                                <option value="order_type">Order Type</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <select name="filter[0][condition]" class="custom-select" id="filter_0_conditions" required>
                                                                <option value="is">Is</option>
                                                                <option value="is_not">Is not</option>
                                                                <option value="starts_with">Starts with</option>
                                                                <option value="greater_than">GT - Greater than</option>
                                                                <option value="less_than">LE - Less than Equal to</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-6 mt-3 mb-3">
                                                        <div class="form-group" id="filter_0_value">
                                                            <textarea class="form-control" rows="2" name="filter[0][value]" placeholder="" id="filter_0_value"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 mt-3">
                                                        <div class="form-group">
                                                            <button type="button" class="btn btn-success add_new_condition"><ion-icon style="font-size: 1.8rem;" name="add-outline"></ion-icon></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>

                                    <div class="col-md-6 border-left border-dark">
                                        <div class="row m-b-10">
                                            <div class="col-sm-12">
                                                <h5>Courier Priority</h5>
                                            </div>
                                        </div>
                                        <div class="row bg-gray-300 p-t-20 m-b-10">
                                            <div class="form-group col-sm-6">
                                                <label class="col-form-label">Priority 1</label>
                                                <select name="courier_priority_1" class="custom-select partnerSelect" data-id="1" id="courier_priority_1">
                                                    <option value="">Select</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label class="col-form-label">Priority 2</label>
                                                <select name="courier_priority_2" class="custom-select partnerSelect" data-id="2" id="courier_priority_2">
                                                    <option value="">Select</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label class="col-form-label">Priority 3</label>
                                                <select name="courier_priority_3" class="custom-select partnerSelect" data-id="3" id="courier_priority_3">
                                                    <option value="">Select</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label class="col-form-label">Priority 4</label>
                                                <select name="courier_priority_4" class="custom-select partnerSelect" data-id="4" id="courier_priority_4">
                                                    <option value="">Select</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="overflow:auto;">
                                    <div style="float:right;">
                                        <button type="button" class="btn btn-danger" id="cancelButton">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('seller.pages.scripts')
<script type="text/javascript">
    let allPartnerList = '<option value="">Select Partner</option>',reversePartnerList='<option value="">Select Partner</option>',qcPartnerList='<option value="">Select Partner</option>';
    @foreach($partner as $p)
        allPartnerList += "<option value='{{$p->keyword}}'>{{$p->title}}</option>";
    @endforeach
        @foreach($partner as $p)
        @if($p->reverse_enabled == 'y')
        reversePartnerList += "<option value='{{$p->keyword}}'>{{$p->title}}</option>";
    @endif
        @endforeach
        @foreach($partner as $p)
        @if($p->reverse_enabled == 'y' && $p->qc_enabled == 'y')
        qcPartnerList += "<option value='{{$p->keyword}}'>{{$p->title}}</option>";
    @endif
    @endforeach
    $('.partnerSelect').html(allPartnerList);
    $('.addInfoButton').click(function() {

        $('#data_div').hide();
        $('#form_div').fadeIn();
    });
    $('#cancelButton').click(function() {
        $('#form1').trigger("reset");
        $('#form_div').hide();
        $('#data_div').fadeIn();
    });

    // $(document).ready(function () {
    //     $(".custom-select").on("change", function () {
    //         // Enable all options
    //         $("option").prop("disabled", false);

    //         // Get an array of all current selections
    //         var selected = [];
    //         $("select").each(function () {
    //             selected.push($(this).val());
    //         });

    //         // Disable all selected options, except the current showing one, from all selects
    //         $("select").each(function () {
    //             for (var i = 0; i < selected.length; i++) {
    //                 if (selected[i] != $(this).val()) {
    //                     $(this).find("option[value='" + selected[i] + "']").prop("disabled", true);
    //                 }
    //             }
    //         });
    //     });
    // });

    var x = 1;
    $('.add_new_condition').click(function() {
        x++;
        add_row(x);
        $('#count_total_rules').val(x);
    });

    function add_row(x) {
        var fieldHTML = '<div class="row bg-gray-300 p-t-20 m-b-10  border-light" id="filter_number_' + x + '">' +
            '<div class="col-sm-6">' +
            '<div class="form-group">' +
            '<select name="filter[' + x + '][field]" id="filter_' + x + '_type" onchange="on_field_change(' + x + ',this.value)" required class="custom-select">' +
            '<option value="">Select</option>' +
            '<option value="payment_type">Payment Mode</option>' +
            '<option value="order_amount">Order Amount</option>' +
            '<option value="pickup_pincode">Pickup Pincode</option>' +
            '<option value="delivery_pincode">Delivery Pincode</option>' +
            '<option value="weight">Weight (In Kg.)</option>' +
            '<option value="product_name">Product Name</option>' +
            '<option value="product_sku">Product SKU</option>' +
            '<option value="order_type">Order Type</option>' +
            '</select>' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-6">' +
            '<div class="form-group">' +
            '<select name="filter[' + x + '][condition]" id="filter_' + x + '_conditions" required class="custom-select">' +
            '<option value="is">Is</option>' +
            '<option value="is_not">Is not</option>' +
            '<option value="starts_with">Starts with</option>' +
            '<option value="greater_than">GT - Greater than</option>' +
            '<option value="less_than">LE - Less than Equal to</option>' +
            '</select>' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-6 mt-3 mb-3">' +
            '<div class="form-group" id="filter_' + x + '_value">' +
            '<textarea class="form-control" rows="2"  name="filter[' + x + '][value]" placeholder=""></textarea>' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-6 mt-3 mb-3">' +
            '<div class="form-group">' +
            '<button type="button" class="btn btn-danger" onclick="deleteFilterRow(' + x + ');"><ion-icon style="font-size: 1.8rem;" name="trash-outline"></ion-icon></button>' +
            '</div>' +
            '</div>' +
            '</div>';
        $('.filter-body').append(fieldHTML);
    }

    function deleteFilterRow(id) {
        --x;
        var element = document.getElementById("filter_number_" + id);
        element.parentNode.removeChild(element);
        $('#count_total_rules').val(x);

    }

    function on_field_change(row = false, value = false) {
        var options = '';


        var values_options = '<textarea class="form-control" rows="1" required="" name="filter[' + row + '][value]" placeholder=""></textarea>';
        document.getElementById("filter_" + row + "_value").innerHTML = values_options;

        switch (value) {
            case 'payment_type':
                options = '<option value="is">Is</option><option value="is_not">Is not</option>';
                var values_options = '<select name="filter[' + row + '][value]" class="custom-select" required=""><option value="">Select</option><option value="cod">COD</option><option value="prepaid">Prepaid</option><option value="reverse">Reverse</option></select>';
                document.getElementById("filter_" + row + "_value").innerHTML = values_options;
                break;
            case 'order_amount':
                options = '<option value="greater_than">GT - Greater than</option><option value="less_than">LE - Less than Equal to</option>';
                break;
            case 'pickup_pincode':
                options = '<option value="is">Is</option><option value="is_not">Is not</option><option value="any_of">Any of (Comma Separated)</option>';
                break;
            case 'delivery_pincode':
                options = '<option value="is">Is</option><option value="is_not">Is not</option><option value="starts_with">Starts with</option><option value="any_of">Any of (Comma Separated)</option>';
                break;
            case 'state':
                options = '<option value="is">Is</option><option value="is_not">Is not</option>';
                break;
            case 'zone':
                options = '<option value="is">Is</option><option value="is_not">Is not</option>';
                var values_options = '<select name="filter[' + row + '][value]" class="custom-select" required=""><option value="">Select</option><option value="z1">Z1</option><option value="z2">Z2</option><option value="z3">Z3</option><option value="z4">Z4</option><option value="z5">Z5</option></select>';
                document.getElementById("filter_" + row + "_value").innerHTML = values_options;
                break;
            case 'weight':
                options = '<option value="greater_than">GT - Greater than</option><option value="less_than">LE - Less than Equal to</option>';
                break;
            case 'product_name':
                options = '<option value="is">Is</option><option value="is_not">Is not</option><option value="starts_with">Starts with</option><option value="contain">Contain word</option><option value="any_of">Any of (Comma Separated)</option>';
                break;
            case 'product_sku':
                options = '<option value="is">Is</option><option value="is_not">Is not</option><option value="starts_with">Starts with</option><option value="contain">Contain word</option><option value="any_of">Any of (Comma Separated)</option>';
                break;
            case 'order_type':
                options = '<option value="is">Is</option>';
                var values_options = '<select name="filter[' + row + '][value]" class="custom-select orderTypeSelection" required=""><option value="">Select</option><option value="reverse">Reverse</option><option value="reverse_qc">Reverse with QC</option></select>';
                document.getElementById("filter_" + row + "_value").innerHTML = values_options;
                break;
            default:
                options = '<option value="is">Is</option><option value="is_not">Is not</option><option value="starts_with">Starts with</option><option value="greater_than">GT - Greater than</option><option value="less_than">LE - Less than Equal to</option>';
        }

        document.getElementById("filter_" + row + "_conditions").innerHTML = options;
    }


    $(document).on('click', '.modify_data', function() {
        showOverlay();
        var that = $(this);

        $.ajax({
            url: '{{url('/')."/modify-rule/"}}' + that.data('id'),
            success: function(response) {
                var info = JSON.parse(response);
                console.log(info);
                $('#quickForm').prop('action', '{{route('seller.update_rule')}}');
                $('#pid').val(info.preferences.id);
                $('#rule_name').val(info.preferences.rule_name);
                $('#priority').val(info.preferences.priority);
                $('#courier_priority_1').val(info.preferences.priority1);
                $('#courier_priority_2').val(info.preferences.priority2);
                $('#courier_priority_3').val(info.preferences.priority3);
                $('#courier_priority_4').val(info.preferences.priority4);
                if(info.preferences.match_type == 'any')
                    $('#any').prop('checked','checked');
                else
                    $('#all').prop('checked','checked');
                $('#weight').val(info.weight);
                $('#length').val(info.length);
                $('#breadth').val(info.breadth);
                $('#height').val(info.height);
                $('#data_div').hide();

                //setting the rules pre-filled

                $('.filter-body').html('');
                for(var i=0;i<info.rules.length;i++){
                    add_row(i);
                }
                for(var i=0;i<info.rules.length;i++){
                    // alert(info.rules[i].criteria);
                    $('#filter_' + [i] + '_type').val(info.rules[i].criteria);
                    $('#filter_' + [i] + '_type').trigger('change');
                    $('#filter_' + [i] + '_conditions').val(info.rules[i].match_type);
                    var eweight = info.rules[i].match_value;
                    if(info.rules[i].criteria === 'weight')
                        eweight = info.rules[i].match_value / 1000;
                    $('#filter_' + [i] + '_value').children().val(eweight);
                }

                $('#form_div').fadeIn();
                hideOverlay();
            },
            error: function(response) {
                hideOverlay();
                $.notify(" Oops... Something went wrong!", {animationType:"scale", align:"right", type: "danger", icon:"close"});
            }
        });
    });


    $(document).on('click', '.remove_data', function() {
        var that = $(this);
        if (window.confirm("Are you sure want to Delete?")) {
            showOverlay();
            $.ajax({
                url: '{{url('/')."/delete-rule"}}/' + that.data('id'),
                success: function(response) {
                    hideOverlay();
                    $.notify(" Rules has been Deleted.", {animationType:"scale", align:"right", type: "success", icon:"check"});
                    $('#row' + that.data('id')).remove();
                },
                error: function(response) {
                    hideOverlay();
                    $.notify(" Oops... Something went wrong!", {animationType:"scale", align:"right", type: "danger", icon:"close"});
                }
            });
        }
    });




    $(document).on('change', '.change_status', function() {
        var that = $(this);
        //  showOverlay();
        $.ajax({
            method: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                'id': that.data('id'),
                'status': that.prop('checked') ? 'y' : 'n',
            },
            url: '{{url('/')."/rule-status"}}',
            success: function (response) {
                $.notify(" Status has been Changed.", {animationType:"scale", align:"right", type: "success", icon:"check"});
            },
            error: function(response) {
                hideOverlay();
                $.notify(" Oops... Something went wrong!", {animationType:"scale", align:"right", type: "danger", icon:"close"});
            }
        });
    });

    $('#priority').blur(function() {
        var that = $(this);
        // that.removeClass('invalid');
        showOverlay();
        $.ajax({
            type: 'get',
            url: '{{url('/')}}' + '/check-priority/' + that.val(),
            success: function(response) {
                hideOverlay();
                if (response > 0) {
                    $.notify(" Oops... This Priority is already set!", {animationType:"scale", align:"right", type: "danger", icon:"close"});
                    that.val('');
                }
            },
            error: function(response) {
                hideOverlay();
            }
        });
    });
    $('#form_div').on('change','.orderTypeSelection',function () {
        if($(this).val().trim() === 'reverse'){
            $('.partnerSelect').html(reversePartnerList);
        }else if($(this).val().trim() === 'reverse_qc'){
            $('.partnerSelect').html(qcPartnerList);
        }else{
            $('.partnerSelect').html(allPartnerList);
        }
    });
    $('#quickForm').validate({
        rules: {
            name: {
                required: true
            },
            priority: {
                required: true,
            },
            courier_priority_1: {
                required: true,
            },
            courier_priority_2: {
                required: true,
            },
            courier_priority_3: {
                required: true,
            },
            courier_priority_4: {
                required: true,
            },
        },
        messages: {
            name: {
                required: "Please Enter a Rule Name",
            },
            priority: {
                required: "Please Enter a Priority",
            },
            courier_priority_1: {
                required: "Please Courier Priority 1",
            },
            courier_priority_2: {
                required: "Please Courier Priority 2",
            },
            courier_priority_3: {
                required: "Please Courier Priority 3",
            },
            courier_priority_4: {
                required: "Please Courier Priority 4",
            },
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
</script>
</body>

</html>
