//Biblioteca de pequeños plugins y funciones globales

//jQuery plugin bocadillo.
//Estrcutura estandar de pluging de jquery. Ver: http://docs.jquery.com/Plugins/Authoring
/* History
/* v 1.3 (20120702)
/* !Bug detectado, no soporta bien el cambio de tamaño en FF (ctrl+ y ctrl-)
/* Añadido parametro destroyThis, si true el bocadillo hace un remove del elemento sobre el que fue aplicado cuando se destruye
/* v 1.2 (20120503)
/* Modificados parametros color y size, ahora estan dentro de pointer.
/* Añadido parametro backgroundColor, color del fondo del bocadillo
/* Modificado para que el puntero siempre señale al centro del elemento de referencia, el bocadillo se desplaza lo necesario mediante un offset en su position
/* Problema detectado, no hace el flip correctamente,
	* si el elemento esta en top, no hace flip a bottom, sin embargo al reves (bottom to top) si.
/* v 1.1 (20120425)
/* Modificado para que si no cabe en el lugar designado, haga flip y la flecha del bocadillo se recalcule conforme al flip
/* v 1.0 (20120404)
/* Problema conocido: Comportamiento desconocido si existen varios bocadillos asociados a elementos con la misma id,
	* porque la id se usa para identificar el evento resize del bocadillo, así que los eventos resize se mezclarían
/*
*/

