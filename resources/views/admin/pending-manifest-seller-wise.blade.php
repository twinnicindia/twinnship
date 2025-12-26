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
                                                @foreach(request()->query() as $key => $val)
                                                <input type="text" name="{{ $key }}" value="{{ $val }}" hidden>
                                                @endforeach
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <input type="text" name="q" id="search" class="form-control" placeholder="Search..." value="{{ request()->q ?? '' }}">
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
                                            <a href="{{ route('admin.pendingManifestOrder.export', ['seller' => request()->seller]) }}" class="btn btn-primary">Export</a>
                                        </div>
                                    </div>
                                    <div class="table-responsive">

                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Sr.No</th>
                                                    <th>Seller ID</th>
                                                    <th>Seller Code</th>
                                                    <th>Order Number</th>
                                                    <th>Awb Number</th>
                                                    <th>Courier Partner</th>
                                                    <th>Awb Assigned Date</th>
                                                    <th>Status</th>
                                                    <th>Manifest Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php($cnt=$manifest->firstItem())
                                                @forelse($manifest as $row)
                                                <tr id="row-{{$row->id}}">
                                                    <td>{{$cnt++}}</td>
                                                    <td>{{$row->seller_id}}</td>
                                                    <td>{{$row->seller->code}}</td>
                                                    <td>{{$row->order_number}}</td>
                                                    <td>{{$row->awb_number}}</td>
                                                    <td>{{$PartnerName[$row->courier_partner]}}</td>
                                                    <td>{{$row->awb_assigned_date}}</td>
                                                    <td>{{$row->status}}</td>
                                                    <td>{{$row->manifest_sent}}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="9">No Data</td>
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
            $(".reset-search").click(function() {
                history.back();
            });
        });
    </script>

</body>

</html>
