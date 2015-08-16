/******************************************************************************/
/* jqueryUI.sucesos v 0.2

/* widget para mostrar avisos leidos mediante ajax de una URL pasada como
	parametro. El formato del objeto devuelto por la URL de consulta
	esta prefijado. Compatible con temas de jqueryUI

/* Creado from scratch


/* History
/* v 0.2 (20121122)
/* -Añadido el parametro APP a los datos del POST (params)
/* -Cambiado timestamp por id, cada aviso debe tener una id unica, que ademas sera
/*		el dato utilizado para enviar al servidor que avisos han sido vistos.*/

/* v 0.1 (20110719)
/* Creación del widget
/******************************************************************************/

/* Eventos
	beforeClick
	afterClick
	beforeOpen
	afterOpen
	beforeLoad
	afterLoad
*/
/*
/* Ejemplo de llamada
	<script type="text/javascript">
		// <![CDATA[
		$(document).ready(function() {
			$(<SELECTOR>).notificator({OPTIONS})
		});
		// ]]>
	</script>
*/

/* Ejemplo de css
	No usa CSS externo.
*/

/*
Ejemplo de marcado
	aplicable a cualquier elemento al que se pueda aplicar el widget button
	<button>Texto</button>
*/

/*
JSON esperado de vuelta en la URL llamada, dentro de la propiedad data del objeto de respuesta
{
	id=uniqID// Identificador único utilizado para comprobar si un suceso ya está en la lista y para referirse a él en conexiones al server
	titulo="Suceso acaecido fortuitamente",//html
	texto="Un suceso ha sucedido, ¿quien lo desucederá?, el desucedeador que lo desuceda buen desucedeador será, he dicho!.",//html
	imagen="./imgs/agujero.world.png",//src
	//Obsoleto v 0.2 -> timestamp="20110719020433"// Identificador único utilizado para comprobar si un suceso ya está en la lista
}
*/

