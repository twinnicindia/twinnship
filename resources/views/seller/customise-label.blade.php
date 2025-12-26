<!DOCTYPE html>
<html lang="zxx">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <title>Customize Label | {{$config->title}}</title>
      @include('seller.pages.styles')
      <style>
         .tableInner {
         border: 0;
         }
         table {
         font-family: sans-serif !important;
         font-size: 10px !important;
         line-height: 18px !important;
         width: 100% !important;
         border-collapse: collapse !important;
         }
         table, td, th {
         text-align: left !important;
         border: 1px solid black !important;
         box-shadow: none !important;
         border-collapse: collapse !important;
         }
      </style>
      <style>
         h3{
         font-weight: 700;
         }
         table,
         td,
         th {
         text-align: left;
         border: 1px solid black;
         border-collapse: collapse;
         }
         .tableInner{
         border: 0;
         }
         table {
         font-family: sans-serif;
         font-size: 12px;
         line-height: 18px;
         width: 100%;
         border-collapse: collapse;
         }
         @if($label->tabular_form_enabled == 'y')
         #productTable td{
         font-size: 10px;
         border: solid 1px black;
         }
         @else
         #productTable td{
         font-size: 10px;
         border:0;
         }
         @endif
         .noPadding{
         padding: 0;
         }
         th,td{
         padding: 3px;
         }
         .user-dashboard .content-inner  { padding: 51px; background: #F0F1F7;}
         .round-box{padding: 15px;
         border-radius: 10px;
         background: #fff;margin-bottom: 30px;}
         .round-box h2{
         color: #000;
         font-size: 20px;
         padding: 15px 20px;
         border-radius: 10px;margin-bottom: 25px;}
         .green-txt{color: #3BB54B;}
         .green-bg{background: #EBF8ED;}
         .red-txt{color: #D81B2A;}
         .red-bg{background: #FBE8EA;}
         .blue-txt{color: #1975C9;}
         .blue-bg{background: #E8F1FA;}
         .purpal-txt{color: #845ADF;}
         .purpal-bg{background: #F3EEFC;}
         .round-box .label-label-header-style{margin-bottom: 10px;
         border-bottom: 1px solid #d6d6d6;
         padding-bottom: 10px;}
         .round-box span{color: #727272;}
         .round-box i{font-size: 18px;cursor: pointer;}
         .round-box .fa-toggle-on{color: #464845;}
         .round-box .fa-toggle-off{color: black;}
         .mt10{margin-top: 10px;}
      </style>
   </head>
   <body>
      <div class="container-fluid">
        @include('seller.pages.header')
        @include('seller.pages.sidebar')
         <div class="main-content d-flex flex-column">
            <div class="nav-scroll scroll-bar card mb-4 col-12">
               <div
                  class="d-flex justify-content-between p-2 align-items-center">
                  <div class="tablist mt-3" id="pills-tab" role="tablist">
                     <div class="me-2" role="presentation">
                        <h4>Customize Label</h4>
                     </div>
                  </div>
                  <div class="text-end d-flex justify-content-end align-items-center">
                     <a type="button" href="#" class="btn btn-primary text-white fw-semibold me-2 save" id="saveConfig">Save Configuration
                     </a>
                  </div>
               </div>
            </div>
            <div class="row justify-content-center">
               <div class="col-xxl-12 ">
                  <div class="row">
                     <div class="col-md-3">
                        <div class="round-box" >
                           <h2 >1st Section</h2>
                           <div class="label-label-header-style label-header-paragraph-mask" data-visibility="{{ $label->contact_mask == 'y' ? 'on' : 'off' }}" style="cursor: pointer;">
                              <span class="font-size: 18px; font-weight: bold;">Contact Mask: </span>
                              <i class="fa fa-toggle-{{ $label->contact_mask == 'y' ? 'on' : 'off' }}"></i>
                           </div>
                           <div class="label-label-header-style label-header" data-visibility="{{ $label->header_visibility == 'y' ? 'on' : 'off' }}" style="cursor: pointer;">
                              <span class="font-size: 18px; font-weight: bold;">Header: </span>
                              <i class="fa fa-toggle-{{ $label->header_visibility == 'y' ? 'on' : 'off' }}"></i>
                           </div>
                           <div class="label-label-header-style label-shipping-address" data-visibility="{{ $label->shipping_address_visibility == 'y' ? 'on' : 'off' }}" style="cursor: pointer;">
                              <span class="font-size: 18px; font-weight: bold;">Shipping Address: </span>
                              <i class="fa fa-toggle-{{ $label->shipping_address_visibility == 'y' ? 'on' : 'off' }}"></i>
                           </div>
                           <div class="label-label-header-style label-header-logo" data-visibility="{{ $label->header_logo_visibility == 'y' ? 'on' : 'off' }}" style="cursor: pointer;">
                              <span class="font-size: 18px; font-weight: bold;">Logo: </span>
                              <i class="fa fa-toggle-{{ $label->header_logo_visibility == 'y' ? 'on' : 'off' }}"></i>
                           </div>
                        </div>
                        <div class="round-box">
                           <h2 >2nd Section</h2>
                           <div class="label-label-header-style label-shipment-detail" data-visibility="{{ $label->shipment_detail_visibility == 'y' ? 'on' : 'off' }}" style="cursor: pointer;">
                              <span class="font-size: 18px; font-weight: bold;">Shipment Detail: </span>
                              <i class="fa fa-toggle-{{ $label->shipment_detail_visibility == 'y' ? 'on' : 'off' }}"></i>
                           </div>
                           <div class="label-label-header-style label-awb-barcode" data-visibility="{{ $label->awb_barcode_visibility == 'y' ? 'on' : 'off' }}" style="cursor: pointer;">
                              <span class="font-size: 18px; font-weight: bold;">Awb Barcode: </span>
                              <i class="fa fa-toggle-{{ $label->awb_barcode_visibility == 'y' ? 'on' : 'off' }}"></i>
                           </div>
                        </div>
                        <div class="round-box">
                           <h2 >3rd Section</h2>
                           <div class="label-label-header-style label-header-paragraph-mask1" data-visibility="{{ $label->s_contact_mask == 'y' ? 'on' : 'off' }}" style="cursor: pointer;">
                              <span class="font-size: 18px; font-weight: bold;">Seller Contact : </span>
                              <i class="fa fa-toggle-{{ $label->s_contact_mask == 'y' ? 'on' : 'off' }}"></i>
                           </div>
                           <div class="label-label-header-style label-header-paragraph-mask2" data-visibility="{{ $label->s_gst_mask == 'y' ? 'on' : 'off' }}" style="cursor: pointer;">
                              <span class="font-size: 18px; font-weight: bold;">Seller GstIn : </span>
                              <i class="fa fa-toggle-{{ $label->s_gst_mask == 'y' ? 'on' : 'off' }}"></i>
                           </div>
                           <div class="label-label-header-style label-order-detail" data-visibility="{{ $label->order_detail_visibility == 'y' ? 'on' : 'off' }}" style="cursor: pointer;">
                              <span class="font-size: 18px; font-weight: bold;">Order Detail: </span>
                              <i class="fa fa-toggle-{{ $label->order_detail_visibility == 'y' ? 'on' : 'off' }}"></i>
                           </div>
                           <div class="label-label-header-style label-manifest-date" data-visibility="{{ $label->manifest_date_visibility == 'y' ? 'on' : 'off' }}" style="cursor: pointer;">
                              <span class="font-size: 18px; font-weight: bold;">Manifest Date: </span>
                              <i class="fa fa-toggle-{{ $label->manifest_date_visibility == 'y' ? 'on' : 'off' }}"></i>
                           </div>
                           <div class="label-label-header-style label-order-barcode" data-visibility="{{ $label->order_barcode_visibility == 'y' ? 'on' : 'off' }}" style="cursor: pointer;">
                              <span class="font-size: 18px; font-weight: bold;">Order Barcode: </span>
                              <i class="fa fa-toggle-{{ $label->order_barcode_visibility == 'y' ? 'on' : 'off' }}"></i>
                           </div>
                           <div class="label-label-header-style label-order-barcode-visibility" data-visibility="{{ $label->barcode_visibility == 'y' ? 'on' : 'off' }}" style="cursor: pointer;">
                              <span class="font-size: 18px; font-weight: bold;">Barcode visibility : </span>
                              <i class="fa fa-toggle-{{ $label->barcode_visibility == 'y' ? 'on' : 'off' }}"></i>
                           </div>
                           <div class="label-label-header-style label-ordernumber_visibility" data-visibility="{{ $label->ordernumber_visibility == 'y' ? 'on' : 'off' }}" style="cursor: pointer;">
                              <span class="font-size: 18px; font-weight: bold;">Order Number visibility : </span>
                              <i class="fa fa-toggle-{{ $label->ordernumber_visibility == 'y' ? 'on' : 'off' }}"></i>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="round-box">
                           <h2 >4th Section</h2>
                           <div class="label-label-header-style label-product-detail" data-visibility="{{ $label->product_detail_visibility == 'y' ? 'on' : 'off' }}" style="cursor: pointer;">
                              <span class="font-size: 18px; font-weight: bold;">Product Detail: </span>
                              <i class="fa fa-toggle-{{ $label->product_detail_visibility == 'y' ? 'on' : 'off' }}"></i>
                           </div>
                           <div class="label-label-header-style label-invoice-value" data-visibility="{{ $label->invoice_value_visibility == 'y' ? 'on' : 'off' }}" style="cursor: pointer;">
                              <span class="font-size: 18px; font-weight: bold;">Invoice Value: </span>
                              <i class="fa fa-toggle-{{ $label->invoice_value_visibility == 'y' ? 'on' : 'off' }}"></i>
                           </div>
                           <div class="label-label-header-style label-gift" data-visibility="{{ $label->gift_visibility == 'y' ? 'on' : 'off' }}" style="cursor: pointer;">
                              <span class="font-size: 18px; font-weight: bold;">As a gift: </span>
                              <i class="fa fa-toggle-{{ $label->gift_visibility == 'y' ? 'on' : 'off' }}"></i>
                           </div>
                           <div class="label-label-header-style display-products" data-visibility="{{ $label->all_product_display == 'y' ? 'on' : 'off' }}" style="cursor: pointer;">
                              <span class="font-size: 18px; font-weight: bold;">Display All Products </span>
                              <i class="fa fa-toggle-{{ $label->all_product_display == 'y' ? 'on' : 'off' }}"></i>
                           </div>
                           <div class="label-label-header-style display_full_product_name" data-visibility="{{ $label->display_full_product_name == 'y' ? 'on' : 'off' }}" style="cursor: pointer;">
                              <span class="font-size: 18px; font-weight: bold;">Full Product Name:</span>
                              <i class="fa fa-toggle-{{ $label->display_full_product_name == 'y' ? 'on' : 'off' }}"></i>
                           </div>
                        </div>
                        <div class="round-box">
                           <h2 >5th Section</h2>
                           <div class="label-label-header-style other-charges" data-visibility="{{ $label->other_charges == 'y' ? 'on' : 'off' }}" style="cursor: pointer;">
                              <span class="font-size: 18px; font-weight: bold;">Other Charges: </span>
                              <i class="fa fa-toggle-{{ $label->other_charges == 'y' ? 'on' : 'off' }}"></i>
                           </div>
                        </div>
                        <div class="round-box">
                           <h2 >6th Section</h2>
                           <div class="label-label-header-style disclaimer-text" data-visibility="{{ $label->disclaimer_text == 'y' ? 'on' : 'off' }}" style="cursor: pointer;">
                              <span class="font-size: 18px; font-weight: bold;">Disclaimer Text: </span>
                              <i class="fa fa-toggle-{{ $label->disclaimer_text == 'y' ? 'on' : 'off' }}"></i>
                           </div>
                           <div class="label-label-header-style label-footer" data-visibility="{{ $label->footer_visibility == 'y' ? 'on' : 'off' }}" style="cursor: pointer;">
                              <span class="font-size: 18px; font-weight: bold;">Footer: </span>
                              <i class="fa fa-toggle-{{ $label->footer_visibility == 'y' ? 'on' : 'off' }}"></i>
                           </div>
                           <div class="label-label-header-style tabular-form" data-visibility="{{ $label->tabular_form_enabled == 'y' ? 'on' : 'off' }}" style="cursor: pointer;">
                              <span class="font-size: 18px; font-weight: bold;">Tabular Product Data: </span>
                              <i class="fa fa-toggle-{{ $label->tabular_form_enabled == 'y' ? 'on' : 'off' }}"></i>
                           </div>
                           <div id="label-label-header-style label-footerId">
                              <div class="label-label-header-style label-footer-text" data-visibility="{{ $label->custom_footer_enable == 'y' ? 'on' : 'off' }}" style="cursor: pointer;">
                                 <span class="font-size: 18px; font-weight: bold;">Footer Customize Text: </span>
                                 <i class="fa fa-toggle-{{ $label->custom_footer_enable == 'y' ? 'on' : 'off' }}"></i>
                              </div>
                              <input type="text" name="footer_customize_value" id="footer_customize_value" class="form-control" value="{{$label->footer_customize_value}}" style="{{ $label->custom_footer_enable == 'y' ? 'display:block' : 'display:none' }}" >
                              <label id="errorName" class="error" style="color: red;"></label>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="round-box text-center">
                           <div class="col-md-12" >
                              <div style="width: 32rem; margin: 0 auto; display: flex; flex-direction: column;">
                                 <table>
                                    <tbody>
                                       <tr id="label-header">
                                          <td class="noPadding">
                                             <table class="tableInner">
                                                <tr>
                                                   <td style="width: 70%;border:0;">
                                                      <div id="label-shipping-address">
                                                         <b style="padding:0;margin:0;margin-bottom:5px;">Ship To</b>
                                                         <p id="contact_paragraph" style="padding:0;margin:0;">ABC XYZ<br> 123 abc xyz<br>surat,gujarat,india<bR>1244<bR>Contact : <span id="spanContactMaskStar" style="display: none;font-size: 11px;">74523243231</span><span id="spanContactMask" >**********</span></p>
                                                      </div>
                                                   </td>
                                                   <td style="width: 30%;align-items: center;align-content: center;text-align: center;border:0;" id="label-header-logo">
                                                      <img src="{{asset($config->logo)}}" style="width: 80%;height:60px;">
                                                   </td>
                                                </tr>
                                             </table>
                                          </td>
                                       </tr>
                                       <tr>
                                          <td class="noPadding">
                                             <table class="tableInner">
                                                <tr>
                                                   <td style="width: 50%;border:0;" id="label-shipment-detail">
                                                      Dimension(cm) : 1 x 1 x 1<bR>
                                                      Payment : <b>Perpaid</b><bR>
                                                      Weight(kg) : 1<bR>
                                                      AWB No. : 123456789<br/>
                                                      Route Code : DEL/ALT
                                                   </td>
                                                   <td style="width: 50%;text-align: center;border:0;" id="label-awb-barcode">
                                                      <span style="margin-left:110px">Delhivery</span><br />
                                                      <img src="{{asset($config->payment_qrcode)}}" style="height:60px;margin:10px;max-width:160px;margin-left:60px;">
                                                   </td>
                                                </tr>
                                             </table>
                                          </td>
                                       </tr>
                                       <tr>
                                          <td class="noPadding">
                                             <table class="tableInner">
                                                <tr>
                                                   <td style="width: 50%;border:0;">
                                                      <div id="label-order-detail">
                                                         <b>Shipped By</b>(if undelivered,return to)<br/>
                                                         <p style="padding:0;margin:0;">123 abc xyz society, surat Contact : <span id="spanSContactMaskStar" style="display: none;font-size: 11px;">74523243231</span><span id="spanSContactMask" >****</span><br>surat, gujarat, india 1244</p>
                                                         GSTIN: <span id="spanGstStar" style="display: none;font-size: 11px;">ABCG-1234321</span><span id="spanGstMask" >****</span><br/>
                                                         Invoice No. : SE-1000123<br/>
                                                      </div>
                                                      <div id="label-manifest-date">
                                                         Manifest Date. : 2022-01-05
                                                      </div>
                                                   </td>
                                                   <td style="width: 50%;text-align: center;border:0;" id="label-order-barcode">
                                                      <span style="margin-left:110px">Essentials</span><br/>
                                                      <span id="barcodeImage">
                                                      <img src="{{asset($config->payment_qrcode)}}" style="height:60px;margin:10px;max-width:160px;margin-left:60px;"><br/>
                                                      </span>
                                                      <span id="ordernumberVisibility" style="margin-left:80px">
                                                      Order #: 1000123
                                                      </span>
                                                   </td>
                                                </tr>
                                             </table>
                                          </td>
                                       </tr>
                                       <tr id="label-product-detail">
                                          <td class="noPadding">
                                             <table class="tableInner" id="productTable" style="width: 100%;border-collapse:collapse;border:0;">
                                                <thead>
                                                   <tr style="border: 1px solid black;">
                                                      <th style="width: 90%;">Name & SKU</th>
                                                      <th style="width: 10%;">QTY</th>
                                                   </tr>
                                                </thead>
                                                <tbody>
                                                   <tr>
                                                      <td>
                                                         Item : Apple iPhone <span class="bullet">...</span> <span class="full-name" style="display: none;font-size: 11px;">13 12GB</span> &nbsp; &nbsp; SKU : SKU-Name
                                                      </td>
                                                      <td>1</td>
                                                   </tr>
                                                   <tr>
                                                      <td><br></td>
                                                      <td></td>
                                                   </tr>
                                                   <tr>
                                                      <td><br></td>
                                                      <td></td>
                                                   </tr>
                                                   <tr id="label-invoice-value">
                                                      <td colspan="3" style="text-align: right;">TOTAL Amount : <span id="label-gift">AS A GIFT</span><span id="label-amount" style="font-size: 11px;">Rs. 100</span></td>
                                                   </tr>
                                                </tbody>
                                             </table>
                                          </td>
                                       </tr>
                                       <tr id="other-charges">
                                          <td class="noPadding">
                                             <table class="tableInner" border="5" id="productTable" style="width: 100%;border-collapse:collapse;border:0;">
                                                <tbody>
                                                   <tr>
                                                      <th>COD Charges</th>
                                                      <th>40</th>
                                                   </tr>
                                                   <tr>
                                                      <th>Shipping Charges</th>
                                                      <th>30</th>
                                                   </tr>
                                                   <tr>
                                                      <th>Discount</th>
                                                      <th>50</th>
                                                   </tr>
                                                </tbody>
                                             </table>
                                          </td>
                                       </tr>
                                       <tr id="disclaimer-text">
                                          <td>
                                             <p style="margin: 0;">All disputes are subject to delhi jurisdiction. Goods once sold will only be taken back or exchange as per the store's exchange/return policy.</p>
                                          </td>
                                       </tr>
                                       <tr id="label-footer">
                                          <td class="noPadding">
                                             <table class="tableInner">
                                                <tr>
                                                   <td style="width:70%;">{{$label->footer_customize_value}}</td>
                                                   <td style="width:30%;align-content: center;align-items: center;text-align: center;">
                                                      <img src="{{asset($config->logo)}}" style="height:25px;width: 70px;float: right;">
                                                   </td>
                                                </tr>
                                             </table>
                                          </td>
                                       </tr>
                                    </tbody>
                                 </table>
                                 <br>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- Recharge Modal -->
      <div class="modal fade" id="Rechargemodel" tabindex="-1"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
               <div class="modal-header">
                  <h1 class="modal-title fs-5"
                     id="exampleModalLabel">Upgrade Your Shipping
                     Limit
                  </h1>
                  <button type="button" class="btn-close"
                     data-bs-dismiss="modal"
                     aria-label="Close"></button>
               </div>
               <div class="modal-body">
                  <div class="payment_option">
                     <div class="row ">
                        <div class="col">
                           <span
                              class="fs-3 fw-semibold">₹0.00</span>
                           <h5 class="text-primary me-2"
                              style="white-space: nowrap;">Available
                              Balance
                           </h5>
                        </div>
                        <div class="col">
                           <span
                              class="fs-3 fw-semibold">₹0.00</span>
                           <h5 class="text-danger">Hold
                              Balance
                           </h5>
                        </div>
                        <div class="col">
                           <span
                              class="fs-3 fw-semibold">₹0.00</span>
                           <h5 class="text-primary">Usable
                              Amount
                           </h5>
                        </div>
                     </div>
                     <h5
                        class="text-primary fw-semibold mb-4 mt-2"><b>Your
                        wallet has been migrated to
                        Twinnship
                        Dashboard</b>
                     </h5>
                     <div
                        class="card-row border-0 bg-light-primary">
                        <p class="label">Enter the amount for
                           your
                           recharge 
                        </p>
                        <div class="form-group row"
                           id="data_amount">
                           <label for="inputPassword"
                              class="col-sm-3 text-right label">Amount
                           :</label>
                           <div class="col-sm-9">
                              <input type="number"
                                 autocomplete="off"
                                 name="filter"
                                 class="form-control bg-white border-0 text-dark rounded-pill"
                                 id="recharge_wallet_amount"
                                 placeholder="Enter Amount"
                                 value="500">
                           </div>
                           <span class="label mt-3">Or Select
                           amount
                           for quick recharge</span>
                           <div class="col-sm-12 text-center ">
                              <button type="button"
                                 class="btn btn-outline-success btn-sm set_recharge_amount"
                                 data-amount="500">500</button>
                              <button type="button"
                                 class="btn btn-outline-success btn-sm set_recharge_amount"
                                 data-amount="1000">1000</button>
                              <button type="button"
                                 class="btn btn-outline-success btn-sm set_recharge_amount"
                                 data-amount="2000">2000</button>
                              <button type="button"
                                 class="btn btn-outline-success btn-sm set_recharge_amount"
                                 data-amount="5000">5000</button>
                              <button type="button"
                                 class="btn btn-outline-success btn-sm set_recharge_amount"
                                 data-amount="10000">10000</button>
                           </div>
                           <span class="label mt-3">Have a
                           coupon?
                           Enter code to validate</span>
                           <div
                              class="form-group mb-4 position-relative">
                              <input type="text"
                                 class="form-control bg-white border-0 text-dark rounded-pill"
                                 placeholder="Enter Coupon">
                              <button type="submit"
                                 class="position-absolute top-50 end-0 translate-middle-y bg-primary p-0 border-0 text-center text-white rounded-pill px-3 py-2 me-2 fw-semibold">
                              Validate
                              </button>
                           </div>
                        </div>
                        <div class="modal-footer">
                           <button type="button"
                              class="btn btn-danger text-white"
                              data-bs-dismiss="modal">Close</button>
                           <button type="button"
                              class="btn btn-primary text-white">Recharge</button>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end model -->
      @include('seller.pages.scripts')
      <script>
         $(document).ready(function() {
         
             $(".save").click(function(event) {
                 event.preventDefault();
                 var footer_value = $("#footer_customize_value").val();
                 if (footer_value === "") {
                     $("#footer_customize_value").focus();
                     $("#footer_customize_value").addClass('is-invalid');
                     $("#errorName").text("Please enter a footer customize text");
                     return false;
                 }
                 $("#footer_customize_value").removeClass('is-invalid');
                 $("#errorName").hide();
                 return true;
             });
         
             // Init all config
             $(function() {
                 if($(".label-header-paragraph-mask").data("visibility") == "off") {
                     $("#spanContactMask").hide();
                     $("#spanContactMaskStar").show();
                 }
                 if($(".label-header-paragraph-mask1").data("visibility") == "off") {
                     $("#spanSContactMask").hide();
                     $("#spanSContactMaskStar").show();
                 }
                 if($(".label-header-paragraph-mask2").data("visibility") == "off") {
                     $("#spanGstMask").hide();
                     $("#spanGstStar").show();
                 }
                 if($(".label-header").data("visibility") == "off") {
                     $("#label-header").hide();
                 }
                 if($(".label-shipping-address").data("visibility") == "off") {
                     $("#label-shipping-address").hide();
                 }
                 if($(".label-header-logo").data("visibility") == "off") {
                     $("#label-header-logo").hide();
                 }
                 if($(".label-shipment-detail").data("visibility") == "off") {
                     $("#label-shipment-detail").hide();
                 }
                 if($(".label-awb-barcode").data("visibility") == "off") {
                     $("#label-awb-barcode").hide();
                 }
                 if($(".label-order-detail").data("visibility") == "off") {
                     $("#label-order-detail").hide();
                 }
                 if($(".label-manifest-date").data("visibility") == "off") {
                     $("#label-manifest-date").hide();
                 }
                 if($(".label-order-barcode").data("visibility") == "off") {
                     $("#label-order-barcode").hide();
                 }
                 if($(".label-order-barcode-visibility").data("visibility") == "off") {
                     $("#barcodeImage").hide();
                 }
                 if($(".label-ordernumber_visibility").data("visibility") == "off") {
                     $("#ordernumberVisibility").hide();
                 }
         
                 if($(".label-product-detail").data("visibility") == "off") {
                     $("#label-product-detail").hide();
                 }
                 if($(".other-charges").data("visibility") == "off") {
                     $("#other-charges").hide();
                 }
                 if($(".display_full_product_name").data("visibility") == "off") {
                     $(".bullet").show();
                     $(".full-name").hide();
                 }
                 if($(".label-invoice-value").data("visibility") == "off") {
                     $("#label-invoice-value").hide();
                 }
                 if($(".label-gift").data("visibility") == "off") {
                     $("#label-gift").hide();
                     $("#label-amount").show();
                 } else {
                     $("#label-gift").show();
                     $("#label-amount").hide();
                 }
                 if($(".disclaimer-text").data("visibility") == "off") {
                     $("#disclaimer-text").hide();
                 }
                 if($(".label-footer").data("visibility") == "off") {
                     $("#label-footer").hide();
                     $("#label-footerId").hide();
                 }
                 if($(".label-footer-text").data("visibility") == "off") {
                     $("#footer_customize_value").hide();
                 }
             });
         
             $(".label-header").click(function() {
                 if($(this).data("visibility") == "on") {
                     $("#label-header").hide();
                     $(this).data("visibility", "off");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Header: </span> <i class="fa fa-toggle-off"></i>`);
                 } else {
                     $("#label-header").show();
                     $(this).data("visibility", "on");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Header: </span> <i class="fa fa-toggle-on"></i>`);
                 }
             });
             $(".label-header-paragraph-mask").click(function() {
                 if($(this).data("visibility") == "on") {
                     $("#spanContactMask").hide();
                     $("#spanContactMaskStar").show();
                     $(this).data("visibility", "off");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Contact Mask: </span> <i class="fa fa-toggle-off"></i>`);
                 } else {
                     $("#spanContactMask").show();
                     $("#spanContactMaskStar").hide();
                     $(this).data("visibility", "on");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Contact Mask: </span> <i class="fa fa-toggle-on"></i>`);
                 }
             });
             $(".label-header-paragraph-mask1").click(function() {
                 if($(this).data("visibility") == "on") {
                     $("#spanSContactMask").hide();
                     $("#spanSContactMaskStar").show();
                     $(this).data("visibility", "off");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Seller Contact : </span> <i class="fa fa-toggle-off"></i>`);
                 } else {
                     $("#spanSContactMask").show();
                     $("#spanSContactMaskStar").hide();
                     $(this).data("visibility", "on");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Seller Contact : </span> <i class="fa fa-toggle-on"></i>`);
                 }
             });
         
             $(".label-header-paragraph-mask2").click(function() {
                 if($(this).data("visibility") == "on") {
                     $("#spanGstMask").hide();
                     $("#spanGstStar").show();
                     $(this).data("visibility", "off");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Seller GstIn : </span> <i class="fa fa-toggle-off"></i>`);
                 } else {
                     $("#spanGstMask").show();
                     $("#spanGstStar").hide();
                     $(this).data("visibility", "on");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Seller GstIn : </span> <i class="fa fa-toggle-on"></i>`);
                 }
             });
         
             $(".label-shipping-address").click(function() {
                 if($(this).data("visibility") == "on") {
                     $("#label-shipping-address").hide();
                     $(this).data("visibility", "off");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Shipping Address: </span> <i class="fa fa-toggle-off"></i>`);
                 } else {
                     $("#label-shipping-address").show();
                     $(this).data("visibility", "on");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Shipping Address: </span> <i class="fa fa-toggle-on"></i>`);
                 }
             });
             $(".label-header-logo").click(function() {
                 if($(this).data("visibility") == "on") {
                     $("#label-header-logo").hide();
                     $(this).data("visibility", "off");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Logo: </span> <i class="fa fa-toggle-off"></i>`);
                 } else {
                     $("#label-header-logo").show();
                     $(this).data("visibility", "on");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Logo: </span> <i class="fa fa-toggle-on"></i>`);
                 }
             });
             $(".label-shipment-detail").click(function() {
                 if($(this).data("visibility") == "on") {
                     $("#label-shipment-detail").hide();
                     $(this).data("visibility", "off");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Shipment Detail: </span> <i class="fa fa-toggle-off"></i>`);
                 } else {
                     $("#label-shipment-detail").show();
                     $(this).data("visibility", "on");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Shipment Detail: </span> <i class="fa fa-toggle-on"></i>`);
                 }
             });
             $(".label-awb-barcode").click(function() {
                 if($(this).data("visibility") == "on") {
                     $("#label-awb-barcode").hide();
                     $(this).data("visibility", "off");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Awb Barcode: </span> <i class="fa fa-toggle-off"></i>`);
                 } else {
                     $("#label-awb-barcode").show();
                     $(this).data("visibility", "on");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Awb Barcode: </span> <i class="fa fa-toggle-on"></i>`);
                 }
             });
             $(".label-order-detail").click(function() {
                 if($(this).data("visibility") == "on") {
                     $("#label-order-detail").hide();
                     $(this).data("visibility", "off");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Order Detail: </span> <i class="fa fa-toggle-off"></i>`);
                 } else {
                     $("#label-order-detail").show();
                     $(this).data("visibility", "on");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Order Detail: </span> <i class="fa fa-toggle-on"></i>`);
                 }
             });
             $(".label-manifest-date").click(function() {
                 if($(this).data("visibility") == "on") {
                     $("#label-manifest-date").hide();
                     $(this).data("visibility", "off");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Manifest Date: </span> <i class="fa fa-toggle-off"></i>`);
                 } else {
                     $("#label-manifest-date").show();
                     $(this).data("visibility", "on");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Manifest Date: </span> <i class="fa fa-toggle-on"></i>`);
                 }
             });
             $(".label-order-barcode").click(function() {
                 if($(this).data("visibility") == "on") {
                     $("#label-order-barcode").hide();
                     $(this).data("visibility", "off");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Order Barcode: </span> <i class="fa fa-toggle-off"></i>`);
                 } else {
                     $("#label-order-barcode").show();
                     $(this).data("visibility", "on");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Order Barcode: </span> <i class="fa fa-toggle-on"></i>`);
                 }
             });
             $(".label-order-barcode-visibility").click(function() {
                 if($(this).data("visibility") == "on") {
                     $("#barcodeImage").hide();
                     $(this).data("visibility", "off");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Barcode Visibility: </span> <i class="fa fa-toggle-off"></i>`);
                 } else {
                     $("#barcodeImage").show();
                     $(this).data("visibility", "on");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Barcode Visibility: </span> <i class="fa fa-toggle-on"></i>`);
                 }
             });
             $(".label-ordernumber_visibility").click(function() {
                 if($(this).data("visibility") == "on") {
                     $("#ordernumberVisibility").hide();
                     $(this).data("visibility", "off");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Order Number Visibility: </span> <i class="fa fa-toggle-off"></i>`);
                 } else {
                     $("#ordernumberVisibility").show();
                     $(this).data("visibility", "on");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Order Number Visibility: </span> <i class="fa fa-toggle-on"></i>`);
                 }
             });
         
         
             $(".label-product-detail").click(function() {
                 if($(this).data("visibility") == "on") {
                     $("#label-product-detail").hide();
                     $(this).data("visibility", "off");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Product Detail: </span> <i class="fa fa-toggle-off"></i>`);
                 } else {
                     $("#label-product-detail").show();
                     $(this).data("visibility", "on");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Product Detail: </span> <i class="fa fa-toggle-on"></i>`);
                 }
             });
             $(".other-charges").click(function() {
                 if($(this).data("visibility") == "on") {
                     $("#other-charges").hide();
                     $(this).data("visibility", "off");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Other Charges: </span> <i class="fa fa-toggle-off"></i>`);
                 } else {
                     $("#other-charges").show();
                     $(this).data("visibility", "on");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Other Charges: </span> <i class="fa fa-toggle-on"></i>`);
                 }
             });
             $(".display_full_product_name").click(function() {
                 if($(this).data("visibility") == "on") {
                     $('.bullet').show();
                     $('.full-name').hide();
                     $(this).data("visibility", "off");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Full Product Name: </span> <i class="fa fa-toggle-off"></i>`);
                 } else {
                     $('.bullet').hide();
                     $('.full-name').show();
                     $(this).data("visibility", "on");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Full Product Name: </span> <i class="fa fa-toggle-on"></i>`);
                 }
             });
             $(".label-invoice-value").click(function() {
                 if($(this).data("visibility") == "on") {
                     $("#label-invoice-value").hide();
                     $(this).data("visibility", "off");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Invoice Value: </span> <i class="fa fa-toggle-off"></i>`);
                 } else {
                     $("#label-invoice-value").show();
                     $(this).data("visibility", "on");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Invoice Value: </span> <i class="fa fa-toggle-on"></i>`);
                 }
             });
             $(".label-gift").click(function() {
                 if($(this).data("visibility") == "on") {
                     $("#label-gift").hide();
                     $("#label-amount").show();
                     $(this).data("visibility", "off");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">As a gift: </span> <i class="fa fa-toggle-off"></i>`);
                 } else {
                     $("#label-gift").show();
                     $("#label-amount").hide();
                     $(this).data("visibility", "on");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">As a gift: </span> <i class="fa fa-toggle-on"></i>`);
                 }
             });
             $(".disclaimer-text").click(function() {
                 if($(this).data("visibility") == "on") {
                     $("#disclaimer-text").hide();
                     $(this).data("visibility", "off");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Disclaimer Text: </span> <i class="fa fa-toggle-off"></i>`);
                 } else {
                     $("#disclaimer-text").show();
                     $(this).data("visibility", "on");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Disclaimer Text: </span> <i class="fa fa-toggle-on"></i>`);
                 }
             });
             $(".label-footer").click(function() {
                 if($(this).data("visibility") == "on") {
                     $("#label-footer").hide();
                     $("#label-footerId").hide();
                     $("#footer_customize_value").removeClass('is-invalid');
                     $("#errorName").hide();
                     $(this).data("visibility", "off");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Footer: </span> <i class="fa fa-toggle-off"></i>`);
                 } else {
                     $("#label-footer").show();
                     $("#label-footerId").show();
                     $(this).data("visibility", "on");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Footer: </span> <i class="fa fa-toggle-on"></i>`);
                 }
             });
             $(".tabular-form").click(function() {
                 if($(this).data("visibility") === "on") {
                     $('#productTable td').css({
                         'border' : '0'
                     });
                     $(this).data("visibility", "off");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Tabular Product Data: </span> <i class="fa fa-toggle-off"></i>`);
                 } else {
                     $('#productTable td').css({
                         'border' : 'solid 1px black'
                     });
                     $(this).data("visibility", "on");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Tabular Product Data: </span> <i class="fa fa-toggle-on"></i>`);
                 }
             });
             $(".display-products").click(function() {
                 if($(this).data("visibility") == "on") {
                     $(this).data("visibility", "off");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Display All Products: </span> <i class="fa fa-toggle-off"></i>`);
                 } else {
                     $("#label-header-logo").show();
                     $(this).data("visibility", "on");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Display All Products: </span> <i class="fa fa-toggle-on"></i>`);
                 }
             });
             $(".label-footer-text").click(function() {
                 if($(this).data("visibility") == "on") {
                     $("#footer_customize_value").hide();
                     $("#footer_customize_value").removeClass('is-invalid');
                     $("#errorName").hide();
                     $(this).data("visibility", "off");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Footer Customize Text: </span> <i class="fa fa-toggle-off"></i>`);
                 } else {
                     $("#footer_customize_value").show();
                     $(this).data("visibility", "on");
                     $(this).html(`<span class="font-size: 18px; font-weight: bold;">Footer Customize Text: </span> <i class="fa fa-toggle-on"></i>`);
                 }
             });
         
             $(".save").click(function() {
                 if($('#footer_customize_value').val() !== "")
                 {
                     showOverlay();
                 }
                 $.ajax({
                     url: "{{ route('seller.storeCustomisedLabel') }}",
                     method: "POST",
                     data: {
                         _token: "{{ csrf_token() }}",
                         header_visibility: $(".label-header").data("visibility") == "on" ? "y" : "n",
                         shipping_address_visibility: $(".label-shipping-address").data("visibility") == "on" ? "y" : "n",
                         header_logo_visibility: $(".label-header-logo").data("visibility") == "on" ? "y" : "n",
                         shipment_detail_visibility: $(".label-shipment-detail").data("visibility") == "on" ? "y" : "n",
                         awb_barcode_visibility: $(".label-awb-barcode").data("visibility") == "on" ? "y" : "n",
                         order_detail_visibility: $(".label-order-detail").data("visibility") == "on" ? "y" : "n",
                         manifest_date_visibility: $(".label-manifest-date").data("visibility") == "on" ? "y" : "n",
                         order_barcode_visibility: $(".label-order-barcode").data("visibility") == "on" ? "y" : "n",
                         product_detail_visibility: $(".label-product-detail").data("visibility") == "on" ? "y" : "n",
                         invoice_value_visibility: $(".label-invoice-value").data("visibility") == "on" ? "y" : "n",
                         other_charges: $(".other-charges").data("visibility") == "on" ? "y" : "n",
                         display_full_product_name: $(".display_full_product_name").data("visibility") == "on" ? "y" : "n",
                         gift_visibility: $(".label-gift").data("visibility") == "on" ? "y" : "n",
                         disclaimer_text: $(".disclaimer-text").data("visibility") == "on" ? "y" : "n",
                         footer_visibility: $(".label-footer").data("visibility") == "on" ? "y" : "n",
                         tabular_form_enabled: $(".tabular-form").data("visibility") == "on" ? "y" : "n",
                         custom_footer_enable: $(".label-footer-text").data("visibility") == "on" ? "y" : "n",
                         footer_customize_value: $('#footer_customize_value').val(),
                         all_product_display: $('.display-products').data('visibility') == "on" ? "y" : "n",
                         contact_mask: $(".label-header-paragraph-mask").data("visibility") == "on" ? "y" : "n",
                         ordernumber_visibility: $(".label-ordernumber_visibility").data("visibility") == "on" ? "y" : "n",
                         s_contact_mask: $(".label-header-paragraph-mask1").data("visibility") == "on" ? "y" : "n",
                         s_gst_mask: $(".label-header-paragraph-mask2").data("visibility") == "on" ? "y" : "n",
                         barcode_visibility: $(".label-order-barcode-visibility").data("visibility") == "on" ? "y" : "n"
                     },
                     success: function(res) {
                         hideOverlay();
                         if(res.statusCode == 200) {
                             // ok
                             $('#label-footer').load(document.URL +  ' #label-footer tr');
                             if($('#footer_customize_value').val() !== "")
                                 $.notify(" Label configuration saved successfully.", {animationType:"scale", align:"right", type: "success", icon:"check"});
                         } else {
                             // error
                             $.notify(" Sorry label configuration is not saved.", {animationType:"scale", align:"right", type: "danger", icon:"close"});
                         }
                     },
                     error: function(error) {
                         hideOverlay();
                         // error
                         $.notify(" Internal server error.", {animationType:"scale", align:"right", type: "danger", icon:"close"});
                     }
                 });
             });
         });
      </script>
   </body>
</html>