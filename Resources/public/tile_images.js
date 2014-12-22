jQuery(function ($) {
    var tile = $("<div class=\"col-sm-2 col-xs-4\"><div class=\"tile\"><div class=\"carousel slide\" data-ride=\"carousel\"><div class=\"carousel-inner\"></div></div></div></div>");
    
    for (var i = 0; i < tile_images.length / 3; ++i) {
        var current_tile = tile.clone();
        var inner = current_tile.find(".tile");
        inner.css("padding", 0);
        $(".dynamicTile .row").append(current_tile);
    
        for (var j = i * 3; j < tile_images.length && j < (i + 1) * 3; ++j) {
            var item = $("<div class=\"item\" />");
            item.css({
                background: "url('" + tile_images[j] + "')",
                backgroundSize: "cover"
            });
            var inner = current_tile.find(".carousel-inner").append(item);
            if (inner.children().length == 1) {
                item.addClass("active");
            }
        }
    }
    
    $(window).trigger('resize');
});
