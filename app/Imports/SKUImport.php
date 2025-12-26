<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\SKU;

class SKUImport implements ToCollection, WithHeadingRow, SkipsEmptyRows {
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection) {
        try {
            $validator = Validator::make($rows->toArray(), [
                '*.seller_id' => 'required|exists:sellers,id',
                '*.product_sku' => 'required',
                '*.product_name' => 'required',
                '*.product_weight' => 'required',
                '*.product_length' => 'required',
                '*.product_width' => 'required',
                '*.product_height' => 'required',
            ]);

            if($validator->stopOnFirstFailure()->fails()) {
                throw new Exception($validator->errors()->first());
            }
            // Start transaction
            DB::beginTransaction();

            foreach($rows->toArray() as $row) {
                // Delete all previous skus
                SKU::where('seller_id', $row['seller_id'])->delete();

                $sku = new SKU();
                $sku->seller_id = $row['seller_id'];
                $sku->sku = $row['product_sku'];
                $sku->product_name = $row['product_name'];
                $sku->weight = $row['product_weight'];
                $sku->length = $row['product_length'];
                $sku->width = $row['product_width'];
                $sku->height = $row['product_height'];
                $sku->save();
            }
            // Commit transaction
            DB::commit();
        } catch(Exception $e) {
            // Rollback transaction
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
