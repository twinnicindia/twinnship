<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SKU Mapping | {{$config->title}} </title>
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
                        <h3 class="h4 mb-4">SKU Mapping</h3>

                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{route('seller.export_csv_sku_mapping')}}"><button type="button" class="btn btn-primary btn-sm"><i class="fa fa-download"></i> Export</button></a>
                                <button type="button" class="btn btn-primary addInfoButton btn-sm"><i class="fa fa-plus"></i> Add SKU</button>
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
                        <form method="get" id="searchSku">
                            <div class="row">
                                <div class="col-md-5 mt-2 pr-1">
                                    <div class="form-group mb-2">
                                        <input type="text" class="form-control" style="height:38px;" name="sku" id="sku" placeholder="Search SKUs">
                                    </div>
                                </div>
                                <div class="col-md-2 mt-2 pl-0">
                                    <div class="form-group mb-2">
                                        <button type="submit" class="btn btn-primary mx-0 applyFilterAllOrderSearch" data-form="searchSku" data-id="filter_order" style="height:38px; min-width: 40px;" data-placement="top" data-toggle="tooltip" data-original-title="Search"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive h-600" style="min-height: 400px;">
                            <table class="table table-hover mb-0" id="example1">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="checkAllButton" value="y"></th>
                                        <th>Sr.No</th>
                                        <th>Parent SKU</th>
                                        <th>Child SKU</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="filter_sku">
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
                                        <option value="40">40</option>
                                        <option value="60">60</option>
                                        <option value="80">80</option>
                                        <option value="100">100</option>
                                        <option value="300">300</option>
                                        <option value="500">500</option>
                                    </select>
                                    Per Page</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-inner" id="form_div" style="display: none;">
                <form id="quickForm" method="post" action="{{route('seller.add_sku_mapping')}}">
                    @csrf
                <input type="hidden" name="sid" id="sid">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h3 class="h4 mb-4">SKU Mapping</h3>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="parent_sku">Parent SKU</label>
                                                <input type="text" class="form-control" placeholder="Parent SKU" id="parent_sku" name="parent_sku" value="" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="child_sku">Child SKU</label>
                                                <input type="text" class="form-control" placeholder="Child SKU" id="child_sku" name="child_sku" value="" required>
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
                            </div>
                        </div>

                    </div>

                </form>
            </div>
        </div>

        <div class="modal fade" id="bulkupload" tabindex="-1" role="dialog" aria-labelledby="bulkuploadTitle" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content" id="fulfillment_info">
                    <form method="post" action="{{route('seller.import_csv_sku_mapping')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="mySmallModalLabel">Bulk Upload SKU</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12 pb-10 mb-2">
                                    Download sample SKU upload file : <a class="text-info" href="{{url('public/assets/seller/sku-mapping.csv')}}">Download</a>
                                </div>
                                <div class="col-sm-12">
                                    <div class="m-b-10">
                                        <div class="input-group mb-3">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="inputGroupFile02" name="importFile">
                                                <label class="custom-file-label" for="inputGroupFile02">Choose file</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 text-right">
                                    <button type="submit" class="btn btn-info btn-sm">Upload</button>
                                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </form>
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
                        url: "{{ route('seller.ajax_sku_mapping')}}",
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
                        url: "{{ route('seller.ajax_sku_mapping')}}",
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
                        url: '{{url('/')."/modify-sku-mapping/"}}' + that.data('id'),
                        success: function(response) {
                            var info = JSON.parse(response);
                            $('#quickForm').prop('action', '{{route('update_sku_mapping')}}');
                            $('#sid').val(info.id);
                            $('#parent_sku').val(info.parent_sku);
                            $('#child_sku').val(info.child_sku);
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
                                url: '{{url('/')."/delete-sku-mapping"}}/' + that.data('id'),
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
                                url: '{{url('/')."/remove-selected-sku-mapping"}}',
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
            });
        </script>
</body>

</html>
