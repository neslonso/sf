/******************************************************************************/
/* jqueryUI.mostrador v 0.1



/* Sistema de mostrador para tienda.
Consta de 2 widgets jquery UI, uno


widget para mostrar la UI de chat. Compatible con temas de jqueryUI

/* Creado from scratch


/* History
/* v 0.1 (20120302)
/* Creación del widget
/******************************************************************************/

/* Eventos
*/
/*
/* Ejemplo de llamada
	<script type="text/javascript">
		// <![CDATA[
		$(document).ready(function() {
			$(<SELECTOR>).mostrador({OPTIONS})
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

/*http://ajpiano.com/widgetfactory/*/
(function ($) {
    'use strict';

	//El codigo, hace uso de la widget factory de jqueryUI, ver http://docs.jquery.com/UI_Developer_Guide#jQuery_UI_API_Developer_Guide
	$.widget("mostrador.mBase",{
		version: "@VERSION",
		options: { // initial values are stored in the widget's prototype
			sessionChatboxes:[]
		},

		_create:function() {//Creación del widget, solo se ejecuta una vez cada elemento al que se aplique el widget, si el objeto JS del widget asociado al elemento no existe
			var self = this;
			var element = this.element;

			//Abrimos las chatboxes que correspondan segun las options:
			//console.log (this.options);
			for (var i = 0; i < this.options.sessionChatboxes.length; i++) {
				var sessionData=this.options.sessionChatboxes[i];
				self._chatBox(sessionData.id,sessionData.name);
			};

			//TODO: mejora: parametrizar el src del sonido de notificacion
			var audioTag=$([
				'<audio id="chatMsgNotify">',
					'<source src="./imgs/lib/chatMsgNotify.ogg">',
					'<source src="./imgs/lib/chatMsgNotify.mp3">',
				'</audio>',
			].join(''));
			audioTag.prependTo(element);


			//console.log('mostrador _create');
		},

		_init: function() {//Si el widget ya esta creado las llamadas al widget sin parametros ejecutan _init en lugar de _create, puede servir para resetear el widget
		},

		// Use the _setOption method to respond to changes to options
		_setOption: function( key, value ) {
			/*
			switch( key ) {
				case "optionName":
				break;
			}

			// In jQuery UI 1.8, you have to manually invoke the _setOption method from the base widget
			$.Widget.prototype._setOption.apply( this, arguments );
			//$.Widget.prototype._setOption.call( this, key,value );//Equivalente
			// In jQuery UI 1.9 and above, you use the _super method instead
			//this._super( "_setOption", key, value );
			*/
		},

		// Use the destroy method to clean up any modifications your widget has made to the DOM
		destroy: function() {
			// In jQuery UI 1.8, you must invoke the destroy method from the base widget
			$.Widget.prototype.destroy.call( this );
			//$.Widget.prototype.destroy.apply(this, arguments); // default destroy, ¿equivalente a la anterior?
			// In jQuery UI 1.9 and above, you would define _destroy instead of destroy and not call the base method
		},

		_chatBox: function (idSession, sessName) {
			var self = this;
			var element = this.element;

			// chatbox
			var chatBox=$('<div class="chatBox" id="chatBox'+idSession+'"></div>');

			//Contenido
			var content=$('<div></div>')
			.addClass('ui-widget-content '
				+ 'ui-corner-top')
			.css ({
				//display:'table',
				width:'100%',
				height:'100%'
			})
			.appendTo(chatBox);

			//Titulo
			var titulo=$('<div>'+idSession+': '+sessName+'</div>')
			.css ({
				//display:'table-row',
				height:'20px',
				cursor:'pointer'
			})
			.addClass('ui-widget-header ' +
				'ui-corner-top ' +
				'ui-dialog-header' // take advantage of dialog header style
			)
			.appendTo(content);

			var icono=$('<img class="ui-icon ui-icon-person" src="./admin.php?MODULE=IMAGES&almacen=IMGS_DIR&fichero=lib/spacer.gif">')
			.prependTo(titulo);
			var chatBoxCmds=$('<span></span>')
			.css ({float:'right'})
			.prependTo(titulo);
			var cerrar=$('<img class="ui-icon ui-icon-close" alt="cerrar" src="./admin.php?MODULE=IMAGES&almacen=IMGS_DIR&fichero=lib/spacer.gif">')
			.click(function(event) {
				clearInterval(chatBox.getNewMsgsIntervalID);
				//Guardamos en session que se cerro la chatbox
				//TODO: Imprescindible: Deberiamos meter un closure para idSession
				$.post('admin.php', {
						MODULE:'ACTIONS',
						acClase:'Home',
						acMetodo:'chatBoxClosed',
						acTipo:'ajax',
						acReturnURI:'',
						idSession:idSession
					},
					function(data) {
						var action=$.parseJSON(data);
						if (!action.exito) {
							muestraMsgModal('Error cerrando chatBox',action.msg);
						}
					}
				);
				chatBox.remove();
				return false;
			})
			.appendTo(chatBoxCmds);
			//

			//Msgs
			var divMsgs=$('<div id="divMsgs'+idSession+'"></div>')
			.css({
				//display:'table-row',
				color:'#000',
				'background-color':'#FFF',
				height:'100px',
				overflow:'auto'
			})
			.appendTo(content);
			//

			//input
			var divInput=$('<div></div>')
			.css ({
				//display:'table-row',
			})
			.appendTo(content);

			var input=$('<textarea></textarea>')
			.css ({
				border:'none',
				padding:'0px',
				width:'100%',
				height:'60px',
				overflow:'hidden',
				resize:'none'
			})
			.focusin(function() {
				input.addClass('ui-state-focus');
				divMsgs.scrollTop(divMsgs.get(0).scrollHeight);
			})
			.focusout(function() {
				input.removeClass('ui-state-focus');
			})
			.keydown(function(event) {
				if(event.keyCode && event.keyCode == $.ui.keyCode.ENTER) {
					var msg = $.trim($(this).val());
					if(msg.length > 0) {
						var momento=new Date();
						self._addMsgToBox(idSession,msg,momento,true,true);

						//Enviar el mensaje a server.
						//self._sendMsg();
						$.post('admin.php', {
								MODULE:'ACTIONS',
								acClase:'Home',
								acMetodo:'addChatMsg',
								acTipo:'ajax',
								acReturnURI:'',
								msg:msg,
								to:idSession,
								momentoEnvio:momento.toMysql()
								},
							function(data) {
								var action=$.parseJSON(data);
								if (action.exito) {
									//muestraMsgModal('Exito',action.data);
								} else {
									muestraMsgModal('Fallo',action.msg);
								}
							}
						);
					}
					$(this).val('');
					return false;
				}
			})
			.appendTo(divInput);
			//

			//Pie
			var divPie=$('<div id="divPie'+idSession+'"></div>')
			.css ({
				//display:'table-row',
				width:'100%',
				height:'20px',
				overflow:'hidden'
			})
			.appendTo(content);


			chatBox
			.addClass('ui-widget '
				+ 'ui-corner-top')
			.css ({
				position:'fixed',
				'z-index':99999,
				width:'200px',
				height:'200px'
			})
			.focusin(function(){
				titulo.addClass('ui-state-focus');
			})
			.focusout(function(){
				titulo.removeClass('ui-state-focus');
			})
			.appendTo(document.body)
			.position ({
				my: 'left bottom',
				at: 'left bottom',
				of: $(window),
				collision: 'fit fit'
			})
			.draggable({
				containment:'document',
				handle:titulo,
				stack: ".chatBox"
			})
			.resizable({
				handles: 'se',
				minWidth:'200',
				minHeight:'200',
				resize: function(event, ui) {
					divMsgs.height(content.height()-(20+60+20));
				}
			});

			//chatBox.disableSelection();

			chatBox.children().click(function(){
				input.focus();
			});

			//Guardamos en session que se abrio la chatbox
			$.post('admin.php', {
					MODULE:'ACTIONS',
					acClase:'Home',
					acMetodo:'chatBoxOpened',
					acTipo:'ajax',
					acReturnURI:'',
					idSession:idSession,
					nameSession:sessName
				},
				function(data) {
					var action=$.parseJSON(data);
					if (!action.exito) {
						muestraMsgModal('Error cerrando chatBox',action.msg);
					}
				}
			);

			self._getAllMsgs(idSession);
			self._loadCola();//Para qeu se actualice la cola al abrir una ventana de chat
			//Para que this apunte a este objeto cuando se llame desde el ambito de setInterval
			chatBox.getNewMsgsIntervalID=setInterval((function(context,idSession) {return function() {context._getNewMsgs(idSession);}})(self,idSession),self.options.getNewMsgsInterval);
		},

		_getAllMsgs: function (idSession) {
			var self = this;
			var element = this.element;

			//Pedir Msgs al server
			$.post('admin.php', {
					MODULE:'ACTIONS',
					acClase:'Home',
					acMetodo:'getChatMsgs',
					acTipo:'ajax',
					acReturnURI:'',
					idSession:idSession
					},
				function(data) {
					var action=$.parseJSON(data);
					if (action.exito) {
						if (action.data.length) {
							for (var i=0;i<action.data.length;i++) {
								var msgData=action.data[i];
								var momento=Date.fromMysql(msgData.enviado);
								var isFrom=(msgData.idSessionFrom!=idSession)?true:false;
								self._addMsgToBox(idSession,msgData.msg,momento,isFrom,false);
							}
						}
					} else {
						//muestraMsgModal('Fallo',action.msg);
					}
				}
			);
		},

		_getNewMsgs: function (idSession) {
			var self = this;
			var element = this.element;

			//Pedir Msgs al server
			$.post('admin.php', {
					MODULE:'ACTIONS',
					acClase:'Home',
					acMetodo:'getNewChatMsgs',
					acTipo:'ajax',
					acReturnURI:'',
					idSession:idSession
					},
				function(data) {
					var action=$.parseJSON(data);
					if (action.exito) {
						if (action.data.length) {
							for (var i=0;i<action.data.length;i++) {
								var msgData=action.data[i];
								var momento=Date.fromMysql(msgData.enviado);
								var isFrom=(msgData.idSessionFrom!=idSession)?true:false;
								self._addMsgToBox(idSession,msgData.msg,momento,isFrom,true);
								//Reproducir un sonido
							}
						}
					} else {
						//muestraMsgModal('Fallo',action.msg);
					}
				}
			);
		},

		_addMsgToBox: function (idSession,msg,momento,isFrom,audio) {
			var self = this;
			var element = this.element;
			var img=$('<img class="ui-icon ui-icon-clock" alt="'+momento.toStringES()+'" title="'+momento.toStringES()+
				'" src="./admin.php?MODULE=IMAGES&almacen=IMGS_DIR&fichero=lib/spacer.gif" />');
			var loader=$('<img class="ui-icon ui-icon-none" style="float:right; padding-right:3px;" src="./binaries/imgs/lib/ajax-loader-mini.gif" />');
			var divMsg=$('<div style="clear:both; white-space:nowrap;"></div>');
			var pMsg=$('<p>'+msg+'</p>')
			.css({
				'white-space':'normal',
				//'-webkit-box-shadow':'0px 0px 7px rgba(0, 0, 0, 0.7)',
				//'-moz-box-shadow':'0px 0px 7px rgba(0, 0, 0, 0.7)',
				'max-width':'85%',
				'margin-top':'7px',
				'margin-bottom':'7px',
				padding:'7px'
			});
			if (isFrom) {
				img.prependTo(pMsg);
				if (false) {//El loader solo lo metemos si el mensaje proviene del usuario
					loader.appendTo(pMsg);
				}
				pMsg
				.addClass('ui-corner-all')
				.css ({float:'left', 'background-color':'#3399ff', 'border-left':'none'});
				var pCallout=$('<p></p>')
				.css({
					float:'left',
					'margin-top':'14px',
					'border-top':'5px solid transparent',
					'border-right':'7px solid #3399ff',
					'border-left':'0px',
					'border-bottom':'5px solid transparent'
				});
				pCallout.appendTo(divMsg);
			} else {
				img.appendTo(pMsg);
				pMsg
				.addClass('ui-corner-all')
				.css ({float:'right', 'background-color':'#ff9933', 'border-right':'none'});
				var pCallout=$('<p></p>')
				.css({
					float:'right',
					'margin-top':'14px',
					'border-top':'5px solid transparent',
					'border-right':'0px',
					'border-left':'7px solid #ff9933',
					'border-bottom':'5px solid transparent'
				});
				pCallout.appendTo(divMsg);
				if (audio) {
					$('#chatMsgNotify')[0].play();
				}
			}
			pMsg.appendTo(divMsg);
			var divMsgs=$('#divMsgs'+idSession);
			divMsg.appendTo (divMsgs);
			divMsgs.scrollTop(divMsgs.get(0).scrollHeight);
			var divPie=$('#divPie'+idSession);
			divPie.html('Ultimo mensaje: '+momento.toStringES());
		}
	});
}(jQuery));

