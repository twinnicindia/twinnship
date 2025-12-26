<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Shipping Rates | {{$config->title}} </title>
    @include('seller.pages.styles')
</head>

<body>

@include('seller.pages.header')

@include('seller.pages.side_links')



<div class="container-fluid">
    <div class="main-content d-flex flex-column">
        <h3 class="fw-semibold mb-4">Rate Card</h3>
        <div class="row justify-content-center">
            <div class="col-xxl-12 mb-3">
                <!-- <div class="d-flex card-row p-2">
                    <div class="col-lg-2 col-md-4 col-sm-4 mb-3 me-3" style="border: 1px solid #dbd5d5;">
                        <div class="form-group ">
                            <select class="form-select  bg-white w-100 h-30" aria-label=" select example">
                                <option selected class="text-dark">Select Couriers</option>
                                <option value="Aarya ship" class="text-dark"> Aarya ship</option>
                                <option value="Air Cargo X" class="text-dark"> Air Cargo X</option>
                                <option value="AJ World Wide" class="text-dark">AJ World Wide</option>
                                <option value="Amazon 250 g" class="text-dark">Amazon 250 g</option>
                                <option value="Amazon Brands" class="text-dark">Amazon Brands</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-4 mb-3 me-3" style="border: 1px solid #dbd5d5;">
                        <div class="form-group ">
                            <select class="form-select  bg-white w-100 h-30" aria-label=" select example">
                                <option selected class="text-dark">Select Modes</option>
                                <option value="Air" class="text-dark">Air</option>
                                <option value="Surface" class="text-dark">Surface</option>
                                <option value="HyperLocal" class="text-dark">HyperLocal</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-4 mb-3 me-3" style="border: 1px solid #dbd5d5;">
                        <div class="form-group ">
                            <select class="form-select  bg-white w-100 h-30" aria-label=" select example">
                                <option selected class="text-dark"> Select Weights </option>
                                <option value="0.25kg" class="text-dark">0.25kg</option>
                                <option value="0.5kg" class="text-dark">0.5kg</option>
                                <option value="1kg" class="text-dark">1kg</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-end d-flex flex-grow-1 justify-content-end align-items-center">
                        <div class="col-lg-3 col-md-4 col-sm-4 mb-3 me-3" style="border: 1px solid #dbd5d5;">
                            <div class="form-group ">
                                <select class="form-select  bg-white w-100 h-30" aria-label=" select example">
                                    <option selected class="text-dark"> Sort by: Low to High Weight
                                    </option>
                                    <option value="Sort by: High to Low Weight" class="text-dark">Sort by: High to
                                        Low Weight</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="row">
                    <div class="col-12">
                        <div class="">
                            <div class="scroll-bar active">
                                <table class="table">
                                    <thead class="sticky-header">
                                    <tr class="text-center rounded-10">

                                        <th>Courier Partner</th>
                                        <th>ZONE A</th>
                                        <th>ZONE B</th>
                                        <th>ZONE C</th>
                                        <th>Zone D</th>
                                        <th>Zone E</th>
                                        <th>COD Charges</th>
                                        <th>COD Maintenance(%)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($partners as $pa)
                                        <tr>
                                            <td>{{$pa->title}}{{$pa->keyword == 'gati' ? " **" : ""}}</td>
                                            <td id="within_city_{{$pa->id}}"> </td>
                                            <td id="within_state_{{$pa->id}}"> </td>
                                            <td id="metro_to_metro_{{$pa->id}}"> </td>
                                            <td id="rest_india_{{$pa->id}}"> </td>
                                            <td id="north_j_k_{{$pa->id}}"> </td>
                                            <td id="cod_charge_{{$pa->id}}"> </td>
                                            <td id="cod_maintenance_{{$pa->id}}"> </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
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
    var base_path = '{{url(' / ')}}/';
    $(document).ready(function() {
        $.LoadingOverlay("show", {
            image       : "{{asset('assets/1.png')}}",
            imageAutoResize : true,
            imageResizeFactor : 2
        });
        $.ajax({
            type: 'get',
            url: '{{route('seller.get_shipping_rates')}}?rand=' + (Math.random() * 100000) + 1,
            success: function(response) {
                var info = JSON.parse(response);
                for (var i = 0; i < info.length; i++) {
                    $('#within_city_' + info[i].partner_id).html(info[i].within_city);
                    $('#within_state_' + info[i].partner_id).html(info[i].within_state);
                    $('#metro_to_metro_' + info[i].partner_id).html(info[i].metro_to_metro);
                    $('#rest_india_' + info[i].partner_id).html(info[i].rest_india);
                    $('#north_j_k_' + info[i].partner_id).html(info[i].north_j_k);
                    $('#cod_charge_' + info[i].partner_id).html(info[i].cod_charge);
                    $('#cod_maintenance_' + info[i].partner_id).html(info[i].cod_maintenance);
                }
                $.LoadingOverlay('hide');
            },
            error: function(response) {
                showError('Something went wrong please try again later');
                $.LoadingOverlay('hide');
            }
        });
    });
</script>
</body>

</html>
