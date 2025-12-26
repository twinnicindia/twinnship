<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Receipt</title>
    <!-- Latest compiled and minified CSS -->

    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"> -->
    <style>
        table,
        td,
        th {
            border: 1px solid black;
            padding: 7px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="invoice-title">
                    <h2 style="text-align: center;">Credit Note</h2>
                </div>
                <div class="row">
                        <strong>Credit Note No.: {{$receipt->note_number}}</strong><br>
                        Credit Note Date: {{$receipt->note_date}}<br>
                </div>
                <br>
                <div class="row" style="display: flex;  justify-content: space-between;">
                    <table style="border: none !important;">
                        <tr>
                            <td style="width:50%;border: none !important;"> <strong>TO</strong><br><br>
                                {{$basic_info->company_name}}<br>
                                {{$basic_info->street}}<br>
                                {{$basic_info->city}},{{$basic_info->state}}<br>
                            </td>
                            <td style="text-align:right;width:50%;border: none !important;"><strong>{{$config->title}}</strong><br><br>
                                <?= nl2br($config->address) ?><br>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <br>

        <div class="row">
            <table class="table table-bordered">
                <thead>
                    <tr style="text-align: left;">
                        <th>Description</th>
                        <th align="center">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Credit note issue against Lost Credit</td>
                        @php
                        $without_gst = round($receipt->total * 100 / 118,2);
                        @endphp
                        <td style="text-align: center;">Rs.{{$without_gst}}</td>
                    </tr>
                    <tr style="height:30px;">
                    <td> 18% GST</td>
                    <td align="center"> RS. {{$receipt->total - $without_gst}}</td>
                    </tr>
                    <tr>
                        <td style="text-align:right;"><strong>Total Credit Note Value</strong></td>
                        <td style="text-align: center;"><strong>Rs.{{$receipt->total}}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="row">
            <p>CIN number: {{$config->cin_number}}</p><a href='{{url("export_receipt_details/$receipt->receipt_id")}}'>click to view more details</a>
            <p>This is a system generated credit note and does not require a signature</p>
        </div>
     
    </div>
</body>

</html>