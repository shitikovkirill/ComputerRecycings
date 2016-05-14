
	$("#abt").hover(function () {
		if ($("#abtCont").is(":hidden")) {
			$("#abtCont").slideDown("slow");
		} else {
			$("#abtCont").slideUp("slow");
		}
	});
	
	$("#proj").hover(function () {
		if ($("#prjCont").is(":hidden")) {
			$("#prjCont").slideDown("slow");
		} else {
			$("#prjCont").slideUp("slow");
		}
	});
	
	$("#med").hover(function () {
		if ($("#medCont").is(":hidden")) {
			$("#medCont").slideDown("slow");
		} else {
			$("#medCont").slideUp("slow");
		}
	});
	
	$("#snp").hover(function () {
		if ($("#snpCont").is(":hidden")) {
			$("#snpCont").slideDown("slow");
		} else {
			$("#snpCont").slideUp("slow");
		}
	});



    $('#main-slider').liquidSlider();
	
	$(function() {
				$( '#dl-menu' ).dlmenu();
			});

