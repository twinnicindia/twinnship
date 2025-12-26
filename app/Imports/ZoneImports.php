<?php

namespace App\Imports;

use App\Http\Controllers\Utilities;
use App\Models\RatesCardRequest;
use App\Models\RateCardRequestData;
use App\Models\ZoneMapping;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Rates;
use Exception;

class ZoneImports implements ToCollection, WithHeadingRow, SkipsEmptyRows {
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows) {
        try {
            $utilities = new Utilities();
            DB::beginTransaction();
            //dd($rows);
            foreach ($rows->toArray() as $row) {
                $data = [
                    'city' => $row['city'],
                    'state' => $row['state'],
                    'pincode' => $row['pincode'],
                    'picker_zone' => $row['picker_zone'],
                ];

                ZoneMapping::create($data);
            }
            $utilities->generate_notification('Success', 'Excel file imported successfully', 'success');

            // Commit transaction
            DB::commit();
        } catch(Exception $e) {
            // Rollback transaction
            DB::rollBack();
            throw new Exception($e->getMessage()."-".$e->getFile()."-".$e->getLine());
        }
    }
}
