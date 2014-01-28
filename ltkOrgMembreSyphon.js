var ltkOrgMembreSyphon = function()
{

	var MBRs = [];

	$('.membercard').each(function(i,v){
		var $this = $(this);
		var MBR = {
			displayname: $this.find('.frontinfo .name').html(),
			handle: $this.find('.frontinfo .nick').html(),
			logoUrl: $this.find('.rank').get(0).src,
			roles:[],
			rolestr:'',
		};

		$this.find('.rolelist .role').each(function(i,v){
			var role = $(this).html();
			if(role.substr(0,2)=="- ")
				role = role.substr(2);

			if(MBR.roles.length>0)
				MBR.rolestr = MBR.rolestr+",";

			MBR.rolestr = MBR.rolestr+""+role;
			MBR.roles.push(role);
		});

		MBRs.push(MBR);
	});


	$('#ltkOrgMembreSyphon').remove();
	$('footer').prepend($('<textarea id="ltkOrgMembreSyphon"></textarea>'));
	$('#ltkOrgMembreSyphon').bind('click',function(){$(this).select()}).html(JSON.stringify(MBRs));

}

ltkOrgMembreSyphon();
