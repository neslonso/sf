//Biblioteca de pequeños plugins y funciones globales
function muestraMsgModal(title, msg) {
	//muestraMsgModalJqueryUI(title, msg);
	muestraMsgModalBootstrap3(title, msg);
}

//jQuery Plugin Unique ID
(function( $ ){
	var contador=0;
	$.fn.uniqueId = function(prefix) {
		//this=jQuery object the plugin was invoked on
		return this.each(function () {
			contador++;
			$(this).attr("id", prefix + contador);
		});
	};
})( jQuery );
//

//jQuery Plugin Equal Height
(function( $ ){
	$.fn.equalHeight = function() {
		//this=jQuery object the plugin was invoked on
		tallest = 0;
		this.each(function() {
			//this=DOM object
			console.log(this);
			//thisHeight = $(this).height();
			thisHeight = $(this).innerHeight();
			//thisHeight = $(this).outerHeight();
			if(thisHeight > tallest) {
				tallest = thisHeight;
			}
		});
		this.height(tallest);
		return this;
	};
})( jQuery );
//

//jQuery plugin exists
jQuery.fn.exists = function(){return jQuery(this).length>0;};
//

//jQuery plugin disableSelection
(function($){
	$.fn.disableSelection = function() {
		return this
			.attr('unselectable', 'on')
			.css({
				'-moz-user-select': 'none',
				'-khtml-user-select': 'none',
				'-webkit-user-select': 'none',
				'user-select': 'none'
			})
			.on('selectstart', false);
	};
})(jQuery);
//

//jQuery plugin overlay.
/* History
/* v 1.0 (20120705)
/* Version inicial, pone un overlay sobre todo el body con una imagen centrada horizontal y verticalmente
*/
;(function($) {
	var methods = {
		init: function(settings) {
			var self=this;//Al estar en $.overlay, this es una funcion
			var $body=$(document.body);

			self.opt = $.extend(true, {}, $.overlay.defaults, settings);

			var $overlay=$('<div/>').addClass('ui-widget-overlay').css({position:'fixed'});
			var $divTable=$('<div/>').css({
				position:'fixed','text-align':'center',display:'table',
				top:0,left:0,width:'100%',height:'100%'});
			var $divCell=$('<div/>').css ({
				height:'100%;',display:'table-cell','vertical-align':'middle'}).appendTo($divTable);
			var $img=$('<img>')
				.attr({
					src:self.opt.imgSrc,
					alt:self.opt.imgAlt
				})
				.css (self.opt.imgCss)
				.addClass(self.opt.imgClass)
				.appendTo($divCell);

			$overlay.appendTo($body);
			$divTable.appendTo($body);
			$body.data('overlay', {
				settings:self.opt,
				$overlay:$overlay,
				$divTable:$divTable
			});
		},
		destroy:function() {
			var $body=$(document.body);
			if ($body.data('overlay')) {
				$body.data('overlay').$overlay.remove();
				$body.data('overlay').$divTable.remove();
				$body.removeData('overlay');
			}
		}
	};

	$.overlay=function(method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Method ' + method + ' does not exist!');
		}
	};

	$.overlay.defaults = {
		imgSrc:'./binaries/imgs/lib/ajax-loader.gif',
		imgAlt:'Cargando...',
		imgCss:{},
		imgClass:''
	};
})(jQuery);
//

