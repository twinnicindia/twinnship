<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @include('seller.pages.styles')

    <title>Courier Preferences | {{$config->title}}</title>
</head>

<body>

@include('seller.pages.header')

@include('seller.pages.side_links')


<div class="container-fluid">
    <div class="main-content d-flex flex-column">
        <div class="content-wrapper">
            <div class="card p-4">
                <div class="content-inner" id="form_div">

                    <h4 class="mb-4 ">Courier Partner Preferences</h4>
                    <form id="courier_partner" method="post" action="{{route('seller.set_courier_partner')}}">
                        @csrf
                        <input type="hidden" name="seller_id" value="{{$modify->id}}">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-4">
                                    <label class="label">Priority 1</label>
                                    <div class="form-group position-relative">
                                        <select name="courier_priority_1" required="" class="form-select form-control Default bg-white w-100 h-30" aria-label="Default select example" data-id="1"  id="courier_priority_1">
                                            <option value="" class="text-dark">Select</option>
                                            @foreach($partner as $p)
                                                <option class="text-dark" value="{{$p->keyword}}" @if($p->keyword == $modify->courier_priority_1) selected @endif>{{$p->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-4">
                                    <label class="label">Priority 2</label>
                                    <div class="form-group position-relative">
                                        <select name="courier_priority_2" required="" class="form-select form-control Default bg-white w-100 h-30" aria-label="Default select example" data-id="2" id="courier_priority_2">
                                            <option value="" class="text-dark">Select</option>
                                            @foreach($partner as $p)
                                                <option class="text-dark" value="{{$p->keyword}}" @if($p->keyword == $modify->courier_priority_2) selected @endif>{{$p->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-4">
                                    <label class="label">Priority 3</label>
                                    <div class="form-group position-relative">
                                        <select name="courier_priority_3" required="" class="form-select form-control Default bg-white w-100 h-30" aria-label="Default select example" data-id="3" id="courier_priority_3">
                                            <option value="" class="text-dark">Select</option>
                                            @foreach($partner as $p)
                                                <option class="text-dark" value="{{$p->keyword}}" @if($p->keyword == $modify->courier_priority_3) selected @endif>{{$p->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-4">
                                    <label class="label">Priority 4</label>
                                    <div class="form-group position-relative">
                                        <select name="courier_priority_4" required="" class="form-select form-control Default bg-white w-100 h-30" aria-label="Default select example" data-id="4" id="courier_priority_4">
                                            <option value="" class="text-dark">Select</option>
                                            @foreach($partner as $p)
                                                <option class="text-dark" value="{{$p->keyword}}" @if($p->keyword == $modify->courier_priority_4) selected @endif>{{$p->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div style="overflow:auto;">
                                <div style="float:right;">
                                    <button type="submit" class="btn btn-primary" id="saveBasicInformationButton">Submit</button>
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
<script>
    $(document).ready(function () {

        $(".custom-select").on("change", function () {
            // Enable all options
            $("option").prop("disabled", false);

            // Get an array of all current selections
            var selected = [];
            $("select").each(function () {
                selected.push($(this).val());
            });

            // Disable all selected options, except the current showing one, from all selects
            $("select").each(function () {
                for (var i = 0; i < selected.length; i++) {
                    if (selected[i] != $(this).val()) {
                        //$(this).find("option[value='" + selected[i] + "']").prop("disabled", true);
                    }
                }
            });
        });
    });
</script>
</body>

</html>
