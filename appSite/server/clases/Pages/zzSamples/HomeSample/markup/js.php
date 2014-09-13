<?if (false) {?><script><?}?>
<?="\n/*".get_class()."*/\n"?>
$( document ).ready(function() {
	$(window).resize(function(event) {
		var navbarHeight=$(".navbar").height();
		var fotoHeight=$(".foto").height();
		$(".foto").css({
			top:navbarHeight+'px'
		});
		$(".content").css({
			top:navbarHeight+fotoHeight+'px'
		});

		$(".fragment").css ({
			'min-height': ($(window).height()-navbarHeight)*1.20
		});
	});
	$(window).resize();
	$(".foto>img").load(function() {
		$(window).resize();
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
});
