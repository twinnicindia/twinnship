<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Rates;
use Exception;

class SellerRateCardImport implements ToCollection, WithHeadingRow, SkipsEmptyRows {
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows) {
        try {
            $validator = Validator::make($rows->toArray(), [
                '*.plan_id' => 'required|exists:plans,id',
                '*.seller_id' => 'required|exists:sellers,id',
                '*.partner_id' => 'required|exists:partners,id',
                '*.within_city' => 'required|numeric',
                '*.within_state' => 'required|numeric',
                '*.metro_to_metro' => 'required|numeric',
                '*.rest_india' => 'required|numeric',
                '*.north_j_k' => 'required|numeric',
                '*.cod_charge' => 'required|numeric',
                '*.cod_maintenance' => 'required|numeric',
                '*.extra_charge_a' => 'required|numeric',
                '*.extra_charge_b' => 'required|numeric',
                '*.extra_charge_c' => 'required|numeric',
                '*.extra_charge_d' => 'required|numeric',
                '*.extra_charge_e' => 'required|numeric',
            ]);

            if($validator->stopOnFirstFailure()->fails()) {
                throw new Exception($validator->errors()->first());
            }
            // Start transaction
            DB::beginTransaction();

            foreach($rows->toArray() as $row) {
                // Delete all previous skus
                // Rates::where('seller_id', $row['seller_id'])
                //     ->where('plan_id', $row['plan_id'])
                //     ->where('partner_id', $row['partner_id'])
                //     ->delete();

                $data = [
                    'plan_id' => $row['plan_id'],
                    'seller_id' => $row['seller_id'],
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
                    'inserted_by' => Session()->get('MyAdmin')->id,
                ];
                $oldData = Rates::where('plan_id', $row['plan_id'])
                    ->where('seller_id', $row['seller_id'])
                    ->where('partner_id', $row['partner_id'])
                    ->first() ?? [];
                if(!empty($oldData)) {
                    $oldData = $oldData->toArray();
                }
                $changedData = array_diff_assoc($data, $oldData);
                if(count($oldData) > 0) {
                    if(count($changedData) > 0) {
                        $changedData['modified'] = date('Y-m-d H:i:s');
                        $changedData['modified_by'] = Session()->get('MyAdmin')->id ?? null;
                    }
                } else {
                    $changedData['inserted'] = date('Y-m-d H:i:s');
                    $changedData['inserted_by'] = Session()->get('MyAdmin')->id ?? null;
                }
                // Update data
                Rates::updateOrCreate([
                    'plan_id' => $row['plan_id'],
                    'seller_id' => $row['seller_id'],
                    'partner_id' => $row['partner_id'],
                ], $changedData);
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