(function ($) {
    'use strict';

	//El codigo, hace uso de la widget factory de jqueryUI, ver http://docs.jquery.com/UI_Developer_Guide#jQuery_UI_API_Developer_Guide
	$.widget("ui.notificator",{
		version: "@VERSION",
		options: { // initial values are stored in the widget's prototype
			button: {
				text: false,
				icons: {
					primary: "ui-icon-flag",
					secondary: "ui-icon-triangle-1-s"
				}
			},
			container: {
				width: '400px',
				maxHeight: '300px',
				position: {
					my: 'right top',
					at: 'right bottom',
					collision: 'flip'
				}
			},
			interval:60000,
			url:'./admin.php',
			params: {
				MODULE:'ACTIONS',
				acClase: 'Home',
				acMetodo: "listaSucesos",
				acTipo: "ajaxAssoc"
			},
			imgs: {
				width:'48px',
				height:'48px'
			}
		},
		_init: function(){
		},
		_destroy: function() {
			$.Widget.prototype.destroy.apply(this, arguments); // default destroy
			// now do other stuff particular to this widget
			//Supongo que aqui debería destruir el container y el scroller
		},
		_create:function(){
			var object = this;
			var element = this.element;

			object.options.container.position.of=element;

			//options=$.extend(true,{},object.options,options);

			var container=$('<div class="ui-helper-hidden ui-widget ui-widget-content ui-corner-all" style="position:absolute; z-index:9999;" />');
			container.data('arrIdsSucesos',{});//objeto vacio
			var scroller=$('<div />',{
				css: {
					overflow:'auto',
					width: object.options.container.width,
					'max-height': object.options.container.maxHeight,
					padding:'5px',
					'padding-bottom':'0px'
				}
			});

			scroller.appendTo(container);
			container.insertAfter(element);

			$(element).button(object.options.button)
			.click (function (e) {
				//beforeClick
				object._trigger('beforeClick');
				e.stopPropagation();
				//if (container.css('display')=='none') {
				if (!container.is(':visible')) {
					//beforeOpen
					object._trigger('beforeOpen');
					container
						.show()
						.position ({
							my: object.options.container.position.my,
							at: object.options.container.position.at,
							of: object.options.container.position.of,
							collision: object.options.container.position.collision
						})
						.hide()
						.fadeIn();
					//afterOpen
					object._trigger('afterOpen');
				} else {
					//beforeClose
					object._trigger('beforeClose');
					container.fadeOut();
					//afterClose
					object._trigger('afterClose');
				}
				//afterClick
				object._trigger('afterClick');
				clearInterval(object.blinkIntervalID);
				$(element).removeClass('ui-state-highlight');
			});

			//Cuando se pincha en cualquier lugar, el container se cierra si está abierto
			$(document).bind('mousedown.notificator', function(e) {
				//Si el mousedown no ha sido en:
					//algo contenido en container
					//algo contenido en element
					//el propio element
				if (
					(container.exists()) &&
					(element.exists())
				) {
					if (
						(container.is(":visible")) &&
						(!$.contains(container[0], e.target)) &&
						(!$.contains(element[0], e.target)) &&
						(element[0]!==e.target)
					) {
						//beforeClose
						object._trigger('beforeClose');
						container.fadeOut();
						//afterClose
						object._trigger('afterClose');
					}
				}
			});

			this.loadLista();
			//Para que this apunte a este objeto cuando se llame desde el ambito de setInterval
			this.loadColaIntervalID=setInterval((function(context) {return function() {context.loadLista();}})(this),object.options.interval);
		},
		loadLista:function (){
			//console.log(this);//this apunta al widget pq la llamada en setinterval es context.loadLista
			var object = this;
			var element = object.element;
			var container=element.next('div');
			var scroller=container.children('div');
			//beforeLoad
			object._trigger('beforeLoad');
			// Load sucesos:
			$.post(object.options.url,object.options.params,function (data) {
				//console.log(data);
				var arrSucesos=data.data;
				if (arrSucesos.length>0) {
					if (scroller.children('div.noSucesos').exists()) {
						scroller.children('div.noSucesos').remove();
					}
					var arrIds=container.data('arrIdsSucesos');
					var i;
					var blinkIntervalSet=false;
					var algunoNoVisto=false;
					for (i in arrSucesos) {
						if (arrSucesos[i].visto==0) {
							algunoNoVisto=true;
						}
						var yaIncluido=false;
						var idAviso=arrSucesos[i].id;
						if (!$.isArray(arrIds)) {arrIds=new Array();}
						var z;
						for (z in arrIds) {
							if (arrIds[z]==idAviso) {
								yaIncluido=true;
								break;
							}
						}
						if (!yaIncluido) {
							if (
								!blinkIntervalSet
								&& algunoNoVisto
								) {
								blinkIntervalSet=true;
								object.blinkIntervalID=setInterval((function(context) {return function() {$(element).toggleClass('ui-state-highlight');}})(element),600);
							}

							arrIds["id["+idAviso+"]"]=idAviso;
							container.data('arrIdsSucesos',arrIds);
							var classNoVisto="";
							if (arrSucesos[i].visto==0) {
								classNoVisto="ui-state-highlight";
							}
							//console.log(arrSucesos[i].visto);
							//console.log(classNoVisto);
							var imgTag='<img alt="" src="'+arrSucesos[i].imagen+'" style="width:'+object.options.imgs.width+'; height:'+object.options.imgs.height+';" />';

							var divSuceso = jQuery('<div />')
								.addClass("suceso ui-widget-content ui-corner-top")
								.addClass(classNoVisto)
								.css({
									'display':'none',
									'padding':'3px',
									'margin-bottom':'5px',
									'cursor':'pointer'
								})
								.attr({
									"id":"aviso"+idAviso,
									"data-id-aviso":idAviso
								})
								.click(function(e) {
									window.location=arrSucesos[i].url;
								})
								.hover(function (e) {
									$(this).addClass('ui-state-active');
								}, function (e) {
									$(this).removeClass('ui-state-active');
								});
							var divTitulo = jQuery('<div />',{
									html:arrSucesos[i].titulo
								})
								.addClass("titulo ui-widget-header ui-corner-top");
							var divContenido = jQuery('<div />')
								.addClass("contenido ui-helper-clearfix")
								.css ({
									'min-height':'48px'
								});
							var divImagen = jQuery('<div />',{
									html:imgTag
								})
								.addClass("imagen")
								.css ({
									'float':'left'
								});
							var divTexto = jQuery('<div />',{
									html:arrSucesos[i].texto
								})
								.addClass("texto")
								.css ({
									'text-align':'justify'
								});
							var divHace = jQuery('<div />',{
									html:arrSucesos[i].hace
								})
								.addClass("hace")
								.css ({
									'text-align':'right'
								});

							divTitulo.appendTo(divSuceso);
							divContenido.appendTo(divSuceso);
							divImagen.appendTo(divContenido);
							divTexto.appendTo(divContenido);
							divHace.appendTo(divSuceso);
							divSuceso.prependTo(scroller).slideDown('slow');
						}
					}
				} else {
					if (!scroller.children('div.noSucesos').exists()) {
						var divSuceso = jQuery('<div />',{
								html:"No se encontrarón sucesos para mostrar"
							})
							.addClass("noSucesos")
							.css({
								'display':'none',
								'padding':'3px',
								'margin-bottom':'5px'
							});

						divSuceso.appendTo(scroller).slideDown('slow');
					}
				}
				//afterLoad
				object._trigger('afterLoad');
			},"json");
		},
		marcarTodosComoVistos:function() {
			var object = this;
			var element = object.element;
			var container=element.next('div');
			var scroller=container.children('div');

			var arrIdsSucesos=container.data('arrIdsSucesos');
			var arrPost=$.extend(true,{},object.options.params,arrIdsSucesos);
			console.log(arrPost);
			$.post(object.options.url,
				arrPost,
				function (data) {
					console.log(data);
				},
			"json");
		}
	});
}(jQuery));