/************************************************************************************************************************************/

(function ($) {
    'use strict';

	//El codigo, hace uso de la widget factory de jqueryUI, ver http://docs.jquery.com/UI_Developer_Guide#jQuery_UI_API_Developer_Guide
	$.widget("mostrador.mDependiente",$.mostrador.mBase,{
		version: "@VERSION",
		options: { // initial values are stored in the widget's prototype
			loadColaInterval:30000,
			getNewMsgsInterval:5000,
			position: {
				my: 'right top',
				at: 'right top',
				of: $(window)
			},
			createUI: true,
			width: '125px'
		},

		_create:function() {//Creación del widget, solo se ejecuta una vez cada elemento al que se aplique el widget, si el objeto JS del widget asociado al elemento no existe
			var self = this;
			var element = this.element;

			$.mostrador.mBase.prototype._create.call( this );

			//console.log('mostrador.dependiente _create');
			if (self.options.createUI) {
				self.createUI();
			}
			self._loadCola();
			//Para que this apunte a este objeto cuando se llame desde el ambito de setInterval
			self.loadColaIntervalID=setInterval((function(context) {return function() {context._loadCola();}})(self),self.options.loadColaInterval);
		},

		_init: function() {//Si el widget ya esta creado las llamadas al widget sin parametros ejecutan _init en lugar de _create, puede servir para resetear el widget
		},

		// Use the _setOption method to respond to changes to options
		_setOption: function( key, value ) {
		},

		// Use the destroy method to clean up any modifications your widget has made to the DOM
		destroy: function() {
			// In jQuery UI 1.8, you must invoke the destroy method from the base widget
			$.Widget.prototype.destroy.call( this );
			//$.Widget.prototype.destroy.apply(this, arguments); // default destroy, ¿equivalente a la anterior?
			// In jQuery UI 1.9 and above, you would define _destroy instead of destroy and not call the base method
		},

		createUI: function() {
			var self = this;
			var element = this.element;

			var content=$('<div></div>');
			content
			.addClass('ui-widget-content '
				+ 'ui-corner-tl')
			.css ({
				height:'100%'
			})
			.appendTo(element);

			var titulo=$('<div><img class="ui-icon ui-icon-link" src="./admin.php?MODULE=IMAGES&almacen=IMGS_DIR&fichero=lib/spacer.gif">Cola de clientes</div>');
			titulo
			.addClass('ui-widget-header ' +
				'ui-corner-tl ' +
				'ui-dialog-header' // take advantage of dialog header style
			)
			.appendTo(content);

			var lista=$('<ul></ul>');
			lista.appendTo(content);

			/*
			self._resize();
			$(window).resize(function() {
				self._resize();
			});
			*/
		},

		_resize: function () {
			var self = this;
			var element = this.element;

			//Cogemos el elemento al que hemos aplicado el widget y lo posicionamos absolutamente acoplado a la izda y del alto de la ventana
			element
			.css ({
				position:'fixed',
				width: this.options.width,
				height:'100%'
			})
			.position(this.options.position)
			.height($(window).height());
		},

		//Recupera la lista de usuarios en cola de espera
		_loadCola: function () {
			var self = this;
			var element = this.element;

			//Enviar el mensaje a server.
			$.post('admin.php', {
					MODULE:'ACTIONS',
					acClase:'Home',
					acMetodo:'getChatCola',
					acTipo:'ajaxAssoc',
					acReturnURI:''
					},
				function(data) {
					var action=$.parseJSON(data);
					if (action.exito) {
						$('ul',element).empty();
						if (action.data.length) {
							for (var i=0;i<action.data.length;i++) {
								var sessionData=action.data[i];
								var itemCola=$('<li><img class="ui-icon ui-icon-person" alt="Person" src="./admin.php?MODULE=IMAGES&almacen=IMGS_DIR&fichero=lib/spacer.gif">'+sessionData.id+': '+sessionData.name+' ('+sessionData.newMsgs+') .- '+sessionData.lastSeen+'</li>')
								.data('idSession',sessionData.id)
								.data('sessName',sessionData.name)
								.click(function(event) {
									self._chatBox($(this).data('idSession'),$(this).data('sessName'));
									return false;
								})
								.appendTo($('ul',element));
								if (sessionData.newMsgs>0) {
									self._chatBox(sessionData.id,sessionData.name);
								}

							}
						} else {
							var itemCola=$('<li>Ningún cliente en la cola</li>')
							.appendTo($('ul',element));
						}
					} else {
						muestraMsgModal('Fallo',action.msg);
					}
				}
			);
		}
	});
}(jQuery));

