<?php

namespace App\Http\Controllers;

use App\Models\Admin_rights;
use App\Models\AramexRates;
use App\Models\Career;
use App\Models\Faq;
use App\Models\Order;
use App\Models\Partners;
use App\Models\Plans;
use App\Models\Rates;
use App\Models\RemittanceDetails;
use App\Models\Seller;
use App\Exports\SellerRateCardExport;
use App\Exports\SellerRateCardSampleFileExport;
use App\Imports\SellerRateCardImport;
use App\Models\XindusRates;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use PharData;

class GzFileUploadController extends Controller
{
    public $utilities;
    function __construct()
    {
        $this->utilities = new Utilities();
    }
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        return view('admin.web.gzfile',$data);
    }

    public function upload(Request $request)
    {
        $file = $request->image;

        if ($file) {
            $name = "exports/Logs";
            $filename = "Logs";
            $fp = fopen("$name.csv", 'w');
            $info = array('Sr no.', 'Date', 'IP Address 1','IP Address Port 1', 'IP Address 2','IP Address Port2', 'Url');
            fputcsv($fp, $info);
            $cnt = 1;
            for($i = 0;$i < count($file); $i++){
                $contents = file_get_contents($file[$i]->getRealPath());
                $fileContents = $contents;
                $lines = explode("\n", $fileContents);


                foreach ($lines as $line) {
                    if(!empty($line)){
                        $exploded = explode(" ", $line);

                        //1,3,4,12,13
                        $date = $exploded[1] ?? "";
                        $ipAddress = $exploded[3] ?? "";
                        $ipAddress1 = $exploded[4] ?? "";
                        $requestGet = $exploded[13] ?? "";
                        $requestMethod = $exploded[12] ?? "";
                        $Port1 = explode(":", $ipAddress);
                        $Port2 = explode(":", $ipAddress1);
                        $ipAddressPort1 = $Port1[1] ?? "";
                        $ipAddressPort2 = $Port2[1] ?? "";

                        $info = array($cnt, $date,$Port1[0],$ipAddressPort1,$Port2[0],$ipAddressPort2,$requestMethod." ".$requestGet);
                        fputcsv($fp, $info);
                        $cnt++;
                    }
                }
            }

            // Output headers.
            header("Cache-Control: private");
            header("Content-Type: text/csv");
            header("Content-Length: " . filesize("$name.csv"));
            header("Content-Disposition: attachment; filename=$filename.csv");
            // Output file.
            readfile("$name.csv");
            @unlink("$name.csv");

        }
        return "No file uploaded.";
    }
}
