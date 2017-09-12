/**
 * Stále viditelná hlavička tabulky  
 */
$(window).scroll(function () {

    var menuHeight = $('#Menu').height();
    var tbHeight = $('.tDiv').height();

    var position = $('.flexigrid .tDiv').offset().top - $(window).scrollTop();
    if (!$('.flexigrid .tDiv').visible(true) || (position < menuHeight)) {
        $('.flexigrid .tDiv').css({"position": "fixed", "top": menuHeight, "z-index": 1, "width": "100%"});
        $('.flexigrid .hDiv').css({"position": "fixed", "top": menuHeight + tbHeight, "z-index": 1});
    }

    if ($(window).scrollTop() < menuHeight) {
        $('.flexigrid .tDiv').css({"position": "relative", "top": 0});
//        var move = $('.bDiv').scrollLeft + $('.mDiv').offset().left;
        
        var move = $('.bDiv').scrollLeft;
        if(move){
            move = move + $('.mDiv').offset().left * 2;
        }
        $('.flexigrid .hDiv').css({"position": "relative", "top": 0, "left": move});
    }


});    