//jQuery plugin DBdataTable
/* History
/* v 1.0 (20120912)
/* Version inicial, encapsulamiento para usar dataTables en modo server-side
*/
;(function($) {
	var methods = {
		init: function(settings) {
			return this.each(function() {
				var self = this;
				var $self = $(self);
				if ( ! $self.data('DBdataTable') ) {
					self.opt = $.extend(true, {}, $.fn.DBdataTable.defaults, settings);

					$self.disableSelection();
					$self.dataTable(self.opt);

					//$('tbody tr',self).live('click',
					//Obsoleto, para jquery 1.7+ usar on
					$('tbody',self).on('click','tr',
						(function ($self) {
							return function (evt) {
								$self.trigger ('rowClick',$(this).attr('id'));
							}
						})($self)
					);

					$self.data('DBdataTable', true);
				}
			});
		},
		destroy:function() {
			return this.each(function(){
				// Namespacing FTW
				if ($(this).data('DBdataTable')) {
					$(this).removeData('DBdataTable');
				}
			});
		}
	};

	$.fn.DBdataTable=function(method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Method ' + method + ' does not exist!');
		}
	};

	$.fn.DBdataTable.defaults = {
		"bProcessing": true,
		"bServerSide": true,
		"sServerMethod": "POST",
		"sAjaxSource": "./index.php",
		"sAjaxDataProp": "data",
		'bJQueryUI': true,
		'sPaginationType': "full_numbers",
		//'sScrollY': "200px",
		//'bPaginate': false,
		//'bScrollCollapse': true,

		"bStateSave": true,
		"fnServerData": function ( sSource, aoData, fnCallback ) {
			//console.log (aoData);
			$.ajax({
				type: 'POST',
				url: sSource,
				data: aoData,
				success: function (data) {
					//console.log (data);
					fnCallback(data.data)
				},
				dataType: 'json'
			});
		},
		"fnStateLoadParams": function (oSettings, oData) {//Si el parametro oSearch esta definido ("oSearch": {"sSearch": $catalogTable.data('busq')}), ignoramos el filtro guardado y usamos el pasado como parametro
			//console.log(oSettings);
			if (oSettings.oPreviousSearch.sSearch!="") {
				oData.oSearch.sSearch = oSettings.oPreviousSearch.sSearch;
			}
		},
		"fnFormatNumber": function ( iIn ) {
			if ( iIn < 1000 ) {
				return iIn;
			} else {
				var
				s=(iIn+""),
				a=s.split(""), out="",
				iLen=s.length;

				for ( var i=0 ; i<iLen ; i++ ) {
					if ( i%3 === 0 && i !== 0 ) {
						out = "."+out;
					}
					out = a[iLen-i-1]+out;
				}
			}
			return out;
		},
		'oLanguage': {
			"sProcessing":   "Procesando...",
			"sLengthMenu": "Mostrar _MENU_ elementos por página",
			"sZeroRecords": "No se encontraron resultados",
			"sInfo": "Mostrando del _START_ al _END_ de _TOTAL_ elementos",
			"sInfoEmpty": "Sin resuldados",
			"sInfoFiltered": "(buscando entre _MAX_ elementos en total)",
			"sInfoPostFix":  "",//This information will be appended to the sInfo (sInfo, sInfoEmpty and sInfoFiltered in whatever combination they are being used) at all times.
			"sSearch":       "Buscar:",
			"sUrl":          "",
			"oPaginate": {
				"sFirst":    "Primero",
				"sPrevious": "Anterior",
				"sNext":     "Siguiente",
				"sLast":     "Último"
			}
		}
	};

})(jQuery);
//