;(function($){
	var methods = {
		init:function( options ) {
			//this=jQuery object the plugin was invoked on
			return this.each(function () {
				//this=Objeto del DOM the plugin was invoked on (uno por vuelta si el objeto jQuery tenía mas de uno
				if ( ! $(this).data('bocadillo') ) {
					options = $.extend(true,{
						width:'auto',//anchura del bocadillo, valor css
						height:'auto',//altura del bocadillo, valor css
						backgroundColor:'none', //color de fondo del bocadillo
						position:{
							at:'top',//top|right|botom|left
							//align:'center',//Será la alineación del bocadillo en el eje de contacto con su elemento. No implementado.
							offset:0//valor nuemrico, desplazamiento del bocadillo a lo largo del eje de contacto con su elemento
						},
						pointer:{
							size:10,//tamaño de la flecha, valor numerico, será usado como width del border del span de la flecha
							color:'#FFFFFF',//color de borde de la flecha
							align:'center',//center|top|right|bottom|left, debe ser congruente con position.at, e.g. top y left, no valido top y bottom o top y top
							offset:0//valor numerico, desplazamineto de la flecha respecto de la posicion indicada con pointer.align
						},
						border:{
							style:'solid',//estilo del border del bocadillo, no afecta a la flecha
							color:'#000000',//color del borde del bocadillo y de la flecha de borde
							width:0//anchura del borde del bocadillo y desplazamiento de la flecha de borde respecto a la original
						},
						msg:'msg por omisión',//HTML, contenido del bocadillo
						css:{},//Obj, css a aplicar al bocadillo
						cssClasses:'',//lista de clases a aplicar al bocadillo
						timeout:0,//Milisegundos que dura el bocadillo antes de destruirse. 0 eterno.
						destroyThis:false//Si true el bocadillo hace un remove del elemento sobre el que fue aplicado cuando se destruye
					}, options || {});

					var $bocadillo=$('<div>'+options.msg+'</div>')
					.addClass(options.cssClasses)
					.css(options.css)
					.css({
						'z-index':9999,
						width:options.width,
						height:options.height,
						position:'absolute',
						'background-color':options.backgroundColor,
						'border':options.border.style+' '+options.border.color+' '+options.border.width+'px'
					})
					.appendTo($('body'));

					var t='auto';var r='auto';var b='auto';var l='auto';
					var t2='auto';var r2='auto';var b2='auto';var l2='auto';

					var pointerAlignOffset=0;
					switch (options.pointer.align) {
						case "center":
							if (options.position.at=='top') {
								l=($bocadillo.innerWidth()/2)-(2*options.pointer.size/2)+options.pointer.offset;
								b=-options.pointer.size; l2=l; b2=b-options.border.width;
							}
							if (options.position.at=='left') {
								t=($bocadillo.innerHeight()/2)-(2*options.pointer.size/2)+options.pointer.offset;
								r=-options.pointer.size; t2=t; r2=r-options.border.width;
							}
							if (options.position.at=='bottom') {
								l=($bocadillo.innerWidth()/2)-(2*options.pointer.size/2)+options.pointer.offset;
								t=-options.pointer.size; l2=l; t2=t-options.border.width;
							}
							if (options.position.at=='right') {
								t=($bocadillo.innerHeight()/2)-(2*options.pointer.size/2)+options.pointer.offset;
								l=-options.pointer.size; t2=t; l2=l-options.border.width;
							}
						break;
						case "top":
						case "bottom":
							pointerAlignOffset=($bocadillo.innerHeight()/2)-(2*options.pointer.size/2)-options.pointer.offset;
							if (options.pointer.align=='top') {t=0+options.pointer.offset; t2=t;}
							else {b=0+options.pointer.offset; b2=b; pointerAlignOffset=pointerAlignOffset*-1;}
							if (options.position.at=='left') {r=-options.pointer.size; r2=r-options.border.width;}
							if (options.position.at=='right') {l=-options.pointer.size; l2=l-options.border.width;}
						break;
						case "left":
						case "right":
							pointerAlignOffset=($bocadillo.innerWidth()/2)-(2*options.pointer.size/2)-options.pointer.offset;
							if (options.pointer.align=='left') {l=0+options.pointer.offset; l2=l;}
							else {r=0+options.pointer.offset; r2=r; pointerAlignOffset=pointerAlignOffset*-1;}
							if (options.position.at=='top') {b=-options.pointer.size; b2=b-options.border.width;}
							if (options.position.at=='bottom') {t=-options.pointer.size; t2=t-options.border.width;}
						break;
					}

					var bT,bR,bB,bL,bT2,bR2,bB2,bL2,position;
					bT=bR=bB=bL=bT2=bR2=bB2=bL2=options.pointer.size+'px solid transparent';

					var offsetManual=8;//No se porque jqueryui position calcula mal y desvía las cosas 8 pixeles a izquierda (?page=newUsr)
					var offsetManual=0;//He encontrado otro caso en que el offsetManual debe ser 0 pq position coloca bien (?page=cat)

					switch (options.position.at) {
						case "top":
							position={
								my: 'center bottom',
								at: 'center top',
								offset:(options.position.offset+offsetManual+pointerAlignOffset)+' -'+options.pointer.size
							};
							bT=options.pointer.size+'px solid '+options.pointer.color;
							bB='0';
							bT2=options.pointer.size+'px solid '+options.border.color;
							bB2='0';
						break;
						case "bottom":
							position={
								my: 'center top',
								at: 'center bottom',
								offset:(options.position.offset+offsetManual+pointerAlignOffset)+' '+options.pointer.size
							};
							bT='0';
							bB=options.pointer.size+'px solid '+options.pointer.color;
							bT2='0';
							bB2=options.pointer.size+'px solid '+options.border.color;
						break;
						case "left":
							position={
								my: 'right center',
								at: 'left center',
								offset:'-'+(Math.abs(options.pointer.size-offsetManual))+' '+(pointerAlignOffset+options.position.offset)
							}
							bL=options.pointer.size+'px solid '+options.pointer.color;
							bR='0';
							bL2=options.pointer.size+'px solid '+options.border.color;
							bR2='0';
						break;
						case "right":
							position={
								my: 'left center',
								at: 'right center',
								offset:options.pointer.size+offsetManual+' '+(pointerAlignOffset+options.position.offset)
							};
							bL='0';
							bR=options.pointer.size+'px solid '+options.pointer.color;
							bL2='0';
							bR2=options.pointer.size+'px solid '+options.border.color;
						break;
					}

					var targetPosition=this;
					var aux;
					$bocadillo.position(
						$.extend(position, {
							of: targetPosition,
							collision:'none',
							using: function (pos) {
								$(this).position(
									$.extend(position, {
										of: targetPosition,
										collision:'flip',
										using: function (pos2) {
											//console.log (pos);
											//console.log(pos2);
											if (pos.top!=pos2.top) {//Flip vertical
												aux=t;t=b;b=aux;
												aux=bT;bT=bB;bB=aux;
												aux=t2;t2=b2;b2=aux;
												aux=bT2;bT2=bB2;bB2=aux;

											}
											if (pos.left!=pos2.left) {//Flip horizontal
												aux=l;l=r;r=aux;
												aux=bL;bL=bR;bR=aux;
												aux=l2;l2=r2;r2=aux;
												aux=bL2;bL2=bR2;bR2=aux;
											}
											$(this).css({
												top:pos2.top+'px',
												left:pos2.left+'px'
											});
										}
									})
								);
							}
						})
					);
					$(window).bind('resize.bocadillo'+$(this).attr('id'), (function(context) {return function() {methods.reposition.call(context);}})(this));
					//Intervalo para comprobar si el elemento de referencia es visible, sino, ocultamos el bocadillo
					var chkVisibleIntervalID=setInterval((function(context) {return function() {($(context).is(':visible'))?methods.show.call(context):methods.hide.call(context);}})(this),100);
					if (options.timeout>0) {setTimeout((function(context) {return function() {methods.timeout.call(context);}})(this),options.timeout);}

					var $flecha=$('<span />')
					.css({
						//IE6/7 height fix (La flecha no se coloca en su sitio)
						'font-size': 0,
						'line-height': 0,
						/* ie6 transparent fix */
						'_border-right-color': 'pink',
						'_border-left-color': 'pink',
						'_filter': 'chroma(color=pink)',
						//
						position:'absolute',
						top:t2,
						right:r2,
						bottom:b2,
						left:l2,
						'border-top': bT2,
						'border-right': bR2,
						'border-bottom': bB2,
						'border-left': bL2
					})
					.appendTo($bocadillo)
					.clone()
					.css({
						position:'absolute',
						top:t,
						right:r,
						bottom:b,
						left:l,
						'border-top': bT,
						'border-right': bR,
						'border-bottom': bB,
						'border-left': bL
					})
					.appendTo($bocadillo);

					//Guardamos en el objeto del DOM referencias al bocadillo
					$(this).data('bocadillo', {
						target : $(this),
						bocadillo: $bocadillo,
						position: position,
						chkVisibleIntervalID: chkVisibleIntervalID,
						destroyThis:options.destroyThis
					});
				}
			});
		},
		show:function() {
			context=$(this);
			context.data('bocadillo').bocadillo.show();
		},
		hide:function() {
			context=$(this);
			context.data('bocadillo').bocadillo.hide();
		},
		reposition:function() {
			context=$(this);
			context.data('bocadillo').bocadillo.position(context.data('bocadillo').position);
			//$bocadillo.position(position);
		},
		timeout:function () {
			context=$(this);
			//console.log(context.data('bocadillo').target);
			$(context.data('bocadillo').target).bocadillo('destroy');
		},
		destroy:function() {
			return this.each(function(){
				// Namespacing FTW
				var $this=$(this)
				if ($this.data('bocadillo')) {
					var destroyThis=$this.data('bocadillo').destroyThis;
					$(window).unbind('.bocadillo'+$this.attr('id'));
					clearInterval($this.data('bocadillo').chkVisibleIntervalID);
					$this.data('bocadillo').bocadillo.remove();
					$this.removeData('bocadillo');
					if (destroyThis) {
						$this.remove();
					}
				}
			});
		}
	};

	$.fn.bocadillo = function(method) {
		// Method calling logic
		if ( methods[method] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.bocadillo' );
		}
	};
})(jQuery);
//

