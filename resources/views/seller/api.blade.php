<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @include('seller.pages.styles')

    <title>API Key | {{$config->title}}</title>
</head>

<body>

@include('seller.pages.header')

@include('seller.pages.side_links')


<div class="container-fluid">
    <div class="main-content d-flex flex-column">
        <div class="content-wrapper">
            <div class="content-inner">
                <div class="card">
                    <div class="card-body">
                        <h3 class="h4 mb-4">Twinnship API</h3>
                        <div class="row mt-3">
                            <div class="col-sm-12 mb-3">
                                <p><b>Expand and automate your online business with Twinnship API.</b></p>
                                <h6 class="mb-3">Your API Key: <span class="text-muted">{{Session()->get('MySeller')->api_key}}</span></h6>
                                <form method="post" action="{{route('seller.generate_api_key')}}" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    <button type="submit" name="generate" value="generate" class="btn btn-primary btn-sm">Generate API Key</button>
                                </form>
                            </div>
                            <div class="col-sm-12 mb-3">
                                <h6 class=""> API Documentation: <span class="text-muted"></span></h6>
                                <div class="col-sm-1">
                                    <a type="submit" target="_blank" href="https://documenter.getpostman.com/view/39053816/2sAYHzG3N2" class="btn btn-primary btn-sm">Click Here</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('seller.pages.scripts')
</body>

</html>
