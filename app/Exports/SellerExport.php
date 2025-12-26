<?php

namespace App\Exports;

use App\Models\Seller;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class SellerExport implements FromCollection, WithHeadings {
    /**
     * Filter criteria
     * 
     * @var array
     */
    protected $filter;

    public function __construct(array $filter = []) {
        $this->filter = $filter;
    }

    /**
     * Set export header
     * 
     * @return array
     */
    public function headings(): array {
        return [
            'Seller Name',
            'Seller Code',
            'Company Name',
            'Email Id',
            'Contact Number',
            'GST Number',
            'Bank Name',
            'Account Holder Name',
            'Bank Account Number',
            'Address',
            'IFSC Number',
            'KYC Status'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        $sellers = DB::table('sellers')
            ->select(
                DB::raw("concat(sellers.first_name, sellers.last_name) as 'Seller Name'"),
                "sellers.code as 'Seller Code'",
                "basic_informations.company_name as 'Company Name'",
                "sellers.email as 'Seller Id'",
                "sellers.mobile as 'Contact Number'",
                "basic_informations.gst_number as 'GST Number'",
                "account_informations.bank_name as 'Bank Name'",
                "account_informations.account_holder_name as 'Account Holder Name'",
                "account_informations.account_number as 'Bank Account Number'",
                DB::raw("concat(basic_informations.street, ',', basic_informations.city, basic_informations.state, ',', basic_informations.pincode) as 'Address'"),
                "account_informations.ifsc_code as 'IFSC Number'",
                DB::raw("IF(sellers.verified = 'y', 'Verified', 'Not Verified') as 'KYC Status'")
            )
            ->join('basic_informations','basic_informations.seller_id','=','sellers.id')
            ->join('account_informations','account_informations.seller_id','=','sellers.id')
            ->orderBy('sellers.id', 'desc')
            ->get();
        return $sellers;
    }
}
