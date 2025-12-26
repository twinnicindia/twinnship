<html>

<head>
    <title>Invoice</title>
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
    <style type="text/css" media="all">
        @page {
            margin: 0px;
        }

        .rs {
            width: 10px;
        }

        body {
            font-size: 11px;
            margin: 0;
            padding: 5mm;
            background: rgba(235, 235, 235, 1);
            background-color: rgba(235, 235, 235, 1);
            font-family: arial, sans-serif;
            line-height: 19px;
        }

        @media print {
            body {
                font-size: 11px;
                margin: 0;
                padding: 10mm;
                background: rgba(235, 235, 235, 1);
                background-color: rgba(235, 235, 235, 1);
                font-family: arial, sans-serif;
                line-height: 20px;
            }
        }

        .fluid-table {
            border: none;
            margin: 2mm 0;
        }

        .fluid-table th {
            border-bottom: 1px solid #7f7f7f;
            padding: 3mm;
            text-align: left;
        }

        .stripe-table tr:nth-child(even) td {
            background: #f8f8f8;
        }

        .fluid-table td {
            padding: 1mm;
        }

        .btn {
            width: 46mm;
            font-size: 16px;
            background: #285eda;
            color: #fff;
            border-style: ridge;
            border-color: blue;
            padding: 4mm;
            margin-right: 3mm;
            border-radius: 8mm;
            display: block;
            text-align: center;
            text-decoration: none;
            margin: 12px 0;
            font-family: arial, sans-serif;
        }

        ​ .btn:hover {
            cursor: pointer;
        }

        .tdpl10 td:first-child {
            padding-right: 20px;
        }

        .container {
            position: relative;
        }

        .topright {
            position: absolute;
            top: 21px;
            right: 35px;
            font-size: 18px;
            border-radius: 5px;


        }

        .link-btn {
            background-color: #285eda;
            color: white;
            padding: 3px !important;
            text-decoration: none;
            border-radius: 2px;
        }
    </style>
    <style>
        /* The Modal (background) */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1;
            /* Sit on top */
            padding-top: 300px;
            /* Location of the box */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgb(0, 0, 0);
            /* Fallback color */
            background-color: rgba(0, 0, 0, 0.62);
            /* Black w/ opacity */
        }

        /* Modal Content */
        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 40%;
        }

        /* The Close Button */
        .close {
            color: #aaaaaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            margin-top: -10px;
            margin-right: -10px;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>

<body style="padding-left:225px;padding-right:225px">
    <div class="container">
        <center style="border-radius: 10px;background: #fff;margin-bottom: 2mm">
            <p><i style="width:100px;height:100px;color:black" class="icon-login"></i></p>
            <table style="width: 100%;padding: 3mm" class="tdpl10">
                <tbody>
                    <tr>
                        <td colspan="2">
                            <!-- <img src="../images/logo.png" alt="" style="width: 200px;padding: 0px;"> -->
                            <img style="height:50px; padding-left:10px;" src="data:image/png;base64,{{base64_encode(file_get_contents(asset($config->logo)))}}">
                        </td>

                    </tr>
                    <tr>
                        <td style="width: 60%;padding-left: 25px;">
                            <p><b>{{$config->title}}</b> <br>
                                {{$config->address}} <br>
                            </p>
                        </td>
                        <td style="width: 40%;">
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                            <p>
                                                <strong>TAX INVOICE</strong> <br><br>
                                                <span style="font-size: 25px;
                                            color: green;
                                            font-weight: 600;">
                                                    {{$invoice->status}}</span>
                                                <br>
                                                <a href='{{url("administrator/billing/invoice/download/$invoice->id")}}' style="height: 17px;"> Download Invoice</a>
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </td>
                    </tr>
                    <tr>
                        <td style="width: 60%;padding-left: 25px;vertical-align: top;">
                            <strong>PAN Number:</strong> {{$config->pan_number}}<br>
                            <strong>CIN Number:</strong> {{$config->cin_number}}<br>
                            <strong>GSTIN:</strong> {{$config->gstin}}<br>
                            <strong>Phone:</strong> {{$config->mobile}} <br>
                            <strong>Email:</strong> {{$config->email}} <br>
                            <strong>IRN:</strong> {{$config->irn_number}}
                        </td>
                        <td style="width: 40%;vertical-align: top;">
                            <strong>Invoice No. : </strong> {{$invoice->inv_id}}<br>
                            <strong>Invoice Date :</strong> {{$invoice->invoice_date}}<br>
                            <strong>Due Date :</strong> {{$invoice->due_date}}<br>

                        </td>


                    </tr>
                    <tr>
                        <td colspan="2"><br>
                            <hr style="border:none;border-bottom: 1px solid #7f7f7f">
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 60%;padding-left: 25px;">
                            <strong>Invoice To:</strong><br>
                            {{$seller->company_name}}<br>
                            {{$seller_info->street ?? ''}},{{$seller_info->city ?? ''}},{{$seller_info->state ?? ''}},{{$seller_info->pincode ?? ''}}
                        </td>
                        <td style="width: 40%;">
                            <p>
                                <strong>State Code:</strong> {{$seller_info->state ?? ''}}<br>
                                <strong>Place of Supply:</strong> {{$seller_info->city ?? ''}}<br>
                                <strong>GSTIN:</strong> {{$seller_info->gst_number ?? ''}}<br>
                                <strong>Reverse Charge:</strong> No
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </center>


        <!-- <img src="https://s3-ap-southeast-1.amazonaws.com/kr-shipmultichannel/files/0f1bb329af8357da0d17113a87d1ebaf02d8da25f5bc9b195d53c8d47bed93f4.png" alt="" style="float: right;" class="topright"> -->
    </div>
    <center style="border-radius: 10px;padding: 0mm 0 2mm 0;background: #fff;">
        <table style="width: 100%;" rules="none" ellspacing="0" cellpadding="0" class="fluid-table stripe-table" border="0">
            <tbody>
                <tr>
                    <th style="padding-left: 60px;">SAC No.</th>
                    <th style="padding-left: 75px;">Description</th>
                    <th style="padding-right: 75px;text-align: right">Total</th>
                </tr>
                <tr>
                    <td style="padding-left: 50px;">{{$config->sac_number}}</td>
                    <td style="padding-left: 50px;">Twinnship V2 Freight<sup>*</sup></td>
                    <td style="padding-left: 50px;text-align: right;padding-right: 50px;">Rs. {{round($invoice->invoice_amount)}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td style="padding-left: 50px;">18.00% GST</td>
                    <td style="padding-left: 50px;text-align: right;padding-right: 50px;">Rs. {{round($invoice->gst_amount)}}</td>
                </tr>

                <tr>
                    <td></td>
                    <td style="padding-left: 50px;"><strong>Grand Total Value</strong></td>
                    <td style="padding-left: 50px;text-align: right;padding-right: 50px">Rs. {{$invoice->total}}</td>
                </tr>

            </tbody>
        </table>
    </center>
    <center style="border-radius: 10px;background: #fff;padding: 1mm;margin-top: 2mm">
        <table style="width: 100%;padding: 0" rules="none" ellspacing="0" cellpadding="0" class="fluid-table" border="0">
            <tbody>
                <tr>
                    <th colspan="2" style="padding-left: 50px;padding-top: 1.5mm!important;padding-bottom: 2mm!important;">Bank and Other Commercial Details</th>
                </tr>
                <tr>
                    <td style="padding-left: 50px;width: 70%;">

                        All Payments by transfer/check/DD should be draw in favour of<br>
                        <strong>Entity Name:</strong> {{$config->account_holder}}<br>
                        <strong>Account number:</strong> {{$config->account_number}}<br>
                        <strong>Bank:</strong> {{$config->bank_name}} <br>
                        <strong>Branch:</strong> {{$config->bank_branch}}<br>
                        <strong>RTGS/NEFT/IFSC Code:</strong> {{$config->ifsc_code}}<br>

                    </td>
                    <td style="text-align: bottom; width:30%">

                    </td>
                </tr>
            </tbody>
        </table>
    </center>

    <center style="border-radius: 10px;padding: 0mm 0 2mm 0;background: #fff;">
        <table style="width: 100%;" rules="none" ellspacing="0" cellpadding="0" class="fluid-table stripe-table" border="0">
            <tbody>
                <tr>
                    <th style="padding-left: 50px;">Transaction Date</th>
                    <th style="padding-left: 50px;">Gateway</th>
                    <th style="padding-left: 50px;">Transaction ID</th>
                    <th style="padding-left: 50px;">Amount</th>
                </tr>
                <tr>
                    <td style="padding-left: 50px;">15/01/2021</td>
                    <td style="padding-left: 50px;">Credit Balance</td>
                    <td style="padding-left: 50px;">NA</td>
                    <td style="padding-left: 50px;text-align: right;padding-right: 50px;">Rs. {{$invoice->total}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td style="padding-left: 50px;"><strong>Amount Due</strong></td>
                    <td style="padding-left: 50px;text-align: right;padding-right: 50px">Rs. 0.00</td>
                </tr>

            </tbody>
        </table>
    </center>
<br>
    <center style="border-radius: 10px;padding: 1mm;background: #fff;text-align: left;word-break: break-all;">
        <p style="padding-left: 50px;">Download Itemized Shipment Details: <a href='{{url("administrator/billing/invoice/csv/$invoice->id")}}' class="link-btn">Download Now</a>
        </p>

    </center>
    <p>* Indicates taxable item *This is an system generated invoice does not need signature</p>


    <div id="myModal" class="modal">

        <!-- Modal content -->
        <div class="modal-content">
            <span class="close">×</span>
            <p>This payment cannot be processed as the payable amount is more than our Payment Gateway's limit. Kindly make the payment using <strong>NEFT/RTGS/Cheque</strong>.</p>
        </div>

    </div>

</body>

</html>