/************************************************************************/
(function ($) {
    'use strict';

	//El codigo, hace uso de la widget factory de jqueryUI, ver http://docs.jquery.com/UI_Developer_Guide#jQuery_UI_API_Developer_Guide
	$.widget("mostrador.mCliente",$.mostrador.mBase,{
		version: "@VERSION",
		options: { // initial values are stored in the widget's prototype
			loadColaInterval:30000,
			getNewMsgsInterval:5000,
			position: {
				my: 'right bottom',
				at: 'right-2% bottom',
				of: $(window)
			},
			width: '125px'
		},

		_create:function() {//Creación del widget, solo se ejecuta una vez cada elemento al que se aplique el widget, si el objeto JS del widget asociado al elemento no existe
			var self = this;
			var element = this.element;

			$.mostrador.mBase.prototype._create.call( this );

			var divBocadillo=$([
				'<div style="font-size:larger; position:relative;">',
					'<div style="text-align:right; position:absolute; top:-3px; right:0px;">',
						'<img id="mCLienteCloseBocadillo" class="ui-icon ui-icon-close" style="cursor:pointer;" src="./admin.php?MODULE=IMAGES&almacen=IMGS_DIR&fichero=lib/spacer.gif">',
					'</div>',
					'<div>¿Dudas?...</div>',
					'<div style="text-align:right; clear:both;">¡consultanos!</div>',
				'</div>'
				].join(''))
			.addClass('ui-corner-all')
			.css ({
				'background-color':'#FFE45C',
				border:'1px solid #000',
				position:'relative',
				top:'-10px',
				padding:'7px'
			})
			.appendTo(element);

			$('#mCLienteCloseBocadillo')
			.hover(function(e) {
				divBocadillo.addClass('ui-state-disabled');
			},
			function(e) {
				divBocadillo.removeClass('ui-state-disabled');
			})
			.click(function(e){
				divBocadillo.remove();
				self._resize();
			})

			var pCallout=$('<span />')
			.css({
				position:'absolute',
				bottom:'-10px',
				left:'45%',
				'border-top':'10px solid #000',
				'border-right':'10px solid transparent',
				'border-left':'10px solid transparent',
				'border-bottom':'0px'
			});
			pCallout.appendTo(divBocadillo);
			var pCallout2=$('<span />')
			.css({
				position:'absolute',
				bottom:'-9px',
				left:'45%',
				'border-top':'10px solid #FFE45C',
				'border-right':'10px solid transparent',
				'border-left':'10px solid transparent',
				'border-bottom':'0px'
			});
			pCallout2.appendTo(divBocadillo);


			var content=$('<div></div>')
			.addClass('ui-widget-content '
				+ 'ui-corner-top '
				+ 'chatConectionsInfo')
			.css ({

			})
			.appendTo(element);

			/*
			var titulo=$('<div><img class="ui-icon ui-icon-link" src="./admin.php?MODULE=IMAGES&almacen=IMGS_DIR&fichero=lib/spacer.gif">Cola de clientes</div>')
			.addClass('ui-widget-header ' +
				'ui-corner-tl ' +
				'ui-dialog-header'
			)
			.appendTo(content);

			var lista=$('<ul></ul>');
			lista.appendTo(content);
			*/

			self._resize();
			$(window).resize(function() {
				self._resize();
			});

			//No cargamos ninguna cola, sencillamente al pinchar mostramos una ventana de chat con la sesion de administrador mas reciente
			/*
			self._loadCola();
			//Para que this apunte a este objeto cuando se llame desde el ambito de setInterval
			self.loadColaIntervalID=setInterval((function(context) {return function() {context._loadCola();}})(self),self.options.loadColaInterval);
			*/

			//Consultamos el estado de conexión
			self._checkConnections();
			//Para que this apunte a este objeto cuando se llame desde el ambito de setInterval
			self.checkConnectionsIntervalID=setInterval((function(context) {return function() {context._checkConnections();}})(self),self.options.loadColaInterval);
		},

		_init: function() {//Si el widget ya esta creado las llamadas al widget sin parametros ejecutan _init en lugar de _create, puede servir para resetear el widget
		},

		// Use the _setOption method to respond to changes to options
		_setOption: function( key, value ) {
		},

		// Use the destroy method to clean up any modifications your widget has made to the DOM
		destroy: function() {
			// In jQuery UI 1.8, you must invoke the destroy method from the base widget
			$.Widget.prototype.destroy.call( this );
			//$.Widget.prototype.destroy.apply(this, arguments); // default destroy, ¿equivalente a la anterior?
			// In jQuery UI 1.9 and above, you would define _destroy instead of destroy and not call the base method
		},

		_resize: function () {
			var self = this;
			var element = this.element;

			//Cogemos el elemento al que hemos aplicado el widget y lo posicionamos
			element
			.css ({
				position:'fixed',
				width: this.options.width
			})
			.position(this.options.position);
		},

		//Recupera la lista de usuarios en cola de espera
		_checkConnections: function () {
			var self = this;
			var element = this.element;

			//Enviar el mensaje a server.
			$.post('index.php', {
					MODULE:'ACTIONS',
					acClase:'Home',
					acMetodo:'getChatConections',
					acTipo:'ajaxAssoc',
					acReturnURI:''
					},
				function(data) {
					var action=$.parseJSON(data);
					if (action.exito) {
						var $destino=$('.chatConectionsInfo',element);
						$destino.empty();
						//console.log(action.data);
						if (action.data) {
							$destino.addClass('ui-state-highlight');
							$destino.removeClass('ui-state-error');
							var p=$('<p>Tu asesor conectado</p>')
							.css ({
								cursor:'pointer',
								'text-align':'center'
							})
							.click(function(event) {
								self._chatBox(action.data.id,action.data.name);
								return false;
							})
							.appendTo($destino);
							if (action.data.newMsgs>0) {
								self._chatBox(action.data.id,action.data.name);
							}
						} else {
							$destino.addClass('ui-state-error');
							$destino.removeClass('ui-state-highlight');
							var p=$('<p>No contectado</p>')
							.css ({
								'text-align':'center'
							})
							.appendTo($destino);
						}
					} else {
						muestraMsgModal('Fallo',action.msg);
					}
					self._resize();
				}
			);
		},

		//Recupera la lista de usuarios en cola de espera
		_loadCola: function () {
			var self = this;
			var element = this.element;

			//Enviar el mensaje a server.
			$.post('index.php', {
					MODULE:'ACTIONS',
					acClase:'Home',
					acMetodo:'getChatCola',
					acTipo:'ajaxAssoc',
					acReturnURI:''
					},
				function(data) {
					var action=$.parseJSON(data);
					if (action.exito) {
						$('ul',element).empty();
						if (action.data.length) {
							var arrImgTags=[];
							for (var i=0;i<action.data.length;i++) {
								var sessionData=action.data[i];
								var arrImgTags
								var itemCola=$('<li><img class="ui-icon ui-icon-person" alt="Person" src="./admin.php?MODULE=IMAGES&almacen=IMGS_DIR&fichero=lib/spacer.gif">'+sessionData.id+': '+sessionData.name+' ('+sessionData.newMsgs+')</li>')
								.data('idSession',sessionData.id)
								.data('sessName',sessionData.name)
								.click(function(event) {
									self._chatBox($(this).data('idSession'),$(this).data('sessName'));
									return false;
								})
								.appendTo($('ul',element));
							}
						} else {
							var itemCola=$('<li>Ningún cliente en la cola</li>')
							.appendTo($('ul',element));
						}
					} else {
						muestraMsgModal('Fallo',action.msg);
					}
					self._resize();
				}
			);
		}
	});
}(jQuery));

//No valen los espacios de nombre anidados
//$.widget.bridge( "dependiente", $.neslonso.mostrador.dependiente);
//$.widget.bridge( "cliente", $.neslonso.mostrador.cliente);