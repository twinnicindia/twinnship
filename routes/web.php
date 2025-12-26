<?php

use App\Http\Controllers\AboutusController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\BlogsController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\CareerExpectController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CountryChanelController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\CRMController;
use App\Http\Controllers\EcomExpressController;
use App\Http\Controllers\EcomExpress3kgController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\ChannelPartnersController;
use App\Http\Controllers\CoverageController;
use App\Http\Controllers\CroneController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\FeaturesController;
use App\Http\Controllers\FooterCategoryController;
use App\Http\Controllers\FooterSubController;
use App\Http\Controllers\GlossaryController;
use App\Http\Controllers\GzFileUploadController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OMSController;
use App\Http\Controllers\LogisticsController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ReconEngineController;
use App\Http\Controllers\RedeemController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ShopifyController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\StepsController;
use App\Http\Controllers\SupportChildController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\SupportSubController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\SellerAdminController;
use App\Http\Controllers\WebConfigController;
use App\Http\Controllers\WebController;
use App\Http\Controllers\Why_chooseController;
use App\Http\Middleware\AuthCRM;
use App\Http\Middleware\CheckSellerSession;
use App\Http\Controllers\RulesController;
use App\Http\Controllers\Utilities;
use App\Http\Middleware\CheckSession;
use App\Http\Middleware\AuthEmployee;
use App\Libraries\BlueDart;
use App\Libraries\BucketHelper;
use App\Libraries\MyUtility;
use App\Libraries\XpressBees;
use App\Models\Basic_informations;
use App\Models\COD_transactions;
use App\Models\Courier_blocking;
use App\Models\DownloadOrderReportModel;
use App\Models\Partners;
use App\Models\Product;
use App\Models\RemittanceDetails;
use App\Models\Seller;
use App\Models\XbeesAwbnumber;
use App\Models\XbeesAwbnumberUnique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckKYC;
use Illuminate\Support\Facades\Artisan;
use App\Models\Order;
use App\Models\Channels;
use Illuminate\Support\Facades\Http;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Razorpay\Api\Api;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Routes for the Main Website Look
Route::get('/login', [LoginController::class, 'index'])->name('/login');
Route::get('/', [PortalController::class, 'index'])->name('/');
Route::get('all-features', [PortalController::class, 'allFeatures'])->name('portal.all-features');
Route::get('amazon-self-ship', [PortalController::class, 'amazonSelfShip'])->name('portal.amazon-self-ship');
Route::get('ndr-management', [PortalController::class, 'ndrManagement'])->name('portal.ndr-management');
Route::get('early-cod', [PortalController::class, 'earlyCOD'])->name('portal.early-cod');
Route::get('all-faq', [PortalController::class, 'faq'])->name('portal.faq');
Route::get('damaged-shipment', [PortalController::class, 'damagedShipment'])->name('portal.damaged-shipment');
Route::get('hyper-local', [PortalController::class, 'hyperLocal'])->name('portal.hyper-local');
Route::get('pickup-locations', [PortalController::class, 'pickupLocations'])->name('portal.pickup-locations');
Route::get('shipment-protection', [PortalController::class, 'shipmentProtection'])->name('portal.shipment-protection');
Route::get('whatsapp-integration', [PortalController::class, 'whatsAppIntegration'])->name('portal.whatsapp-integration');
Route::get('referral-code', [PortalController::class, 'referralCode'])->name('portal.referral-code');

Route::get('plans-and-pricing', [PortalController::class, 'plansAndPricing'])->name('portal.plan-and-pricing');
Route::get('rate-calculators', [PortalController::class, 'rateCalculators'])->name('portal.rate-calculators');
Route::post('calculate-rate-form', [PortalController::class, 'calculateRateForUser'])->name('portal.calculate-rate-for-user');
Route::get('prepaid-and-cod', [PortalController::class, 'prepaidAndCOD'])->name('portal.prepaid-and-cod');
Route::get('carrier-integration', [PortalController::class, 'carrierIntegration'])->name('portal.carrier-integration');
Route::get('channel-integration', [PortalController::class, 'channelIntegration'])->name('portal.channel-integration');
Route::get('partner-integration', [PortalController::class, 'partnerIntegration'])->name('portal.partner-integration');
Route::get('refer-and-earn', [PortalController::class, 'referAndEarn'])->name('portal.refer-and-earn');
Route::get('order-tracking', [PortalController::class, 'trackOrder'])->name('portal.track-order');
Route::post('home-page-order-tracking', [PortalController::class, 'trackOrderHome'])->name('portal.home-page-order-tracking');
Route::get('order-tracking/{awb}', [PortalController::class, 'trackOrderDetail'])->name('portal.track-order-detail');
Route::get('track-order/{awb_number}', [PortalController::class, 'trackOrderDetail'])->name('web.track_order');
Route::get('single-order-tracking', [PortalController::class, 'singleOrderTracking'])->name('portal.single-order-tracking');
Route::get('about-us', [PortalController::class, 'aboutUs'])->name('web.about');
Route::get('contact-us', [PortalController::class, 'contactUs'])->name('web.contact-us');
Route::post('submit-contact-us', [PortalController::class, 'submitContactUs'])->name('portal.submit-contact');
Route::get('terms-of-services', [PortalController::class, 'termsOfServices'])->name('web.terms-of-services');
Route::get('privacy-policy', [PortalController::class, 'privacyPolicy'])->name('web.privacy');
Route::get('cancellation', [PortalController::class, 'cancellation'])->name('web.cancel');
Route::get('disclaimer', [PortalController::class, 'disclaimer'])->name('web.disclaimer');

// new twinship
Route::get('tracking', [PortalController::class, 'tracking'])->name('web.tracking');
Route::get('web-pricing', [PortalController::class, 'pricing'])->name('web.web-pricing');
Route::get('web-login', [PortalController::class, 'login'])->name('web.web-login');
Route::get('web-register', [PortalController::class, 'register'])->name('web.web-register');
Route::post('email-submit', [PortalController::class, 'emailSubmit'])->name('web.email_submit');

// web new
Route::get('api-integration', [PortalController::class, 'apiIntegration'])->name('portal.api-integration');
Route::get('help-center', [PortalController::class, 'helpCenter'])->name('web.help_center');
Route::get('blogs', [PortalController::class, 'blogs'])->name('portal.blogs');
Route::get('/blogs/{id}', [PortalController::class, 'blogDetail'])->name('web.blog-detail');
Route::get('support', [PortalController::class, 'support'])->name('portal.support');
Route::get('/support/{id}', [PortalController::class, 'supportDetail'])->name('web.support-detail');
Route::get('/support-child/{id}', [PortalController::class, 'supportDetailChild'])->name('web.support-detail-child');
Route::get('/career', [PortalController::class, 'career'])->name('portal.career');
Route::get('/fullfillment', [PortalController::class, 'fullfillment'])->name('portal.fullfillment');
Route::get('/lease', [PortalController::class, 'lease'])->name('portal.lease');
Route::get('/warahouse_provider', [PortalController::class, 'warahouse_providersData'])->name('portal.warahouse_provider');
Route::get('/WPR', [PortalController::class, 'WPR'])->name('portal.WPR');
Route::get('/guid', [PortalController::class, 'quickGuid'])->name('portal.guid');
Route::get('/all-storage', [PortalController::class, 'storage'])->name('portal.storage');
Route::get('/all-glossary', [PortalController::class, 'glossary'])->name('portal.glossary');
Route::get('subscribe', [PortalController::class, 'subscribe'])->name('portal.subscribe');
Route::post('submit-subscribe', [PortalController::class, 'submitSubscribe'])->name('portal.submit-subscribe');
Route::get('/check-subscribe-email/{email}', [PortalController::class, 'check_subscribe'])->name('portal.check_subscribe');

Route::get('/unorganised-tracking-data', [CroneController::class, 'getCourierUnorganisedTracking']);

// Shopify Redirect Route
Route::get('auth/shopify-redirect',[OperationController::class,'shopifyRedirect']);
Route::get('shopify/shopify-app',[OperationController::class,'shopifyAppOnInstallation']);

Route::get('service/sent-employee-report-mail',[CroneController::class,'SendEmployeeReportMail']);
Route::get('downloads/label/{orderId}',[Utilities::class,'LablePDF']);
Route::get('cron/update-ekart-manifestation',[CroneController::class,'updateEkartManifestationDetails']);
Route::get('/trackTestXbees',[WebController::class,'trackTestXbees']);
Route::get('cron/push-woocommerce-status/{orderId}/{status}',[WebController::class,'manualPushWoocommerceStatus']);
Route::get('push-woocommerce-status/{order}/{status}',[WebController::class,'pushWooCommerceStatusCustom']);
Route::get('delhivery/fixRTOStatus',[WebController::class,'performDelhiveryRTO']);
Route::get('/shipOrderTest',[SellerController::class,'shipOrderTest']);
Route::get('/checkTrackOrder/{awb}',[WebController::class,'checkTrackOrder']);
Route::get('/sendManifestation',[CroneController::class,'sendManifestation']);
Route::get('cron/fetch-delhivery-serviceable-pincodes',[CroneController::class,'fetchDelhiveryServiceablePincodes']);
Route::get('cron/send-email-for-status-logs',[CroneController::class,'SendMailForNotUpdatingStatus']);//SendMailForNotUpdatingStatus
Route::get('cron/send-email-for-manifested-orders',[CroneController::class,'SendMailForManifestOrders']);//SendMailForNotUpdatingStatus
Route::get('cron/send-email-for-not-fulfilled-orders',[CroneController::class,'SendMailForNotFulfilledOrders']); //
Route::get('cron/send-email-for-manifested-orders',[CroneController::class,'SendMailForManifestOrders']);//SendMailForNotUpdatingStatus
Route::get('cron/send-email-for-blocked-pincode',[CroneController::class,'SendMailForBlockedPincodes']);//SendMailForJobStatus
Route::get('cron/send-email-for-awb-threshold',[CroneController::class,'awbThreshold']);//awbThreshold
Route::get('/delhivery/createWarehouse',[CroneController::class,'createWarehouseForHeavyDelhivery']);
Route::get('/fixManifest',[CroneController::class,'checkAndFixManifest']);
Route::get('/fetchCustomChannelOrder/{seller}/{from}/{to}',[CroneController::class,'fetchAmazonOrdersCustom']);
Route::get('/checkManifestEcom/{id}',[EcomExpressController::class,'ManifestOrder']);
Route::get('/fetchAwbs/{type}',[EcomExpressController::class,'fetchAirwayBillNumbers']);
Route::get('/cancelEcomOrder/{awb}',[EcomExpressController::class,'CancelEcomExpressOrder']);
Route::get('oauth/amazon-response',[SellerController::class,'handleAmazonResponse']);

// Main Website Routes
Route::get('/fetchAirwayBillNumbers/{orderType}',[EcomExpressController::class,'fetchAirwayBillNumbers']);
Route::get('ecom/fetchAirwayBillNumbers3kg/{orderType}',[EcomExpress3kgController::class,'fetchAirwayBillNumbers']);
Route::get('/testShopifyPush/{orderID}',[SellerController::class,'checkFulfillShopifyMethod']);
Route::get('/testStatusEasyEcom',[ShopifyController::class,'testPushStatus']);
Route::get('custom/test-custom-push',[WebController::class,'testPush']);
Route::get('/pricing', [WebController::class, 'pricing'])->name('web.pricing');
Route::get('/table_pricing', [WebController::class, 'table_pricing'])->name('web.table_pricing');
Route::get('/terms-and-condition', [WebController::class, 'terms'])->name('web.terms');
Route::get('/export_xml', [WebController::class, 'export_xml'])->name('web.export_xml');
Route::get('/track-clone/{awb_number}', [WebController::class, 'tracking_clone'])->name('web.track_order_clone');
Route::get('cron/track-order-job', [ServiceController::class, 'trackOrderJob'])->name('web.trackOrder');
Route::get('utility/tracking-custom', [WebController::class, 'trackOrderCustom'])->name('web.trackOrderCustom');
Route::get('jobs/track-second-job', [WebController::class, 'trackingSecondJob'])->name('web.trackOrderSecondJob');
Route::get('/sync/manual_sync', [WebController::class, 'track_order_for_sync'])->name('web.trackOrderManual');
Route::get('/track_order_udaan', [WebController::class, 'track_order_udaan'])->name('web.trackOrderUdaan');

//Route::get('/track_order_xbees', [WebController::class, 'track_order_xbees'])->name('web.track_order_xbees');
Route::post('/add_newsletter', [WebController::class, 'newsletter'])->name('web.newsletter');
Route::get('/order_track', [WebController::class, 'order_track'])->name('web.order_track');
Route::post('/single_order_track', [WebController::class, 'single_order_track'])->name('web.single_order_track');
Route::get('/see_track_status', [WebController::class, 'single_order_tracks'])->name('web.single_order_tracks');
Route::get('/ndr_management', [WebController::class, 'ndr_management'])->name('web.ndr_management');
Route::get('/postpaid', [WebController::class, 'postpaid'])->name('web.postpaid');
Route::get('/web/early_cod', [WebController::class, 'early_cod'])->name('web.early_cod');
Route::get('/web/recommendation_engine', [WebController::class, 'recommendation_engine'])->name('web.recommendation_engine');
Route::get('send-test-email',[WebController::class,'send_test_email'])->name('web.test_send_email');
Route::get('service/generate-dtdc-awbs',[WebController::class,'insertDtdcAdvanceAwbs']);
Route::get('service/generate-dtdc-se-awbs',[WebController::class,'insertDtdcSEAdvanceAwbs']);
Route::get('service/generate-dtdc-ll-awbs',[WebController::class,'insertDtdcLLAdvanceAwbs']);
Route::get('service/generate-maruti-ecom-awbs',[WebController::class,'insertMarutiEcomAdvanceAwbs']);
Route::get('service/generate-maruti-new-awbs',[WebController::class,'insertMarutiEcomNewAdvanceAwbs']);
Route::get('service/generate-professional-awbs',[WebController::class,'insertProfessionalAdvanceAwbs']);
Route::get('service/download-serviceable_pincodes',[WebController::class,'exportSelectedPincodesWithCourier']);

