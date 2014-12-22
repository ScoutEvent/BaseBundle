
jQuery(function($) {
     
    $(window).resize(function() {
        if (this.resizeTo) clearTimeout(this.resizeTo);
        this.resizeTo = setTimeout(function() {
            $(this).trigger('resizeEnd');
        }, 10);
    }).bind('resizeEnd', function() {
        var singleTile = $(".col-sm-2 .tile:eq(0)");
    	$(".tile").css("height", singleTile.width());
        $(".carousel").css("height", singleTile.width());
        $(".item").css("height", singleTile.width());
    }).trigger('resizeEnd');

});
