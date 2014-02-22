function ltkImageSyphon()
{
	// extract download link url when you have opened an image gallery from RSI website
	var $container = $('<textarea id="ltksyphon"></textarea>');
	$('.download').each(function(i,v){
		$container.append('https://robertsspaceindustries.com/'+$(this).attr('href')+'\n');
	});
	$('body').html('').append($container);
	$container.bind('click', function(){$(this).select()}).css('height','400').css('width','100%');
}
ltkImageSyphon();
