<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Pending Reports | {{$config->title}} </title>
    @include('seller.pages.styles')
</head>

<body>
    <div class="container-fluid user-dashboard">
        @include('seller.pages.header')
        @include('seller.pages.sidebar')
        <div class="content-wrapper">

            <div class="content-inner" id="data_div">
                <div class="card">
                    <div class="card-body">
                        <h3 class="h4 mb-4">Report Status  </h3>

                        <div class="row">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#bulkupload"><i class="fa fa-upload"></i> Import</button>
                                <button type="button" class="btn btn-danger btn-sm" id="removeAllButton" style="display: none;"><i class="fa fa-trash"></i> Remove</button>
                            </div>
                            <div class="col-md-2"></div>
                            <div class="col-md-4">
                                <br>
                                <span class="float-right data-counter" style="display: inline-block;">
                                    <p class="mb-0 h6 f-14">Showing <span class="sku_display_limit"></span> of <span id="order_count"></span></p>
                                    <p class="mb-0 h6 f-14">Selected <span class="total_sku_selected">0</span> out of <span class="sku_display_limit"></span></p>
                                </span>
                            </div>
                        </div>
                        <div class="table-responsive h-600" style="min-height: 400px;">
                            <table class="table table-hover mb-0" id="example1">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="checkAllButton" value="y"></th>
                                        <th>Sr.No</th>
                                        <th>Report Title</th>
                                        <th>Report Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($cnt=1)
                                    @foreach($reports as $r)
                                        <tr>
                                            <td>{{$cnt++}}</td>
                                            <td>{{$r->report_name}}</td>
                                            <td>{{$r->report_type}}</td>
                                            <td>{{$r->report_status}}</td>
                                            <td>
                                                @if($r->report_status == 'success')
                                                    <a download="" href="{{asset($r->report_download_url)}}"><i class="fa fa-download"></i> </a>
                                                @else
                                                    <p>Not Ready</p>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="flex justify-between flex-1 sm:hidden mt-3 ml-2">
                            <a class="firstPageButton relative inline-flex items-center py-2 text-sm font-medium bg-white"><i class="fas fa-fast-backward"></i></a>
                            <a class="previousPageButton inline-flex items-center px-1 py-2 text-sm font-medium bg-white"><i class="fas fa-backward"></i></a>
                            <a class="relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"> Page</a>
                            <a><input type="text"  class="currentPage relative inline-flex items-cente py-1 px-3 text-sm font-medium bg-white border" disabled style="width: 4%; text-align:center"></a>
                            <a class="relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"> of</a>
                            <a class="totalPage relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"></a>
                            <a class="nextPageButton inline-flex items-center px-1 py-2 text-sm font-medium bg-white"><i class="fas fa-forward"></i></a>
                            <a class="lastPageButton inline-flex items-center py-2 text-sm font-medium bg-white"><i class="fas fa-fast-forward"></i></a>
                            <div class="float-right">
                                <a>Show
                                    <select name="per_page_record" class="perPageRecord">
                                        <option value="20">20</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="250">250</option>
                                        <option value="500">500</option>
                                        <option value="1000">1000</option>
                                    </select>
                                    Per Page</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('seller.pages.scripts')
        <script type="text/javascript">
            var pageCount = 1,totalPage=1,perPage=20,sellerId=null;
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
                            $('.sku_display_limit').html($("#total_sku").val() >= $(".perPageRecord").val() ? $(".perPageRecord").val() : $("#total_sku").val());
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
                            $('.sku_display_limit').html($("#total_sku").val() >= $(".perPageRecord").val() ? $(".perPageRecord").val() : $("#total_sku").val());
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
                            showOverlay();
                            $.ajax({
                                url: '{{url('/')."/delete-sku"}}/' + that.data('id'),
                                success: function(response) {
                                    hideOverlay();
                                    $.notify(" SKU has been Deleted.", {animationType:"scale", align:"right", type: "success", icon:"check"});
                                    $('#row' + that.data('id')).remove();
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
            });
        </script>
</body>

</html>
