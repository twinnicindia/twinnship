<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @include('seller.pages.styles')

    <title>SKU Information | {{$config->title}} </title>
</head>

<body>

@include('seller.pages.header')

@include('seller.pages.side_links')


<div class="container-fluid">
    <div class="main-content d-flex flex-column">
        <div class="content-wrapper">
            <div class="card-row content-inner" id="form_div" style="display: none;">
                <h4 class="mb-4 ">SKU Information</h4>
                <form id="quickForm" method="post" action="{{route('seller.add_sku')}}">
                    @csrf
                    <input type="hidden" name="sid" id="sid">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label class="label">Product SKU</label>
                                <div class="form-group position-relative">
                                    <input type="text" class="form-control text-dark ps-5 h-58" placeholder="Product SKU" id="product_sku" name="product_sku" value="" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label class="label">Product Name</label>
                                <div class="form-group position-relative">
                                    <input type="text" class="form-control text-dark ps-5 h-58" placeholder="Product Name" id="product_name" name="product_name" value="" required>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label class="label">Brand Name</label>
                                <div class="form-group position-relative">
                                    <input type="text" class="form-control text-dark ps-5 h-58" placeholder="Brand Name" id="brand_name" name="brand_name" value="">

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label class="label">Product Weight(In K.g) 0.5 for 500 gm</label>
                                <div class="form-group position-relative">
                                    <input type="text" class="form-control text-dark ps-5 h-58" placeholder="Product Weight" id="product_weight" name="product_weight" value="" required>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label class="label">Product Length(In cm)</label>
                                <div class="form-group position-relative">
                                    <input type="number" class="form-control text-dark ps-5 h-58" placeholder="Product Length"  id="product_length" name="product_length" value="" required>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label class="label">Product Breadth(In cm)</label>
                                <div class="form-group position-relative">
                                    <input type="number" class="form-control text-dark ps-5 h-58" placeholder="Product Breadth" id="product_width" name="product_width" value="" required>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label class="label">Product Height(In cm)</label>
                                <div class="form-group position-relative">
                                    <input type="number" class="form-control text-dark ps-5 h-58" placeholder="Product Height" id="product_height" name="product_height" value="" required>
                                </div>
                            </div>
                        </div>
                        <div style="overflow:auto;">
                            <div style="float:right;">
                                <button type="button" class="btn btn-danger" id="cancelButton">Cancel</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="content-inner" id="data_div">
                <div class="card">
                    <div class="card-body">
                        <h3 class="h4 mb-4">SKU Information</h3>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-3">
                                        <a type="button" class="btn btn btn-primary text-white fw-semibold exportSkuBtn" id="exportSkuBtn">
                                            <i class="ri-upload-2-line"></i> Export
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a type="button" class="btn btn btn-primary addInfoButton text-white fw-semibold">
                                            <i class="ri-add-fill"></i> Add SKU
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a type="button"  class="btn btn btn-primary text-white fw-semibold" data-bs-toggle="modal" data-bs-target="#bulkupload">
                                            <i class="ri-download-2-line"></i> Import
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a type="button" class="btn btn btn-primary text-white fw-semibold" id="removeAllButton" style="display: none;">
                                            <i class="ri-delete-bin-2-fill"></i> Remove All
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2"></div>
                            <div class="col-md-4">
                                <br>
                                <span class="float-end data-counter" style="display: inline-block;">
                                        <p class="mb-0 h6 f-14">Showing <span class="sku_display_limit"></span> of <span
                                                id="order_count"></span></p>
                                        <p class="mb-0 h6 f-14">Selected <span class="total_sku_selected">0</span> out of <span class="sku_display_limit"></span></p>
                                    </span>
                            </div>
                        </div>
                        <form method="get" id="searchSku">
                            <div class="row">
                                <div class="col-md-5 mt-2 pr-1">
                                    <div class="form-group mb-2">
                                        <input type="text" class="form-control" style="height:38px;" name="sku" id="sku" placeholder="Search SKUs">
                                    </div>
                                </div>
                                <div class="col-md-2 mt-2 pl-0">
                                    <div class="form-group mb-2">
                                        <button type="submit" class="btn btn-primary mx-0 applyFilterAllOrderSearch" data-form="searchSku" data-id="filter_order" style="height:38px; min-width: 40px;" data-placement="top" data-toggle="tooltip" data-original-title="Search"> <i data-feather="search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive  scroll-bar active">
                            <table class="table" id="example1">
                                <thead class="sticky-header">
                                <tr class="text-center rounded-10">
                                    <th style="width: 40px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value=""
                                                   id="checkAllButton" value="y" >
                                        </div>
                                    </th>
                                    <th>Sr.No</th>
                                    <th>SKU</th>
                                    <th>Product Name</th>
                                    <th>Brand Name</th>
                                    <th>Product Weight</th>
                                    <th>Dimensions</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody id="filter_sku">

                                </tbody>
                            </table>
                        </div>
                        <div class="table-footer">
                            <div class="my-2">
                                <div class="pagination-container">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="pagination">
                                            <a class="firstPageButton"><img src="{{asset('assets/sellers/')}}/images/1.svg" alt=""></a>
                                            <a class="previousPageButton"><img src="{{asset('assets/sellers/')}}/images/2.svg" alt=""></a>
                                            <span class="currentPage page-info text-dark">1 </span>
                                            <span class="page-info text-dark">of</span>
                                            <span class="totalPage page-info text-dark">2</span>
                                            <a class="nextPageButton"><img src="{{asset('assets/sellers/')}}/images/3.svg" alt=""></a>
                                            <a class="lastPageButton"><img src="{{asset('assets/sellers/')}}/images/4.svg" alt=""></a>
                                        </div>
{{--                                        <div class="go-to-page text-dark">Go to page: <input type="number" min="1"--}}
{{--                                                                                             max="73" value=""><button class="go-btn">Go</button>--}}
{{--                                        </div>--}}
                                    </div>
                                    <div class="items-per-page-dropdown text-dark">Rows per page:
                                        <select class="rows-per-page">
                                            <option value="20">20</option>
                                            <option value="100">100</option>
                                            <option value="500">500</option>
                                            <option value="1000">1000</option>
                                            <option value="All">All</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="bulkupload" tabindex="-1" aria-labelledby="bulkupload" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content justify-content-center">
                        <form method="post" action="{{route('seller.import_csv_sku')}}" enctype="multipart/form-data">
                            @csrf
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="bulkupload">Import Sku
                                Orders via CSV</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <a href="#" style="color: blue;"><i class="ri-download-line"></i>Download Sample CSV
                                File</a>
                        </div>
                        <div class="form-group  text-center">
                            <div class="form-control h-100 w-80 text-center position-relative p-5 p-lg-5">
                                <div class="product-upload">
                                    <label for="file-upload" class="file-upload mb-0">
                                        <i class="ri-upload-cloud-2-line fs-2 text-gray-light"></i>
                                        <span class="d-block fw-semibold text-body">Drop files here or click to upload.</span>
                                    </label>
                                    <input id="file-upload" type="file">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger text-white" data-bs-dismiss="modal">Cancle</button>
                            <button type="button" class="btn btn-primary text-white">Upload
                                File</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- end Modal -->
        </div>
    </div>
