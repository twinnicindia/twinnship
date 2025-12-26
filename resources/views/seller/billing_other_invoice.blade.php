<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Twinship - Tax Invoice-BFRS-530006</title>

    <!-- Bootstrap -->
    <!-- <link rel="stylesheet" href="{{asset('public/assets/seller/')}}/css/bootstrap.min.css" type="text/css"> -->
    <!-- <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"> -->

    <!-- Styling -->

    <!-- <link href="{{asset('public/assets/seller/')}}/css/invoice_style.css" rel="stylesheet"> -->
    <style>
    /*# sourceMappingURL=bootstrap.min.css.map */
        body {
            background-color: #efefef;
        }

        /* Container Responsive Behaviour */

        .invoice-container {
            margin: 15px auto;
            padding: 70px;
            max-width: 850px;
            background-color: #fff;
            border: 1px solid #ccc;
            -moz-border-radius: 6px;
            -webkit-border-radius: 6px;
            -o-border-radius: 6px;
            border-radius: 6px;
        }

        @media (max-width: 895px) {
            .invoice-container {
                margin: 15px;
            }
        }

        @media (max-width: 767px) {
            .invoice-container {
                padding: 45px 45px 70px 45px;
            }
        }

        /* Invoice Status Formatting */

        .invoice-container .invoice-status {
            margin: 20px 0 0 0;
            text-transform: uppercase;
            font-size: 24px;
            font-weight: bold;
        }

        /* Invoice Status Colors */

        .draft {
            color: #888;
        }

        .unpaid {
            color: #cc0000;
        }

        .paid {
            color: #779500;
        }

        .refunded {
            color: #224488;
        }

        .cancelled {
            color: #888;
        }

        .collections {
            color: #ffcc00;
        }

        /* Payment Button Formatting */

        .invoice-container .payment-btn-container {
            margin-top: 5px;
            text-align: center;
        }

        .invoice-container .payment-btn-container table {
            margin: 0 auto;
        }

        /* Text Formatting */

        .invoice-container .small-text {
            font-size: 0.9em;
        }

        /* Invoice Items Table Formatting */

        .invoice-container td.total-row {
            background-color: #f8f8f8;
        }

        .invoice-container td.no-line {
            border: 0;
        }


        @import url("//fonts.googleapis.com/css?family=Open+Sans:400,600|Raleway:400,700");

        body,
        input,
        button,
        select,
        textarea {
            font-family: "Open Sans", Verdana, Tahoma, serif;
            font-size: 16px;
            line-height: 1.42857143;
            color: #333333;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: "Raleway", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-weight: 700;
        }

        .navbar-main {
            margin-bottom: 0;
            background-color: #006687;
            border: 0;
            min-height: 38px;
            font-family: "Raleway", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 15px;
        }

        .navbar-main .navbar-nav>li>a {
            color: #fff;
        }

        .navbar-main .navbar-nav>li>a {
            padding-top: 9px;
            padding-bottom: 9px;
        }

        .navbar-main .navbar-nav>li>a:hover,
        .navbar-main .navbar-nav>li>a:focus,
        .navbar-main .navbar-nav>.active>a,
        .navbar-main .navbar-nav>.active>a:hover,
        .navbar-main .navbar-nav>.active>a:focus,
        .navbar-main .navbar-nav>.open>a,
        .navbar-main .navbar-nav>.open>a:hover,
        .navbar-main .navbar-nav>.open>a:focus {
            color: #222222;
            background-color: #eee;
        }

        #main-menu .navbar-nav {
            margin-left: -15px;
            margin-right: -15px;
        }

        @media (max-width: 768px) {
            .navbar-main .navbar-nav .open .dropdown-menu>li>a {
                color: #ddd;
            }

            .navbar-main .navbar-nav .open .dropdown-menu>li>a:hover {
                color: #fff;
            }
        }

        @media (max-width: 991px) {
            .navbar-header {
                float: none;
            }

            .navbar-left,
            .navbar-right {
                float: none !important;
            }

            .navbar-toggle {
                display: block;
            }

            .navbar-collapse {
                border-top: 1px solid transparent;
                box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1);
            }

            .navbar-fixed-top {
                top: 0;
                border-width: 0 0 1px;
            }

            .navbar-collapse.collapse {
                display: none !important;
            }

            .navbar-nav {
                float: none !important;
                margin-top: 7px;
            }

            .navbar-nav>li {
                float: none;
            }

            .navbar-nav>li>a {
                padding-top: 10px;
                padding-bottom: 10px;
            }

            .collapse.in {
                display: block !important;
            }
        }

        .panel {
            font-size: 0.9em;
        }

        .list-group {
            font-size: 0.9em;
        }

        a.list-group-item.active,
        a.list-group-item.active:hover,
        a.list-group-item.active:focus {
            background-color: #006687;
        }

        .pagination {
            font-size: 0.8em;
        }

        .pagination>.active>a,
        .pagination>.active>span,
        .pagination>.active>a:hover,
        .pagination>.active>span:hover,
        .pagination>.active>a:focus,
        .pagination>.active>span:focus {
            color: white;
            background-color: #006687;
            border-color: #006687;
        }

        .navbar-main .dropdown-menu>li>a:hover,
        .dropdown-menu>li>a:focus {
            text-decoration: none;
            color: #fff;
            background-color: #006687;
        }

        .label {
            border-radius: 0;
        }

        input[type='file'].form-control {
            height: inherit;
            margin-bottom: 5px;
        }

        .list-group-item {
            padding: 7px 15px;
        }

        .panel-body {
            padding: 10px 15px;
        }

        /*
 * We use a larger font-size than the bootstrap default, which requires an increase
 * in the top offset to maintain correct alignment in form control feedback.
 */
        .has-feedback label~.form-control-feedback {
            top: 27px !important;
        }

        .list-group-item>i.fa.fa-circle-o {
            color: #808080;
        }

        /*
 * Language strings which are too long to fit into buttons should be "chopped off" inside the div
 * of that button, instead of "overflowing" outside of the button.  CORE-9272
 */
        .btn {
            overflow: hidden;
        }

        .row {
            display: -ms-flexbox;
            display: flex;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }

        @media (min-width: 576px) .col-sm-12 {
            -ms-flex: 0 0 100%;
            flex: 0 0 100%;
            max-width: 100%;
        }

        .panel {
            font-size: 0.9em;
        }

        *,
        ::after,
        ::before {
            box-sizing: border-box;
        }


    </style>
