<?php

namespace App\Imports;

use App\Http\Controllers\Utilities;
use App\Models\RatesCardRequest;
use App\Models\RateCardRequestData;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Rates;
use Exception;

class SellerRateCardImports implements ToCollection, WithHeadingRow, SkipsEmptyRows {
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows) {
        try {
            $utilities = new Utilities();
            DB::beginTransaction();
            $allRequestData = RatesCardRequest::where('seller_id',$rows[0]['seller_id'])->where('status','pending')->get();
            if($allRequestData == null || count($allRequestData) == 0)
            {
                $dataRequest = [
                    'plan_id' => $rows[0]['plan_id'],
                    'seller_id' => $rows[0]['seller_id'],
                    'created' => date('Y-m-d H:i:s'),
                    'created_by' => Session()->get('MySales')->id,
                    'status' => "pending",
                    'status_datetime' => date('Y-m-d H:i:s')
                ];
                $rateRequestId = RatesCardRequest::create($dataRequest)->id;
                foreach ($rows->toArray() as $row) {
                    $data = [
                        'plan_id' => $row['plan_id'],
                        'seller_id' => $row['seller_id'],
                        'request_id' => $rateRequestId,
                        'partner_id' => $row['partner_id'],
                        'within_city' => $row['within_city'],
                        'within_state' => $row['within_state'],
                        'metro_to_metro' => $row['metro_to_metro'],
                        'rest_india' => $row['rest_india'],
                        'north_j_k' => $row['north_j_k'],
                        'cod_charge' => $row['cod_charge'],
                        'cod_maintenance' => $row['cod_maintenance'],
                        'extra_charge_a' => $row['extra_charge_a'],
                        'extra_charge_b' => $row['extra_charge_b'],
                        'extra_charge_c' => $row['extra_charge_c'],
                        'extra_charge_d' => $row['extra_charge_d'],
                        'extra_charge_e' => $row['extra_charge_e'],
                        'inserted' => date('Y-m-d H:i:s'),
                        'inserted_by' => Session()->get('MySales')->id,
                    ];
                    RateCardRequestData::create([
                            'plan_id' => $row['plan_id'],
                            'seller_id' => $row['seller_id'],
                            'partner_id' => $row['partner_id'],
                        ] + $data);
                }
                $utilities->generate_notification('Success', 'Excel file imported successfully', 'success');
            }
            else{
                $utilities->generate_notification('Error', 'Already a pending request for the seller please try after previous request is approved', 'error');
            }
            // Commit transaction
            DB::commit();
        } catch(Exception $e) {
            // Rollback transaction
            DB::rollBack();
            throw new Exception($e->getMessage()."-".$e->getFile()."-".$e->getLine());
        }
    }
}
