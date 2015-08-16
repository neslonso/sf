//jQuery plugin pila
//El ; inicial es para que el interprete tenga claro que aquí empieza una sentencia,
//no vaya a ser que falte un ; en algun código anterior
//Estrcutura estandar de pluging de jquery. Ver: http://docs.jquery.com/Plugins/Authoring
/* History
/* v 1.0 (20120509)
/*	Creado from Scratch
/*	Posible problema: options no esta correctamente individualizado para cada instancia del plugin
/*	collapseTo no implementado
*/

;(function($){
	var methods = {
		init:function( options ) { 
			//this=jQuery object the plugin was invoked on
			return this.each(function () {
				//this=Objeto del DOM the plugin was invoked on (uno por vuelta si el objeto jQuery tenía mas de uno)
				var $self=$(this);
				if ( ! $self.data('pila') ) {
					
					options = $.extend(true,{
						/*
						collapseTo: {
							elto:'parent',
							position:'collapsedCenter'
						},
						*/
						spread: {
							vertical:10,
							horizontal:150
						},
						rotation: 15,
						delay: 80,
						duration: 1000,
						easing: 'easeOutBack',
						//'stuckWithItem': null, //null, 'first', last', function or index of the stuck element (0-based)
						initialCollapse: false //true if you want the initial state to be collapsed
					}, options || {});
					
					$self.css({
						position:'relative'
						//width:$self.width(),
						//height:$self.height()
					});

					//eltos = $self.children().css({'z-index': 10});
					var eltos = $self.children();

					eltos.each(function(index, elto) {
						//this=Objeto del DOM
						var $elto=$(this);
						$elto.data('pila',{
							originalPosition:$(this).css('position'),
							coords: {
								top:$(this).position().top,
								left:$(this).position().left
							},
							collapsed: false,
							angle:0,
							spreadV:0,
							spreadH:0
						})
						.css({
							top:$(this).position().top,
							left:$(this).position().left
						});
					})
					.click(function(e) {
						$elto=$(this);
						eltos = $elto.parent().children();
						if (!$elto.data('pila').collapsed) {
							methods.collapseAll(eltos,options);
						} else {
							methods.expandAll(eltos,options);
						}
					})
					.mouseover(function(e) {
						eltos = $(this).parent().children().css({'z-index': 10});
						$(this).css({'z-index':11});
					});
					
					$(window).bind('resize.pila'+$self.attr('id'), (function(context) {return function() {methods.reposition(context);}})(eltos));

					//Guardamos en el objeto del DOM referencias
					$self.data('pila',$.extend(true,{
						//originalWidth:$self.width(),
						originalHeight:$self.height(),
						//collapsedWidth:collapsedWidth,
						collapsedHeight:methods.calculateCollapsedHeight(eltos,options)
					},$self.data('pila') || {}));
					
					if (options.initialCollapse) {
						methods.collapseAll(eltos,options,true);
					}
				}
			}); 
		},
		collapseAll:function(eltos,options,instant) {
			eltos.each(function(index, elto) {
				var context=this;
				$(context).data('pila').coords.top=$(context).position().top;
				$(context).data('pila').coords.left=$(context).position().left;
				$(context).css({
					top:$(this).position().top,
					left:$(this).position().left
				});

			});

			eltos.parent().css({height:'auto'});
			//console.log(eltos.parent().data('pila').originalHeight+"::"+eltos.parent().height());
			var nuevaHeight=eltos.parent().height();
			eltos.parent().css({height:nuevaHeight});
			eltos.parent().data('pila').originalHeight=nuevaHeight;

			eltos.css({
				position:'absolute'
			});

			eltos.each(function(index, elto) {
				setTimeout((function(context,options,instant) {return function() {methods.collapseElto(context,options,instant);}})(this,options,instant),options.delay*index);
				//methods.collapseElto(this,options);
			});
			
			
			var collapsedHeight=methods.calculateCollapsedHeight(eltos,options);
			var totalDuration=options.duration+(eltos.length*options.delay);
			setTimeout((function(context,options,collapsedHeight) {return function() {methods.collapseParent(context,options,collapsedHeight);}})(eltos,options,collapsedHeight),totalDuration/2);
			eltos.parent().data('pila').collapsedHeight=collapsedHeight;
		},
		collapseParent:function(eltos,options,collapsedHeight) {
			eltos.parent().animate({
				//width:eltos.parent().data('pila').originalWidth,
				height:collapsedHeight
			},
			{
				duration:options.duration/2,
				easing:options.easing,
				complete:function() {
				},
				step:function(now,fx) {
					eltos.parent().css("overflow","visible");
				},
				queue:false
			});
		},
		expandAll:function(eltos,options) {
			//Pasamos los elemento a su posicionamiento original,
			//averiguamos la posicion que toca a cada uno con ese posicionamiento
			//los volvemos a colocar en absolute y
			//los expandimos (animacion) a la posicion original, al tiempo que animamos al
			//padre a la altura correspondiente
			/************************************/
			eltos.each(function(index, elto) {
				$(elto).css({
					position:$(this).data('pila').originalPosition,
					'-webkit-transform': 'rotate(0deg)',
					'-moz-transform': 'rotate(0deg)'
				});
			});
			
			eltos.parent().css({height:'auto'});
			//console.log(eltos.parent().data('pila').originalHeight+"::"+eltos.parent().height());
			var nuevaHeight=eltos.parent().height();
			//eltos.parent().css({height:eltos.parent().data('pila').originalHeight});
			eltos.parent().css({height:eltos.parent().data('pila').collapsedHeight});
			eltos.parent().animate({
				//width:eltos.parent().data('pila').originalWidth,
				height:nuevaHeight
			},
			{
				duration:options.duration/2,
				easing:options.easing,
				complete:function() {
				},
				step:function(now,fx) {
					eltos.parent().css("overflow","visible");
				},
				queue:false
			});
			eltos.parent().data('pila').originalHeight=nuevaHeight;

			eltos.each(function(index, elto) {
					context=this;
					$(context).data('pila').coords.top=$(context).position().top;
					$(context).data('pila').coords.left=$(context).position().left;
			});

			eltos.each(function(index, elto) {
				$(elto).css({
					position:'absolute',
					'-webkit-transform': 'rotate('+$(this).data('pila').angle+'deg)',
					'-moz-transform': 'rotate('+$(this).data('pila').angle+'deg)'
				});
			});

			eltos.each(function(index, elto) {
				setTimeout((function(context,options) {return function() {methods.expandElto(context,options);}})(this,options),options.delay*index);
				//methods.expandElto(this,options);
			});
			var totalDuration=options.duration+(eltos.length*options.delay);
			setTimeout((function(context) {return function() {methods.restoreEltosPosition(context);}})(eltos),totalDuration);
			/**/
		},
		collapseElto:function(elto,options,instant) {
			$(elto).data('pila').collapsed=true;
			$(elto).data('pila').spreadV=methods.random(-options.spread.vertical, options.spread.vertical);
			$(elto).data('pila').spreadH=methods.random(-options.spread.horizontal, options.spread.horizontal);
			
			//var randRot = methods.random(-options.rotation, options.rotation);
			var randAngle=methods.random(-options.rotation, options.rotation);
			$(elto).data('pila').angle=randAngle;

			var top=($(elto).parent().data('pila').collapsedHeight-$(elto).height())/2 + $(elto).data('pila').spreadV;
			var left=($(elto).parent().width()-$(elto).width())/2 + $(elto).data('pila').spreadH;
			if (instant) {
				$(elto).css({
					top: top,
					left: left,
					'-webkit-transform': 'rotate('+randAngle+'deg)',
					'-moz-transform': 'rotate('+randAngle+'deg)'
				});
			} else {
				$(elto).animate({
					top: top,
					left: left
				},
				{
					duration:options.duration,
					easing:options.easing,
					complete:function() {
						/*$(elto).css({
							position:'static',
						});*/
					},
					step:function(now,fx) {
						if (fx.prop=="top") {
							var rango=Math.abs(fx.start-fx.end);
							var actual=Math.abs(fx.start-now)
							var progress=actual/rango;
							var angle=randAngle*progress;
							//var angle=0;
							$(elto).data('pila').angle=angle;
							//console.log (fx.start+'::'+fx.end+'::'+now+':--:'+actual+'/'+rango+'='+progress);
							$(elto).css({
								'-webkit-transform': 'rotate('+angle+'deg)',
								'-moz-transform': 'rotate('+angle+'deg)'
							});
							/**/
						}
					},
					queue:false
				});
			}
		},
		expandElto:function(elto,options) {
			$(elto).data('pila').collapsed=false;
			var top=$(elto).data('pila').coords.top;
			var left=$(elto).data('pila').coords.left;
			//console.log ($(elto).data('pila'));
			//console.log (top + "::" + left);

			$(elto).animate({
				top: top,
				left: left
			},
			{
				duration:options.duration,
				easing:options.easing,
				complete:function() {
				},
				step:function(now,fx) {
					if (fx.prop=="top") {
						var rango=Math.abs(fx.start-fx.end);
						var actual=Math.abs(fx.start-now)
						var progress=actual/rango;
						var angle=($(elto).data('pila').angle)*(1-progress);
						//console.log (angle);
						$(elto).css({
							'-webkit-transform': 'rotate('+angle+'deg)',
							'-moz-transform': 'rotate('+angle+'deg)'
						});
						/**/
					}
				},
				queue:false
			});
		},
		random:function(min,max) {
			return Math.floor(Math.random() * (max - min + 1) + min);
		},
		restoreEltosPosition:function(eltos){
			eltos.each(function(index, elto) {
				$(elto).css({
					position:$(elto).data('pila').originalPosition,
					'-webkit-transform': 'rotate(0deg)',
					'-moz-transform': 'rotate(0deg)'
				});
			});
			eltos.parent().css({
				height:'auto'
			});
		},
		calculateCollapsedHeight:function(eltos,options) {
			var width=$(eltos[0]).width();
			var height=$(eltos[0]).height();
			
			var angle = options.rotation * Math.PI / 180,
			sin   = Math.sin(angle),
			cos   = Math.cos(angle);
			
			// (0,0) stays as (0, 0)
			
			// (w,0) rotation
			var x1 = cos * width,
			y1 = sin * width;
			
			// (0,h) rotation
			var x2 = -sin * height,
			y2 = cos * height;
			
			// (w,h) rotation
			var x3 = cos * width - sin * height,
			y3 = sin * width + cos * height;
			
			var minX = Math.min(0, x1, x2, x3),
			maxX = Math.max(0, x1, x2, x3),
			minY = Math.min(0, y1, y2, y3),
			maxY = Math.max(0, y1, y2, y3);
			
			var rotatedWidth  = maxX - minX,
			rotatedHeight = maxY - minY;

			//collapsedWidth=rotatedWidth+options.spread.horizontal;
			var collapsedHeight=rotatedHeight+options.spread.vertical;

			return collapsedHeight;
		},
		reposition:function (eltos) {
			eltos.each(function(index, elto) {
				var top=($(elto).parent().data('pila').collapsedHeight-$(elto).height())/2 + $(elto).data('pila').spreadV;
				var left=($(elto).parent().width()-$(elto).width())/2 + $(elto).data('pila').spreadH;
				$(elto).css({
					top: top,
					left: left
				});
			});
		},
		destroy:function() {
			return this.each(function(){
				// Namespacing FTW
				if ($(this).data('pila')) {
					$(window).unbind('.pila'+$(this).attr('id'));
					$(this).removeData('pila');
				}
			});
		}
	};
	
	$.fn.pila = function(method) {
		// Method calling logic
		if ( methods[method] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.pila' );
		}
	};
})(jQuery);