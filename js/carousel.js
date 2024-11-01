var j = jQuery.noConflict();

j(document).ready(function($) {
	
	//var delay = 8;
	//var bdelay = delay*1000;
	var banners = $('#mastin .slide').size(); 
	var bannersorig = $('#mastin .slide').size();
	var limit = 6;
	var pages = limit*bannersorig; 	
	var i = 1;
	var clone = $('#mastin .slide').clone();
	var c = 2;
	
	function rotateBanner(width) {
		if(i < banners) {
				//alert("banners: "+banners+" i: "+i);
				bannerScroll(i,width); //Trigger the paging and slider function
				n=i+1;
				i ++;  
			} else if (i < pages) {
				$('.mastdiv').append(clone);
				clone = $('#mastin .slide').clone();
				
				//alert("(else) banners: "+banners+" i: "+i);
				bannerScroll(i,width); //Trigger the paging and slider function
				n=i+1;
				i++;
				
				banners=banners+bannersorig;
				//$('.slide').slice(0, 4).remove();

				c++;
			} else {
				bannerScroll(0); //Trigger the paging and slider function
				i = 1;	
			}
	}

	function bannerScroll(n,width) {
		var margin = n*width;
		$(".slidetitle").fadeOut(750);
		window.setTimeout(function() {  
    		$("#mastin .mastdiv").animate({ 
				marginLeft: "-"+margin+"px"
			}, 2000 );
		}, 1250);  
		window.setTimeout(function() {  
			$(".slidetitle").fadeIn(750);
		}, 3000);		
	}

	jQuery.rotateSwitch = function rotateSwitch(tdelay, width){
		var delay = tdelay*1000;
		play = setInterval(function(){ //Set timer - this will repeat itself every 3 seconds
			rotateBanner(width);
			}, delay);
	}
	
});