</head>

<body>

    <div class="container-fluid invoice-container">
        <div class="row">
            <div style="text-align:right;">
            </div>
            <div class="col-sm-12">
                <p><img src="data:image/png;base64,{{base64_encode(file_get_contents(asset($config->logo)))}}" title="Twinship" style="height: 35px;"></p>
                <h3>Tax Invoice-SE{{date('ymdHi')}}-{{rand(100,999)}}</h3>
                <div></div>
            </div>
            <div style="margin-top: 25px;display: flex;justify-content: space-between;" class="col-sm-12">
                <div style="text-align:left;">
                </div>
                <div>
                    <div class="invoice-status" style="margin:0;">
                        <span class="refunded">Refunded</span>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-6 pull-sm-right text-right-sm">
                <strong>Pay To:</strong>
                <address class="small-text">
                    {{$config->title}},<br>
                    {{$config->address}}<br>
                    GSTIN: {{$config->gstin}}
                </address>
            </div>
            <div class="col-sm-6">
                <strong>Invoiced To:</strong>
                <address class="small-text">
                    {{$seller->company_name}}<br>
                    {{$seller_info->street}}<br>
                    {{$seller_info->city}}<br>
                    {{$seller_info->pincode}}
                    <br><br>
                    State Code: {{$seller_info->state}}<br>
                    GSTIN: {{$seller_info->gst_number}}<br>
                </address>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <strong>Payment Method:</strong><br>
                <span class="small-text">
                    EBS
                </span>
                <br><br>
            </div>
            <div class="col-sm-6 text-right-sm">
                <strong>Invoice Date:</strong><br>
                <span class="small-text">
                    {{date('d-M-Y',strtotime($invoice->invoice_date))}}<br><br>
                </span>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><strong>Invoice Items</strong></h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td width="20%">HSN</td>
                                <td><strong>Description</strong></td>
                                <td width="20%" class="text-center"><strong>Amount</strong></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{$config->hsn_number}}</td>
                                <td>Legal &amp; Penal Charges against AWB NO- {{$invoice->awb_number}} *</td>
                                <td class="text-center">Rs.{{$invoice->invoice_amount}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <!-- <td class="total-row text-right"><strong>Sub Total</strong></td> -->
                                <td class="total-row text-right"><strong>Total Taxable Value of Supply</strong></td>
                                <td class="total-row text-center">{{$invoice->invoice_amount}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="total-row text-right"><strong>18.00% IGST</strong></td>
                                <td class="total-row text-center">Rs.{{$invoice->gst_amount}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="total-row text-right">Total Value of Supply Including GST<strong>
                                    </strong></td>
                                <td class="total-row text-center">Rs.{{$invoice->total}}</td>
                            </tr>
                        </tbody>
                    </table>
                    * Indicates a taxed item.
                </div>
            </div>
        </div>

        <div class="transactions-container small-text">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <td class="text-center"><strong>Transaction Date</strong></td>
                            <td class="text-center"><strong>Gateway</strong></td>
                            <td class="text-center"><strong>Transaction ID</strong></td>
                            <td class="text-center"><strong>Amount</strong></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">{{date('d/m/Y',strtotime($invoice->invoice_date))}}</td>
                            <td class="text-center">Credit Balance</td>
                            <td class="text-center">N.A</td>
                            <td class="text-center">Rs.0.00</td>
                        </tr>
                        <tr>
                            <td class="text-right" colspan="3"><strong>Balance</strong></td>
                            <td class="text-center">Rs.{{$invoice->invoice_amount}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-body">
                Disclaimer: Please ignore this invoice if you have already upgraded to a Twinship SaaS plan through Shopify.
            </div>
        </div>

        <div class="pull-right btn-group btn-group-sm hidden-print">
            <a href="javascript:window.print()" class="btn btn-default"><i class="fa fa-print"></i> Print</a>
            <a href='{{url("billing_other_invoice/pdf/$invoice->id")}}' class="btn btn-default"><i class="fa fa-download"></i> Download</a>
        </div>
    </div>
</body>

</html>
