<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Configuration extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table='configuration',$timestamps=false;
    protected $fillable = [
        'title',
        'email',
        'mobile',
        'address',
        'meta_keyword',
        'meta_description',
        'logo',
        'favicon',
        'copyright',
        'analytics_code',
        'login_message',
        'register_message',
        'forget_message',
        'logistic_partner',
        'channel_partner',
        'brands',
        'press_coverage',
        'testimonial_image',
        'account_details',
        'about',
        'working_hour',
        'agreement',
        'stats_title',
        'associates_title',
        'steps_title',
        'signup_title',
        'ease_title',
        'logistics_title',
        'brand_title',
        'press_title',
        'channel_title',
        'subscribe_title',
        'e_cod_title',
        'e_cod_features',
        'reconciliation_days',
        'rto_charge',
        'reverse_charge',
        'account_holder',
        'account_number',
        'ifsc_code',
        'bank_name',
        'bank_branch',
        'gstin',
        'cin_number',
        'irn_number',
        'pan_number',
        'signature_image',
        'gst_percent',
        'invoice_generate_days',
        'hsn_number',
        'sac_number',
        'razorpay_key',
        'razorpay_secret',
        'payment_qrcode',
        'ekart_awb',
        'last_report_date',
        'qc_charges',
        'bulkship_limit',
        'mis_download_limit',
        'send_reassignment_email'
    ];
}
