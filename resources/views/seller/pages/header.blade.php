@php
    $seller = \App\Models\Seller::find(Session()->get('MySeller')->id);
    $notification=[
        'reassign' => [
            'status' => false,
            'data' => null
        ],
        'reconciliation' => false
    ];
    foreach ($seller->unreadNotifications as $n){
        if(str_contains(strtolower($n->type),'reassign')){
            $notification['reassign']['status'] = true;
            $notification['reassign']['data'] = $n->data;
        }
        else if(str_contains(strtolower($n->type),'dispute'))
            $notification['reconciliation'] = true;
    }
@endphp

<div class="preloader" id="preloader">
    <div class="preloader">
        <div class="waviy position-relative">

        </div>
    </div>
</div>
<<div class="container-fluid">
    <div class="main-content">
        <header class="header-area card bg-white mb-4 rounded-bottom-10" id="header-area">
            <div class="d-flex justify-content-between align-items-center">
                <?php 
                    $basicInfoData = App\Models\Basic_informations::where('seller_id', Session()->get('MySeller')->id)->first();
                ?>
                <div>
                    @if(isset($basicInfoData) && $basicInfoData !== null)
                        <h4 class="text-danger">
                            Welcome To {{$basicInfoData->company_name}}
                        </h4>
                    @else
                        <h4 class="text-danger">
                            Company Name
                        </h4>
                    @endif
                </div>
                <div class="text-end d-flex flex-grow-1 justify-content-end align-items-center">

                    <div class="me-2">
                        <a class="btn quick balance text-decoration-none me-2" id="rechargeButtonModal" data-bs-toggle="modal"
                           data-bs-target="#Rechargemodel"><ion-icon name="wallet-outline"
                                                                     style="font-size: 1.8rem;"></ion-icon> ₹ <span id="sellerBalanceLabel">{{Session()->get('MySeller')->balance}}</span>
                        </a>
                    </div>
                    <div class="">
                        <div style="font-size: 2rem; display: flex; align-items: center;"><ion-icon
                                name="notifications-outline"></ion-icon></div>
                    </div>
                    <div class="">
                        <div style="font-size: 2rem; display: flex; align-items: center;" data-bs-toggle="dropdown">
                            <ion-icon name="person-circle-outline"></ion-icon>
                            <i data-feather="chevron-down"></i>
                        </div>
                        <div class="dropdown-menu dropdown-menu-end bg-white border box-shadow">
                            <div>
                                <a class="dropdown-item text-dark" href="{{route('seller.profile')}}"> Edit Profile </a>
                            </div>
                            <div>
                                <a class="dropdown-item text-dark" href="{{route('seller.logout')}}">Log Out</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
    </div>
</div>
<script>
  //get dimensi data in modal

        function refreshRecharge(){
             $.ajax({
                url: '{{route('seller.refreshRecharge')}}',
                success: function (response) {
                    $.LoadingOverlay('hide');
                    location.reload();
                },
                error: function (response) {
                    $.LoadingOverlay('hide');
                    $.notify(" Oops... Something went wrong!", {
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
        }

        function FetchChannelOrder(){
            showOverlay();
            $.ajax({
                url: '{{route('seller.fetch_all_orders')}}',
                success: function (response) {
                    hideOverlay();
                    location.reload();
                },
                error: function (response) {
                    hideOverlay();
                    $.notify(" Oops... Order Not Fetched!", {
                        blur: 0.2,
                        delay: 0,
                        verticalAlign: "top",
                        animationType: "scale",
                        align: "right",
                        type: "danger",
                        icon: "close"
                    });
                }
            });
        }
</script>