</div>


@include('seller.pages.scripts')
<script type="text/javascript">
    var pageCount = 1,totalPage=1,perPage=20,sellerId=null,totalRecordFetched,perPageCount;
    $('.currentPage').val(pageCount);
    $('.totalPage').html(totalPage);
    var del_ids = [];
    $(document).ready(function() {

        get_value_filter('filter_sku');

        function get_value_filter(divId) {
            showOverlay();
            $.ajax({
                method: 'get',
                data: {
                    'page': pageCount,
                    'per_page': perPage,
                },
                url: "{{ route('seller.ajax_sku')}}",
                success: function(response) {
                    $('#' + divId).html(response);
                    // Pagination
                    $('.total_sku_selected').html(0);
                    $('.sku_display_limit').html(parseInt($("#total_sku").val()) >= parseInt($(".perPageRecord").val()) ? $(".perPageRecord").val() : $("#total_sku").val());
                    $('#order_count').html($("#total_sku").val());
                    $('.currentPage').val($("#currentPage").val());
                    $('.totalPage').html($("#totalPage").val());
                    hideOverlay();
                }
            });
        }

        // Pagination
        $('.firstPageButton').click(function(){
            if(pageCount > 1){
                pageCount = 1 ;
                get_value_filter('filter_sku');
            }
        });

        $('.previousPageButton').click(function(){
            if(pageCount > 1){
                pageCount--;
                get_value_filter('filter_sku');
            }
        });

        $('.nextPageButton').click(function(){
            var totalPage = $('.totalPage').html();
            if(pageCount < totalPage){
                pageCount++;
                get_value_filter('filter_sku');
            }
        });

        $('.lastPageButton').click(function(){
            var totalPage = $('.totalPage').html();
            if(pageCount < totalPage){
                pageCount = $('.totalPage').html();
                get_value_filter('filter_sku');
            }
        });

        $(".perPageRecord").change(function() {
            perPage = $(this).val();
            get_value_filter('filter_sku');
        });

        $("#searchSku").submit(function(e) {
            e.preventDefault();
            showOverlay();
            $.ajax({
                method: 'get',
                data: {
                    'page': 1,
                    'per_page': perPage,
                    'sku': $("#sku").val(),
                },
                url: "{{ route('seller.ajax_sku')}}",
                success: function(response) {
                    $('#filter_sku').html(response);
                    // Pagination
                    $('.total_sku_selected').html(0);
                    $('.sku_display_limit').html(parseInt($("#total_sku").val()) >= parseInt($(".perPageRecord").val()) ? $(".perPageRecord").val() : $("#total_sku").val());
                    $('#order_count').html($("#total_sku").val());
                    $('.currentPage').val($("#currentPage").val());
                    $('.totalPage').html($("#totalPage").val());
                    hideOverlay();
                }
            });
        });

        //get the file name
        $('#inputGroupFile02').on('change',function(){
            var fileName = $(this).val();
            $(this).next('.custom-file-label').html(fileName);
        });
        $('#example1').on('click', '.modify_data', function() {
            showOverlay();
            var that = $(this);
            $.ajax({
                url: '{{url('/')."/modify-sku/"}}' + that.data('id'),
                success: function(response) {
                    var info = JSON.parse(response);
                    $('#quickForm').prop('action', '{{route('update_sku')}}');
                    $('#sid').val(info.id);
                    $('#product_sku').val(info.sku);
                    $('#product_name').val(info.product_name);
                    $('#brand_name').val(info.brand_name);
                    $('#product_weight').val(info.weight);
                    $('#product_length').val(info.length);
                    $('#product_width').val(info.width);
                    $('#product_height').val(info.height);
                    $('#data_div').hide();
                    $('#form_div').fadeIn();
                    hideOverlay();
                },
                error: function(response) {
                    hideOverlay();
                    $.notify(" Oops... Something went wrong!", {animationType:"scale", align:"right", type: "danger", icon:"close"});
                }
            });
        });

        $('.addInfoButton').click(function() {
            $('#data_div').hide();
            $('#form_div').fadeIn();
        });
        $('#cancelButton').click(function() {
            $('#form1').trigger("reset");
            $('#form_div').hide();
            $('#data_div').fadeIn();
        });

        $('#example1').on('click', '.remove_data', function() {
            var that = $(this);
            if (window.confirm("Are you sure want to Delete?")) {
                location.reload();
                showOverlay();
                $.ajax({
                    url: '{{url('/')."/delete-sku"}}/' + that.data('id'),
                    success: function(response) {
                        hideOverlay();
                        $.notify(" SKU has been Deleted.", {animationType:"scale", align:"right", type: "success", icon:"check"});
                        //  $('#row' + that.data('id')).remove();
                    },
                    error: function(response) {
                        hideOverlay();
                        $.notify(" Oops... Something went wrong!", {animationType:"scale", align:"right", type: "danger", icon:"close"});
                    }
                });
            }
        });
        $('#checkAllButton').click(function() {
            var that = $(this);
            if (that.prop('checked')) {
                $('.selectedCheck').prop('checked', true);
                $('#removeAllButton').fadeIn();
                $('.total_sku_selected').html($('.sku_display_limit').html());
            } else {
                $('.selectedCheck').prop('checked', false);
                $('#removeAllButton').hide();
                $('.total_sku_selected').html(0);
            }
        });
        $('#example1').on('click', '.selectedCheck', function () {
            var cnt = 0;
            $('.selectedCheck:visible').each(function () {
                if($(this).prop('checked'))
                    cnt++;
            });
            $('.total_sku_selected').html(cnt);
            if (cnt > 0)
                $('#removeAllButton').fadeIn();
            else
                $('#removeAllButton').hide();
        });

        //Multiple Delete
        $('#removeAllButton').click(function() {
            del_ids = [];
            $('.selectedCheck').each(function() {
                if ($(this).prop('checked'))
                    del_ids.push($(this).val());
            });
            if (window.confirm("Are you sure want to Delete?")) {
                showOverlay();
                $.ajax({
                    type: 'post',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'ids': del_ids
                    },
                    url: '{{url('/')."/remove-selected-sku"}}',
                    success: function(response) {
                        hideOverlay();
                        $.notify(" SKU has been Deleted.", {animationType:"scale", align:"right", type: "success", icon:"check"});
                        // setTimeout(function() {
                        //     location.reload();
                        // }, 1000);
                        location.reload();

                    },
                    error: function(response) {
                        hideOverlay();
                        $.notify(" Oops... Something went wrong!", {animationType:"scale", align:"right", type: "danger", icon:"close"});
                    }
                });
            }
        });

        $('#quickForm').validate({
            rules: {
                product_sku: {
                    required: true
                },
                product_name: {
                    required: true
                },
            },
            messages: {
                product_sku: {
                    required: "Please Enter Product SKU",
                },
                product_name: {
                    required: "Please Enter Product Name",
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

        //sKU Blur Event
        $('#product_weight').blur(function () {
            var that = $(this);
            showOverlay();
            $.ajax({
                url: '{{url('/')."/fetch_dimension_data/"}}' + (that.val() * 1000),
                success: function (response) {
                    hideOverlay();
                    var info = JSON.parse(response);
                    $('#product_length').val(info.length);
                    $('#product_width').val(info.width);
                    $('#product_height').val(info.height);
                },
                error: function (response) {
                    hideOverlay();
                }
            });
        });

        $('.exportSkuBtn').click(function () {
            sku_ids = [];
            $('.selectedCheck:visible').each(function () {
                if ($(this).prop('checked'))
                    sku_ids.push($(this).val());
            });
            if(sku_ids.length > 0) {
                location.href = "{{route('seller.export_csv_sku')}}?skuIds="+sku_ids;
            } else {
                location.href = "{{route('seller.export_csv_sku')}}?skuIds=";
            }
        });
    });
</script>
</body>

</html>
