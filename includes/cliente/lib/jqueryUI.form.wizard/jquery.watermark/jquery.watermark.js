/******************************************************************************/
/* jquery.watermark.js v1.2

/* utilidad para añadir marcar de agua a los inputs y los textareas de un form

/* Creado a partir de:
/* jquery.watermark.js
	(http://daersystems.com/jquerywatermark.asp)
 
/* History
/* v 1.2 (20120424)
/* Modificado para que, si el attr usado es placeholder, solo se coloque la marca si el navegador no soporta nativamente placeholder

/* v 1.1 (20120331)
/* Modificado para que la marca de agua se coloque correctamente, probado en IE7 (Aceptable), IE8,9, FF11, Opera 11.62 (Aceptable) y Chrome 17

/* v 1.0 (20110709)
/* Reestrucutrado totalmente para que se base en un solo nombre en el
	espacio de nombres jquery.fn y tenga opciones por defecto
	La prioridad de las opciones es:
		* opciones pasadas en la llamada JS
		* opciones del objeto codificado en el atributo del marcado
		* opciones por defecto

/* v 0.1
/* Modificada $.fn.watermark para que no necesite recibir ningún parametro		
/******************************************************************************/
  
(function($) {
	$.fn.watermark = function(options) {
		var opts = $.extend({}, $.fn.watermark.defaults, options);
		if (this.attr(opts.attr) !== undefined && this.attr(opts.attr) !== false) {
			//Si tenemos atributo pasado en las opciones (por omisión es "rel") en el element
			return this.each(function() {
				var o=$(this).attr(opts.attr);
				if(typeof(o) == "string") {
					try {
						//intentamos tratar el contenido del atributo como un objeto
						o = eval("(" + o + ")");
						//console.log("o recien eval");
						//console.log(o);
					} catch(ex) {
						//si no es tratable como un objeto, suponemos que solo es el html
						//contenido de la marca de agua y lo convertimos en un objeto
						o = {html:o};
					}
				}
				o=$.extend({},$.fn.watermark.defaults, o, options);

				o.el = this;
				//Si el attr es "placeholder" y el navegador tiene soporte para él, no hcemos nada
				var input = document.createElement('input');
				var hasPlaceholderSupport=('placeholder' in input);
				if (
					!hasPlaceholderSupport ||
					o.attr!="placeholder"
				) {
					return $.fn.watermark.add(o);
				}
			});
		} else {
			return this;
		}
	};

	$.fn.watermark.add=function(o) {
		o.el = $(o.el);
		if(o.el.parent().attr("wmwrap") != 'true') {
			o.el = o.el.wrap("<span wmwrap='true' style='position:relative;'/>");
			var l = $("<span/>");
			
			if(o.html) { l.html(o.html); }
			if(o.cls) { l.addClass(o.cls); }

			//Metemos el CSS y los atributos heredados
			if(o.inheritCss) {
				if(typeof o.inheritCss == "string") {
					l.css(o.inheritCss,o.el.css(o.inheritCss));
				} else {							
					for(var x=0;x<o.inheritCss.length;x++) {
						l.css(o.inheritCss[x],o.el.css(o.inheritCss[x]));
					}							
				}
			}

			if(o.inheritAttr) {
				if(typeof o.inheritAttr == "string") {
					if (o.el.attr(o.inheritAttr)) {
						l.attr(o.inheritAttr,o.el.attr(o.inheritAttr));
					}
				} else {
					for(var x=0;x<o.inheritCss.length;x++) {
						if (o.el.attr(o.inheritAttr[x])) {
							l.attr(o.inheritAttr[x],o.el.attr(o.inheritAttr[x]));
						}
					}							
				}
			}

			//Metemos el CSS que tenemos como parametro
			if(o.css) { l.css(o.css); }

			//Metemos el css obligatorio que coloca la label en su sitio
			l.css({
				position:"absolute",
				left: parseInt(o.el.css('margin-left'))+parseInt(o.el.css('padding-left'))+2,
				top:'0px',
				//top: parseInt(o.el.css('margin-top'))+parseInt(o.el.css('padding-top'))+2,
				'line-height':o.el.css('line-height'),
				width:o.el.width(),
				height:o.el.height(),
				display:"inline",
				cursor:"text",
				overflow:'hidden',
				opacity: o.opacity //funciona en IE8 (Tb en IE8 modo IE7), Chrome 12, FF5, Opera 11.50 (En IE8 sale chungamente, pero sale)
			});

			if(o.el.is("TEXTAREA")) {
				if($.browser.msie) {
					l.css("width",o.el.width());
				}
				if($.browser.mozilla || $.browser.safari) {
					l.css("top","");
				}
			}

			var focus = function() {
				l.hide();
			};

			var blur = function() {
				if(!o.el.val()) {
					l.show();
				} else {
					l.hide();
				}
			};

			var click = function() {
				o.el.focus();
			};

			o.el.focus(focus).blur(blur);
			l.click(click);
			
			o.el.before(l);
			if(o.el.val()) { l.hide(); }
		}
		return o.el;
	}

	//Esta sin probar
	$.fn.watermark.remove=function() {
		if(this.parent().attr("wmwrap") == 'true') {
			this.parent().replaceWith(this);
		}
	}
	
	//Esta sin probar
	$.fn.watermark.removeAll=function() {
		var that=this;
		$("[wmwrap='true']").find("input,textarea").each(function() {
			if(that.parent().attr("wmwrap") == 'true') {
				that.parent().replaceWith(that);
			}
		});
	}

	$.fn.watermark.defaults = {
		attr: 'alt',
		inheritCss:['font-family','font-size','font-weight','color'],
		opacity:'.37'
	};
	
	//muestra completa del objeto a codificar en el atributo o a pasar en la llamada
	/*
	{
		html:'Texto de la label',
		inheritCss:['font-weight','font-family','font-size','cursor'],//propiedades css heredadas en la label
		inheritAttr:['title'],//propiedades css heredadas en la label
		css:{color:'blue'},//style aplicado a la label
		cls:'claseParaLaLabel'//class aplicado a la label
		opacity:'.25'//opacity css a la label
	}
	*/
})(jQuery);

/*
$(document).ready(function() {
	$.addwatermarks();
});
*/