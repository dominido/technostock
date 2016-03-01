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

    $('.benefits-grid .benefit').matchHeight();

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

    //acc/tabs fix
    $(".acctab").click(function () {

        var checkElement = $(this).next();

        if ((checkElement.is('.panel')) && (checkElement.is(':visible'))) {
            $(this).removeClass('current');
            checkElement.hide();
        }
        else {
            $(this).addClass('current');
            checkElement.show();
        }
    });

    //RESIZE
    $(window).resize(function () {
        if ($(window).width() < 751) {
            $('.product-img-column').prepend($('.product-shop-heading'));
        } else {
            $('.product-shop').prepend($('.product-shop-heading'));
        }
    }).resize();
});
