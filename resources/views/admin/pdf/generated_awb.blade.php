<html>
    <head>
        <style type="text/css">
            @page { margin: 5px; }
            body { margin: 5px; }
        </style>
    </head>
    <body>
        <table>
            @foreach($awbs as $a)
            <tr>
                <td style="align-content: center;align-items: center;text-align: center;margin:0;">
                    <img style="min-width:150px;" src="data:image/png;base64,{{base64_encode(file_get_contents(url('/')."/barcode/test.php?code=$a->awb_number"))}}"><br>
                    {{$a->awb_number}}
                    <bR>
                    <bR>
                </td>
            </tr>
            @endforeach
        </table>
    </body>
</html>
