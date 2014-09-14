<?if (false) {?><script><?}?>
<?="\n/*".get_class()."*/\n"?>
$( document ).ready(function() {
	$(window).resize(function(event) {
		var navbarHeight=$(".navbar").height();
		var headerHeight=$(".header").height();

		$(".header").add(".mainLinks").css({
			top:navbarHeight+'px'
		});
		$(".content").css({
			top:navbarHeight+headerHeight+'px'
		});
		/*
		var fixedHeight=0;
		var $fixedElements=$('*').filter(function() {return $(this).css("position") === 'fixed';});
		$fixedElements.each(function(index, el) {
			fixedHeight+=$(this).height();
		});
		$(".content").css({
			top:fixedHeight+'px'
		});
		*/


		$(".fragment").css ({
			'min-height': ($(window).height()-navbarHeight)*0.90
		});
		$(window).scroll();
	});
	$(window).resize();
	/*
	$(".header>img").load(function() {
		$(window).resize();
	});
	*/
	$(window).scroll(function(event) {
		var newTop=calcMainLinksTop();
		$(".mainLinks").css({
			top: newTop+'px'
		});
	});

	$(".mainLinks>.btn-group").hover(function() {
		var navbarHeight=$(".navbar").height();
		$(".mainLinks").stop();
		$(".mainLinks").animate({
			top: navbarHeight+'px',
		}, {
			duration:'fast',
			easing: 'easeOutBounce'
		});

	}, function() {
		var top=calcMainLinksTop();
		$(".mainLinks").stop();
		$(".mainLinks").animate({
			top: top+'px',
		}, {
			duration:'fast'
		});
	});


	$(document).on("click", ".yamm .dropdown-menu", function(e) {
		e.stopPropagation()
	});

	$(".navbar-brand").tooltip();

	/* Nav & Yamm  Multi-level dropdowns */
		$('ul.dropdown-menu [data-toggle=dropdown]').on('click', function(event) {
			// Avoid following the href location when clicking
			event.preventDefault();
			// Avoid having the menu to close when clicking
			event.stopPropagation();
			//$(this).parent().addClass('open');
			//$(this).parent().find("ul").parent().find("li.dropdown").addClass('open');
			if ($(this).parent().hasClass('open')) {
				//Cerramos el dropdown que rebice el click
				$(this).parent().removeClass('open');
			} else {
				//Quitamos el open a todos los dropdown hermanos del que rebice el click
				$(this).parent().parent().find("li.dropdown").removeClass('open');
				//Reañadimos open al dropdown que rebice el click
				$(this).parent().addClass('open');
			}
			//
		});
	/**/
	/**/
	$('.mainLinks button').click(function(event) {
		return scrollToName($.attr(this, 'id'));
	});
	/**/
});

function scrollToName (name) {
	var elDest=$('[name="' + name + '"]');
	var scrollDest=(elDest.offset().top-elDest.height())*0.97;
	$('html, body').animate({
		scrollTop: scrollDest
	}, 500);
	return false;
}

function calcMainLinksTop() {
	var navbarHeight=$(".navbar").height();
	var topStop=(0-$(".mainLinks").height()+navbarHeight)+7;
	if ($(this).scrollTop()>100) {
		newTop=navbarHeight-(($(this).scrollTop()-100)/5);
	} else {
		newTop=navbarHeight;
	}
	if (newTop<topStop) {newTop=topStop;}
	return newTop;
}
