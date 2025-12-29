<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Channels\ShopifyAPIController;
use App\Http\Controllers\MicroServices\DashboardAPIs;
use App\Http\Controllers\OMSGuruApi;
use App\Http\Controllers\ONDCApi;
use App\Http\Controllers\WhatsAppApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnicommerceApi;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('process-order-report',[ApiController::class,'processExportReport'])->name('api.processExportOrder');

Route::post('order-create', [ApiController::class, 'createOrder'])->name('api.createOrder');
Route::post('order-create-with-pickup', [ApiController::class, 'createOrderWithPickup']);
Route::post('reverse-with-qc-order-create', [ApiController::class, 'createReverseOrderWithQc'])->name('api.createReverseOrder');
Route::post('reverse-order-create', [ApiController::class, 'createReverseOrder'])->name('api.createReverseOrder');
Route::post('order-update', [ApiController::class, 'updateOrder'])->name('api.updateOrder');
Route::post('order-track', [ApiController::class, 'trackOrder'])->name('api.trackOrder');
Route::post('track-orders', [ApiController::class, 'trackBulkOrder'])->name('api.trackBulkOrder');
Route::post('track-order-by-id', [ApiController::class, 'trackOrderById'])->name('api.trackOrderById');
Route::post('track-orders-by-id', [ApiController::class, 'trackBulkOrderById'])->name('api.trackBulkOrderById');
Route::post('order-cancel', [ApiController::class, 'cancelOrder'])->name('api.cancelOrder');
Route::post('order-cancel-by-awb', [ApiController::class, 'cancelOrderByAwb'])->name('api.cancelOrderByAwb');
Route::post('order-ship', [ApiController::class, 'shipOrder'])->name('api.shipOrder');
Route::post('order-ship-courier', [ApiController::class, 'shipOrderCourier'])->name('api.shipOrderCourier');
Route::post('order-ship-cheapest', [ApiController::class, 'shipOrderCheapest'])->name('api.shipOrderCheapest');
Route::post('bulk-ship-orders', [ApiController::class, 'shipBulkOrder'])->name('api.shipBulkOrder');
Route::post('bulk-ship-orders-courier', [ApiController::class, 'shipBulkOrderCourier'])->name('api.shipBulkOrderCourier');
Route::post('generate-manifest', [ApiController::class, 'generateManifest'])->name('api.generateManifest');
Route::post('generate-bulk-manifest', [ApiController::class, 'generateBulkManifest'])->name('api.generateBulkManifest');
Route::post('serviceable-pincode', [ApiController::class, 'serviceablePincode'])->name('api.serviceablePincode');
Route::get('download-label',[ApiController::class,'downloadAwbLabel']);
Route::post('get-all-partners',[ApiController::class,'getAllCourierPartner']);
Route::get('download-label-encoded',[ApiController::class,'downloadAwbLabelEncoded']);
Route::post('get-order-id-from-number',[ApiController::class,'getOrderIdFromNumber']);
Route::get('download-zone-mappings/{pincode}',[ApiController::class,'downloadZoneMapping']);
Route::get('get-routing-code/{awb}',[ApiController::class,'getRoutingCode']);
Route::post('check-serviceable-pincode',[ApiController::class,'checkPincodeServiceability']);
Route::post('/check-pincode-pair', [ApiController::class, 'checkPincodeServiceable'])->name('/checkPincodePair');
Route::post('order-track-by-order-number', [ApiController::class, 'trackOrderByOrderNumber']);