//jQuery plugin block ticker.
/* History
/* v 0.1 (20130124)
/* Version inicial, crea un ticker con los elementos hijos del elemento dado

//Issues:
//En webkit es necesario especificar el ancho/alto de los items, porque si contienen imagenes, los miden antes de cargar la imagen y luego crecen
*/
;(function($) {
	var methods = {
		init: function(settings) {
			//this=jQuery object the plugin was invoked on
			return this.each(function () {
				//this=Objeto del DOM the plugin was invoked on (uno por vuelta si el objeto jQuery tenía mas de uno
				var self=this;
				var $self=$(this);
				if ( ! $self.data('blockTicker') ) {
					self.opts = $.extend(true, {}, $.fn.blockTicker.defaults, settings);
					$self.data('blockTicker', {
						//origWidth: $self.outerWidth(true)
					});

					$self.wrap('<div class="blockTicker" />').parent().css({
						//outline:'dashed red 1px',
						overflow:'hidden',
						position:'relative'
					});

					$(window).bind('resize.blockTicker'+$(this).attr('id'), (function(context) {return function() {methods.calculateDimensions.apply(context);}})(this));
					methods.calculateDimensions.apply(this);
					methods.play.call(this,73);
				}
			});
		},
		reset:function() {
			var self=this;
			var $self=$(this);

			$self.parent().css ({width:'auto'});

			$self.css({
				display:'block',
				position:'relative',
				width: 'auto',
				height: 'auto'
			});

			$self.children('.blockTickerAppended').remove();
			var $childs=$self.children();
			$childs.css({display:'block',float:'none'});
		},
		calculateDimensions:function() {
			var self=this;
			var $self=$(this);

			//methods.stop.apply(this);
			methods.reset.apply(this);

			var tickerWidth=$self.outerWidth(true);
			//console.log("tickerWidth: "+tickerWidth);

			//Cogemos los hijos directos del objeto DOM
			var $childs=$self.children();
			$childs.css({display:'block',float:'left'});
			var minWidth=0;
			var maxHeight=0;
			var totalWidth=0;
			var totalChilds=0;
			$childs.each(function () {
				$this = $(this);
				totalWidth+=$this.outerWidth(true);
				totalChilds+=1;
				//console.log(this);
				//console.log('Width ('+totalChilds+'): '+$this.outerWidth(true));
				if ( $this.outerWidth(true) < minWidth || minWidth==0) {
					minWidth=$this.outerWidth(true);
				}
				if ( $this.outerHeight(true) > maxHeight ) {
					maxHeight=$this.outerHeight(true);
				}
			});
			var extraWidth=0;
			//console.log('Childs: '+totalChilds);
			var visibleChilds=Math.ceil(tickerWidth/minWidth);
			//console.log('visibleChilds: '+visibleChilds);
			var $childsSlice=$childs.slice(0,visibleChilds);
			$childsSlice.each(function () {
				$this = $(this);
				extraWidth+=$this.outerWidth(true);
			});
			$childsSlice.clone().addClass('blockTickerAppended').appendTo($self);

			$self.parent().css({width: tickerWidth, height:maxHeight});
			$self.css({
				display:'block',
				position:'absolute',
				width: totalWidth+extraWidth,
				height: maxHeight
			})
			.hover(
				function() {methods.stop.call(self);},
				function() {methods.play.call(self,73);}
			);
			self.totalWidth=totalWidth;
			//methods.play.call(this,73);
		},
		reposition:function(direction,amount) {
			var self=this;
			var $self=$(this);
			//console.log ($self.position().left+'::'+amount+':##:'+($self.position().left-amount)+"px");
			switch (direction) {
				case "horizontalToRight":
				break;
				case "verticalToTop":
				break;
				case "verticalToBottom":
				break;
				case "horizontalToLeft":
				default:
					if ($self.position().left*-1>=self.totalWidth) {var newLeft=0-amount;}
					else {var newLeft=($self.position().left-amount);}
					$self.css({left:newLeft+"px"});
			}
		},
		play:function (pps) {
			var self=this;
			var $self=$(this);

			//recalculamos pps para que salgan justos respecto al ancho total
			var pasos=Math.round(self.totalWidth/pps);
			pps=self.totalWidth/pasos;
			//console.log(pps);

			var intervalID=setInterval(
				(function (context) {
					return function () {methods.reposition.call(context,"horizontalToLeft",pps/10);}
				})(this),
				100);

			$.extend(true, $self.data('blockTicker'), {intervalID:intervalID});
		},
		stop:function() {
			var self=this;
			var $self=$(this);

			clearInterval($self.data('blockTicker').intervalID);
		},
		destroy:function() {
			return this.each(function() {
				var self=this;
				var $self=$(this);

				if ($self.data('blockTicker')) {
					$(window).unbind('.blockTicker'+$this.attr('id'));
					$self.unwrap();
					$self.removeData('blockTicker');
				}
			});
		}
	};

	$.fn.blockTicker=function(method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Method ' + method + ' does not exist!');
		}
	};

	$.fn.blockTicker.defaults = {
		width:'100%'
	};
})(jQuery);
//