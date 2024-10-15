jQuery(document).ready(function($) {
    $('.owl-carousel').owlCarousel({
        loop: false,
        rewind: true,
        nav: false,
        dots:  false,
        autoplay: true,
        margin: 10,
        autoplayHoverPause: true,
        autoplayTimeout: 2500,
        responsiveClass: true,
        responsive:{
            0:{
                items: 1,
            },
            500:{
                items: 2,
            },
            700: {
                items: 3
            },
            1200:{
                items:4
            }
        }
    });
});
