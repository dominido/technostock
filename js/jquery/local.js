jQuery(document).ready(function($) {
    $(".fancybox").fancybox({
        autoSize	: true,
        closeClick	: false,
        openEffect	: 'fade',
        closeEffect	: 'fade'
    });

    $(".qty-wrapper input").spinner({
        min: 1
    });

    var itemProductName = $('.item .product-name');

    itemProductName.dotdotdot();
    $('.compare-table .product-name').dotdotdot();

    //price slider for mobile fix
    var handle = $( ".slider-wrap .handle" );
    handle.draggable({
        axis: 'x',
        containment: jQuery('.slider-wrap'),
        revert: true,
        stop: function (){
            var l = ( 100 * parseFloat($(this).css("left")) / parseFloat($(this).parent().css("width")) )+ "%" ;
            var t = ( 100 * parseFloat($(this).css("top")) / parseFloat($(this).parent().css("height")) )+ "%" ;
            $(this).css("left" , l);
            $(this).css("top" , t);
        }
    });

    $('.vert-nav').parent().addClass('fixed-nav');

    $('.pager li').click(function() {
        $(this).addClass('current');
    });

    //RESIZE
    $(window).resize(function () {
        itemProductName.dotdotdot();
        $('.benefits-grid .benefit .benefit-content').matchHeight();

        if ($(window).width() < 751) {
            $('.product-img-column').prepend($('.product-shop-heading'));

            var footerAbout = $('.footer-about');

            if(!footerAbout.hasClass('contactsCloned')) {
                $('.header-top .header-contacts').clone().insertBefore($('.callback-footer'));
                footerAbout.addClass('contactsCloned');

                $(".callback-footer-holder .header-contacts .dropdown-heading").on("click",function(){
                    var url = $(this).attr('href');
                    $(location).attr('href',url);
                });
            }

        } else {
            $('.product-shop').prepend($('.product-shop-heading'));
            $('.footer-about .header-contacts').remove();
            $('.footer-about').removeClass('contactsCloned');
        }
    }).resize();
});