//funcion muestraMsgModalJqueryUI
//
function muestraMsgModalJqueryUI(title, msg) {
	//Comprobamos si existe un selector que case con el title pasado,
	//Si existe, usamos los datos de ese objeto del DOM para el mensaje
	//if ($('#'+title).exists()) {//Este falla con algunos titles, Error: Syntax error, unrecognized expression: <title>
	/*if (title!="") {
		var selector=title.replace(/([;&,\.\+\*\~':"\!\^#$%@\[\]\(\)=>\|])/g, '\\$1');
		if ($("*[id='"+selector+"']").exists()) {
			msg=$("*[id='"+selector+"']").html();
			title=$("*[id='"+selector+"']").attr('title');
		}
	}*/
	/**/
	var $div=$('<div>');
	$div
		.uniqueId('msgModal')
		.css({
			'text-align':'justify',
			'display':'none'
		})
		.html(msg);
	$('body')
	.append($div);
	$($div)
	.dialog({
		draggable:true,
		title:title,
		closeOnEscape: true,
		modal: true,
		close: function(event, ui) {
			$($div).remove();
		},
		buttons: [{
			text: 'Aceptar',
			click: function() {
				$(this).dialog( "close" );
			}
		}]
	});
	//$('#msgModal').dialog( "option", "zIndex", 9999999 );
	//$('#msgModal').dialog( "moveToTop" );
}