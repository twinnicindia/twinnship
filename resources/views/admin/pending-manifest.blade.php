<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Pending Manifestation | {{env('appTitle')}} </title>
    @include('admin.pages.styles')
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        @include('admin.pages.header')
        <!-- /.navbar -->
        <!-- Main Sidebar Container -->
        @include('admin.pages.sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Manage Pending Manifestation</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active">Pending Manifestation</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <section class="content" id="data_div">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">All Pending Manifestation</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <form action="{{route('administrator.pendingManifestOrder')}}" method="get">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <select name="seller_id[]" id="seller_id" class="form-control" multiple>
                                                                <option value="0">All Sellers</option>
                                                                @foreach($sellers as $row)
                                                                <option value="{{$row->id}}" {{ in_array($row->id, request()->seller_id ?? []) ? 'selected' : ''}}>{{$row->first_name." ".$row->last_name."(".$row->code.")"}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <input type="submit" class="btn btn-primary" value="Search">
                                                            <button type="reset" class="btn btn-primary reset-search">Reset</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <a href="{{ route('administrator.pendingManifestOrder.export') }}" class="btn btn-primary">Export All</a>
                                        </div>
                                    </div>
                                    <div class="table-responsive">

                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Sr.No</th>
                                                    <th>Seller ID</th>
                                                    <th>Seller Code</th>
                                                    <th>Seller Email</th>
                                                    <th>Total Pending Orders</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php($cnt=$manifest->firstItem())
                                                @forelse($manifest as $row)
                                                <tr id="row-{{$row->id}}">
                                                    <td>{{$cnt++}}</td>
                                                    <td>{{$row->seller_id}}</td>
                                                    <td>{{$row->seller->code}}</td>
                                                    <td>{{$row->seller->email}}</td>
                                                    <td>{{$row->total_orders}}</td>
                                                    <td><a href="{{ route('administrator.pendingManifestOrder', ['seller' => $row->seller_id]) }}" class="btn btn-primary btn-sm">View</a> <a href="{{ route('administrator.pendingManifestOrder.export', ['seller' => $row->seller_id]) }}" class="btn btn-primary btn-sm">Export</a></td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5">No Data</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    {{$manifest->links()}}

                                </div>
                                <!-- /.card-body -->
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>

        <!-- /.content-wrapper -->
        @include('admin.pages.footer')

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    @include('admin.pages.scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#seller_id').select2({
                placeholder: "Select seller",
                allowClear: true
            });

            $(".reset-search").click(function() {
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('seller_id[]')) history.back();
            });
        });
    </script>

</body>

</html>
