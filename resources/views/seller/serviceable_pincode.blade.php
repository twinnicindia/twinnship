<?php error_reporting(0); ?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Serviceable Pincode | {{$config->title}}</title>

    @include('seller.pages.styles')

</head>

<body>
    <div class="container-fluid user-dashboard">
        @include('seller.pages.header')
        @include('seller.pages.sidebar')
        <div class="content-wrapper">
            <div class="content-inner">
                <div class="card">
                    <div class="card-body">
                        <h3 class="h4 mb-4">Serviceable Pincode</h3>
                        <form action="{{route('seller.download_serviceable_pincode')}}" method="post">
                            <div class="row">
                                @csrf
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="seller_id">Select Courier Partner</label>
                                        <select name="courier_partner" class="form-control" id="courier_partner">
                                            @foreach($courier_partner as $c)
                                            <option value="{{$c->courier_partner}}">{{$PartnerName[$c->courier_partner]}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mt-2">
                                        <br>
                                        <button type="submit" class="btn btn-primary">Download</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('seller.pages.scripts')
</body>

</html>