//pickndel webhook
Route::post('/pickndel-webhook', [WebController::class, 'pickndelWebhook'])->name('pickndel-webhook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Extra Services and Routes
Route::post('seller/generate-magento-administrator-access-token',[OperationController::class,'generateMagentoAdminAccessToken'])->name('seller.generate-magento-administrator-access-token');

//Date Issues
Route::get('pickup-connection-date-missing',[CroneController::class,'pickupConnectionDateMissing']);
Route::get('zero-charges',[CroneController::class,'zeroCharges']);
Route::get('first-ofd-missing',[CroneController::class,'firstOfdDateMissing']);
Route::get('rto-initiated-date-missing',[CroneController::class,'rtoInitiatedDateMissing']);
Route::get('rto-delivered-date-missing',[CroneController::class,'rtoDeliveredDateMissing']);

// Crone jobs API Routes fulfillPendingOrders
Route::get('/generate-invoices', [CroneController::class, 'generate_invoices'])->name('crone.generate_invoices');
Route::get('seller/generate-invoice/{id}', [CroneController::class, 'GenerateSellerInvoice'])->name('seller.generate-invoice');
Route::get('/fulfill-pending-orders', [CroneController::class, 'fulfillPendingOrders'])->name('crone.fulfillPendingOrders');
Route::get('cron/fulfill-amazon-direct-orders', [CroneController::class, 'fulfillAmazonDirectOrders'])->name('crone.fulfillAmazonDirectOrders');
Route::get('cron/fulfill-amazon-direct-orders-flat-file', [CroneController::class, 'fulfillAmazonDirectOrdersFlatFile'])->name('crone.fulfillAmazonDirectOrdersFlatFile');
Route::get('/get-servicable-pincode/{partner}', [CroneController::class, 'getServicablePincodes'])->name('crone.getServicablePincodes');
Route::get('/update_cod_amount', [CroneController::class, 'updateCodAomunt'])->name('crone.update_cod_amount');
Route::get('/auto_accept_weight_reconciliation', [CroneController::class, 'auto_accept_weight_reconciliation'])->name('crone.auto_accept_weight_reconciliation');
Route::get('/update_status_xbees', [CroneController::class, 'update_status_xbees'])->name('crone.update_status_xbees');
Route::get('/update_status_delhivery', [CroneController::class, 'update_status_delhivery'])->name('crone.update_status_delhivery');
Route::get('/process-shipments', [CroneController::class, 'shipPendingOrders'])->name('crone.process_shipments');
Route::get('/verify-channel-orders', [SellerController::class, 'verifyChannelOrders'])->name('seller.verify_channel_orders');
Route::get('/fetch-all-channel-orders-job',[CroneController::class,'fetchChannelOrdersJob'])->name('cron.fetchAllChannelOrders');
Route::get('/fetch-amazon-orders-job',[CroneController::class,'fetchAmazonOrdersJob'])->name('cron.fetchAmazonOrders');
Route::get('/crone/populate-amazon-weight',[CroneController::class,'populateAmazonWeight'])->name('cron.populateAmazonWeight');
Route::get('crone/fetch-amazon-direct-orders',[CroneController::class,'fetchAmazonDirectOrders'])->name('cron.fetchAmazonDirectOrders');
Route::get('crone/fetch-amazon-direct-orders-custom/{seller}/{orderIds}',[CroneController::class,'fetchAmazonDirectOrdersCustom'])->name('cron.fetchAmazonDirectOrdersCustom');
Route::get('cron/fetch-amazon-direct-orders-custom-date/{seller}/{startDateTime}/{endDateTime}',[CroneController::class,'fetchAmazonDirectOrdersCustomDate'])->name('cron.fetchAmazonDirectOrdersCustomDate');
Route::get('cron/fetch-incomplete-order-amazon-direct/{limit}',[CroneController::class,'fetchAmazonDirectOrdersMinor']);

// Fetch amazon order using report api
Route::get('cron/create-amazon-direct-orders-report', [CroneController::class, 'createAmazonDirectOrderReport'])->name('crone.createAmazonDirectOrderReport');
Route::get('cron/fetch-amazon-direct-orders-report', [CroneController::class, 'fetchAmazonDirectOrderReport'])->name('crone.fetchAmazonDirectOrderReport');

// cancel order service route
Route::get('service/cancel-order',[CroneController::class,'cancelOrderService']);

Route::get('/generate_barcode', [CroneController::class, 'generate_barcode'])->name('crone.generate_barcode');

//Login and Logout Routes
Route::get('/administrator-login', [LoginController::class, 'index'])->name('administrator.login');
Route::post('/check-login', [LoginController::class, 'check_login'])->name('administrator.check_login');
Route::get('/administrator-logout', [LoginController::class, 'logout'])->name('administrator.logout');

// All Archive Routes will be goes here
Route::get('archive/orders-archive',[ArchiveController::class,'index']);
Route::get('archive/others-archive',[ArchiveController::class,'archiveOtherTables']);
Route::get('archive/pending-orders-archive',[ArchiveController::class,'archivePendingOrders']);

// End of Archive Routes
//All Admin Routes that will be handled by session
Route::middleware([CheckSession::class])->group(function () {

    //Ravi bhai
    Route::get('/administrator-seller/export/{partner}', [AdminController::class, 'export_pincode'])->name('export_pincode');
    Route::get('/administrator-seller/export-fm/{partner}', [AdminController::class, 'export_fm_pincode'])->name('export_fm_pincode');

    Route::get('delete-archive-orders',[AdminController::class, 'deleteArchiveOrder']);
    Route::post('submit-delete-archive-orders',[AdminController::class,'submitDeleteArchiveOrder']);
    //Mark Order Job Failed Manually
    Route::get('administrator/mark-failed-order-job/{id}',[AdminController::class,'markFailedOrderJob'])->name('administrator.markFailedOrderJob');

    Route::get('administrator/message-counter',[AdminController::class,'getMessageCounter'])->name('administrator.message-counter');
    Route::post('administrator/submit-message-counter',[AdminController::class,'submitMessageCounter'])->name('administrator.submit-message-counter');

    Route::post('/administrator/seller-information', [SellerAdminController::class, 'seller_information'])->name('administrator.seller.seller_information');
    Route::any('/administrator/utility/query-utility',[OperationController::class,'queryUtility']);
    Route::any('/administrator/utility/query-utility-new',[OperationController::class,'queryUtilityNew']);


    //Check Populate Routes
    Route::get('administrator/populate-weight',[AdminController::class,'fillAllWeightAndDimension'])->name('administrator.autoPopulate');
    Route::get('administrator/auto-populate-weight',[AdminController::class,'populateWeight'])->name('administrator.autoPopulateWeight');

    //Import Serviceability DTDC Routes
    Route::get('administrator/import-serviceability',[AdminController::class,'importServiceability'])->name('administrator.import');
    Route::post('administrator/submit-import-serviceability',[AdminController::class,'importServiceabilityCsv'])->name('administrator.import_serviceability');

    //Import Serviceability Bluedart
    Route::get('administrator/import-serviceability-bluedart-surface',[AdminController::class,'importServiceabilityBlueDartSurface'])->name('administrator.importBlueDartSurface');
    Route::post('administrator/submit-import-serviceability-bluedart-surface',[AdminController::class,'importServiceabilityBlueDartSurfaceCsv'])->name('administrator.import_serviceability_bluedart_surface');

    //Import Serviceability Dtdc FM Routes
    Route::get('administrator/import-serviceability-dtdc-fm',[AdminController::class,'importServiceabilityDtdcFM'])->name('administrator.importDtdctFM');
    Route::post('administrator/submit-import-serviceability-dtdc-fm',[AdminController::class,'importServiceabilityDtdcCsvFM'])->name('administrator.import_serviceability_dtdc_fm');

    //Import Serviceability Shree Maruti Ecom FM Routes
    Route::get('administrator/import-serviceability-maruti-ecom-fm',[AdminController::class,'importServiceabilityMarutiEcomFM'])->name('administrator.import');
    Route::post('administrator/submit-import-serviceability-maruti-ecom-fm',[AdminController::class,'importServiceabilityCsvMarutiEcomFM'])->name('administrator.import_serviceability_maruti_ecom_fm');

    //Import Serviceability Shree Maruti Ecom Routes
    Route::get('administrator/import-serviceability-maruti-ecom',[AdminController::class,'importServiceabilityMarutiEcom'])->name('administrator.import');
    Route::post('administrator/submit-import-serviceability-maruti-ecom',[AdminController::class,'importServiceabilityCsvMarutiEcom'])->name('administrator.import_serviceability_maruti_ecom');

    //Import Serviceability Shree Maruti New Routes
    Route::get('administrator/import-serviceability-maruti-new',[AdminController::class,'importServiceabilityMarutiNew'])->name('administrator.import');
    Route::post('administrator/submit-import-serviceability-maruti-new',[AdminController::class,'importServiceabilityCsvMarutiNew'])->name('administrator.import_serviceability_maruti_new');


    //Import Serviceability Amazon SWA Routes
    Route::get('administrator/import-serviceability-swa',[AdminController::class,'importServiceabilitySWA'])->name('administrator.importSWA');
    Route::post('administrator/submit-import-serviceability-swa',[AdminController::class,'importServiceabilitySWACsv'])->name('administrator.import_serviceability_swa');

    //Import Serviceability Xpressbees SWA Routes
    Route::get('administrator/import-serviceability-xbees',[AdminController::class,'importServiceabilityXbees'])->name('administrator.importXbees');
    Route::post('administrator/submit-import-serviceability-xbees',[AdminController::class,'importServiceabilityXbeesCsv'])->name('administrator.import_serviceability_xbees');


    //Import Serviceability shadowfax
    Route::get('administrator/import-serviceability-shadowfax',[AdminController::class,'importServiceabilityshadowfax'])->name('administrator.importShadowfax');
    Route::post('administrator/submit-import-serviceability-shadowfax',[AdminController::class,'importServiceabilityshadowfaxCsv'])->name('administrator.import_serviceability_shadowfax');

    //Import Serviceability xbees FM Routes
    Route::get('administrator/import-serviceability-shadowfax-fm',[AdminController::class,'importServiceabilityShadowfaxFM'])->name('administrator.importShadowfaxFM');
    Route::post('administrator/submit-import-serviceability-shadowfax-fm',[AdminController::class,'importServiceabilityShadowfaxCsvFM'])->name('administrator.import_serviceability_shadowfax_fm');


    //Import Bluedart Origin Codes
    Route::get('administrator/service/import-bluedart-origin-codes',[AdminController::class,'importBluedartOriginCodes'])->name('administrator.import-bluedart-origin-codes');
    Route::post('administrator/service/submit-bluedart-origin-codes',[AdminController::class,'submitBluedartOriginCodes'])->name('administrator.submit-bluedart-origin-codes');


    //Import Serviceability Ekart LM Routes
    Route::get('administrator/import-serviceability-ekart',[AdminController::class,'importServiceabilityEkart'])->name('administrator.importEkart');
    Route::post('administrator/submit-import-serviceability-ekart',[AdminController::class,'importServiceabilityEkartCsv'])->name('administrator.import_serviceability_ekart');

    //Import Serviceability Ekart FM Routes
    Route::get('administrator/import-serviceability-ekart-fm',[AdminController::class,'importServiceabilityEkartFM'])->name('administrator.importEkartFM');
    Route::post('administrator/submit-import-serviceability-ekart-fm',[AdminController::class,'importServiceabilityEkartCsvFM'])->name('administrator.import_serviceability_ekart_fm');

    // Archive Data Route
    Route::get('administrator/archive',[AdminController::class,'archiveData'])->name('administrator.archive-data');
    Route::post('administrator/run-archival',[AdminController::class,'runArchival'])->name('administrator.run-archival');

    //Import Serviceability Amazon SWA Routes
    Route::get('administrator/import-serviceability-dtdc',[AdminController::class,'importServiceabilityDtdc'])->name('administrator.importDtdc');
    Route::post('administrator/submit-import-serviceability-dtdc',[AdminController::class,'importServiceabilityDtdcCsv'])->name('administrator.import_serviceability_dtdc');

    //Import Serviceability bluedart Air FM Routes
    Route::get('administrator/import-serviceability-bluedart-fm',[AdminController::class,'importServiceabilityBlueDartFM'])->name('administrator.importBluedartFM');
    Route::post('administrator/submit-import-serviceability-bluedart-fm',[AdminController::class,'importServiceabilityBlueDartCsvFM'])->name('administrator.import_serviceability_bluedart_fm');

    //Import Serviceability bluedart Air FM Routes
    Route::get('administrator/import-serviceability-bluedart-surface-fm',[AdminController::class,'importServiceabilityBluedartSurfaceFM'])->name('administrator.importBluedartSurfaceFM');
    Route::post('administrator/submit-import-serviceability-bluedart-surface-fm',[AdminController::class,'importServiceabilityBluedartSurfaceCsvFM'])->name('administrator.import_serviceability_bluedart_surface_fm');

    //Import Serviceability Ecom Express Routes
    Route::get('administrator/import-serviceability-ecom',[AdminController::class,'importServiceabilityEcom'])->name('administrator.importEcom');
    Route::post('administrator/submit-import-serviceability-ecom',[AdminController::class,'importServiceabilityEcomCsv'])->name('administrator.import_serviceability_ecom');

    //Import Serviceability Ecom  FM Routes
    Route::get('administrator/import-serviceability-ecom-fm',[AdminController::class,'importServiceabilityEcomFM'])->name('administrator.importEcomFM');
    Route::post('administrator/submit-import-serviceability-ecom-fm',[AdminController::class,'importServiceabilityEcomCsvFM'])->name('administrator.import_serviceability_ecom_fm');

    //Import Serviceability Ecom Express Ros Routes
    Route::get('administrator/import-serviceability-ecom-ros',[AdminController::class,'importServiceabilityEcomRos'])->name('administrator.importEcomRos');
    Route::post('administrator/submit-import-serviceability-ecom-ros',[AdminController::class,'importServiceabilityEcomRosCsv'])->name('administrator.import_serviceability_ecom_ros');

    //Import Serviceability Ecom Ros FM Routes
    Route::get('administrator/import-serviceability-ecom-ros-fm',[AdminController::class,'importServiceabilityEcomRosFM'])->name('administrator.importEcomRosFM');
    Route::post('administrator/submit-import-serviceability-ecom-ros-fm',[AdminController::class,'importServiceabilityEcomRosCsvFM'])->name('administrator.import_serviceability_ecom_ros_fm');

    //Import Serviceability Ecom Express  3Kg Routes
    Route::get('administrator/import-serviceability-ecom-three',[AdminController::class,'importServiceabilityEcomThree'])->name('administrator.importEcomThree');
    Route::post('administrator/submit-import-serviceability-ecom-three',[AdminController::class,'importServiceabilityEcomThreeCsv'])->name('administrator.import_serviceability_ecom_three');



    //Import Serviceability Ecom Express Ros 3Kg Routes
    Route::get('administrator/import-serviceability-ecom-ros-three',[AdminController::class,'importServiceabilityEcomRosThree'])->name('administrator.importEcomRosThree');
    Route::post('administrator/submit-import-serviceability-ecom-ros-three',[AdminController::class,'importServiceabilityEcomRosThreeCsv'])->name('administrator.import_serviceability_ecom_ros_three');

    //Import Serviceability Ecom Ros 3Kg FM Routes
    Route::get('administrator/import-serviceability-ecom-ros-three-fm',[AdminController::class,'importServiceabilityEcomRosThreeFM'])->name('administrator.importEcomRosThreeFM');
    Route::post('administrator/submit-import-serviceability-ecom-ros-three-fm',[AdminController::class,'importServiceabilityEcomRosThreeCsvFM'])->name('administrator.import_serviceability_ecom_ros_three_fm');


    //Import Serviceability Bluedart Routes
    Route::get('administrator/import-serviceability-bluedart',[AdminController::class,'importServiceabilityBlueDart'])->name('administrator.importBlueDart');
    Route::post('administrator/submit-import-serviceability-bluedart',[AdminController::class,'importServiceabilityBlueDartCsv'])->name('administrator.import_serviceability_bluedart');

    //Import Serviceability Smartr Routes
    Route::get('administrator/import-serviceability-smartr',[AdminController::class,'importServiceabilitySmartr'])->name('administrator.importSmartr');
    Route::post('administrator/submit-import-serviceability-smartr',[AdminController::class,'importServiceabilitySmartrCsv'])->name('administrator.import_serviceability_smartr');

    //Import AWBs Smartr Routes
    Route::get('administrator/import-awbs-smartr',[AdminController::class,'importAWBSmartr'])->name('administrator.importSmartrAWB');
    Route::post('administrator/submit-import-awbs-smartr',[AdminController::class,'importAWBSmartrCsv'])->name('administrator.import_awb_smartr');

    //Import Serviceability delhivery Routes
    Route::get('administrator/import-serviceability-delhivery',[AdminController::class,'importServiceabilityDelhivery'])->name('administrator.importSmartr');
    Route::post('administrator/submit-import-serviceability-delhivery',[AdminController::class,'importServiceabilityDelhiveryCsv'])->name('administrator.import_serviceability_delhivery');

    //Import Serviceability Delhivery FM Routes
    Route::get('administrator/import-serviceability-delhivery-fm',[AdminController::class,'importServiceabilityDelhiveryFM'])->name('administrator.importDelhiveryFM');
    Route::post('administrator/submit-import-serviceability-delhivery-fm',[AdminController::class,'importServiceabilityDelhiveryCsvFM'])->name('administrator.import_serviceability_delhivery_fm');


    //Import Serviceability Pick Routes
    Route::get('administrator/import-serviceability-pick',[AdminController::class,'importServiceabilityPick'])->name('administrator.importPick');
    Route::post('administrator/submit-import-serviceability-pick',[AdminController::class,'importServiceabilityPickCsv'])->name('administrator.import_serviceability_pick');

    //Import Serviceability Pick FM Routes
    Route::get('administrator/import-serviceability-pick-fm',[AdminController::class,'importServiceabilityPickFM'])->name('administrator.importPickFM');
    Route::post('administrator/submit-import-serviceability-pick-fm',[AdminController::class,'importServiceabilityPickCsvFM'])->name('administrator.import_serviceability_pick_fm');


    //Import AWBs Bluedart Routes
    Route::get('administrator/import-awbs-bluedart',[AdminController::class,'importAWBBluedart'])->name('administrator.importAWBBluedart');
    Route::post('administrator/submit-import-awbs-bluedart',[AdminController::class,'importAWBBluedartCsv'])->name('administrator.importAWBBluedartCsv');

    //Import AWBs NSE Bluedart Routes
    Route::get('administrator/import-awbs-nse-bluedart',[AdminController::class,'importAWBNSEBluedart'])->name('administrator.importAWBNSEBluedart');
    Route::post('administrator/submit-import-awbs-nse-bluedart',[AdminController::class,'importAWBNSEBluedartCsv'])->name('administrator.importAWBNSEBluedartCsv');


    //Import AWBs XpreesBees Unique Routes
    Route::get('administrator/import-awbs-xbees-unique',[AdminController::class,'importAWBXBeesUnique'])->name('administrator.importAWBXBeesUnique');
    Route::post('administrator/submit-import-xbees-unique',[AdminController::class,'importAWBXBeesUniqueCsv'])->name('administrator.importAWBXBeesUniqueCsv');

    //Import AWBs Gati Routes
    Route::get('administrator/import-awbs-gati',[AdminController::class,'importAWBGati'])->name('administrator.importGatiAWB');
    Route::post('administrator/submit-import-awbs-gati',[AdminController::class,'importAWBGatiCsv'])->name('administrator.import_awb_gati');

    // Import Movin Advance AWBs
    Route::get('administrator/import-awbs-movin',[AdminController::class,'importAWBMovin'])->name('administrator.importMovinAWB');
    Route::post('administrator/submit-import-awbs-movin',[AdminController::class,'importAWBMovinCSV'])->name('administrator.import_awb_movin');

    //Get Order Download Report
    Route::get('administrator/get-Order-Download-Report',[AdminController::class,'getDownloadOrderReport'])->name('administrator.get-Order-Download-Report');

    //Import Serviceability xbees FM Routes
    Route::get('administrator/import-serviceability-xbees-fm',[AdminController::class,'importServiceabilityXbeesFM'])->name('administrator.importXbeesFM');
    Route::post('administrator/submit-import-serviceability-xbees-fm',[AdminController::class,'importServiceabilityXbeesCsvFM'])->name('administrator.import_serviceability_xbees_fm');

    //Import Serviceability Movin Routes
    Route::get('administrator/import-serviceability-movin',[AdminController::class,'importServiceabilityMovin'])->name('administrator.importMovin');
    Route::post('administrator/submit-import-serviceability-movin',[AdminController::class,'importServiceabilityMovinCsv'])->name('administrator.import_serviceability_movin');

    //Import Serviceability MovinFM Routes
    Route::get('administrator/import-serviceability-movin-fm',[AdminController::class,'importServiceabilityMovinFM'])->name('administrator.importMovinFM');
    Route::post('administrator/submit-import-serviceability-movin-fm',[AdminController::class,'importServiceabilityMovinCsvFM'])->name('administrator.import_serviceability_movin_fm');

    //Import Serviceability professional  FM Routes
    Route::get('administrator/import-serviceability-professional-fm',[AdminController::class,'importServiceabilityProfessionalFM'])->name('administrator.importProfessionalFM');
    Route::post('administrator/submit-import-serviceability-professional-fm',[AdminController::class,'importServiceabilityProfessionalCsvFM'])->name('administrator.import_serviceability_professional_fm');

    //Import Serviceability professional  Routes
    Route::get('administrator/import-serviceability-professional',[AdminController::class,'importServiceabilityProfessional'])->name('administrator.importProfessional');
    Route::post('administrator/submit-import-serviceability-professional',[AdminController::class,'importServiceabilityProfessionalCsv'])->name('administrator.import_serviceability_professional');

    //Import Serviceability professional  Routes
    Route::get('administrator/import-serviceability-delhiveryheavey',[AdminController::class,'importServiceabilityDelhiveryHeavey'])->name('administrator.importDelhiveryHeavey');
    Route::post('administrator/submit-import-serviceability-delhiveryheavey',[AdminController::class,'importServiceabilityDelhiveryHeaveyCsv'])->name('administrator.import_serviceability_delhiveryheavey');


    //Dashboard Routes for Admin Panel
    Route::get('/administrator-dashboard', [AdminController::class, 'dashboard'])->name('administrator.dashboard');
    Route::get('/administrator-dashboard/order-report', [AdminController::class, 'orderReport'])->name('administrator.orderReport');
    Route::get('/administrator-dashboard/order-report-export', [AdminController::class, 'exportOrderReport'])->name('administrator.orderReport.export');
    Route::get('/administrator-profile', [AdminController::class, 'profile'])->name('administrator.profile');
    Route::post('/save-profile', [AdminController::class, 'save_profile'])->name('administrator.save.profile');
    Route::post('/change-password', [AdminController::class, 'change_password'])->name('administrator.change.password');
    Route::get('administrator/recharge-request', [AdminController::class, 'recharge_request'])->name('administrator.recharge_request');
    Route::post('/approve-neft', [AdminController::class, 'approve_neft'])->name('administrator.approve_neft');
    Route::post('/add_seller_balance', [AdminController::class, 'add_seller_balance'])->name('administrator.add_seller_balance');
    Route::post('/deduct_seller_balance', [AdminController::class, 'deduct_seller_balance'])->name('administrator.deduct_seller_balance');

    //Admin Management Routes
    Route::get('/administrator', [AdminController::class, 'index'])->name('administrator');
    Route::get('administrator/configuration', [AdminController::class, 'configuration'])->name('configuration');
    Route::post('administrator/save-configuration', [AdminController::class, 'save_configuration'])->name('save_configuration');
    Route::post('/add-administrator', [AdminController::class, 'insert'])->name('add_administrator');
    Route::get('/delete-administrator/{id}', [AdminController::class, 'delete'])->name('delete_administrator');
    Route::get('/modify-administrator/{id}', [AdminController::class, 'modify'])->name('modify_administrator');
    Route::get('/get_administrator_rights/{id}', [AdminController::class, 'get_administrator_rights'])->name('get_administrator_rights');
    Route::post('/update-administrator', [AdminController::class, 'update'])->name('update_administrator');
    Route::post('/administrator-status', [AdminController::class, 'status'])->name('administrator_status');
    Route::post('/save_rights', [AdminController::class, 'save_rights'])->name('save_rights');

    //Generated APIs Keys
    Route::get('administrator/keys', [AdminController::class, 'sellerKeys'])->name('administrator.sellerKeys');

    //Admin Management Routes
    Route::get('/administrator-employee', [AdminController::class, 'employee'])->name('administrator_employee');
    Route::post('/add-administrator-employee', [AdminController::class, 'employee_insert'])->name('add_administrator_employee');
    Route::get('/delete-administrator-employee/{id}', [AdminController::class, 'employee_delete'])->name('delete_administrator_employee');
    Route::get('/modify-administrator-employee/{id}', [AdminController::class, 'employee_modify'])->name('modify_administrator_employee');
    Route::post('/update-administrator-employee', [AdminController::class, 'employee_update'])->name('update_administrator_employee');
    Route::post('/administrator-employee-status', [AdminController::class, 'employee_status'])->name('administrator_employee_status');

    //Admin Management Routes
    Route::get('/administrator/cron-jobs', [AdminController::class, 'cronJobs'])->name('cronJobs');
    Route::get('/administrator/cron-log/{slug}', [AdminController::class, 'cronLogData'])->name('cronLogData');
    Route::get('/administrator/async-cron-jobs', [AdminController::class, 'asyncCronJobs'])->name('asyncCronJobs');
    Route::get('/administrator/export-cron-jobs', [AdminController::class, 'exportCronJobs'])->name('export.cronJobs');
    Route::get('/administrator/export-cron-log/{slug}', [AdminController::class, 'exportCronLogData'])->name('export.cronLogData');
    Route::get('/administrator/awb-thresholds', [AdminController::class, 'awbThreshold'])->name('awbThreshold');


    //Approval by administrator
    Route::get('/administrator/approval-request-sales', [AdminController::class, 'ApprovalRequestBySales'])->name('ApprovalRequestBySales');
    Route::get('/administrator/fetch-seller-rates/{id}', [AdminController::class, 'FetchSellerRates'])->name('FetchSellerRates');
    Route::get('/administrator/reject-status/{id}', [AdminController::class, 'RejectStatus'])->name('RejectStatus');
    Route::get('/administrator/approve-status/{id}', [AdminController::class, 'ApproveStatus'])->name('ApproveStatus');

    //Master Management Routes
    Route::get('administrator/master', [MasterController::class, 'index'])->name('master');
    Route::post('administrator/add-master', [MasterController::class, 'insert'])->name('add_master');
    Route::get('administrator/delete-master/{id}', [MasterController::class, 'delete'])->name('delete_master');
    Route::get('administrator/modify-master/{id}', [MasterController::class, 'modify'])->name('modify_master');
    Route::post('administrator/update-master', [MasterController::class, 'update'])->name('update_master');
    Route::post('administrator/master-status', [MasterController::class, 'status'])->name('master_status');


    // Blogs Management Routes
    Route::get('administrator/blog', [BlogsController::class, 'index'])->name('blog');
    Route::post('administrator/add-blog', [BlogsController::class, 'insert'])->name('add_blog');
    Route::get('administrator/delete-blog/{id}', [BlogsController::class, 'delete'])->name('delete_blog');
    Route::get('administrator/modify-blog/{id}', [BlogsController::class, 'modify'])->name('modify_blog');
    Route::post('administrator/update-blog', [BlogsController::class, 'update'])->name('update_blog');
    Route::post('administrator/blog-status', [BlogsController::class, 'status'])->name('blog_status');
    // about Management Routes
    Route::get('administrator/about', [AboutusController::class, 'index'])->name('about');
    Route::post('administrator/add-about', [AboutusController::class, 'insert'])->name('add_about');
    Route::get('administrator/delete-about/{id}', [AboutusController::class, 'delete'])->name('delete_about');
    Route::get('administrator/modify-about/{id}', [AboutusController::class, 'modify'])->name('modify_about');
    Route::post('administrator/update-about', [AboutusController::class, 'update'])->name('update_about');
    Route::post('administrator/about-status', [AboutusController::class, 'status'])->name('about_status');

    // Config Management Routes
    Route::get('administrator/config', [WebConfigController::class, 'index'])->name('config');
    Route::post('administrator/add-config', [WebConfigController::class, 'insert'])->name('web.add_config');

    // glossary Management Routes
    Route::get('administrator/glossary', [GlossaryController::class, 'index'])->name('glossary');
    Route::post('administrator/add-glossary', [GlossaryController::class, 'insert'])->name('web.add_glossary');

    //Faq Management Routes
    Route::get('administrator/faq', [FaqController::class, 'index'])->name('faq');
    Route::post('administrator/add-faq', [FaqController::class, 'insert'])->name('add_faq');
    Route::get('administrator/delete-faq/{id}', [FaqController::class, 'delete'])->name('delete_faq');
    Route::get('administrator/modify-faq/{id}', [FaqController::class, 'modify'])->name('modify_faq');
    Route::post('administrator/update-faq', [FaqController::class, 'update'])->name('update_faq');
    Route::post('administrator/faq-status', [FaqController::class, 'status'])->name('faq_status');

    // About Management Routes
    Route::get('administrator/about', [AboutusController::class, 'index'])->name('about');
    Route::post('administrator/add-about', [AboutusController::class, 'insert'])->name('add_about');
    Route::get('administrator/delete-about/{id}', [AboutusController::class, 'delete'])->name('delete_about');
    Route::get('administrator/modify-about/{id}', [AboutusController::class, 'modify'])->name('modify_about');
    Route::post('administrator/update-about', [AboutusController::class, 'update'])->name('update_about');
    Route::post('administrator/about-status', [AboutusController::class, 'status'])->name('about_status');

    //Career Management Routes
    Route::get('administrator/all-career', [CareerController::class, 'index'])->name('career');
    Route::post('administrator/add-career', [CareerController::class, 'insert'])->name('add_career');
    Route::get('administrator/delete-career/{id}', [CareerController::class, 'delete'])->name('delete_career');
    Route::get('administrator/modify-career/{id}', [CareerController::class, 'modify'])->name('modify_career');
    Route::post('administrator/update-career', [CareerController::class, 'update'])->name('update_career');
    Route::post('administrator/career-status', [CareerController::class, 'status'])->name('career_status');

    //Career Management Routes
    Route::get('administrator/all-career_expect', [CareerExpectController::class, 'index'])->name('career_expect');
    Route::post('administrator/add-career_expect', [CareerExpectController::class, 'insert'])->name('add_career_expect');
    Route::get('administrator/delete-career_expect/{id}', [CareerExpectController::class, 'delete'])->name('delete_career_expect');
    Route::get('administrator/modify-career_expect/{id}', [CareerExpectController::class, 'modify'])->name('modify_career_expect');
    Route::post('administrator/update-career_expect', [CareerExpectController::class, 'update'])->name('update_career_expect');
    Route::post('administrator/career_expect-status', [CareerExpectController::class, 'status'])->name('career_expect_status');

    // Courier Management Routes
    Route::get('administrator/courier', [CourierController::class, 'index'])->name('courier');
    Route::post('administrator/add-courier', [CourierController::class, 'insert'])->name('add_courier');
    Route::get('administrator/delete-courier/{id}', [CourierController::class, 'delete'])->name('delete_courier');
    Route::get('administrator/modify-courier/{id}', [CourierController::class, 'modify'])->name('modify_courier');
    Route::post('administrator/update-courier', [CourierController::class, 'update'])->name('update_courier');
    Route::post('administrator/courier-status', [CourierController::class, 'status'])->name('courier_status');


    //Support Management Routes
    Route::get('administrator/all-support', [SupportController::class, 'index'])->name('support');
    Route::post('administrator/add-support', [SupportController::class, 'insert'])->name('add_support');
    Route::get('administrator/delete-support/{id}', [SupportController::class, 'delete'])->name('delete_support');
    Route::get('administrator/modify-support/{id}', [SupportController::class, 'modify'])->name('modify_support');
    Route::post('administrator/update-support', [SupportController::class, 'update'])->name('update_support');
    Route::post('administrator/support-status', [SupportController::class, 'status'])->name('support_status');

    //Supportsub Management Routes
    Route::get('administrator/all-supportsub', [SupportSubController::class, 'index'])->name('supportsub');
    Route::post('administrator/add-supportsub', [SupportSubController::class, 'insert'])->name('add_supportsub');
    Route::get('administrator/delete-supportsub/{id}', [SupportSubController::class, 'delete'])->name('delete_supportsub');
    Route::get('administrator/modify-supportsub/{id}', [SupportSubController::class, 'modify'])->name('modify_supportsub');
    Route::post('administrator/update-supportsub', [SupportSubController::class, 'update'])->name('update_supportsub');
    Route::post('administrator/supportsub-status', [SupportSubController::class, 'status'])->name('supportsub_status');

    // Supportchild Page Routes Goes Here
    Route::get('administrator/childcategory', [SupportChildController::class, 'index'])->name('administrator.childcategory');
    Route::post('administrator/add-childcategory', [SupportChildController::class, 'insert'])->name('add-childcategory');
    Route::get('administrator/delete-childcategory/{id}', [SupportChildController::class, 'delete'])->name('delete-childcategory');
    Route::get('administrator/modify-childcategory/{id}', [SupportChildController::class, 'modify'])->name('modify-childcategory');
    Route::post('administrator/update-childcategory', [SupportChildController::class, 'update'])->name('update-childcategory');
    Route::post('administrator/status-childcategory', [SupportChildController::class, 'status'])->name('status-childcategory');
    Route::post('administrator/get-category-subcategory', [SupportChildController::class, 'getCategorySubCategory'])->name('administrator.get-category-subcategory');
    Route::post('administrator/get-subcategory-childcategory', [SupportChildController::class, 'getSubCategoryChildCategory'])->name('administrator.get-category-subcategory');

    //CountryChanelController Management Routes
    Route::get('administrator/all-country_currency', [CountryChanelController::class, 'index'])->name('country_currency');
    Route::post('administrator/add-country_currency', [CountryChanelController::class, 'insert'])->name('add_country_currency');
    Route::get('administrator/delete-country_currency/{id}', [CountryChanelController::class, 'delete'])->name('delete_country_currency');
    Route::get('administrator/modify-country_currency/{id}', [CountryChanelController::class, 'modify'])->name('modify_country_currency');
    Route::post('administrator/update-country_currency', [CountryChanelController::class, 'update'])->name('update_country_currency');
    Route::post('administrator/country_currency-status', [CountryChanelController::class, 'status'])->name('country_currency_status');

    //FooterCategory Management Routes
    Route::get('administrator/all-footercategory', [FooterCategoryController::class, 'index'])->name('footercategory');
    Route::post('administrator/add-footercategory', [FooterCategoryController::class, 'insert'])->name('add_footercategory');
    Route::get('administrator/delete-footercategory/{id}', [FooterCategoryController::class, 'delete'])->name('delete_footercategory');
    Route::get('administrator/modify-footercategory/{id}', [FooterCategoryController::class, 'modify'])->name('modify_footercategory');
    Route::post('administrator/update-footercategory', [FooterCategoryController::class, 'update'])->name('update_footercategory');
    Route::post('administrator/footercategory-status', [FooterCategoryController::class, 'status'])->name('footercategory_status');

    //footer Management Routes
    Route::get('administrator/all-footersub', [FooterSubController::class, 'index'])->name('footersub');
    Route::post('administrator/add-footersub', [FooterSubController::class, 'insert'])->name('add_footersub');
    Route::get('administrator/delete-footersub/{id}', [FooterSubController::class, 'delete'])->name('delete_footersub');
    Route::get('administrator/modify-footersub/{id}', [FooterSubController::class, 'modify'])->name('modify_footersub');
    Route::post('administrator/update-footersub', [FooterSubController::class, 'update'])->name('update_footersub');
    Route::post('administrator/footersub-status', [FooterSubController::class, 'status'])->name('footersub_status');



    //Slider Management Routes
    Route::get('administrator/slider', [SliderController::class, 'index'])->name('slider');
    Route::post('administrator/add-slider', [SliderController::class, 'insert'])->name('add_slider');
    Route::get('administrator/delete-slider/{id}', [SliderController::class, 'delete'])->name('delete_slider');
    Route::get('administrator/modify-slider/{id}', [SliderController::class, 'modify'])->name('modify_slider');
    Route::post('administrator/update-slider', [SliderController::class, 'update'])->name('update_slider');
    Route::post('administrator/slider-status', [SliderController::class, 'status'])->name('slider_status');

    //Features Management Routes
    Route::get('administrator/feature', [FeatureController::class, 'index'])->name('feature');
    Route::post('administrator/add-feature', [FeatureController::class, 'insert'])->name('add_feature');
    Route::get('administrator/delete-feature/{id}', [FeatureController::class, 'delete'])->name('delete_feature');
    Route::get('administrator/modify-feature/{id}', [FeatureController::class, 'modify'])->name('modify_feature');
    Route::post('administrator/update-feature', [FeatureController::class, 'update'])->name('update_feature');
    Route::post('administrator/feature-status', [FeatureController::class, 'status'])->name('feature_status');

    //Features Management Routes
    Route::get('administrator/features', [FeaturesController::class, 'index'])->name('features');
    Route::post('administrator/add-features', [FeaturesController::class, 'insert'])->name('add_features');
    Route::get('administrator/delete-features/{id}', [FeaturesController::class, 'delete'])->name('delete_features');
    Route::get('administrator/modify-features/{id}', [FeaturesController::class, 'modify'])->name('modify_features');
    Route::post('administrator/update-features', [FeaturesController::class, 'update'])->name('update_features');
    Route::post('administrator/features-status', [FeaturesController::class, 'status'])->name('features_status');


    //Features Management Routes
    Route::get('administrator/why', [Why_chooseController::class, 'index'])->name('why');
    Route::post('administrator/add-why', [Why_chooseController::class, 'insert'])->name('add_why');
    Route::get('administrator/delete-why/{id}', [Why_chooseController::class, 'delete'])->name('delete_why');
    Route::get('administrator/modify-why/{id}', [Why_chooseController::class, 'modify'])->name('modify_why');
    Route::post('administrator/update-why', [Why_chooseController::class, 'update'])->name('update_why');
    Route::post('administrator/why-status', [Why_chooseController::class, 'status'])->name('why_status');


    //Logistic Partners Management Routes
    Route::get('administrator/logistics', [LogisticsController::class, 'index'])->name('logistics');
    Route::post('administrator/add-logistics', [LogisticsController::class, 'insert'])->name('add_logistics');
    Route::get('administrator/delete-logistics/{id}', [LogisticsController::class, 'delete'])->name('delete_logistics');
    Route::get('administrator/modify-logistics/{id}', [LogisticsController::class, 'modify'])->name('modify_logistics');
    Route::post('administrator/update-logistics', [LogisticsController::class, 'update'])->name('update_logistics');
    Route::post('administrator/logistics-status', [LogisticsController::class, 'status'])->name('logistics_status');

    //Channel Partners Management Routes
    Route::get('administrator/channel', [ChannelPartnersController::class, 'index'])->name('channel');
    Route::post('administrator/add-channel', [ChannelPartnersController::class, 'insert'])->name('add_channel');
    Route::get('administrator/delete-channel/{id}', [ChannelPartnersController::class, 'delete'])->name('delete_channel');
    Route::get('administrator/modify-channel/{id}', [ChannelPartnersController::class, 'modify'])->name('modify_channel');
    Route::post('administrator/update-channel', [ChannelPartnersController::class, 'update'])->name('update_channel');
    Route::post('administrator/channel-status', [ChannelPartnersController::class, 'status'])->name('channel_status');

    // Admin Import Orders Route
    Route::post('administrator/import-awb-order', [AdminController::class, 'importCsvOrders'])->name('administrator.importAwbOrders');
    Route::get('administrator/gzfile', [GzFileUploadController::class, 'index'])->name('administrator.web.gzfile');
    Route::post('administrator/upload', [GzFileUploadController::class, 'upload'])->name('administrator.web.upload');


    // Brand Management Routes
    Route::get('administrator/brand', [BrandsController::class, 'index'])->name('brand');
    Route::post('administrator/add-brand', [BrandsController::class, 'insert'])->name('add_brand');
    Route::get('administrator/delete-brand/{id}', [BrandsController::class, 'delete'])->name('delete_brand');
    Route::get('administrator/modify-brand/{id}', [BrandsController::class, 'modify'])->name('modify_brand');
    Route::post('administrator/update-brand', [BrandsController::class, 'update'])->name('update_brand');
    Route::post('administrator/brand-status', [BrandsController::class, 'status'])->name('brand_status');

    // Redeem Codes Management Routes
    Route::get('administrator/code', [RedeemController::class, 'index'])->name('code');
    Route::post('administrator/add-code', [RedeemController::class, 'insert'])->name('add_code');
    Route::get('administrator/delete-code/{id}', [RedeemController::class, 'delete'])->name('delete_code');
    Route::get('administrator/modify-code/{id}', [RedeemController::class, 'modify'])->name('modify_code');
    Route::post('administrator/update-code', [RedeemController::class, 'update'])->name('update_code');
    Route::post('administrator/code-status', [RedeemController::class, 'status'])->name('code_status');

    // Steps Management Routes
    Route::get('administrator/step', [StepsController::class, 'index'])->name('step');
    Route::post('administrator/add-step', [StepsController::class, 'insert'])->name('add_step');
    Route::get('administrator/delete-step/{id}', [StepsController::class, 'delete'])->name('delete_step');
    Route::get('administrator/modify-step/{id}', [StepsController::class, 'modify'])->name('modify_step');
    Route::post('administrator/update-step', [StepsController::class, 'update'])->name('update_step');
    Route::post('administrator/step-status', [StepsController::class, 'status'])->name('step_status');

    // Coverage Media Management Routes
    Route::get('administrator/coverage', [CoverageController::class, 'index'])->name('coverage');
    Route::post('administrator/add-coverage', [CoverageController::class, 'insert'])->name('add_coverage');
    Route::get('administrator/delete-coverage/{id}', [CoverageController::class, 'delete'])->name('delete_coverage');
    Route::get('administrator/modify-coverage/{id}', [CoverageController::class, 'modify'])->name('modify_coverage');
    Route::post('administrator/update-coverage', [CoverageController::class, 'update'])->name('update_coverage');
    Route::post('administrator/coverage-status', [CoverageController::class, 'status'])->name('coverage_status');

    // Testimonial Management Routes
    Route::get('administrator/testimonial', [TestimonialController::class, 'index'])->name('testimonial');
    Route::post('administrator/add-testimonial', [TestimonialController::class, 'insert'])->name('add_testimonial');
    Route::get('administrator/delete-testimonial/{id}', [TestimonialController::class, 'delete'])->name('delete_testimonial');
    Route::get('administrator/modify-testimonial/{id}', [TestimonialController::class, 'modify'])->name('modify_testimonial');
    Route::post('administrator/update-testimonial', [TestimonialController::class, 'update'])->name('update_testimonial');
    Route::post('administrator/testimonial-status', [TestimonialController::class, 'status'])->name('testimonial_status');

    //contact us
    Route::get('administrator/contact', [ContactController::class, 'index'])->name('contact');

    // Social Links Management Routes
    Route::get('administrator/social', [SocialController::class, 'index'])->name('social');
    Route::post('administrator/add-social', [SocialController::class, 'insert'])->name('add_social');
    Route::get('administrator/delete-social/{id}', [SocialController::class, 'delete'])->name('delete_social');
    Route::get('administrator/modify-social/{id}', [SocialController::class, 'modify'])->name('modify_social');
    Route::post('administrator/update-social', [SocialController::class, 'update'])->name('update_social');
    Route::post('administrator/social-status', [SocialController::class, 'status'])->name('social_status');

    // Stats Management Routes
    Route::get('administrator/stats', [StatsController::class, 'index'])->name('stats');
    Route::post('administrator/add-stats', [StatsController::class, 'insert'])->name('add_stats');
    Route::get('administrator/delete-stats/{id}', [StatsController::class, 'delete'])->name('delete_stats');
    Route::get('administrator/modify-stats/{id}', [StatsController::class, 'modify'])->name('modify_stats');
    Route::post('administrator/update-stats', [StatsController::class, 'update'])->name('update_stats');
    Route::post('administrator/stats-status', [StatsController::class, 'status'])->name('stats_status');

    // Recommendation Engine Route
    Route::get('/administrator/recommendation_engine', [ReconEngineController::class, 'index'])->name('recommendation_engine');
    Route::post('administrator/add-recommendation_engine', [ReconEngineController::class, 'insert'])->name('add_recommendation_engine');
    Route::get('administrator/delete-recommendation_engine/{id}', [ReconEngineController::class, 'delete'])->name('delete_recommendation_engine');
    Route::get('administrator/modify-recommendation_engine/{id}', [ReconEngineController::class, 'modify'])->name('modify_recommendation_engine');
    Route::post('administrator/update-recommendation_engine', [ReconEngineController::class, 'update'])->name('update_recommendation_engine');
    Route::post('administrator/recommendation_engine-status', [ReconEngineController::class, 'status'])->name('recommendation_engine');


    //Career Management Routes
    Route::get('administrator/all-career', [CareerController::class, 'index'])->name('career');
    Route::post('administrator/add-career', [CareerController::class, 'insert'])->name('add_career');
    Route::get('administrator/delete-career/{id}', [CareerController::class, 'delete'])->name('delete_career');
    Route::get('administrator/modify-career/{id}', [CareerController::class, 'modify'])->name('modify_career');
    Route::post('administrator/update-career', [CareerController::class, 'update'])->name('update_career');
    Route::post('administrator/career-status', [CareerController::class, 'status'])->name('career_status');

    //Career Management Routes
    Route::get('administrator/all-career_expect', [CareerExpectController::class, 'index'])->name('career_expect');
    Route::post('administrator/add-career_expect', [CareerExpectController::class, 'insert'])->name('add_career_expect');
    Route::get('administrator/delete-career_expect/{id}', [CareerExpectController::class, 'delete'])->name('delete_career_expect');
    Route::get('administrator/modify-career_expect/{id}', [CareerExpectController::class, 'modify'])->name('modify_career_expect');
    Route::post('administrator/update-career_expect', [CareerExpectController::class, 'update'])->name('update_career_expect');
    Route::post('administrator/career_expect-status', [CareerExpectController::class, 'status'])->name('career_expect_status');


    //KYC Approve Routes
    Route::get('administrator/seller', [SellerAdminController::class, 'index'])->name('kyc_approve');
    Route::get('administrator/view_kyc_information/{id}', [SellerAdminController::class, 'view'])->name('view_kyc_information');
    Route::post('administrator/verify_document', [SellerAdminController::class, 'verify'])->name('verify_kyc_information');
    Route::post('administrator/seller_status', [SellerAdminController::class, 'status'])->name('administrator.seller_status');
    Route::post('administrator/zone_type', [SellerAdminController::class, 'zone_type'])->name('administrator.zone_type');
    Route::post('administrator/seller_order_type', [SellerAdminController::class, 'seller_order_type'])->name('administrator.seller_order_type');
    Route::post('administrator/seller_is_alpha', [SellerAdminController::class, 'sellerIsAlpha'])->name('administrator.seller_is_alpha');
    Route::post('administrator/seller_gst_status', [SellerAdminController::class, 'gst_status'])->name('seller.gst_status');
    Route::post('administrator/seller_cheque_status', [SellerAdminController::class, 'cheque_status'])->name('seller.cheque_status');
    Route::post('administrator/seller_document_status', [SellerAdminController::class, 'document_status'])->name('seller.document_status');
    Route::post('administrator/seller_agreement_status', [SellerAdminController::class, 'agreement_status'])->name('seller.agreement_status');
    Route::get('administrator/delete-seller/{id}', [SellerAdminController::class, 'delete'])->name('delete_seller');
    Route::get('administrator/view-seller/{id}', [SellerAdminController::class, 'viewSeller'])->name('viewSeller');
    Route::get('/administrator-seller/export', [SellerAdminController::class, 'export_seller'])->name('export_seller');
    Route::post('/administrator/seller/sms-status', [SellerAdminController::class, 'sms_status'])->name('administrator.seller.sms_status');
    Route::post('/administrator/seller/pincode-editable', [SellerAdminController::class, 'pincode_editable'])->name('administrator.seller.pincode_editable');
    Route::post('/administrator/basic-information', [SellerAdminController::class, 'basic_information'])->name('administrator.seller.basic_information');
    Route::post('/administrator/account-information', [SellerAdminController::class, 'account_information'])->name('administrator.seller.account_information');
    Route::post('/administrator/kyc-information', [SellerAdminController::class, 'kyc_information'])->name('administrator.seller.kyc_information');
    Route::get('/administrator/pincode-detail/{pincode}', [SellerAdminController::class, 'get_pincode_details'])->name('seller.pincode_detail');
    Route::get('/administrator/ifsc-detail/{ifsc}', [SellerAdminController::class, 'get_ifsc_detail'])->name('seller.get_ifsc_detail');

    // Ravi Bhai Changes
    Route::post('/administrator/seller/verified', [SellerAdminController::class, 'verified'])->name('administrator.seller.verified');

    //Assign Seller Employee
    Route::post('administrator/assign-seller-employee', [SellerAdminController::class, 'assignMultipleSellerEmployee'])->name('assign-seller-employee');
    Route::get('administrator/seller-employee', [SellerAdminController::class, 'sellerEmployee'])->name('sellerEmployee');

    //Generate AWB Numbers Route
    Route::get('administrator/generate_awb', [AdminController::class, 'generateAwb'])->name('administrator.generateAwb');
    Route::post('administrator/generate_seller_awb',[AdminController::class,'generateSellerAwb'])->name('administrator.generateSellerAwb');
    Route::get('administrator/check-import',[AdminController::class,'importAwbNumbers'])->name('administrator.checkImport');
    Route::get('administrator/download-generated-awb/{generated}',[AdminController::class,'downloadGeneratedAwb'])->name('administrator.downloadGeneratedAWB');

    //Logistics Partner Routes
    Route::get('administrator/partner', [PartnerController::class, 'index'])->name('partner');
    Route::post('administrator/add-partner', [PartnerController::class, 'insert'])->name('add_partner');
    Route::get('administrator/delete-partner/{id}', [PartnerController::class, 'delete'])->name('delete_partner');
    Route::get('administrator/modify-partner/{id}', [PartnerController::class, 'modify'])->name('modify_partner');
    Route::post('administrator/update-partner', [PartnerController::class, 'update'])->name('update_partner');
    Route::post('administrator/partner-status', [PartnerController::class, 'status'])->name('partner_status');
    Route::post('administrator/upload-zone-mapping', [PartnerController::class, 'upload_zone_mapping'])->name('add_zone_mapping');


    //Logistics Plans Routes
    Route::get('administrator/plans', [PlanController::class, 'index'])->name('plans');
    Route::post('administrator/add-plans', [PlanController::class, 'insert'])->name('add_plans');
    Route::get('administrator/delete-plans/{id}', [PlanController::class, 'delete'])->name('delete_plans');
    Route::get('administrator/modify-plans/{id}', [PlanController::class, 'modify'])->name('modify_plans');
    Route::post('administrator/update-plans', [PlanController::class, 'update'])->name('update_plans');
    Route::post('administrator/plans-status', [PlanController::class, 'status'])->name('plans_status');

    //Logistics Rates Routes
    Route::get('administrator/rates', [PlanController::class, 'rates'])->name('rates');
    Route::get('administrator/seller-rates', [PlanController::class, 'seller_rates'])->name('seller_rates');
    Route::get('administrator/xindus-rates', [PlanController::class, 'xindusRates'])->name('administrator.xindus-rates');
    Route::post('administrator/save-xindus-rates', [PlanController::class, 'saveXindusRates'])->name('administrator.save-xindus-rates');
    Route::get('administrator/aramex-rates', [PlanController::class, 'aramexRates'])->name('administrator.aramex-rates');
    Route::post('administrator/save-aramex-rates', [PlanController::class, 'saveAramexRates'])->name('administrator.save-aramex-rates');
    Route::post('administrator/save-rates', [PlanController::class, 'save_rates'])->name('save_rates');
    Route::get('administrator/get-rates', [PlanController::class, 'get_rates'])->name('get_rates');
    Route::get('administrator/check_upload', [PlanController::class, 'check_upload'])->name('check_upload');
    Route::get('administrator/import-seller-rate-card', [PlanController::class, 'exportSellerRateCardSample'])->name('sellerRateCard.exportSample');
    Route::post('administrator/import-seller-rate-card', [PlanController::class, 'importSellerRateCard'])->name('sellerRateCard.import');
    Route::get('administrator/export-seller-rate-card', [PlanController::class, 'exportSellerRateCard'])->name('sellerRateCard.export');


    //Features Management Routes
    Route::get('administrator/early_cod', [AdminController::class, 'early_cod'])->name('early_cod');
    Route::post('administrator/add-early_cod', [AdminController::class, 'early_cod_insert'])->name('add_early_cod');
    Route::get('administrator/delete-early_cod/{id}', [AdminController::class, 'early_cod_delete'])->name('delete_e_cod');
    Route::get('administrator/modify-early_cod/{id}', [AdminController::class, 'early_cod_modify'])->name('modify_e_cod');
    Route::post('administrator/update-early_cod', [AdminController::class, 'early_cod_update'])->name('update_e_cod');
    Route::post('administrator/early_cod-status', [AdminController::class, 'early_cod_status'])->name('early_cod_status');

    //Credit Receipt Routes
    Route::get('administrator/credit_receipt', [AdminController::class, 'credit_receipt'])->name('credit_receipt');
    Route::post('administrator/add-credit_receipt', [AdminController::class, 'credit_receipt_insert'])->name('add_credit_receipt');
    Route::get('administrator/delete-credit_receipt/{id}', [AdminController::class, 'credit_receipt_delete'])->name('credit_receipt_delete');


    //Credit Receipt Routes
    Route::get('administrator/zone_mapping', [AdminController::class, 'zone_mapping'])->name('zone_mapping');

    //Weight Reconciliation Routes
    Route::get('administrator/weight_reconciliation', [AdminController::class, 'weightReconciliation'])->name('weight_reconciliation');
    Route::get('administrator/export_weight_reconciliation', [AdminController::class, 'exportWeightReconciliation'])->name('export_weight_reconciliation');
    Route::get('administrator/delete-weight_reconciliation/{id}', [AdminController::class, 'weightReconciliationDelete'])->name('weight_reconciliation_delete');
    Route::post('administrator/import_csv_weight_reconciliation', [AdminController::class, 'importCsvWeigthReconciliation'])->name('import_csv_weight_reconciliation');
    Route::get('administrator/weight_reconciliation_error', [AdminController::class, 'weightReconciliationError'])->name('weight_reconciliation_error');
    Route::get('administrator/export_csv_weight_reconciliation', [AdminController::class, 'exportWeightReconciliationError'])->name('export_csv_weight_reconciliation');


    // Weight Reconciliation Revoke
    Route::get('administrator/weight_reconciliation_revoke', [AdminController::class, 'weightReconciliationRevoke'])->name('weight_reconciliation_revoke');
    Route::post('administrator/import_revoke_csv_weight_reconciliation', [AdminController::class, 'importCsvWeightReconciliationRevoke'])->name('import_revoke_csv_weight_reconciliation');


    //Settle Weight Reconciliation Routes
    Route::get('administrator/settlement_weight_reconciliation', [AdminController::class, 'settlementWeightReconciliation'])->name('settlement_weight_reconciliation');
    Route::get('administrator/export_settled_weight_reconciliation', [AdminController::class, 'exportSettledWeightReconciliation'])->name('export_settled_weight_reconciliation');
    Route::get('administrator/delete_settlement_weight_reconciliation/{id}', [AdminController::class, 'settlementWeightReconciliationDelete'])->name('settlement_weight_reconciliation_delete');
    Route::post('administrator/import_csv_settlement_weight_reconciliation', [AdminController::class, 'importCsvSettlementWeigthReconciliation'])->name('import_csv_settlement_weight_reconciliation');
    Route::get('administrator/settled_weight_reconciliation_error', [AdminController::class, 'settledWeightReconciliationError'])->name('settled_weight_reconciliation_error');
    Route::get('administrator/export_csv_settled_weight_reconciliation', [AdminController::class, 'exportSettledWeightReconciliationError'])->name('export_csv_settled_weight_reconciliation');

    //Weight Reconciliation Routes
    Route::get('administrator/weight_reconciliation_logs', [AdminController::class, 'weightReconciliationLogs'])->name('weight_reconciliation_logs');

    //Weight Dispute Routes
    Route::get('/administrator/weight_dispute', [AdminController::class, 'weightDispute'])->name('administrator.weight_dispute');
    Route::get('/administrator/get_history_weight_reconciliation/{id}', [AdminController::class, 'getHistoryWeightReconciliation'])->name('administrator.getHistoryWeightReconciliation');
    Route::post('/administrator/add_weigth_rec_comment', [AdminController::class, 'addWeightRecComment'])->name('administrator.addWeightRecComment');
    Route::post('/administrator/close_weight_dispute', [AdminController::class, 'close_weight_dispute'])->name('administrator.close_weight_dispute');


    //Weight Reconciliation Routes
    Route::get('administrator/cod_remittance', [AdminController::class, 'codRemittance'])->name('cod_remittance');
    Route::post('administrator/import_csv_cod_remittance', [AdminController::class, 'importCsvCodRemittance'])->name('import_csv_cod_remittance');
    Route::get('administrator/cod_remittance_error', [AdminController::class, 'codRemittanceError'])->name('cod_remittance_error');
    Route::get('administrator/export_csv_cod_remittance', [AdminController::class, 'exportCodRemittanceError'])->name('export_csv_cod_remittance');

    // COD Remiitance BY Date
    Route::get('administrator/cod_remittance_bank', [AdminController::class, 'codRemittanceByDate'])->name('cod_remittance_bank');
    Route::get('administrator/export-cod-remittance-bank', [AdminController::class, 'ExportCodRemittanceByDate'])->name('export-cod-remittance-bank');

    // Revert COD Transation
    Route::get('administrator/service/revert-cod-remittance',[OperationController::class,'revertCODRemittance']);

    //Weight Reconciliation Routes
    Route::get('administrator/cod_remittance_logs', [AdminController::class, 'codRemittanceLogs'])->name('cod_remittance_logs');

    //Admin Customer Support
    Route::get('administrator/customer_support', [AdminController::class, 'customerSupport'])->name('administrator.customer_support');
    Route::get('administrator/export_escalation', [AdminController::class, 'export_escalation'])->name('administrator.export_escalation');
    Route::post('administrator/add-ticket_comment', [AdminController::class, 'add_ticket_comment'])->name('administrator.add_ticket_comment');
    Route::get('administrator/view-escalation/{id}', [AdminController::class, 'view_escalation'])->name('administrator.view_escalation');
    Route::get('administrator/close-ticket/{id}', [AdminController::class, 'close_ticket'])->name('employee.close_ticket');
    Route::post('administrator/escalate-ticket', [AdminController::class, 'escalateTicket'])->name('administrator.escalateTicket');


    //Admin Open Reconciliation
    Route::get('administrator/open_reconciliation', [AdminController::class, 'openReconciliation'])->name('administrator.openReconciliation');
    // Route::post('administrator/add-ticket_comment', [AdminController::class, 'add_ticket_comment'])->name('administrator.add_ticket_comment');
    Route::get('administrator/view-open_reconciliation/{id}', [AdminController::class, 'view_open_reconciliation'])->name('administrator.view_open_reconciliation');


    //Admin Servicable Pincode Check
    Route::get('administrator/seller-ndr-action', [AdminController::class, 'getNDRData'])->name('administrator.seller-ndr-action');
    Route::get('administrator/servicable_pincodes', [AdminController::class, 'servicable_pincode'])->name('administrator.servicable_pincode');
    Route::get('administrator/order_report', [AdminController::class, 'order_report'])->name('administrator.order_report');
    Route::get('administrator/get_order_report', [AdminController::class, 'get_order_report'])->name('administrator.get_order_report');
    Route::get('administrator/get_order', [AdminController::class, 'get_order'])->name('administrator.get_order');
    Route::post('administrator/export_order_report', [AdminController::class, 'export_order_report'])->name('administrator.export_order_report');
    Route::get('administrator/export_order_manually', [AdminController::class, 'export_order_manually'])->name('administrator.export_order_manually');

    //Admin Route for Finance Section
    Route::get('administrator/finance/seller_invoice', [AdminController::class, 'f_seller_invoice'])->name('administrator.f_seller_invoice');
    Route::get('administrator/billing/invoice/pdf/{id}', [AdminController::class, 'BillingInvoiceView'])->name('administrator.BillingInvoiceView');
    Route::get('administrator/billing/invoice/download/{id}', [AdminController::class, 'BillingInvoicePDF'])->name('administrator.BillingInvoicePDF');
    Route::get('administrator/billing/invoice/csv/{id}', [AdminController::class, 'BillingInvoiceCSV'])->name('administrator.BillingInvoiceCSV');
    Route::get('administrator/billing/invoice/export', [AdminController::class, 'export_billing_invoice_data'])->name('administrator.export_billing_invoice_data');

    Route::get('administrator/finance/seller_remittance_data', [AdminController::class, 'remittance_admin'])->name('administrator.seller_remittance_data');
//
//    Route::get('administrator/billing/seller_remittance_data/export/{id}', [AdminController::class, 'seller_remittance_data_export'])->name('administrator.seller_remittance_data_export');
//    Route::get('administrator/billing/remmitance/report', [AdminController::class, 'export_remittance_report'])->name('administrator.export_remittance_report');
    // Generate Seller Invoice Routes
    Route::post('administrator/finance/generate-seller-invoice', [AdminController::class, 'generateSellerInvoice'])->name('administrator.finance.generate-seller-invoice');

    Route::get('administrator/finance/seller_recharge', [AdminController::class, 'f_seller_recharge'])->name('administrator.f_seller_recharge');
    Route::get('administrator/recharge/export', [AdminController::class, 'export_recharge_data'])->name('administrator.export_recharge_data');

    Route::get('administrator/finance/seller_remittance', [AdminController::class, 'f_seller_remittance'])->name('administrator.f_seller_remittance');
    Route::get('administrator/billing/remmitance/export/{id}', [AdminController::class, 'f_seller_remittance_export'])->name('administrator.f_seller_remittance_export');
    Route::get('administrator/billing/remmitance/report', [AdminController::class, 'export_remittance_report'])->name('administrator.export_remittance_report');

    // Courier blocking
    Route::get('/administrator/block-courier', [AdminController::class, 'blockCourier'])->name('administrator.blockCourier');
    Route::post('/administrator/block-courier', [AdminController::class, 'storeBlockCourier'])->name('administrator.blockCourier.store');
    Route::get('/administrator/get-blocked-courier', [AdminController::class, 'getBlockedCourier'])->name('administrator.blockCourier.get');

    Route::get('administrator/import-amazon-direct-order-report',[AdminController::class,'importAmazonDirectOrderReport'])->name('administrator.importAmazonDirectOrderReport');
    Route::post('administrator/import-amazon-direct-order-report',[AdminController::class,'importAmazonDirectOrderReportFile'])->name('administrator.importAmazonDirectOrderReportFile');

    Route::get('administrator/fulfill-amazon-direct-order-flat-file',[AdminController::class,'fulfillAmazonDirectOrder'])->name('administrator.fulfillAmazonDirectOrder');
    Route::post('administrator/fulfill-amazon-direct-order-flat-file',[AdminController::class,'fulfillAmazonDirectOrderFlatFile'])->name('administrator.fulfillAmazonDirectOrderFlatFile');

    // Ops
    Route::get('/administrator/ops/pending-manifest', [AdminController::class, 'pendingManifestOrder'])->name('administrator.pendingManifestOrder');
    Route::get('/administrator/ops/export-pending-manifest', [AdminController::class, 'exportPendingManifestOrder'])->name('administrator.pendingManifestOrder.export');

    Route::get('/administrator/ops/pending-pickup', [AdminController::class, 'pendingPickupOrder'])->name('administrator.pendingPickupOrder');
    Route::get('/administrator/ops/export-pending-pickup', [AdminController::class, 'exportPendingPickupOrder'])->name('administrator.pendingPickupOrder.export');

    Route::get('/administrator/ops/zone-mapping', [AdminController::class, 'zoneMapping'])->name('administrator.zoneMapping');
    Route::post('/administrator/ops/zone-mapping', [AdminController::class, 'addZoneMapping'])->name('administrator.zoneMapping.add');
    Route::get('/administrator/get-pincode', [AdminController::class, 'getPincodeDetail'])->name('administrator.getPincode');

    Route::get('/administrator/ops/generate-order-request-payload', [AdminController::class, 'generateOrderRequestPayload'])->name('administrator.generateOrderRequestPayload');

    Route::get('/administrator/courier-cod-remittance', [AdminController::class, 'courier_cod_remittance'])->name('administrator.courier_cod_remittance');
    Route::post('/administrator/courier-cod-remittance', [AdminController::class, 'upload_courier_cod_remittance'])->name('administrator.upload_courier_cod_remittance.add');
    Route::get('/administrator/export-courier-cod-remittance', [AdminController::class, 'exportCourierCodRemittanceLog'])->name('administrator.exportCourierCodRemittanceLog');

    // Seller COD Remittance Routes
    Route::get('administrator/seller-cod-remittance', [AdminController::class, 'sellerCODRemittance'])->name('administrator.seller_cod_remittance');
    Route::post('administrator/seller-cod-remittance', [AdminController::class, 'uploadSellerCODRemmitance'])->name('administrator.upload_seller_cod_remittance.add');
    Route::get('administrator/export-seller-cod-remittance', [AdminController::class, 'exportSellerCodRemittanceLog'])->name('administrator.exportSellerCodRemittanceLog');
    Route::get('administrator/get-seller-cod-remittance-by-id/{id}',[AdminController::class,'getSellerRemitDetails'])->name('administrator.getSellerRemitDetails');

});

Route::post('/administrator/import-zone', [AdminController::class, 'importZone'])->name('administratorZone.importZone');

//Seller Registration and Login Routes
Route::get('/login', [SellerController::class, 'login'])->name('seller.login');
Route::get('/register', [SellerController::class, 'register'])->name('seller.register');
Route::get('/seller-forget', [SellerController::class, 'forget'])->name('seller.forget');
Route::get('/seller-verify-otp/{otp}/{ref}', [SellerController::class, 'verify_otp'])->name('seller.check_forget_otp');
Route::get('/reset-seller-password', [SellerController::class, 'reset_seller_password'])->name('seller.reset_seller_password');
Route::get('/seller-logout', [SellerController::class, 'logout'])->name('seller.logout');


Route::post('/seller-submit-forget', [SellerController::class, 'submit_forget'])->name('seller.submit_forget');
Route::post('/submit-register', [SellerController::class, 'submit_register'])->name('seller.submit_register');
Route::post('/seller-check-login', [SellerController::class, 'check_login'])->name('seller.check_login');


Route::get('/check-seller-email/{email}', [SellerController::class, 'check_email'])->name('seller.check_email');
Route::get('/check-seller-mobile/{mobile}', [SellerController::class, 'check_mobile'])->name('seller.check_mobile');

//login with google
Route::get('/google-request', [SellerController::class, 'google_request'])->name('google.request');
Route::get('/google-response', [SellerController::class, 'google_response'])->name('google.response');

//login with facebook
Route::get('/facebook-request', [SellerController::class, 'facebook_request'])->name('facebook.request');
Route::get('/facebook-response', [SellerController::class, 'facebook_response'])->name('facebook.response');

Route::get('/seller-otp', [SellerController::class, 'otp'])->name('seller.otp');
Route::post('/seller-submit-otp', [SellerController::class, 'submit_otp'])->name('seller.submit_otp');

Route::get('/check-verify-mobile/{mobile}', [OperationController::class, 'check_mobile'])->name('seller.check_verify_mobile');

Route::get('/seller-otp-verify/{otp}/{mobile}', [SellerController::class, 'OtpIsVerified'])->name('seller.check_otp_verified');
Route::get('/check-seller-email1/{email}', [SellerController::class, 'check_email1'])->name('seller.check_email1');

Route::get('/seller-otp-verify1/{otp}', [SellerController::class, 'OtpIsVerified1'])->name('seller.check_otp_verified1');
Route::get('/check-SellerVerifyByOtpMobile/{mobile}/{email}', [SellerController::class, 'SellerVerifyByOtpMobile'])->name('seller.SellerVerifyByOtpMobile');


//All Seller Routes that will be checked by Session
Route::middleware([CheckSellerSession::class, CheckKYC::class])->group(function () {

    //Check Bulk Ship Is Running Or Not
    Route::get('check-bulk-ship-running',[SellerController::class,'checkBulkShipRunning']);

    //COD Remit Routes
    Route::get('get-cod-remit',[SellerController::class,'getCodRemitAmount']);
    Route::post('submit-cod-remit-recharge',[SellerController::class,'submitCodRemitRecharge']);

    Route::get('/get-shopify-tag/{id}',[SellerController::class, 'getShopifyTag'])->name('seller.getShopifyTag');

    Route::get('/per_page_record/{page}', [SellerController::class, 'setPerPageRecord'])->name('seller.setPerPageRecord');
    Route::get('/fetch-processed-orders', [SellerController::class, 'fetchProcessedOrders'])->name('seller.get_processed_orders');

    Route::get('/dashboard', [SellerController::class, 'dashboard'])->name('seller.dashboard');
    Route::post('/set_date_dashboard', [SellerController::class, 'set_date_dashboard'])->name('seller.set_date_dashboard');
    Route::get('/reset_date_dashboard', [SellerController::class, 'reset_date_dashboard'])->name('seller.reset_date_dashboard');
//    Route::get('/settings', [SellerController::class, 'settings'])->name('seller.settings');
    Route::get('/settings-partner', [SellerController::class, 'settings_partner'])->name('seller.settings_partner');
    Route::post('/seller-set_courier_partner', [SellerController::class, 'set_courier_partner'])->name('seller.set_courier_partner');
    Route::get('/seller-mark-all-as-read', [SellerController::class, 'mark_all_as_read'])->name('seller.mark_all_as_read');
    Route::get('/check-warehouse-email/{id}', [SellerController::class, 'check_warehouse'])->name('seller.check_warehouse');

    // update order Address
    Route::post('seller/update-delivery-address',[OperationController::class,'updateDeliveryAddress'])->name('seller.update-delivery-address');
    Route::post('seller/update-invoice-amount',[OperationController::class,'updateInvoiceAmount'])->name('seller.update-invoice-amount');

    //International CSV Import
    Route::post('/import_csv-order-international', [OperationController::class, 'import_csv_international_order'])->name('seller.import_csv_order_international');

    // Reports
    Route::get('seller/report-status',[OperationController::class,'reports'])->name('seller.report-status');



    //new data
    Route::get('/seller-dashboard-micro', [OperationController::class, 'dashboard1'])->name('seller.dashboard1');
    Route::get('/seller-dashboardTop', [OperationController::class, 'dashboardTop'])->name('seller.dashboardTop');
    Route::get('/seller-dashboardOrderShipment', [OperationController::class, 'dashboardOrderShipment'])->name('seller.dashboardOrderShipment');
    Route::get('/seller-dashboardNdrData', [OperationController::class, 'dashboardNdrData'])->name('seller.dashboardNdrData');
    Route::get('/seller-dashboardCodData', [OperationController::class, 'dashboardCodData'])->name('seller.dashboardCodData');
    Route::get('/seller-dashboardRtoData', [OperationController::class, 'dashboardRtoData'])->name('seller.dashboardRtoData');
    Route::get('/seller-dashboardCourierSplitData', [OperationController::class, 'dashboardCourierSplitDatas'])->name('seller.dashboardCourierSplitData');
    Route::get('/seller-dashboardOverallData', [OperationController::class, 'dashboardOverallData'])->name('seller.dashboardOverallData');
    Route::get('/seller-dashboardDeliveredData', [OperationController::class, 'dashboardDeliveredData'])->name('seller.dashboardDeliveredData');
    Route::get('/seller-dashboardStateData', [OperationController::class, 'dashboardStateData'])->name('seller.dashboardStateData');
    Route::get('/seller-dashboardShipmentData', [OperationController::class, 'dashboardShipmentData'])->name('seller.dashboardShipmentData');
    Route::get('/seller-dashboardOverviewRevenue', [OperationController::class, 'dashboardOverviewRevenue'])->name('seller.dashboardOverviewRevenue');
    Route::get('/seller-dashboardShipmentByCourierData', [OperationController::class, 'dashboardShipmentByCourierDataInfo'])->name('seller.dashboardShipmentByCourierData');
    Route::get('/seller-dashboardOrderTop', [OperationController::class, 'dashboardOrderTop'])->name('seller.dashboardOrderTop');
    Route::get('/seller-dashboardPrepaidOrder', [OperationController::class, 'dashboardPrepaidOrder'])->name('seller.dashboardPrepaidOrder');
    Route::get('/seller-dashboardbuyerOrder', [OperationController::class, 'dashboardbuyerOrder'])->name('seller.dashboardbuyerOrder');
    Route::get('/seller-dashboardLocationOrder', [OperationController::class, 'dashboardLocationOrder'])->name('seller.dashboardLocationOrder');
    Route::get('/seller-dashboardCustomerOrder', [OperationController::class, 'dashboardCustomerOrder'])->name('seller.dashboardCustomerOrder');
    Route::get('/seller-dashboardProductOrder', [OperationController::class, 'dashboardProductOrder'])->name('seller.dashboardProductOrder');
    Route::get('/seller-dashboardShipmentZoneWiseData', [OperationController::class, 'dashboardShipmentZoneWiseData'])->name('seller.dashboardShipmentZoneWiseData');
    Route::get('/seller-dashboardShipmentChannelData', [OperationController::class, 'dashboardShipmentChannelData'])->name('seller.dashboardShipmentChannelData');
    Route::get('/seller-dashboardShipmentWeightData', [OperationController::class, 'dashboardShipmentWeightData'])->name('seller.dashboardShipmentWeightData');
    Route::get('/seller-dashboardShipmentZoneData', [OperationController::class, 'dashboardShipmentZoneData'])->name('seller.dashboardShipmentZoneData');
    Route::get('/seller-dashboardCourierTabData', [OperationController::class, 'dashboardCourierTabData'])->name('seller.dashboardCourierTabData');
    Route::get('/seller-dashboardDelayTabData', [OperationController::class, 'dashboardDelayTabData'])->name('seller.dashboardDelayTabData');
    Route::get('/seller-dashboardNdrTopData', [OperationController::class, 'dashboardNdrTopTabData'])->name('seller.dashboardNdrTopData');
    Route::get('/seller-dashboardMiddleTabData', [OperationController::class, 'dashboardMiddleTabData'])->name('seller.dashboardMiddleTabData');
    Route::get('/seller-dashboardNdrSplitData', [OperationController::class, 'dashboardNdrSplitData'])->name('seller.dashboardNdrSplitData');
    Route::get('/seller-dashboardNdrStatusTabData', [OperationController::class, 'dashboardNdrStatusTabData'])->name('seller.dashboardNdrStatusTabData');
    Route::get('/seller-dashboardNdrAttemptTabData', [OperationController::class, 'dashboardNdrAttemptTabData'])->name('seller.dashboardNdrAttemptTabData');
    Route::get('/seller-dashboardNdrSuccessbyZoneTabData', [OperationController::class, 'dashboardNdrSuccessbyZoneTabData'])->name('seller.dashboardNdrSuccessbyZoneTabData');
    Route::get('/seller-dashboardNdrSuccessbyCourierTabData', [OperationController::class, 'dashboardNdrSuccessbyCourierTabData'])->name('seller.dashboardNdrSuccessbyCourierTabData');
    Route::get('/seller-dashboardRtoDetailTabData', [OperationController::class, 'dashboardRtoDetailTabData'])->name('seller.dashboardRtoDetailTabData');
    Route::get('/seller-dashboardRtoCountTabData', [OperationController::class, 'dashboardRtoCountTabData'])->name('seller.dashboardRtoCountTabData');
    Route::get('/seller-dashboardRtoStatusTabData', [OperationController::class, 'dashboardRtoStatusTabData'])->name('seller.dashboardRtoStatusTabData');
    Route::get('/seller-dashboardRtoReasonTabData', [OperationController::class, 'dashboardRtoReasonTabData'])->name('seller.dashboardRtoReasonTabData');
    Route::get('/seller-dashboardRtoPincodeTabData', [OperationController::class, 'dashboardRtoPincodeTabData'])->name('seller.dashboardRtoPincodeTabData');
    Route::get('/seller-dashboardRtoCourierTabData', [OperationController::class, 'dashboardRtoCourierTabData'])->name('seller.dashboardRtoCourierTabData');


    //Profile
    Route::get('/seller-profile', [SellerController::class, 'profile'])->name('seller.profile');
    Route::post('/seller-update_profile', [SellerController::class, 'update_profile'])->name('seller.update_profile');

    //Change Password
    Route::get('/seller-change-password', [SellerController::class, 'change_password'])->name('seller.change_password');
    Route::get('/check-old-password/{password}', [SellerController::class, 'checkOldPassword'])->name('seller.checkOldPassword');
    Route::post('/seller-update-profile', [SellerController::class, 'update_password'])->name('seller.update_password');

    Route::get('/complete-kyc', [SellerController::class, 'kyc'])->name('seller.kyc')->withoutMiddleware([CheckKYC::class]);
    Route::post('/basic-information', [SellerController::class, 'basic_information'])->name('seller.basic_information')->withoutMiddleware([CheckKYC::class]);
    Route::post('/account-information', [SellerController::class, 'account_information'])->name('seller.account_information')->withoutMiddleware([CheckKYC::class]);
    Route::post('/kyc-information', [SellerController::class, 'kyc_information'])->name('seller.kyc_information')->withoutMiddleware([CheckKYC::class]);
    Route::post('/agreement-information', [SellerController::class, 'agreement_information'])->name('seller.agreement_information')->withoutMiddleware([CheckKYC::class]);
    Route::get('/pincode-detail/{pincode}', [SellerController::class, 'get_pincode_details'])->name('seller.pincode_detail')->withoutMiddleware([CheckKYC::class]);
    Route::get('/ifsc-detail/{ifsc}', [SellerController::class, 'get_ifsc_detail'])->name('seller.get_ifsc_detail')->withoutMiddleware([CheckKYC::class]);


    //for managing the warehouse of the seller
    Route::get('/my-warehouse', [SellerController::class, 'warehouses'])->name('seller.warehouses');
    Route::post('/add-warehouse', [SellerController::class, 'add_warehouses'])->name('seller.add_warehouse');
    Route::get('/delete-warehouse/{id}', [SellerController::class, 'delete_warehouse'])->name('delete_warehouse');
    Route::get('/modify-warehouse/{id}', [SellerController::class, 'modify_warehouse'])->name('modify_warehouse');
    Route::post('/update-warehouse', [SellerController::class, 'update_warehouse'])->name('update_warehouse');
    Route::post('/remove-selected-warehouse', [SellerController::class, 'remove_selected_warehouse'])->name('seller.remove_selected_warehouse');
    Route::get('/make-default/{id}', [SellerController::class, 'make_default_warehouse'])->name('seller.make_default_warehouse');

    //for managing the employees of the seller
    Route::get('/my-employees', [SellerController::class, 'employees'])->name('seller.employees');
    Route::post('/add-employees', [SellerController::class, 'add_employees'])->name('seller.add_employees');
    Route::get('/delete-employees/{id}', [SellerController::class, 'delete_employees'])->name('seller.delete_employees');
    Route::get('/modify-employees/{id}', [SellerController::class, 'modify_employees'])->name('seller.modify_employees');
    Route::post('/update-employees', [SellerController::class, 'update_employees'])->name('seller.update_employees');
    Route::post('/remove-selected-employee', [SellerController::class, 'remove_selected_employee'])->name('seller.remove_selected_employee');
    Route::get('/check-employee-email/{email}', [SellerController::class, 'check_employee_email'])->name('seller.check_employee_email');


    //for managing the orders of the seller
    Route::get('/my-orders', [SellerController::class, 'orders'])->name('seller.orders');
    Route::get('/merge-orders', [SellerController::class, 'merge_orders'])->name('seller.merge_orders');
    Route::post('/merge-orders/set_filter', [SellerController::class, 'mergeSetFilter'])->name('seller.merge_orders.set_filter');
    Route::get('/merge-orders/ajax_filter_order', [SellerController::class, 'merge_ajax_filter_order'])->name('seller.merge_orders.ajax_filter_order');
    Route::get('/merge-orders/reset_key/{key}', [SellerController::class, 'mergeResetFilter'])->name('seller.merge_orders.resetFilter');
    Route::get('/get-merge-orders', [SellerController::class, 'get_merge_orders'])->name('seller.merge_orders.get');
    Route::post('/merge-order', [SellerController::class, 'merge_order'])->name('seller.merge_order');

    // split orders routes
    Route::get('/split-orders', [SellerController::class, 'split_orders'])->name('seller.split_orders');
    Route::post('/split-orders/set_filter', [SellerController::class, 'splitSetFilter'])->name('seller.split_orders.set_filter');
    Route::get('/split-orders/ajax_filter_order', [SellerController::class, 'split_ajax_filter_order'])->name('seller.split_orders.ajax_filter_order');
    Route::get('/split-orders/reset_key/{key}', [SellerController::class, 'splitResetFilter'])->name('seller.split_orders.resetFilter');
    Route::get('/get-split-orders', [SellerController::class, 'get_split_orders'])->name('seller.split_orders.get');
    Route::post('perform-split-order', [SellerController::class, 'performSplitOrder'])->name('seller.performSplitOrders');
    Route::get('get-order-items/{orderId}', [SellerController::class, 'getOrderItems'])->name('seller.getOrderItems');
    Route::get('create-order', [SellerController::class, 'createOrder'])->name('seller.createOrder');


    Route::post('/add-order', [SellerController::class, 'add_order'])->name('seller.add_order');
    Route::get('get-qc-information/{id}',[SellerController::class,'getQcInformation']);
    Route::get('/quick-order', [SellerController::class, 'quick_order'])->name('seller.quick_order');
    Route::post('/ship-quick-order', [SellerController::class, 'ship_quick_order'])->name('seller.ship_quick_order');
    Route::get('/modify-order/{id}', [SellerController::class, 'modify_order'])->name('seller.modify_order');
    Route::post('/update-order', [SellerController::class, 'update_order'])->name('seller.update_order');
    Route::post('/import_csv-order', [SellerController::class, 'import_csv_order'])->name('seller.import_csv_order');
    Route::post('/export_csv-order', [SellerController::class, 'export_csv_order'])->name('seller.export_csv_order');
    Route::get('/delete-order/{id}', [SellerController::class, 'delete_order'])->name('seller.delete_order');
    Route::get('/fetch_dimension_data/{weight}', [SellerController::class, 'fetch_dimension_data'])->name('seller.fetch_dimension_data');
    Route::get('/fetch_product_sku/{sku}', [SellerController::class, 'fetch_product_sku'])->name('seller.fetch_product_sku');
    Route::get('/modify_dimension_data/{id}', [SellerController::class, 'modify_dimension_data'])->name('seller.modify_dimension_data');
    Route::post('/modify_multiple_dimension_data', [SellerController::class, 'modify_multiple_dimension_data'])->name('seller.modify_multiple_dimension_data');
    Route::post('modify_dimension', [SellerController::class, 'modify_dimension'])->name('seller.modify_dimension');
    Route::post('modify_multiple_dimension', [SellerController::class, 'modify_multiple_dimension'])->name('seller.modify_multiple_dimension');
    Route::post('/modify_multiple_warehouse', [SellerController::class, 'modify_multiple_warehouse'])->name('seller.modify_multiple_warehouse');
    Route::get('/cancel-order/{id}', [SellerController::class, 'cancel_order'])->name('seller.cancel_order');
    Route::get('/ship-order/{id}', [SellerController::class, 'ship_order'])->name('seller.ship_order');
    Route::get('/ship-order', [SellerController::class, 'get_courier_partner'])->name('seller.get_courier_partners');
    Route::get('/get_courier_charges', [SellerController::class, 'get_courier_charges'])->name('seller.get_courier_charges');
    Route::post('/single_ship-order/', [SellerController::class, 'single_ship_order'])->name('seller.single_ship_order');
    Route::get('/re-assign', [SellerController::class, 'get_reassign_order'])->name('seller.reassign_orders');
    Route::post('/re-assign/set_filter', [SellerController::class, 'reassignSetFilter'])->name('seller.reassign_orders.set_filter');
    Route::get('/re-assign/ajax_filter_order', [SellerController::class, 'reassign_ajax_filter_order'])->name('seller.reassign_orders.ajax_filter_order');
    Route::get('/re-assign/reset_key/{key}', [SellerController::class, 'reassignResetFilter'])->name('seller.reassign_orders.resetFilter');
    Route::post('/re-assign-order/', [SellerController::class, 'reassign_order'])->name('seller.reassign_order');
    Route::get('/test-order/{id}', [SellerController::class, 'test_order'])->name('seller.test_order');
    Route::get('/view-order/{id}', [SellerController::class, 'view_order'])->name('seller.view_order');
    Route::post('/remove-selected-order', [SellerController::class, 'remove_selected_order'])->name('seller.remove_selected_order');
    Route::post('/cancel-selected-order', [SellerController::class, 'cancel_selected_order'])->name('seller.cancel_selected_order');
    Route::post('/ship-selected-order', [SellerController::class, 'ship_selected_order'])->name('seller.ship_selected_order');
    Route::post('/total-selected-order', [SellerController::class, 'total_selected_order'])->name('seller.total_selected_order');
    Route::get('/check-warehouse', [SellerController::class, 'check_warehouse'])->name('seller.check_warehouse');
    Route::get('get-shipping_data', [SellerController::class, 'get_shipping_data'])->name('seller.get_shipping_data');
    Route::get('ajax_filter_order', [SellerController::class, 'ajax_filter_order'])->name('seller.ajax_filter_order');
    Route::post('order/set_filter', [SellerController::class, 'setFilter'])->name('seller.set_filter');
    Route::get('order/ready_to_ship', [SellerController::class, 'ready_to_ship'])->name('seller.ready_to_ship');
    Route::get('order/shipped', [SellerController::class, 'order_shipped'])->name('seller.order_shipped');
    Route::get('order/unprocessable', [SellerController::class, 'order_unprocessable'])->name('seller.order_unprocessable');
    Route::get('order/processing', [SellerController::class, 'order_processing'])->name('seller.order_processing');
    Route::get('order/manifest', [SellerController::class, 'order_manifest'])->name('seller.order_manifest');
    Route::get('order/return', [SellerController::class, 'order_return'])->name('seller.order_return');
    Route::get('single_order/invoice/pdf/{id}', [SellerController::class, 'singleInvoicePDF'])->name('seller.singleInvoicePDF');
    Route::get('single_order/lable/pdf/{id}', [SellerController::class, 'singleLablePDF'])->name('seller.singleLablePDF');
    Route::get('single_order_archive/lable/pdf/{id}', [SellerController::class, 'singleLableForArchivePDF'])->name('seller.singleArchiveLablePDF');
    Route::get('order/invoice/pdf/{id}', [SellerController::class, 'InvoicePDF'])->name('seller.InvoicePDF');
    Route::get('order/label/pdf/{id}', [SellerController::class, 'LablePDF'])->name('seller.LablePDF');
    Route::get('order/manifest/pdf/{id}', [SellerController::class, 'ManifestPDF'])->name('seller.manifestPDF');
    Route::post('/generate-manifest', [SellerController::class, 'generateManifest'])->name('seller.generate_manifest');
    Route::post('/multi-invoice-download', [SellerController::class, 'multipleInvoiceDownload'])->name('seller.multipleInvoiceDownload');
    Route::post('/multi-lable-download', [SellerController::class, 'multipleLableDownload'])->name('seller.multipleLableDownload');
    Route::post('/multi-manifest-download', [SellerController::class, 'multipleManifest'])->name('seller.multipleManifest');

    Route::get('get-order-status/{awb}', [SellerController::class, 'getOrderStatus'])->name('seller.getOrderStatus');
    Route::get('/get-awb', [SellerController::class, 'getAwbNumber'])->name('seller.getAwbNumber');
    Route::get('/sync_status', [SellerController::class, 'syncStatus'])->name('seller.syncStatus');
    Route::get('/fetch_order_status', [SellerController::class, 'fetch_order_status'])->name('seller.fetch_order_status');
    Route::get('/all_order', [SellerController::class, 'allOrder'])->name('seller.all_order');
    Route::any('seller/load-all-order', [SellerController::class, 'loadAllOrder'])->name('seller.load-all-order');
    Route::any('seller/export-order-data', [SellerController::class, 'exportOrderData'])->name('seller.export-order-data');
    Route::any('seller/load-all-manifest-order', [SellerController::class, 'loadAllManifestOrder'])->name('seller.load-all-manifest-order');

    // Load Warehouse in Order Page
    Route::get('ajax/load-all-warehouse',[OperationController::class,'loadAllWarehouse'])->name('ajax.load-all-warehouse');
    Route::post('ajax/create-warehouse-order',[OperationController::class,'createWarehouseOrder'])->name('ajax.create-warehouse-order');
    Route::get('ajax/refresh-seller-balance',[OperationController::class,'loadSellerBalance'])->name('ajax.refresh-seller-balance');

    //twinship
    Route::get('/all_moreonorder', [SellerController::class, 'allMoreOnOrder'])->name('seller.all_moreonorder');
    Route::any('seller/load-all-moreonorder', [SellerController::class, 'loadAllMoreOnOrder'])->name('seller.load-all-moreonorder');
    Route::any('seller/load-all-split', [SellerController::class, 'loadAllSplitOrder'])->name('seller.load-all-split');
    Route::any('seller/load-all-merge', [SellerController::class, 'loadAllMergeOrder'])->name('seller.load-all-merge');
    Route::any('seller/export-moreonorder-data', [SellerController::class, 'exportMoreOnOrderData'])->name('seller.export-moreonorder-data');

    Route::get('/all_reverse_order', [SellerController::class, 'allReverseOrder'])->name('seller.all_reverse_order');
    Route::get('/count_order', [SellerController::class, 'countOrder'])->name('seller.countOrder');
    Route::get('/reset_key/{key}', [SellerController::class, 'resetFilter'])->name('seller.resetFilter');
    Route::get('/clone_order/{order_number}', [SellerController::class, 'cloneOrder'])->name('seller.cloneOrder');
    Route::post('/all_order_searcing/', [SellerController::class, 'all_order_searching'])->name('seller.all_order_searching');
    Route::post('/processing_searching/', [SellerController::class, 'processing_searching'])->name('seller.processing_searching');



    //twinship
    Route::get('/all_shipment', [SellerController::class, 'allShipment'])->name('seller.all_shipment');
    Route::any('seller/load-all-shipment', [SellerController::class, 'loadAllShipment'])->name('seller.load-all-shipment');

    Route::get('/weight-reconciliation', [SellerController::class, 'weightReconcilations'])->name('seller.weight-reconciliation');
    //twinship
    Route::get('/all_billing', [SellerController::class, 'allBilling'])->name('seller.all_billing');
    Route::any('seller/load-all-billing', [SellerController::class, 'loadAllBilling'])->name('seller.load-all-billing');

    //twinship
    Route::any('seller/load-all-remitance', [SellerController::class, 'loadAllRemitance'])->name('seller.load-all-remitance');
    Route::any('seller/load-all-recharge', [SellerController::class, 'loadAllRecharge'])->name('seller.load-all-recharge');
    Route::any('seller/load-all-invoice', [SellerController::class, 'loadAllInvoice'])->name('seller.load-all-invoice');
    Route::any('seller/load-all-passbook', [SellerController::class, 'loadAllPassbook'])->name('seller.load-all-passbook');
    Route::any('seller/load-all-receipt', [SellerController::class, 'loadAllReceipt'])->name('seller.load-all-receipt');
    Route::any('seller/load-all-wallet', [SellerController::class, 'loadAllWallet'])->name('seller.load-all-wallet');

    //for managing the ndr orders of the seller
    Route::get('/ndr-orders', [SellerController::class, 'ndr_orders'])->name('seller.ndr_orders');
    Route::post('/import_csv-ndr', [SellerController::class, 'import_ndr_order'])->name('seller.import_ndr_order');
    Route::get('/export_csv-ndr', [SellerController::class, 'export_ndr_order'])->name('seller.export_ndr_order');
    Route::post('order/set_filter_ndr', [SellerController::class, 'setFilterNDR'])->name('seller.set_filter_ndr');
    Route::get('/ajax_filter_ndr', [SellerController::class, 'ajax_filter_ndr'])->name('seller.ajax_filter_ndr');
    Route::get('/ndr_action_required', [SellerController::class, 'ndrActionRequired'])->name('seller.ndr_action_required');
    Route::get('/ndr_action_requested', [SellerController::class, 'ndrActionRequested'])->name('seller.ndr_action_requested');
    Route::get('/ndr_delivered', [SellerController::class, 'ndrDelivered'])->name('seller.ndr_delivered');
    Route::get('/ndr_rto', [SellerController::class, 'ndrRTO'])->name('seller.ndr_rto');
    Route::post('ndr-reattempt-order', [SellerController::class, 'ndrReattempOrder'])->name('seller.ndr_reattempt_order');
    Route::post('ndr-rto-order', [SellerController::class, 'ndrRTOOrder'])->name('seller.ndr_rto_order');
    Route::get('/ndr-escalate-order/{id}', [SellerController::class, 'ndrEscalateOrder'])->name('seller.ndr_escalate_order');
    Route::post('/ndr-ivr', [SellerController::class, 'ndrIvr'])->name('seller.ndrIvr');
    Route::get('/count_order_ndr', [SellerController::class, 'countOrderNdr'])->name('seller.countOrderNdr');
    Route::get('/reset_key_ndr/{key}', [SellerController::class, 'resetFilterNDR'])->name('seller.resetFilterNDR');
    Route::get('/view_ndr_history/{id}', [SellerController::class, 'view_ndr_history'])->name('seller.view_ndr_history');



    //Bulk NDR Actions
    Route::post('/bulk-ndr-action', [SellerController::class, 'bulkNDRActions'])->name('seller.bulkNDRAction');


    //for managin the billing of order
    Route::get('/billing', [SellerController::class, 'billing'])->name('seller.billing');
    Route::get('/billing_wallet', [SellerController::class, 'billingWallet'])->name('seller.billing_wallet');
    Route::get('/count_billing', [SellerController::class, 'countBilling'])->name('seller.countBilling');
    Route::any('/billing_weight_reconciliation', [SellerController::class, 'billingWeightReconciliation'])->name('seller.billing_weight_reconciliation');
    Route::post('/dispute_order', [SellerController::class, 'disputeOrder'])->name('seller.disputeOrder');
    Route::get('/weight_reconciliation_accept_order/{id}', [SellerController::class, 'weightReconciliationAcceptOrder'])->name('seller.weightReconciliationAcceptOrder');
    Route::get('/get_history_weight_reconciliation/{awb}', [SellerController::class, 'getHistoryWeightReconciliation'])->name('seller.getHistoryWeightReconciliation');
    Route::post('/add_weigth_rec_comment', [SellerController::class, 'addWeightRecComment'])->name('seller.addWeightRecComment');
    Route::get('/billing_remittance_log', [SellerController::class, 'billingRemmitanceLog'])->name('seller.billing_remittance_log');
    Route::get('/export_administrator_remittance/{id}', [SellerController::class, 'export_administrator_remittance'])->name('seller.export_administrator_remittance');
    Route::get('/billing_recharge_log', [SellerController::class, 'billingRechargeLog'])->name('seller.billing_recharge_log');
    Route::get('/billing_onhold', [SellerController::class, 'billingOnhold'])->name('seller.billing_onhold');
    Route::get('/billing_passbook', [SellerController::class, 'billingPassbook'])->name('seller.billing_passbook');
    Route::get('/billing_receipt', [SellerController::class, 'billingReceipt'])->name('seller.billing_receipt');
    Route::get('/export_receipt_details/{id}', [SellerController::class, 'exportReceiptDetails'])->name('seller.exportReceiptDetails');
    Route::get('/receipt_invoice/{id}', [SellerController::class, 'receiptInvoice'])->name('seller.receiptInvoice');
    Route::get('/billing_invoice', [SellerController::class, 'billingInvoice'])->name('seller.billing_invoice');
    Route::get('/billing_other_invoice', [SellerController::class, 'billingOtherInvoice'])->name('seller.billing_other_invoice');
    Route::get('/billing_other_invoice/view/{id}', [SellerController::class, 'billingOtherInvoiceView'])->name('seller.billing_other_invoice_view');
    Route::get('/billing_other_invoice/pdf/{id}', [SellerController::class, 'billingOtherInvoicePDf'])->name('seller.billing_other_invoice_pdf');
    Route::get('/view_transaction_data/{id}', [SellerController::class, 'viewTransaction'])->name('seller.viewTransaction');
    Route::get('billing/invoice/pdf/{id}', [SellerController::class, 'BillingInvoiceView'])->name('seller.BillingInvoiceView');
    Route::get('billing/invoice/download/{id}', [SellerController::class, 'BillingInvoicePDF'])->name('seller.BillingInvoicePDF');
    Route::get('billing/invoice/csv/{id}', [SellerController::class, 'BillingInvoiceCSV'])->name('seller.BillingInvoiceCSV');
    Route::post('order/set_filter_billing', [SellerController::class, 'setFilterBilling'])->name('seller.set_filter_billing');
    Route::get('ajax_filter_billing', [SellerController::class, 'ajax_filter_billing'])->name('seller.ajax_filter_billing');
    Route::get('/reset_key_billing/{key}', [SellerController::class, 'resetFilterBilling'])->name('seller.resetFilterBilling');
    Route::get('/billing/ajax_shipping_charges', [SellerController::class, 'ajax_shipping_charges'])->name('seller.ajax_shipping_charges');
    Route::post('/billing/export_shipping_details', [SellerController::class, 'exportShippingDetails'])->name('seller.export_shipping_details');
    Route::post('/billing/export_passbook_details', [SellerController::class, 'exportPassbookDetails'])->name('seller.export_passbook_details');
    Route::get('billing/reset_filters', [SellerController::class, 'reset_filters'])->name('seller.reset_filters');

    // Route for weight added by ravishankar please contact @ravishankar for any issue
    Route::get('/weight', [OperationController::class, 'weight'])->name('seller.weight');

    //Routing For Managing Dashboard Ajax Data Tab
    Route::get('/seller-dashboard/counter', [SellerController::class, 'dashboardCounter'])->name('seller.dashboardCounter');
    Route::get('/seller-dashboard/overview', [SellerController::class, 'dashboardOverview'])->name('seller.dashboardOverview');
    Route::get('/seller-dashboard/order', [SellerController::class, 'dashboardOrder'])->name('seller.dashboardOrder');
    Route::get('/seller-dashboard/shipment', [SellerController::class, 'dashboardShipment'])->name('seller.dashboardShipment');
    Route::get('/seller-dashboard/ndr', [SellerController::class, 'dashboardNdr'])->name('seller.dashboardNdr');
    Route::get('/seller-dashboard/rto', [SellerController::class, 'dashboardRTO'])->name('seller.dashboardRTO');
    Route::get('/seller-dashboard/courier', [SellerController::class, 'dashboardCourier'])->name('seller.dashboardCourier');
    Route::get('/seller-dashboard/delays', [SellerController::class, 'dashboardDelays'])->name('seller.dashboardDelays');
    Route::get('/seller-dashboard/get-custom-date-report', [SellerController::class, 'getCustomDateOrder'])->name('seller.getCustomDateOrder');


    //Routes for MIS Report
    Route::get('/mis_report', [SellerController::class, 'mis_report'])->name('seller.mis_report');
    Route::get('pickup-reports', [OperationController::class, 'pickupReports'])->name('seller.pickup_reports');
    Route::post('/ajax_mis_report_data', [SellerController::class, 'ajaxReportData'])->name('seller.ajaxReportData');
    Route::get('/export_report_data', [SellerController::class, 'export_report_data'])->name('seller.export_report_data');

    //Routes for Customer Support
    Route::get('/customer_support', [SellerController::class, 'customerSupport'])->name('seller.customer_support');
    Route::post('/add-escalation', [SellerController::class, 'add_escalation'])->name('seller.add_escalation');
    Route::post('/add-escalation_comment', [SellerController::class, 'add_escalation_comment'])->name('seller.add_escalation_comment');
    Route::get('view-escalation/{id}', [SellerController::class, 'view_escalation'])->name('seller.view_escalation');
    Route::get('/close-ticket/{id}', [SellerController::class, 'close_ticket'])->name('seller.close_ticket');
    Route::post('escalate-ticket', [SellerController::class, 'escalateTicket'])->name('seller.escalateTicket');


    //Rate Calculator Route
    Route::get('/rate-calculator', [SellerController::class, 'rate_calculator'])->name('seller.rate_calculator');
    Route::post('/get-calculated-rates', [SellerController::class, 'getCalculatedRates'])->name('seller.get_calculated_rates');
    Route::get('/download-mapping/{pincode}', [SellerController::class, 'download_mapping'])->name('seller.download_mapping');

    //Twinnship API Key
    Route::get('/seller_api_key', [SellerController::class, 'seller_api_key'])->name('seller.seller_api_key');
    Route::post('/generate_api_key', [SellerController::class, 'generate_api_key'])->name('seller.generate_api_key');

    //Servicable Pincode Download
    Route::get('/serviceable_pincode', [SellerController::class, 'serviceable_pincode'])->name('seller.serviceable_pincode');
    Route::post('/serviceable_pincode/download', [SellerController::class, 'download_serviceable_pincode'])->name('seller.download_serviceable_pincode');

    //for managing the employees of the seller
    Route::get('/my-channels', [SellerController::class, 'channels'])->name('seller.channels');
    Route::post('/add-channels', [SellerController::class, 'add_channels'])->name('seller.add_channels');
    Route::get('/delete-channels/{id}', [SellerController::class, 'delete_channels'])->name('seller.delete_channels');
    Route::get('/modify-channels/{id}', [SellerController::class, 'modify_channels'])->name('seller.modify_channels');
    Route::post('/update-channels', [SellerController::class, 'update_channels'])->name('seller.update_channels');
    Route::post('/remove-selected-channel', [SellerController::class, 'remove_selected_channel'])->name('seller.remove_selected_channel');

    //for managing my oms
    Route::get('/my-oms', [SellerController::class, 'my_oms'])->name('seller.my_oms');
    Route::get('/get-my-oms-order', [SellerController::class, 'getMyOmsOrder'])->name('seller.my_oms.get');
    Route::post('/my-oms/set_filter', [SellerController::class, 'myOmsSetFilter'])->name('seller.my_oms.set_filter');
    Route::get('/my-oms/ajax_filter_order', [SellerController::class, 'my_oms_ajax_filter_order'])->name('seller.my_oms.ajax_filter_order');
    Route::get('/my-oms/reset_key/{key}', [SellerController::class, 'myOmsResetFilter'])->name('seller.my_oms.resetFilter');
    Route::get('/my-oms/modify-order/{id}', [SellerController::class, 'my_oms_modify_order'])->name('seller.my_oms.modify');
    Route::post('/my-oms/update-order', [SellerController::class, 'my_oms_update_order'])->name('seller.my_oms.update');
    Route::get('/my-oms/delete-order/{id}', [SellerController::class, 'my_oms_delete_order'])->name('seller.my_oms.delete');
    Route::post('/my-oms/remove-selected-order', [SellerController::class, 'my_oms_remove_selected_order'])->name('seller.my_oms.remove_selected_order');
    Route::post('/export-my-oms-order', [SellerController::class, 'export_csv_my_oms_order'])->name('seller.export_csv_my_oms_order');
    Route::post('/import-my-oms-order', [SellerController::class, 'import_csv_my_oms_order'])->name('seller.import_csv_my_oms_order');
    Route::get('/my-oms/single_order/lable/pdf/{id}', [SellerController::class, 'myOmsSingleLablePDF'])->name('seller.my_oms.singleLablePDF');

    //for managing oms
    Route::get('/oms', [SellerController::class, 'oms'])->name('seller.oms');
    Route::get('/delete-oms/{id}', [SellerController::class, 'delete_oms'])->name('seller.delete_oms');

    //All Routes for adding oms easyship
    Route::get('/oms-add-easyship', [SellerController::class, 'oms_add_easyship'])->name('seller.oms_add_easyship');
    Route::post('/oms-submit-easyship', [SellerController::class, 'oms_submit_easyship'])->name('seller.oms_submit_easyship');

    //All Routes for adding oms easyecom
    Route::get('/oms-add-easyecom', [SellerController::class, 'oms_add_easyecom'])->name('seller.oms_add_easyecom');
    Route::post('/oms-submit-easyecom', [SellerController::class, 'oms_submit_easyecom'])->name('seller.oms_submit_easyecom');

    //All Routes for adding oms clickpost
    Route::get('/oms-add-clickpost', [SellerController::class, 'oms_add_clickpost'])->name('seller.oms_add_clickpost');
    Route::post('/oms-submit-clickpost', [SellerController::class, 'oms_submit_clickpost'])->name('seller.oms_submit_clickpost');

    //All Routes for adding oms omsguru
    Route::get('/oms-add-omsguru', [SellerController::class, 'oms_add_omsguru'])->name('seller.oms_add_omsguru');
    Route::post('/oms-submit-omsguru', [SellerController::class, 'oms_submit_omsguru'])->name('seller.oms_submit_omsguru');

    //All Routes for adding oms vineretail
    Route::get('/oms-add-vineretail', [SellerController::class, 'oms_add_vineretail'])->name('seller.oms_add_vineretail');
    Route::post('/oms-submit-vineretail', [SellerController::class, 'oms_submit_vineretail'])->name('seller.oms_submit_vineretail');

    //All Routes for adding oms unicommerce
    Route::get('/oms-add-unicommerce', [SellerController::class, 'oms_add_unicommerce'])->name('seller.oms_add_unicommerce');
    Route::post('/oms-submit-unicommerce', [SellerController::class, 'oms_submit_unicommerce'])->name('seller.oms_submit_unicommerce');


    //All Routes for adding channels shopify
    Route::get('/add-shopify', [SellerController::class, 'add_shopify'])->name('seller.add_shopify');
    Route::post('/submit-shopify', [SellerController::class, 'submit_shopify'])->name('seller.submit_shopify');

    //All Routes for adding channels shopify
    Route::get('seller/add-shopify-new', [OperationController::class, 'addShopifyNew'])->name('seller.add-shopify-new');
    Route::post('seller/submit-shopify-new', [OperationController::class, 'submitShopifyNew'])->name('seller.submit-shopify-new');

    //All Routes for adding channels shopify
    Route::get('/add-amazon', [SellerController::class, 'add_amazon'])->name('seller.add_amazon');
    Route::post('/submit-amazon', [SellerController::class, 'submit_amazon'])->name('seller.submit_amazon');

    //All Routes for adding channels shopify
    Route::get('/add-amazon-direct', [SellerController::class, 'add_amazon_direct'])->name('seller.add_amazon_direct');
    Route::post('/submit-amazon-direct', [SellerController::class, 'submit_amazon_direct'])->name('seller.submit_amazon_direct');

    //All Routes for adding channels flipkart
    Route::get('add-flipkart', [OperationController::class, 'addFlipkart'])->name('seller.add-flipkart');
    Route::post('submit-flipkart', [OperationController::class, 'submitFlipkart'])->name('seller.submit-flipkart');
    Route::get('oauth/flipkart-redirect',[OperationController::class,'flipkartRedirect']);

    // Re-Authorize Amazon Direct Channel
    Route::get('seller/reauthorize-amazon-direct/{channel}',[OperationController::class,'reAuthorizeAmazonDirect'])->name('seller.reauthorize-amazon-direct');
    //All Routes for adding channels WooCommerce
    Route::get('/add-woocommerce', [SellerController::class, 'add_woocommerce'])->name('seller.add_woocommerce');
    Route::post('/submit-woocommerce', [SellerController::class, 'submit_woocommerce'])->name('seller.submit_woocommerce');

    //All Routes for adding channels magento
    Route::get('/add-magento', [SellerController::class, 'add_magento'])->name('seller.add_magento');
    Route::post('/submit-magento', [SellerController::class, 'submit_magento'])->name('seller.submit_magento');

    //All Routes for adding channels Store Hippo
    Route::get('/add-storehippo', [SellerController::class, 'add_storehippo'])->name('seller.add_storehippo');
    Route::post('/submit-storehippo', [SellerController::class, 'submit_storehippo'])->name('seller.submit_storehippo');

    //All Routes for adding channels kartrocket
    Route::get('/add-kartrocket', [SellerController::class, 'add_kartrocket'])->name('seller.add_kartrocket');
    Route::post('/submit-kartrocket', [SellerController::class, 'submit_kartrocket'])->name('seller.submit_kartrocket');

    // verify pan card
    Route::get('verify-pan', [SellerController::class, 'verify_pan'])->name('verify_pan');

    //Twinnship Branded tracking
    Route::get('/brand-track', [OperationController::class, 'brandTrack'])->name('seller.brand_track');
    Route::post('/submit-brand-track', [OperationController::class, 'submitBrandTrack'])->name('seller.submit_brand_track');


    //All Routes for adding Rules
    Route::get('/my-rules', [RulesController::class, 'rules'])->name('seller.rules');
    Route::post('/add-rule', [RulesController::class, 'add_rule'])->name('seller.add_rule');
    Route::get('/modify-rule/{id}', [RulesController::class, 'modify_rule'])->name('seller.modify_rule');
    Route::get('/delete-rule/{id}', [RulesController::class, 'delete_rule'])->name('seller.delete_rule');
    Route::get('/check-priority', [RulesController::class, 'check_priority'])->name('seller.check_priority');
    Route::get('/check-priority/{value}', [RulesController::class, 'check_priority'])->name('seller.check_priority');
    Route::post('/rule-status', [RulesController::class, 'rule_status'])->name('rule_status');
    Route::post('/update-rule', [RulesController::class, 'update_rule'])->name('seller.update_rule');

    //for managing the SKU of the seller
    Route::get('/my-sku', [SellerController::class, 'sku'])->name('seller.sku');
    Route::get('/ajax-sku', [SellerController::class, 'ajax_sku'])->name('seller.ajax_sku');
    Route::post('/add-sku', [SellerController::class, 'add_sku'])->name('seller.add_sku');
    Route::get('/modify-sku/{id}', [SellerController::class, 'modify_sku'])->name('modify_sku');
    Route::post('/update-sku', [SellerController::class, 'update_sku'])->name('update_sku');
    Route::get('/delete-sku/{id}', [SellerController::class, 'delete_sku'])->name('seller.delete_sku');
    Route::post('/import_csv-sku', [SellerController::class, 'import_csv_sku'])->name('seller.import_csv_sku');
    Route::get('/export_csv-sku', [SellerController::class, 'export_csv_sku'])->name('seller.export_csv_sku');
    Route::post('/remove-selected-sku', [SellerController::class, 'remove_selected_sku'])->name('seller.remove_selected_sku');

    //for managing the SKU mapping of the seller
    Route::get('/my-sku-mapping', [SellerController::class, 'sku_mapping'])->name('seller.sku_mapping');
    Route::get('/ajax-sku-mapping', [SellerController::class, 'ajax_sku_mapping'])->name('seller.ajax_sku_mapping');
    Route::post('/add-sku-mapping', [SellerController::class, 'add_sku_mapping'])->name('seller.add_sku_mapping');
    Route::get('/modify-sku-mapping/{id}', [SellerController::class, 'modify_sku_mapping'])->name('modify_sku_mapping');
    Route::post('/update-sku-mapping', [SellerController::class, 'update_sku_mapping'])->name('update_sku_mapping');
    Route::get('/delete-sku-mapping/{id}', [SellerController::class, 'delete_sku_mapping'])->name('seller.delete_sku_mapping');
    Route::post('/import_csv-sku-mapping', [SellerController::class, 'import_csv_sku_mapping'])->name('seller.import_csv_sku_mapping');
    Route::get('/export_csv-sku-mapping', [SellerController::class, 'export_csv_sku_mapping'])->name('seller.export_csv_sku_mapping');
    Route::post('/remove-selected-sku-mapping', [SellerController::class, 'remove_selected_sku_mapping'])->name('seller.remove_selected_sku_mapping');

    //shopify API Calls
    Route::get('/fetch-shopify', [SellerController::class, 'fetch_shopify'])->name('seller.fetch_shopify');
    Route::get('/send-mail', [SellerController::class, 'send_mail'])->name('seller.test_email');


    //All Routes for Shipping Rates
    Route::get('/shipping_rates', [SellerController::class, 'shipping_rates'])->name('seller.shipping_rates');
    Route::get('/get-shipping_rates', [SellerController::class, 'get_shipping_rates'])->name('seller.get_shipping_rates');

    //Seller Wallet Topup Routes
    Route::post('/confirm-payment', [SellerController::class, 'confirm_payment'])->name('seller.confirm_payment');
    Route::get('/test_order', [SellerController::class, 'test_order'])->name('seller.test_order');
    Route::get('/amazon', [SellerController::class, 'amazon']);
    Route::get('/woocommerce', [SellerController::class, 'woocommerce']);
    Route::get('/fetch-all-orders', [SellerController::class, 'fetch_all_orders'])->name('seller.fetch_all_orders');



    Route::get('/verify-all-orders', [SellerController::class, 'verifyAllOrders'])->name('seller.verify_all_orders');
    Route::get('/generate-manifest', [SellerController::class, 'generateManifest'])->name('seller.generate_manifest');
    Route::post('/pickup-requested', [SellerController::class, 'pickupRequested'])->name('seller.pickup_requested');
    Route::get('/get-awb', [SellerController::class, 'getAwbNumber'])->name('seller.getAwbNumber');

    //routes for recharge success and adding amount to wallet
    Route::post('/create-recharge_order', [SellerController::class, 'create_recharge_order'])->name('seller.create_payment_order')->withoutMiddleware([CheckKYC::class]);
    Route::post('/create-neft-recharge', [SellerController::class, 'create_neft_recharge'])->name('seller.create_neft_recharge')->withoutMiddleware([CheckKYC::class]);
    Route::post('/payment-success', [SellerController::class, 'recharge_success'])->name('seller.payment_success')->withoutMiddleware([CheckKYC::class, \App\Http\Middleware\VerifyCsrfToken::class]);
//    Route::post('apply-promo', [SellerController::class, 'apply_promo'])->name('seller.apply-promo')->withoutMiddleware([CheckKYC::class]);
    Route::post('remit-cod', [SellerController::class, 'remit_cod'])->name('seller.remit_cod')->withoutMiddleware([CheckKYC::class]);
    Route::get('refresh_recharge', [SellerController::class, 'refreshRecharge'])->name('seller.refreshRecharge')->withoutMiddleware([CheckKYC::class]);


    //Customise Label
    Route::get('/customise-label', [SellerController::class, 'customiseLabel'])->name('seller.customiseLabel');
    Route::post('/customise-label', [SellerController::class, 'storeCustomisedLabel'])->name('seller.storeCustomisedLabel');

    //Wow Express Api
    Route::get('/send_order_wowexpress', [SellerController::class, 'wowExpress'])->name('seller.wowExpress')->withoutMiddleware([CheckKYC::class]);
    Route::get('/fetch_wow_servicable', [SellerController::class, 'fetch_wow_servicable']);
    Route::get('/fetch_xpressbees_servicable', [SellerController::class, 'fetch_xpressbees_servicable']);
    Route::get('/fetch_shadowfax_servicable', [SellerController::class, 'fetch_shadowfax_servicable']);
    Route::get('/fetch_udaan_servicable', [SellerController::class, 'fetch_udaan_servicable']);

    //DTDC Air API
    Route::get('/send_order_dtdcair', [SellerController::class, 'dtdcAir'])->name('seller.dtdcAir');
    Route::get('/wow_pickup_performance', [SellerController::class, 'wowPickup'])->name('seller.wowPickup');

    //Settings
    Route::get('/settings', [SellerController::class, 'settings'])->name('seller.settings');



    //For XBees Api
    Route::get('/generate-awb', [WebController::class, 'getAwbNumbersXbees'])->name('web.generate_awb');
    Route::get('/generate-awb-reverse', [WebController::class, 'getAwbNumbersXbeesReverse'])->name('web.generate_awb_reverse');
    Route::get('/store_xbees_awbnumber', [WebController::class, 'storeXbeesAwbNumber'])->name('seller.storeXbeesAwbNumber');

    Route::get('seller/check-shipped-order-notification',[OperationController::class,'checkShippedOrderNotification'])->name('seller.check-shipped-order-notification');
    Route::post('seller/remove-duplicate-orders',[OperationController::class,'removeDuplicateOrders'])->name('seller.remove-duplicate-orders');


    Route::post("check-seller-awbnumber",[SellerController::class,'checkAwbNumber'])->name('seller.checkAwbNumber');
});


Route::get('clear-cache',function() {
    Artisan::call('optimize:clear');
    Artisan::call('cache:clear');
    return response()->json(['status' => 'Cache Cleared Successfully']);
});

Route::get('/send_sms', [Utilities::class, 'send_sms'])->name('utilities.send_sms');
Route::get('test-oms/{seller}',[OMSController::class,'getOMSData'])->name('oms.get_data');
Route::get('/test_sms', [WebController::class, 'test_sms'])->name('utilities.test_sms');


Route::get('crone/get-amazon-feeds',[CroneController::class,'fetchAmazonFeeds'])->name('cron.fetchAmazonFeeds');
Route::get('crone/get-amazon-feed',[CroneController::class,'fetchAmazonFeed'])->name('cron.fetchAmazonFeed');
Route::get('crone/get-amazon-feed-document',[CroneController::class,'fetchAmazonFeedDocument'])->name('cron.fetchAmazonFeedDocument');
Route::get('crone/create-amazon-feed',[CroneController::class,'createAmazonFeed'])->name('cron.createAmazonFeed');
Route::get('crone/create-amazon-feed-file',[CroneController::class,'createAmazonFeedFile'])->name('cron.createAmazonFeedFile');
Route::get('crone/cancel-amazon-feed',[CroneController::class,'cancelAmazonFeed'])->name('cron.cancelAmazonFeed');
Route::get('crone/populate-cache',[CroneController::class,'populateCache'])->name('cron.populateCache');
Route::get('crone/populate-order-tracking-cache',[CroneController::class,'populateOrderTrackingCache'])->name('cron.populateOrderTrackingCache');

Route::get('/generate-barcode', [SellerController::class, 'generateBarcode']);

// Archive Logs
Route::get('crone/archive-logs', [CroneController::class, 'archiveLogs'])->name('cron.archiveLogs');

// Archive Barcodes
Route::get('crone/archive-barcodes', [CroneController::class, 'archiveBarcodes'])->name('cron.archiveBarcodes');

// Sync transaction
Route::get('crone/sync-transaction', [CroneController::class, 'syncTransaction'])->name('cron.syncTransaction');

// Webhooks
Route::post('webhook/delhivery/track', [WebController::class, 'trackDelhiveryOrderHook'])->name('hook.trackDelhiveryOrderHook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::post('webhook/delhivery-staging/track', [OperationController::class, 'trackDelhiveryStagingOrderHook'])->name('hook.trackDelhiveryStagingOrderHook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::post('webhook/amazon-swa/track', [WebController::class, 'trackAmazonSwaOrderHook'])->name('hook.trackAmazonSwaOrderHook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::post('webhook/ekart/track', [WebController::class, 'trackEkartOrderHook'])->name('hook.trackEkartOrderHook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::post('webhook/pidge/track', [WebController::class, 'trackPidgeOrderHook'])->name('hook.trackPidgeOrderHook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]); // service/webhook-dtdc
Route::post('service/webhook-dtdc', [WebController::class, 'trackDTDCOrdersWebHook'])->name('hook.trackDTDCOrdersWebHook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]); // service/webhook-dtdc
Route::post('service/webhook-dtdc-staging', [WebController::class, 'trackDTDCOrdersWebHookStaging'])->name('hook.trackDTDCOrdersWebHook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]); // service/webhook-dtdc
Route::post('service/webhook-smartr', [WebController::class, 'trackSmartrOrdersWebHook'])->name('hook.trackDTDCOrdersWebHook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]); // service/webhook-dtdc
Route::post('service/webhook-smartr-staging', [OperationController::class, 'trackSmartrStagingOrdersWebHook'])->name('hook.trackDTDCOrdersWebHook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]); // service/webhook-dtdc
Route::post('webhook/webhook-shree-maruti', [WebController::class, 'TrackMarutiEcomOrderWebhook'])->name('hook.TrackMarutiEcomOrderWebhook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]); // service/webhook-dtdc
Route::post('webhook/shadowfax/track', [WebController::class, 'trackShadowFaxOrderHook'])->name('hook.trackShadowFaxOrderHook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::post('webhook/shadowfax-staging/track', [WebController::class, 'trackShadowFaxStagingOrderHook'])->name('hook.trackShadowFaxStagingOrderHook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Webhook for Professional
Route::post('webhook/professional/track', [WebController::class, 'trackProfessionalOrderHook'])->name('hook.trackShadowFaxOrderHook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Fetch serviceable pincodes
Route::get('crone/fetch-serviceable-pincodes-smartr', [CroneController::class, 'getServiceablePincodeSmartr'])->name('cron.getServiceablePincodeSmartr');
Route::get('crone/getServiceablePincodesEcomExpress',[EcomExpressController::class,'getServiceablePincodes']);
Route::get('cron/get-deleted-orders',[WebController::class,'FetchDeletedOrders']);

// Mark order as lost
Route::get('cron/update-lost-order',[CroneController::class,'updateLostStatus']);
// Mark order as cancelled
// Do not live this code order cancellation code is not latest code please update it.
Route::get('cron/update-cancelled-order',[CroneController::class,'updateCancelledStatus']);

Route::post('crone/import-amazon-direct-order',[CroneController::class,'importAmazonDirectOrder'])->name('cron.importAmazonDirectOrder')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::get('cron/upload-backup',[CroneController::class, 'uploadBackupFiles']);
Route::get('cron/generate-label',[CroneController::class, 'generateLable']);
Route::post('cron/queue', [CroneController::class, 'queue'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::post('cron/invoiceQueue', [CroneController::class, 'invoiceQueue'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::get('cron/onboardNewSeller', [CroneController::class, 'onboardingNewSeller']);
Route::get('cron/compareRateCard', [CroneController::class, 'compareRateCard']);
Route::get('cron/sellerInvalidContactSendMail', [CroneController::class, 'sellerInvalidContactSendMail']);

//Reassign Orders
Route::get('cron/reassign-orders',[CroneController::class, 'reassignOrders']);

Route::get('job/mark-order-as-rto',[WebController::class,'markOrderAsRTO']);
Route::get('/testing', function() {
    // mark rto complete
    // $web = new WebController();
    // $orders = DB::select("select * from order_tracking where awb_number like 'SHE%' and status_code = 'rto_completed'");
    // foreach($orders as $o){
    //     $order = Order::where('awb_number',$o->awb_number)->first();
    //     if($order->o_type == "forward")
    //             @$web->_RTOOrder($order->id);
    //     Order::where('awb_number', $order->awb_number)->update(['status' => 'delivered','delivered_date' => $o->updated_date]);
    //     //dd($o->awb_number);
    // }

    // code for performing tracking
    // $orders = DB::select("SELECT id,customer_order_number,seller_id,o_type,status,awb_number,courier_partner,awb_assigned_date,last_sync,manifest_status,manifest_sent FROM `orders` where status not in('pending','shipped','cancelled','delivered') and awb_assigned_date >= '2022-06-01' and last_sync <= '2022-08-30 00:00:00' and date(awb_assigned_date) != '2022-08-30' order by last_sync");
    // $web = new WebController();
    // foreach($orders as $o)
    // {
    //     $web->performTracking($o->awb_number);
    // }
    // return response()->json([
    //     'status' => true
    // ]);

    // code for performing tracking Ekart
    // $orders = DB::select("SELECT id,customer_order_number,seller_id,o_type,status,awb_number,courier_partner,awb_assigned_date,last_sync,manifest_status,manifest_sent FROM `orders` where status not in('pending','shipped','cancelled','delivered') and awb_assigned_date >= '2022-07-01 00:00:00' and courier_partner = 'ekart' order by last_sync");
    // $web = new WebController();
    // foreach($orders as $o)
    // {
    //     $web->performTracking($o->awb_number);
    // }
    // return response()->json([
    //     'status' => true
    // ]);

    // This code is to set expected_delivery_date to null as 0000-00-00 is invalid value for date
//    $orders = DB::select("SELECT * FROM `orders` where inserted < '2022-11-01 00:00:00' and expected_delivery_date = '0000-00-00'");
//    foreach($orders as $o){
//        Order::where('id',$o->id)->update(['expected_delivery_date' => null]);
//    }
    $date = date('Y-m-d 00:00:00');
    $orders = DB::select("select * from orders where status not in('pending','lost','damaged','delivered','cancelled','pickup_requested','shipped') and last_sync < '{$date}' order by last_sync limit 1200");
    foreach($orders as $o)
    {
        TrackingHelper::PerformTracking($o);
    }
    return response()->json([
        'status' => true
    ]);


    // code for performing tracking DTDC for seller id = 16
    // $orders = DB::select("select awb_number from orders where status = 'manifested' and awb_assigned_date >= '2022-12-25 00:00:00' and awb_assigned_date <= '2023-01-15 00:00:00' and courier_partner like '%dtdc%' and last_sync <= '2023-01-17 00:00:00' order by last_sync limit 50");
    // $web = new WebController();
    // foreach($orders as $o)
    // {
    //     $web->performTracking($o->awb_number);
    // }
    // return response()->json([
    //     'status' => true
    // ]);
});
Route::get('/testing2', function(Request $request) {
    // code for performing tracking DTDC for seller id = 16
//    $orders = DB::select("select * from orders where awb_assigned_date >= '2023-02-01 00:00:00' and awb_assigned_date <= '2023-05-11 00:00:00' and status not in('shipped','pickup_requested','delivered','lost','damaged','cancelled','pending') and last_sync < '2023-05-11 00:00:00' and seller_id = 1138 order by last_sync limit 300");
//    $orders = Order::where('awb_number',$request->awb_number)->first();
    //$orders = DB::select("select id,awb_number,courier_partner,customer_order_number,awb_assigned_date,status,rto_status,ndr_status,last_sync,seller_order_type,is_alpha from orders where seller_id = 4006 and status not in('pending','shipped','pickup_requested','lost','damaged','delivered','cancelled') order by last_sync");

    $date = date('Y-m-d 00:00:00');
    $orders = DB::select("select * from orders where courier_partner like '%smartr%' and status not in('pending','lost','damaged','delivered','cancelled','pickup_requested','shipped') and last_sync < '{$date}' order by last_sync limit 1200");
    foreach($orders as $o)
    {
        TrackingHelper::PerformTracking($o);
    }
    return response()->json([
        'status' => true
    ]);
});
Route::get('/testing3', function(Request $request) {
    // code for performing tracking DTDC for seller id = 16
//    $orders = DB::select("select * from orders where awb_assigned_date >= '2023-02-01 00:00:00' and awb_assigned_date <= '2023-05-11 00:00:00' and status not in('shipped','pickup_requested','delivered','lost','damaged','cancelled','pending') and last_sync < '2023-05-11 00:00:00' and seller_id = 1138 order by last_sync limit 300");
//    $orders = Order::where('awb_number',$request->awb_number)->first();
    //$orders = DB::select("select id,awb_number,courier_partner,customer_order_number,awb_assigned_date,status,rto_status,ndr_status,last_sync,seller_order_type,is_alpha from orders where seller_id = 4006 and status not in('pending','shipped','pickup_requested','lost','damaged','delivered','cancelled') order by last_sync");
//    $orders = DB::select("select * from orders where status not in('pending','shipped','pickup_requested','lost','damaged','delivered','cancelled') and last_sync < '2023-06-10 10:00:00' and courier_partner like '%delhivery%' order by last_sync limit 450 offset 450");
    $date = date('Y-m-d 00:00:00');
    $orders = DB::select("select * from orders where courier_partner like '%smartr%' and status not in('pending','lost','damaged','delivered','cancelled','pickup_requested','shipped') and last_sync < '{$date}' order by last_sync limit 1200 offset 1200");
    foreach($orders as $o)
    {
        TrackingHelper::PerformTracking($o);
    }
    return response()->json([
        'status' => true
    ]);
});
Route::get('/testing4', function() {
    // code for performing tracking DTDC for seller id = 16
    $orders = DB::select("select * from orders where awb_assigned_date >= '2023-02-01 00:00:00' and awb_assigned_date <= '2023-04-15 00:00:00' and status not in('shipped','pickup_requested','delivered','lost','damaged','cancelled','pending') and last_sync < '2023-04-15 00:00:00' order by last_sync limit 300 offset 300");
    foreach($orders as $o)
    {
        TrackingHelper::PerformTracking($o);
    }
    return response()->json([
        'status' => true
    ]);
});
Route::get('/testing5', function() {
    // code for performing tracking DTDC for seller id = 16
    $orders = DB::select("select * from orders where awb_assigned_date >= '2023-02-01 00:00:00' and awb_assigned_date <= '2023-04-15 00:00:00' and status not in('shipped','pickup_requested','delivered','lost','damaged','cancelled','pending') and last_sync < '2023-04-15 00:00:00' order by last_sync limit 300 offset 600");
    foreach($orders as $o)
    {
        TrackingHelper::PerformTracking($o);
    }
    return response()->json([
        'status' => true
    ]);
});
Route::get('service/get-bluedart-pickup-token/{awb}',[OperationController::class,'getBluedartPickupToken']);
Route::get('info/php-info',function(){
    phpinfo();
});
Route::get('bucket/get-bucket-file/{report}',[OperationController::class,'downloadReportFile'])->name('bucket.download-from-bucket');
Route::get('bucket/get-order-bucket-file/{report}',[OperationController::class,'downloadOrderReportFile'])->name('bucket.order-download-from-bucket');
Route::get('utility/copy-image',function (Request $request){
    $server1 = "http://13.233.27.80/";
    $server2 = "http://15.206.98.229/";
    $path = str_replace("|","/",$request->path);
    if(!empty($request->from))
    {
        if($request->from == '13'){
//            file_get_contents();
            $urlToExecute = "{$server2}utility/copyfile?path=".base64_encode($path)."&root=".base64_encode($server1);
        }else{
            //file_get_contents("{$server1}/utility/copyfile?path=".base64_encode($path)."&root=".base64_encode($server2));
            $urlToExecute = "{$server1}utility/copyfile?path=".base64_encode($path)."&root=".base64_encode($server2);
        }
        file_get_contents($urlToExecute);
    }
    $response['status'] = true;
    $response['message'] = "Order Status Pushed Successfully";
    return response()->json($response);
});

Route::get('/utility/copyfile',function(Request $request){
    $root = base64_decode($request->root);
    $path = base64_decode($request->path);
    //dd($root.$path);
    file_put_contents($path,(file_get_contents($root.$path)));
});






// ONDC APIs
Route::get("get-image",function(Request $request) {
    $data = base64_encode(file_get_contents(BucketHelper::GetDownloadLink("qc_image/".$request->image)));
    print("<img src='data:image/png;base64,$data'/>");
});


//Do not remove this code it's demo for COD to Prepaid
Route::get("payment-link",function(){
    //return view('seller.payment-text');
    $api = new Api('rzp_test_JOC0wRKpLH1cVW', '9EzSlxvJbTyQ2Hg0Us5ZX4VD');


    $order = $api->paymentLink->create(
        array(
            'amount'=>600,
            'currency'=>'INR',
            'accept_partial'=>true,
            'first_min_partial_amount'=>100,
            'description' => 'For XYZ purpose',
            'customer' => array(
                'name'=>'Gaurav Kumar',
                'email' => 'gaurav.kumar@example.com',
                'contact'=>'+919000090000'
            ),
            'notify'=>  array(
                'sms'=>true,
                'email'=>true
            ) ,
            'reminder_enable'=>true ,
            'notes'=>array(
                'policy_name'=> 'Jeevan Bima'
            ),
            'callback_url' => 'https://www.google.com',
            'callback_method'=>'get'
        )
    );
    dd($order);
});


Route::get("get-payment-details",function (){
    $api = new Api("rzp_test_JOC0wRKpLH1cVW", "9EzSlxvJbTyQ2Hg0Us5ZX4VD");

    dd($api->payment->fetch("pay_MNrtsnUw4oIV5r"));
});

Route::get('enable-qc-for-xbees',function (){
    \App\Models\Partners::where('id',34)->update(['qc_enabled' => 'y','reverse_enabled' => 'y']);
    \App\Models\Partners::where('id',10)->update(['qc_enabled' => 'n','reverse_enabled' => 'n']);
});

Route::get('update-last-sync',function (Request $request){
    Channels::where('id',$request->id)->update(['last_executed' => $request->date]);
});

Route::get('service/remove-first-product-for-order',function (){
    $allOrders = DB::select("select order_id,count(order_id) as total from products where order_id in(select id from orders where seller_id = '1138' and awb_assigned_date >= '2023-09-11 00:00:00') group by order_id having total > 1");
    foreach ($allOrders as $o){
        $products = Product::where('order_id',$o->order_id)->get();
        if(count($products) > 1){
            Product::where('id',$products[0]->id)->delete();
        }
    }
    return response()->json(['status' => true,'message' => 'Products Removed Successfully']);
});

Route::get('test-attachment-mail',function (){
    $u = new Utilities();
    $files = [
        public_path('assets/awbs.json')
    ];
    $u->send_email("singhsatyam219@gmail.com","satyam","satyam","Test Attachment","Test",$files);
});

Route::get('service/populate-order-state',function (){
    $allOrders = DB::select("select id,awb_number,courier_partner,seller_id,s_city,s_state,s_pincode from orders where s_state = '0' or s_city = '0'");
    foreach ($allOrders as $o){
        $pincodeDetails = MyUtility::findPincodeDetails($o->s_pincode);
        if(!empty($pincodeDetails['status']) && $pincodeDetails['status'] == 'Success'){
            Order::where('id',$o->id)->update(['s_city' => $pincodeDetails['city'],'s_state' => $pincodeDetails['state']]);
        }
    }
    return response()->json(['status' => true,'message' => 'Job completed successfully']);
});

Route::get('dashboard/test-query',function (Request $request){
    $startDateTime = date('Y-m-d H:i:s');
    $start_date = date('Y-m-d H:i:s',strtotime("-30 days"));
    $end_date = date('Y-m-d H:i:s');
    $seller_id = $request->seller_id;
    $ndr = [];
    $ndr['total_ndr'] = 0;$ndr['action_required'] = 0;$ndr['action_requested'] = 0;$ndr['ndr_delivered'] = 0;$ndr['ndr_rto'] = 0;$ndr['attempt1_total'] = 0;$ndr['attempt1_pending'] = 0;$ndr['attempt1_delivered'] = 0;$ndr['attempt1_rto'] = 0;$ndr['attempt1_lost'] = 0;$ndr['attempt2_total'] = 0;$ndr['attempt2_pending'] = 0;$ndr['attempt2_delivered'] = 0;$ndr['attempt2_rto'] = 0;$ndr['attempt2_lost'] = 0;$ndr['attempt3_total'] = 0;$ndr['attempt3_pending'] = 0;$ndr['attempt3_delivered'] = 0;$ndr['attempt3_rto'] = 0;$ndr['attempt3_lost'] = 0;
    $ndrCounts = Order::select("ndr_action",'rto_status','status')->where('seller_id',$seller_id)->where('ndr_status','y')->where('awb_assigned_date','>=',$start_date)->where('awb_assigned_date','<=',$end_date)->get();
    foreach ($ndrCounts as $n){
        // Total NDR
        $n->ndr_count = count($n->ndrattempts);
        $ndr['total_ndr'] += 1;
        $ndr['action_required'] += ($n->rto_status == 'n' && $n->status != 'delivered' && $n->ndr_action == 'pending') ? 1 : 0;
        $ndr['action_requested'] += ($n->rto_status == 'n' && $n->status != 'delivered' && $n->ndr_action == 'requested') ? 1 : 0;
        $ndr['ndr_delivered'] += ($n->rto_status == 'n' && $n->status == 'delivered') ? 1 : 0;
        $ndr['ndr_rto'] += ($n->rto_status == 'y') ? 1 : 0;


        $ndr['attempt1_total'] += $n->ndr_count <= 1 ? 1 : 0;
        $ndr['attempt1_pending'] += ($n->ndr_count <= 1 && $n->rto_status == 'n' && $n->status != 'delivered') ? 1 : 0;
        $ndr['attempt1_delivered'] += ($n->ndr_count <= 1 && $n->rto_status == 'n' && $n->status == 'delivered') ? 1 : 0;
        $ndr['attempt1_rto'] += ($n->ndr_count <= 1 && $n->rto_status == 'y') ? 1 : 0;
        $ndr['attempt1_lost'] += ($n->ndr_count <= 1 && ($n->status == 'lost' || $n->status == 'damaged')) ? 1 : 0;

        // Attempt 2
        $ndr['attempt2_total'] += $n->ndr_count == 2 ? 1 : 0;
        $ndr['attempt2_pending'] += ($n->ndr_count == 2 && $n->rto_status == 'n' && $n->status != 'delivered') ? 1 : 0;
        $ndr['attempt2_delivered'] += ($n->ndr_count == 2 && $n->rto_status == 'n' && $n->status == 'delivered') ? 1 : 0;
        $ndr['attempt2_rto'] += ($n->ndr_count == 2 && $n->rto_status == 'y') ? 1 : 0;
        $ndr['attempt2_lost'] += ($n->ndr_count == 2 && ($n->status == 'lost' || $n->status == 'damaged')) ? 1 : 0;

        // Attempt 3
        $ndr['attempt3_total'] += $n->ndr_count == 3 ? 1 : 0;
        $ndr['attempt3_pending'] += ($n->ndr_count == 3 && $n->rto_status == 'n' && $n->status != 'delivered') ? 1 : 0;
        $ndr['attempt3_delivered'] += ($n->ndr_count == 3 && $n->rto_status == 'n' && $n->status == 'delivered') ? 1 : 0;
        $ndr['attempt3_rto'] += ($n->ndr_count == 3 && $n->rto_status == 'y') ? 1 : 0;
        $ndr['attempt3_lost'] += ($n->ndr_count == 3 && ($n->status == 'lost' || $n->status == 'damaged')) ? 1 : 0;
    }
    $endDateTime = date('Y-m-d H:i:s');
    dd($startDateTime,$endDateTime,$ndr);
});

Route::get('mark-failed-download-order-report',function (Request $request) {
    $checkData = DownloadOrderReportModel::find($request->id);
    if(!empty($checkData)){
        if($checkData->status == 'processing')
            DownloadOrderReportModel::where('id','=',$checkData->id)->update(['status' => 'failed']);
    }

    echo "Success";
});

//Route::get('get-job-status-2',function (){
//    $process = new Process(['php', 'artisan', 'queue:listen']);
//    $process->setTimeout(null); // Set the timeout to null to allow the process to run indefinitely
//
//    $process->run(function ($type, $buffer) {
//        // Output from the queue:listen command
//        echo $buffer;
//    });
//
//// Wait for the process to finish (you can interrupt it manually)
//    $process->wait();
//});

Route::get('job/revert-cod-remittance',function (){
    try{
        $awbList = [];
        $codTransaction = COD_transactions::find(1370903);
        if(!empty($codTransaction)){
            $remmitanceDetails = RemittanceDetails::where('cod_transactions_id',$codTransaction->id)->get();
            foreach ($remmitanceDetails as $rd){
                $awbList[]=$rd->awb_number;
                Order::where('awb_number',$rd->awb_number)->update(['cod_remmited' => 'n']);
                RemittanceDetails::where('id',$rd->id)->delete();
            }
            Seller::where('id',$codTransaction->seller_id)->increment('cod_balance',$codTransaction->amount);
            COD_transactions::where('id',$codTransaction->id)->delete();
            dd($awbList,$codTransaction);
        }
    }catch(Exception $e){
        dd($e->getMessage()."-".$e->getFile()."-".$e->getLine());
    }

//    $totalAmount = 0;
//    $orderIDs = [];
//    foreach ($allData as $a){
//        $totalAmount+=$a->amount;
//        $orderIDs[]=$a->order_id;
//        //COD_transactions::where('id',$a->id)->delete();
//    }
//    //Order::whereIn($orderIDs)->update(['cod_remmited' => 'n']);
//    //Seller::where('id',6960)->increment('cod_balance',$totalAmount);
//    dd($totalAmount,$orderIDs);
});



//Route::get('service/convert-rto-delivered-to-delivered',function (){
//    $allOrders = DB::select("select id,awb_number,courier_partner,status,rto_status,seller_id,awb_assigned_date,delivered_date from orders where status = 'rto_delivered' order by awb_assigned_date");
//    $orderIds = [];
//    foreach ($allOrders as $o){
//        if($o->rto_status == 'y' && $o->status == 'rto_delivered'){
//            $orderIds[] = $o->id;
//        }
//        if(count($orderIds) == 500){
//            Order::whereIn('id',$orderIds)->update(['status' => 'delivered']);
//            $orderIds = [];
//        }
//    }
//    Order::whereIn('id',$orderIds)->update(['status' => 'delivered']);
//});

Route::get('ccavenue',[SellerController::class,'CCAvenuePaymentCreate']);
Route::post('ccavenue-response',[OperationController::class,'CCAvenueResponse'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->name('ccavenue-response');

Route::get("notification/redirect-to-reassign/{id}",[OperationController::class,'redirectToReAssign'])->name('notification.redirect-to-reassign');
Route::get('service/send-reassign-mail-for-seller',[CroneController::class,'SendReAssignMailForSeller']);
Route::get('service/send-reassign-mail-for-seller-daily',[CroneController::class,'SendReAssignMailForSellerDaily']);

//CRM Panel
Route::get('/crm-login', [CRMController::class, 'login'])->name('crm.login');
Route::post('/crm-check-login', [CRMController::class, 'check_login'])->name('crm.check_login');
Route::get('/crm-logout', [CRMController::class, 'logout'])->name('crm.logout');



Route::middleware([AuthCRM::class])->group(function () {

    //Change Order Status With Date
    Route::get('crm/change-order-status',[CRMController::class,'changeOrderStatus']);
    Route::post('crm/change-order-status-submit',[CRMController::class,'changeOrderStatusSubmit']);

    //Dashboard In Scan Export
    Route::get('crm-export-dashboard-in-scan-pending/{courier}',[CRMController::class,'exportDashboardInScanPending'])->name('crm.crm-export-dashboard-in-scan-pending');

    //Dashboard In Scan Pending
    Route::get('crm-export-dashboard-pickup-pending/{courier}',[CRMController::class,'exportDashboardPickupPending'])->name('crm.crm-export-dashboard-pickup-pending');


    // Order Status Routes
    Route::any('crm/update-status',[CRMController::class,'updateStatus'])->name('crm.update-order');
    Route::post('crm/update-order-status',[CRMController::class,'updateOrderStatus'])->name('crm.update-order-status');

    // Order RTO Routes
    Route::any('crm/mark-rto',[CRMController::class,'markRTO'])->name('crm.mark-rto');
    Route::post('crm/mark-order-rto',[CRMController::class,'markOrderRTO'])->name('crm.mark-order-rto');

    // Order RTO Routes
    Route::any('crm/reverse-cancellation',[CRMController::class,'reverseCancellation'])->name('crm.reverse-cancellation');
    Route::post('crm/submit-reverse-cancellation',[CRMController::class,'submitReverseCancellation'])->name('crm.submit-reverse-cancellation');

    //Dashboard Routes for Employee Panel
    Route::get('/crm', [CRMController::class, 'dashboard'])->name('crm');
    Route::get('/crm-profile', [CRMController::class, 'profile'])->name('crm.profile');
    Route::post('/crm/save-profile', [CRMController::class, 'save_profile'])->name('crm.save.profile');
    Route::get('/crm/change-password-view', [CRMController::class, 'change_password_view'])->name('crm.change_password_view');
    Route::post('/crm/change-password', [CRMController::class, 'change_password'])->name('crm.change.password');
    Route::get('/check-old-password-crm/{password}', [CRMController::class, 'checkOldPassword'])->name('crm.checkOldPassword');
    Route::get('/crm/seller', [CRMController::class, 'seller'])->name('crm.seller');
    Route::get('/crm/seller/{id}', [CRMController::class, 'seller_view'])->name('crm.view_seller');
    Route::post('/crm/verify_document', [CRMController::class, 'seller_verify'])->name('crm.verify_kyc_information');
    Route::post('/crm/seller_status', [CRMController::class, 'seller_status'])->name('crm.seller_status');
    Route::post('/crm/seller_gst_status', [CRMController::class, 'seller_gst_status'])->name('crm.seller.gst_status');
    Route::post('/crm/seller_cheque_status', [CRMController::class, 'seller_cheque_status'])->name('crm.seller.cheque_status');
    Route::post('/crm/seller_document_status', [CRMController::class, 'seller_document_status'])->name('crm.seller.document_status');
    Route::post('/crm/seller_agreement_status', [CRMController::class, 'seller_agreement_status'])->name('crm.seller.agreement_status');
    Route::get('/crm/delete-seller/{id}', [CRMController::class, 'seller_delete'])->name('crm.delete_seller');
    Route::get('/crm/export_seller_details', [CRMController::class, 'export_seller_details'])->name('crm.export_seller_details');


    //Shipment Hold
    Route::get("/crm/shipment-hold",[CRMController::class,'shipmentHoldView'])->name('crm.shipmentHoldView');
    Route::post("/crm/shipment-hold-submit",[CRMController::class,'shipmentHoldSubmit'])->name('crm.shipmentHoldSubmit');

//for order emoloyee
    Route::post("/crm/cod-to-prepaid",[CRMController::class,'codToPrepaid'])->name('crm.codToPrepaid');
    Route::post("/crm/cod-to-prepaid-bulk",[CRMController::class,'codToPrepaidBulk'])->name('crm.codToPrepaidBulk');
    Route::get("/crm/mop-cod-to-prepaid",[CRMController::class,'codToPrepaidView'])->name('crm.codToPrepaidView');
    Route::get('/crm/order', [CRMController::class, 'order'])->name('crm.order');
    Route::get('/crm/view-order/{order_id}', [CRMController::class, 'view_order'])->name('crm.view_order');
    Route::post('/crm/order/set_filter', [CRMController::class, 'setFilter'])->name('crm.set_filter');
    Route::get('crm/ajax_filter_order', [CRMController::class, 'ajax_filter_order'])->name('crm.ajax_filter_order');
    Route::get('crm/reset_key/{key}', [CRMController::class, 'resetFilter'])->name('crm.resetFilter');
    Route::get('crm/export_csv-order/{id}', [CRMController::class, 'export_csv_order'])->name('crm.export_csv_order');
    Route::get('crm/export_ndr_order/{id}', [CRMController::class, 'export_ndr_order'])->name('crm.export_ndr_order');
    Route::post('crm/download-bulk-invoice', [CRMController::class, 'downloadBulkInvoice'])->name('crm.download-bulk-invoice');
    Route::post('crm/import-awb-order', [CRMController::class, 'importCsvOrders'])->name('crm.importAwbs');



    // Serviceable Pincodes
    Route::get('crm/serviceable-pincode', [CRMController::class, 'serviceAblePincode'])->name('crm.serviceable-pinocde');
    Route::post('crm/get-serviceable-pincode', [CRMController::class, 'getServiceablePincode'])->name('crm.get-serviceable-pincode');

    Route::post('crm/export-pin-codes', [CRMController::class, 'exportServiceAblePincode'])->name('crm.exportServiceAblePincode');

    // Serviceable Pincodes fm
    Route::get('crm/serviceable-pincodefm', [CRMController::class, 'serviceAblePincodeFM'])->name('crm.serviceable-pinocdefm');
    Route::post('crm/get-serviceable-pincodefm', [CRMController::class, 'getServiceablePincodeFm'])->name('crm.get-serviceable-pincodefm');

    Route::post('crm/export-pin-codesfm', [CRMController::class, 'exportServiceAblePincodeFm'])->name('crm.exportServiceAblePincodefm');


    // embargo Pincodes
    Route::get('crm/embargo', [CRMController::class, 'embargoPincode'])->name('crm.embargoPincode');
    Route::post('crm/getEmbargo-Pincode', [CRMController::class, 'getEmbargoPincode'])->name('crm.getEmbargoPincode');


    Route::post('crm/getEmbargo-PincodeId', [CRMController::class, 'saveSelectedPincodes'])->name('crm.getEmbargoPincodeId');
});

Route::get('service/download-zone-mapping',[OperationController::class,'downloadZoneMappingSeller'])->name('service.download-zone-mapping');
Route::get('barcode/generate-barcode',function (Request $request){
    // Initialize the barcode generator
    $generator = new BarcodeGeneratorPNG();

    // Get the code from the request, default to "123" if not provided
    $code = $request->input('code', '123');

    // Sanitize the code
    $code = preg_replace('/[^A-Za-z0-9\-]/', '', $code);

    // Generate the barcode
    $barcode = $generator->getBarcode($code, $generator::TYPE_CODE_128);

    // Return the barcode as a PNG image response
    return response($barcode)
        ->header('Content-Type', 'image/png');
});

Route::get('cron/fetch-serviceability/shadowfax',[CroneController::class, 'fetchAllServiceablePincodeShadowFax'])->name('cron.fetch-serviceability-shadowfax');
Route::get('cron/fetch-serviceability-fm/shadowfax',[CroneController::class, 'fetchAllServiceablePincodeFMShadowFax'])->name('cron.fetch-serviceability-shadowfax');
Route::get('cron/fetch-serviceability/delhivery',[CroneController::class, 'fetchAllServiceablePincodeDelhivery'])->name('cron.fetch-serviceability-delhivery');

Route::get('test/send-email-test',function (){
    $utility = new Utilities();
    $utility->send_email("niteshsingh27354@gmail.com","Deepak Prajapati","Title","Content of the test email","Test Email");
});
