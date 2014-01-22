var ltkOrgSyphon = function()
{

	var ORGs = [];

	$('.org-cell').each(function(i,v){
		var $this = $(this);
		var ORG = {
			name: $this.find('.identity .name').html(),
			symbol: $this.find('.identity .symbol').html(),
			pageUrl: $this.children('a').get(0).href,
			logoUrl: $this.find('.thumb img').get(0).src,
		};

		$this.find('.infocontainer .infoitem').each(function(ii,vv){
			var $item = $(this);
			var item = {
				label: $item.children('.label').html().trim(),
				value: $item.children('.value').html().trim(),
			};
			item.label = item.label.substring(0,item.label.length-1);
			ORG[item.label] = item.value;
		});


		ORGs.push(ORG);
	});
	$('#ltkOrgSyphon').remove();
	$('footer').prepend($('<textarea id="ltkOrgSyphon"></textarea>'));
	$('#ltkOrgSyphon').bind('click',function(){$(this).select()}).html(JSON.stringify(ORGs));

}

ltkOrgSyphon();
