    $(document).ready(function(){
        $('.slider').slick({
            dots: true,  
            infinite: true,  
            speed: 300,  
            slidesToShow: 3,  
            slidesToScroll: 1,  
            autoplay: true,  
            autoplaySpeed: 2000,  
            arrows: false,  
            pauseOnHover: false,  
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });
    });