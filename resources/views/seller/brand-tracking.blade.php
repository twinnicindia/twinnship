<?php error_reporting(0); ?>
    <!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Branded Tracking | {{$config->title}}</title>
    @include('seller.pages.styles')

    <style>
        .pt30{padding-top: 30px;}
        .pb20{padding-bottom: 20px;}
        .txt-right{text-align: right;}

        .mb50{margin-bottom: 50px;}
        .ptb-30{padding: 30px 0px;}
        .p0{padding: 0px !important;}
        .dark-bg1 {
            background: #7C1D4D;
        }


    </style>
</head>

<body>
<div class="container-fluid user-dashboard">
    @include('seller.pages.header')
    @include('seller.pages.sidebar')

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Branded Tracking</h3>
                    </div>

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12 pt30 pb20"><h4>Branding Information</h4></div>
                        </div>
                        <form role="form" id="quickForm" action="{{route('seller.submit_brand_track')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" class="form-control" id="id" placeholder="id" value="{{$brand_tracking->id ?? ""}}">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="title">Upload logo (size=130X50)</label>
                                        <input type="file" name="brand_logo" id="brand_logo" class="form-control">
                                        <label class="offerLabel" style="color: red"></label>
                                        @if($brand_tracking->brand_logo != null)
                                            <a href="{{asset($brand_tracking->brand_logo ?? "")}}" target="_blank">View Image</a>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="banner1">Branding Banner size=(1920x950)</label>
                                        <input type="file" name="banner1" id="banner1" class="form-control">
                                        <label class="offerLabel" style="color: red"></label>
                                        @if($brand_tracking->banner1 != null)
                                            <a href="{{asset($brand_tracking->banner1 ?? "")}}" target="_blank">View Image</a>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="offer_title">Discount</label>
                                        <input type="text" class="form-control" name="offer_title" id="offer_title" value="{{$brand_tracking->offer_title ?? ""}}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="title">Ads Banner size=(1920X650)</label>
                                        <input type="file" name="banner2" id="banner2" class="form-control">
                                        <label class="offerLabel" style="color: red"></label>
                                        @if($brand_tracking->banner2 != null)
                                            <a href="{{asset($brand_tracking->banner2 ?? "")}}" target="_blank">View Image</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row pt30 pb20">
                                <div class="col-md-10"><h4>Best Selling Products Information</h4>
                                </div>
{{--                                <div class="col-md-2 txt-right">--}}
{{--                                    <button type="button" class="btn btn-primary" >--}}
{{--                                        Add New Products--}}
{{--                                    </button>--}}
{{--                                </div>--}}
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="title">Product Name</label>
                                        <input type="text" name="product_title1" id="product_title1" class="form-control" value="{{$brand_tracking->product_title1 ?? ""}}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="title">Product Link</label>
                                        <input type="text" name="link" id="link" class="form-control" value="{{$brand_tracking->link ?? ""}}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="title">Product Price</label>
                                        <input type="text" id="product_amount1" name="product_amount1" class="form-control" value="{{$brand_tracking->product_amount1 ?? "" }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="title">Product Image size=(700X900)</label>
                                        <input type="file" name="product_image1" id="product_image1" class="form-control">
                                        <label class="offerLabel" style="color: red"></label>
                                        @if($brand_tracking->product_image1 != null)
                                            <a href="{{asset($brand_tracking->product_image1 ?? "")}}" target="_blank">View Image</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="title">Product Back Image size=(700X900)</label>
                                        <input type="file" name="product_back_image1" id="product_back_image1" class="form-control">
                                        <label class="offerLabel" style="color: red"></label>
                                        @if($brand_tracking->product_back_image1 != null)
                                            <a href="{{asset($brand_tracking->product_back_image1 ?? "")}}" target="_blank">View Image</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="title">Product Name</label>
                                        <input type="text" name="product_title2" id="product_title2" class="form-control" value="{{$brand_tracking->product_title2 ?? ""}}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="title">Product Link</label>
                                        <input type="text" name="link1" id="link1" class="form-control" value="{{$brand_tracking->link1 ?? "" }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="title">Product Price</label>
                                        <input type="text" id="product_amount2" name="product_amount2" class="form-control" value="{{$brand_tracking->product_amount2 ?? "" }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="title">Product Image size=(700X900)</label>
                                        <input type="file" name="product_image2" id="product_image2" class="form-control">
                                        <label class="offerLabel" style="color: red"></label>
                                        @if($brand_tracking->product_image2 != null)
                                            <a href="{{asset($brand_tracking->product_image2 ?? "")}}" target="_blank">View Image</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="title">Product Back Image size=(700X900)</label>
                                        <input type="file" name="product_back_image2" id="product_back_image2" class="form-control">
                                        <label class="offerLabel" style="color: red"></label>
                                        @if($brand_tracking->product_back_image2 != null)
                                            <a href="{{asset($brand_tracking->product_back_image2 ?? "")}}" target="_blank">View Image</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="title">Product Name</label>
                                        <input type="text" name="product_title3" id="product_title3" class="form-control" value="{{$brand_tracking->product_title3 ?? "" }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="title">Product Link</label>
                                        <input type="text" name="link2" id="link2" class="form-control" value="{{$brand_tracking->link2 ?? ""}}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="title">Product Price</label>
                                        <input type="text" id="product_amount3" name="product_amount3" class="form-control" value="{{$brand_tracking->product_amount3 ?? ""}}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="title">Product Image size=(700X900)</label>
                                        <input type="file" name="product_image3" id="product_image3" class="form-control">
                                        <label class="offerLabel" style="color: red"></label>
                                        @if($brand_tracking->product_image3 != null)
                                            <a href="{{asset($brand_tracking->product_image3 ?? "")}}" target="_blank">View Image</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="title">Product Back Image size=(700X900)</label>
                                        <input type="file" name="product_back_image3" id="product_back_image3" class="form-control">
                                        <label class="offerLabel" style="color: red"></label>
                                        @if($brand_tracking->product_back_image3 != null)
                                            <a href="{{asset($brand_tracking->product_back_image3 ?? "")}}" target="_blank">View Image</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="title">Product Name</label>
                                        <input type="text" name="product_title4" id="product_title4" class="form-control" value="{{$brand_tracking->product_title4 ?? "" }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="title">Product Link</label>
                                        <input type="text" name="link3" id="link3" class="form-control" value="{{$brand_tracking->link3 ?? "" }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="title">Product Price</label>
                                        <input type="text" id="product_amount4" name="product_amount4" class="form-control" value="{{$brand_tracking->product_amount4 ?? "" }}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="title">Product Image size=(700X900)</label>
                                        <input type="file" name="product_image4" id="product_image4" class="form-control">
                                        <label class="offerLabel" style="color: red"></label>
                                        @if($brand_tracking->product_image4 != null)
                                            <a href="{{asset($brand_tracking->product_image4 ?? "")}}" target="_blank">View Image</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="title">Product Back Image size=(700X900)</label>
                                        <input type="file" name="product_back_image4" id="product_back_image4" class="form-control">
                                        <label class="offerLabel" style="color: red"></label>
                                        @if($brand_tracking->product_back_image4 != null)
                                            <a href="{{asset($brand_tracking->product_back_image4 ?? "")}}" target="_blank">View Image</a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb50 pt30">
                                    <div class="group-ui">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
                                            Preview
                                        </button>


                                        <button type="submit" class="btn btn-primary" >
                                            Submit
                                        </button>
                                    </div>
                                    <!-- Button trigger modal -->
                                    <!-- Modal -->
                                    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLongTitle">Branding page preview</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <h3>Preview here</h3>
                                                    <div class="container">
                                                        <div class="content-wrapper" >
                                                            <div class="content-inner">
                                                                <div class="card">
                                                                    <div class="card-body">
                                                                        <section class="p0">
                                                                            <img src="{{asset($brand_tracking->brand_logo ?? "")}}" alt="" />
                                                                        </section>
                                                                        <br>
                                                                        <section class="p0">
                                                                            <img src="{{asset($brand_tracking->banner1 ?? "")}}" alt="" style="width: 100%;"/>
                                                                        </section>
                                                                        <section class="dark-bg1 padding-25px-tb">
                                                                            <div class="container">
                                                                                <div class="row align-items-center justify-content-center">
                                                                                    <div class="col-12 col-xl-12 col-lg-12 text-center">
                                                                                        <span class="text-white" style="font-size: 7px">{{$brand_tracking->offer_title ?? ""}}</span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </section>
                                                                        <br>
                                                                        <section class="p0">
                                                                            @if($brand_tracking->brand_logo != null)
                                                                                <img src="{{url('/')}}/public/assets/images/awbData.PNG" alt="" width="100%"/>
                                                                            @endif
                                                                        </section>
                                                                        <br>
                                                                        <section class="p0">
                                                                            <img src="{{asset($brand_tracking->banner2 ?? "")}}" alt="" style="width: 100%;"/>
                                                                        </section>
                                                                        <br>
                                                                        <section class="p0">
                                                                            <h6 class="text-center">Best Selling Products</h6>
                                                                            <img src="{{asset($brand_tracking->product_image1 ?? "")}}" alt="" style="width: 100px; margin: 15px;"/>
                                                                            <img src="{{asset($brand_tracking->product_image2 ?? "")}}" alt="" style="width: 100px; margin: 15px;"/>
                                                                            <img src="{{asset($brand_tracking->product_image3 ?? "")}}" alt="" style="width: 100px; margin: 15px;"/>
                                                                            <img src="{{asset($brand_tracking->product_image4 ?? "")}}" alt="" style="width: 100px; margin: 15px"/>
                                                                        </section>
                                                                        <section class="p0">
                                                                            <span style="font-size: 8px;font-weight: bold; margin-left: 40px;">{{$brand_tracking->product_title1 ?? ""}}</span>
                                                                            <span style="font-size: 8px;font-weight: bold; margin-left: 70px;">{{$brand_tracking->product_title2 ?? ""}}</span>
                                                                            <span style="font-size: 8px;font-weight: bold; margin-left: 70px;">{{$brand_tracking->product_title3 ?? ""}}</span>
                                                                            <span style="font-size: 8px;font-weight: bold; margin-left: 70px;">{{$brand_tracking->product_title4 ?? ""}}</span>
                                                                        </section>
                                                                        <section class="p0">
                                                                            <span style="font-size: 8px;margin-left: 50px;">₹{{$brand_tracking->product_amount1 ?? ""}}</span>
                                                                            <span style="font-size: 8px;margin-left: 100px;">₹{{$brand_tracking->product_amount2 ?? ""}}</span>
                                                                            <span style="font-size: 8px;margin-left: 100px;">₹{{$brand_tracking->product_amount3 ?? ""}}</span>
                                                                            <span style="font-size: 8px;margin-left: 100px;">₹{{$brand_tracking->product_amount4 ?? ""}}</span>
                                                                        </section>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
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
<script>
    $(document).ready(function() {
        $("#brand_logo").change(function() {
            validateImageDimensions(this, 130, 50, 130, 50);
        });
        $("#banner1").change(function() {
            validateImageDimensions(this, 1920, 950, 1920, 950);
        });
        $("#banner2").change(function() {
            validateImageDimensions(this, 1920, 650, 1920, 650);
        });

        $("#product_image1").change(function() {
            validateImageDimensions(this, 700, 900, 700, 900);
        });
        $("#product_image2").change(function() {
            validateImageDimensions(this, 700, 900, 700, 900);
        });
        $("#product_image3").change(function() {
            validateImageDimensions(this, 700, 900, 700, 900);
        });
        $("#product_image4").change(function() {
            validateImageDimensions(this, 700, 900, 700, 900);
        });
        $("#product_back_image1").change(function() {
            validateImageDimensions(this, 700, 900, 700, 900);
        });
        $("#product_back_image2").change(function() {
            validateImageDimensions(this, 700, 900, 700, 900);
        });
        $("#product_back_image3").change(function() {
            validateImageDimensions(this, 700, 900, 700, 900);
        });
        $("#product_back_image4").change(function() {
            validateImageDimensions(this, 700, 900, 700, 900);
        });

        function validateImageDimensions(inputElement, minWidth, minHeight, maxWidth, maxHeight) {
            var img = new Image();
            img.src = URL.createObjectURL(inputElement.files[0]);

            img.onload = function() {
                var width = this.width;
                var height = this.height;

                var $messageElement = $(inputElement).siblings('.offerLabel');

                if (width < minWidth || height < minHeight || width > maxWidth || height > maxHeight) {
                    $messageElement.text("Image dimensions must be between (" + minWidth + " x " + minHeight + ").");
                    inputElement.value = "";
                } else {
                    $messageElement.hide();
                }
            };
        }
    });


</script>

</html>
