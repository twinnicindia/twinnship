@forelse ($seller->unreadNotifications as $notification)
<div class="col-md-12">
    <h6>Your Order AWB {{$notification['data']['awb_number']}} is Under Weight Reconciliation</h6>
    @if(!$loop->last)
    <hr>
    @else
    <a href="{{route('seller.mark_all_as_read')}}">Mark All as Read</a>
    @endif
</div>
@empty
<div class="col-md-12">
    <h6 class="mb-0">No New Notification</h6>
</div>
@endforelse