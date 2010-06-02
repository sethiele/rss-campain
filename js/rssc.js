jQuery(document).ready(function(){
	
	jQuery(".rssc-head").click(function(){
	  var what_klicked = jQuery(this).attr('id').substr(10);
	  if (jQuery("#rssc-"+what_klicked+"-optionen").is(":visible")){
	    jQuery("#rssc-"+what_klicked+"-optionen").hide();
	  } else {
	    jQuery("#rssc-"+what_klicked+"-optionen").show();
	  }
	});
	
});