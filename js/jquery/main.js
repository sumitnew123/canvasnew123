$(window).load( function() {

	$('#container').masonry({
        "itemSelector": ".item1",
        "columnWidth": ".grid-sizer",
    });
	
    $('#container2').masonry({
        "itemSelector": ".item2",
        "columnWidth": ".grid-sizer2",
    });

	$( '#container' ).append( '<div class="item1"><a href="#"><h4>Computer and Electronics</h4><img src="images/193.png" class="image"></a></div>' );
	$( '#container' ).masonry( 'reloadItems' );
	$( '#container' ).masonry( 'layout' );
});
