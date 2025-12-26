<section class="contactBoxSection">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="slider">
                    <div class="slider-items">
                        @foreach($brand as $b)
                            <img src="{{asset($b->image)}}" alt="">
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
