/******************************************************************************/
/* jqueryUI.forms.wizard v 2.6.2

/* utilidad para converitr HTML forms en forms asistentes decorados
	compatible con temas de jqueryUI

/* Creado a partir de:
/* Enhancing-Forms-using-jQuery-UI.zip
	(http://www.tuttoaster.com/enhancing-forms-using-jquery-ui/)
/* FormToWizard.zip
	(http://www.jankoatwarpspeed.com/post/2009/09/28/webform-wizard-jquery.aspx/)
/* jQuery UI MultiSelect Widget
	(http://www.erichynds.com/jquery/jquery-ui-multiselect-widget/)
/* jquery.watermark.js <- Totalmente remodelada
	/http://daersystems.com/jquerywatermark.asp)


/* History
/* v 2.6.2 (20121024)
	* añadido event.stopPropagation al click de checks y radios, no queremos que el evento suba por el DOM
/* v 2.6.1 (20120815)
	* modificado el wizard para que solo haga pasos con los fieldsets que descienden directamente del form
	* esto hace posible anidar fieldsets en los form con wizard.
/* v 2.6 (20120704)
	* _create cambiado por _init y añadido formApplied al form y los inputs. Ahora se puede volver a llamar
	al form para que se aplique a los campos nuevos
/* v 2.5.1 (20120620)
	* Añadido parametro title a captcha
	* Añadido trigger de evento stepSelected a wizard. Se llama cuando se pulsa el boton siguiente o anterior
	* Añadido trigger de evento captchaSolved a captcha. Se llama cuando el captcha se resuelve correctamente

/* v 2.5 (20120424)
/* Watermark:
	* Modificado para que las watermark tiren del attr placeholder de los inputs

/* v 2.4 (20120331)
/* Wizard:
	* Añadida funcionalidad para botones anterior y siguiente siempre visibles
		(en el primer y ultimo paso se ve el boton correspondiente pero desactivado)
	* Añadidas opciones para especificar el texto (html) de los botones Prev y Next
/* Añadido código para que los checkboxes obedezcan al margin y padding del elemento subyacente
	* Problema conocido, en FF y Chrome, no funciona el padding, en Opera e IE 7, 8 y 9 si.
/* Añadida opcion height al wizard. Representa la altura del panel del wizard, excluyendo legend y steps,
	* si al contenido es mas alto, scroll. Valor css, acepta Auto
/* Añadida opcion submitIsLastNext al wizard, si es true los submits y reset se llevan al lugar del botón
	* next en el último paso del wizard

/* v 2.3 (20110714)
/* Descubierto bug en el funcionamiento de los radios.
	* Remodelado el codigo de los radios para que solo pueda estar uno activo
	* Coregido BUG al quitar ui-state-active de los radios

/* v 2.2 (20110709)
/* Fusionado con una versión totalemnte remodelada de jquery.watermark.js
	* se puede especificar un objeto ({key:'value',key:'value',...}) dentro
		de un atributo (rel por omision) de los inputs o textareas y los
		datos de este objeto se usan para crear la marca de agua
	* Workaround a Problema conocido, no funciona correctamente dentro de
		dialogos jqueryUI si es llamado antes de abrir el dialogo
			* Llamar a esta biblioteca cuando ya se haya abierto el dialogo
	* Problema conocido, no sigue el estandar de jqueryUI para implementar plugins
		* habría que metèr las funciones destroy, enable y disable
			ver http://docs.jquery.com/UI_Developer_Guide

/* v 2.1 (20110706)
/* Fusionado con jQuery UI MultiSelect Widget
	* Los select se convierten en lista de selección simples o multiples
	* Añadido el array multiselects a options
		* Las opciones en él se pasan a todos los selects del form
		* Las contenidas en un subaaray con el id de un select se
			aplican solo a ese select
		* La opción filter:boolean para controlar si se llama a multiselectfilter
		* array multiselectfilter con las opciones de todos los selects que
			tengan filter:true
	* Modifcado toWizard para que no oculte los elementos hasta el final
	* Cambiada _init por _create para que el mismo form no se procese más de una vez
	* Problema reparado, widths relativos
		* Reparado manteniendo visibles los elementos hasta terminar
			los calculos de tamaño. Es posible que usar la clase
			.ui-helper-hidden-accessible de jqueryUI, que los oculta
			via abs positioning off the page tambien funcione
	* Problema conocido, no sigue el estandar de jqueryUI para implementar plugins
		* habría que metèr las funciones _create, destroy, enable y disable
			ver http://docs.jquery.com/UI_Developer_Guide
	* Problema conocido, no funciona correctamente dentro de dialogos jqueryUI
		si es llamado antes de abrir el dialogo
		* No funciona el tema de las colisiones ni los calculo de tamaño,
			porque el select subyacente está oculto en el momento de
			convertirlo en multiselect.
				* esto obliga a especificar un width para los selects en el marcado
				* la mejor solución es llamar a esta biblioteca cuando
					ya se haya abierto el dialogo

/* v 2.0 (20110702)
/* Fusionado con formToWizard. Capacidad de convertir el form en un wizard
	* Añadido options
	* Trasladado parametro captcha a propiedad del objeto options
	* Creación del captcha remasterizada y trasladada a una funcion
		* anadida option fieldset a captcha
		* si true el captcha se hace en un fieldset a mayores
	* Añadida la funcion toWizard y la opción wizard
		* si true el form se convierte en un wizard
		* cada fieldset es un paso del wizard
	* Problema conocido, select con width:100%;
		* Necesario detectar la anchura en pixels, no en %, de los select 
			para aplicarla al ancho del ul "desplegable", cuyo % no se refiere
			al mismo contenedor que el select subyacente.
	* Problema conocido, firefox da un error al pulsar los botones del wizard
		* Al pulsar al ultimo "siguiente" y a cualquier anterior dice
			Cadena vacía pasada a getElementById().
	* Problema conocido, no sigue el estandar de jqueryUI para implementar plugins
		* habría que metèr las funciones _create, destroy, enable y disable
			ver http://docs.jquery.com/UI_Developer_Guide
	
/* v 1.0.1a (20110624)
/* Añadido cursor:pointer a los select y cambiado cursor default por cursor pointer en la lista de los select
/* Añadido overflow y max-height 200 al ul se los select para que saquen barra de desplazamiento al superar la altura máxima
/* corregido el problema del width 100% en los select
/* Anadido cursor:pointer a los buttons

/* v 1.0 (20110622)
/* Añadido parametro en la inicialización para especificar si se debe incluir el captcha
/* Modificada la función selector para que el select generado tenga el mismo ancho, alto y tamaño de fuente que el select subyacente.
	* Error conocido, no funciona para width:100%;
/******************************************************************************/

/*
/* Ejemplo de llamada
	<script type="text/javascript">
		/* <![CDATA[ (asterisco)/
		$(document).ready(function() {
			$('#frmRegistro').form({
				captcha: {captcha:true,fieldset:true},
				wizard: true,
				multiselects: {
					'fnacDia':{
					},
					'sexo':{
					}
				}
			});
		});
		/* ]]> (asterisco)/
	</script>
NOTA, No se puede llamar varias veces sobre el mismo form, se duplican los inputs
*/

/* Ejemplo de css
#frmRegistro fieldset {padding:10px; margin-bottom:10px;}
#frmRegistro legend {margin-left:10px; padding: 0px 5px;}
#frmRegistro .tblFieldset {width:100%;}
#frmRegistro td {vertical-align:middle}
#frmRegistro .labelField {text-align:right; width:77px; height:33px; padding-right:5px;}
#frmRegistro input, #frmRegistro select {font-size:18px; font-weight:bold; width:100%;}
#frmRegistro .tblFnac td {padding-right:10px;}

#frmRegistro .captcha {margin-top:10px; border-top:solid #3399FF 1px; padding-top:10px;}
#frmRegistro .submitsZone {margin-top:10px;}
#frmRegistro .steps { list-style:none; width:100%; overflow:hidden; margin:0px; padding:0px;}
#frmRegistro .steps li {font-size:14px; float:left; padding:10px; color:#b0b1b3;}
#frmRegistro .steps li span {font-size:8px; display:block;}
#frmRegistro .stepCommands {text-align:right; border-top:solid #3399FF 1px; margin-top:10px; padding-top:10px;}
#frmRegistro .stepCommands input {width:auto;}
*/

/*
Ejemplo de marcado
TODO...
*/

(function ($) {
    'use strict';

	//El codigo, hace uso de la widget factory de jqueryUI, ver http://docs.jquery.com/UI_Developer_Guide#jQuery_UI_API_Developer_Guide
	$.widget("ui.form",{
		options: { // initial values are stored in the widget's prototype
			captcha: {
				captcha:false,
				fieldset:false,
				title:'Filtro anti-robots'
			},
			wizard: {
				wizard: false,
				height: 'auto',//altura del panel del wizard, excluyendo legend y steps, si al contenido es mas alto, scroll. Acepta Auto
				PrevText: '< Anterior',
				NextText: 'Siguiente >',
				submitIsLastNext: true,
				alwaysShowPrevNext: false
			},
			multiselects: {
				/*
				//animations
				show: ["bounce", 200],'blind', 'bounce', 'clip', 'drop', 'explode', 'fold', 'highlight', 'puff', 'pulsate', 'scale', 'shake', 'size', 'slide', 'transfer'.
				hide: ["explode", 1000],
				//callbacks
				click: function(event, ui){
					console.log(ui.value + ' ' + (ui.checked ? 'checked' : 'unchecked') );
				},
				beforeopen: function(){
					console.log("Select about to be opened...");
				},
				open: function(){
					console.log("Select opened!");
				},
				beforeclose: function(){
					console.log("Select about to be closed...");
				},
				close: function(){
					console.log("Select closed!");
				},
				checkAll: function(){
					console.log("Check all clicked!");
				},
				uncheckAll: function(){
					console.log("Uncheck all clicked!");
				},
				optgrouptoggle: function(event, ui){
					var values = $.map(ui.inputs, function(checkbox){
						return checkbox.value;
					}).join(", ");
					console.log("Checkboxes " + (ui.checked ? "checked" : "unchecked") + ": " + values);
				},
				//selectedText y selectedList
				selectedText: function(numChecked, numTotal, checkedItems){
					return numChecked + ' of ' + numTotal + ' checked';
				},
				selectedList: 4,//Una forma abreviada de usar la función selectedText
				//singleSelect
				multiple: false,
				selectedList: 1
				*/
				header: false,//True (botones de check/unckeck all), False (Sin header) o bien un String
				height:'auto',
				maxHeight:250,
				minWidth:'auto',
				minMenuWidth:'auto',
				selectedList: false,
				//i18n
				checkAllText: 'todas',
				uncheckAllText: 'ninguna',
				noneSelectedText: 'Sel',//Cuando es un multiple sin nada seleccionado o un simple con selectedList=false
				selectedText: '# sel.',
				//position
				position: {
					my: 'left top',
					at: 'left bottom',
					// only include the "of" property if you want to position
					// the menu against an element other than the button.
					// multiselect automatically sets "of" unless you explictly
					// pass in a value.
					collision:'flip'//flip,fit,none (si son 2 valores se interpretan como horizontal y vertical)
				},
				//opciones añadidas para integración
				filter: false//añade la llamada a multiselect().multiselectfilter()
				//tambien pueden incluirse arrays de opciones individuales para selects,
				//incluyendo un array que tenga por nombre el id del select
				//<idDelSelect>: {array de opciones especificas para el select <idDelSelect>}
			},
			multiselectfilter: {
				//i18n
				label: "Filtro:",
				placeholder: "texto de busqueda"
			}
		},
		_init:function() {
		//_create:function() {
			var object = this;
			var form = this.element;
			
			if ( ! form.data('formApplied') ) {
				//form.data('formApplied',true); Al final de initi se realiza otra comprobación de formApplied y es allí donde se pone a true

				if (this.options.captcha.captcha) {
					this.captcha();
				}
				if (this.options.wizard.wizard) {
					this.toWizard.init(form,this.options);
				} else {//Para compatibilizad con llamadas anteriores a version 2.4
					if (this.options.wizard==true) {
						this.options.wizard={wizard: true,alwaysShowPrevNext: false};
						this.toWizard.init(form,this.options);
					}
				}

				//form.find("fieldset").addClass("ui-widget-content");
				//form.find("legend").addClass("ui-widget-header ui-corner-all");
				form.addClass("ui-widget");
			}

			form.find("fieldset").each (function() {
				if ( ! $(this).data('formApplied') ) {
					$(this).data('formApplied',true)
					.addClass("ui-widget-content");
				}
			});
			form.find("legend").each (function() {
				if ( ! $(this).data('formApplied') ) {
					$(this).data('formApplied',true)
					.addClass("ui-widget-header ui-corner-all");
				}
			});
			
			var inputs = form.find("input , select ,textarea");
			$.each(inputs,function(){
				if ( ! $(this).data('formApplied') ) {
					$(this).data('formApplied',true);

					$(this).addClass('ui-state-default ui-corner-all');
					//20110708 creo que este label no produce ninguna diferencia aunque se quite, pero lo vamos a usar para ajustar el
					//tamaño de letra, ya que si su tamaño de letra es inferior el del control del form, a la hora de watermark da problemas
					//a los hidden no les ponemos la label
					if(!$(this).is("input[type='hidden']")) {
						$(this).wrap('<label style="font-size:'+$(this).css('font-size')+';" />');
					}
					if($(this).is(":reset ,:submit,:button"))
						object.buttons(this);
					else if($(this).is(":checkbox"))
						object.checkboxes(this);
					else if($(this).is("input[type='text']")||$(this).is("textarea")||$(this).is("input[type='password']"))
						object.textelements(this);
					else if($(this).is(":radio"))
						object.radio(this);
					else if($(this).is("select"))
						object.selector(this);
		
					if($(this).hasClass("date"))
						$(this).datepicker();
				}
			});
			
			if ( ! form.data('formApplied') ) {
				form.data('formApplied',true);

				$(".hover").hover(function(){
						$(this).addClass("ui-state-hover");
					},
					function(){
						$(this).removeClass("ui-state-hover");
					}
				);
				//Ocultamos los fieldset no activos del wizard, al final, para que ocultar los elementos no influya en calculos de tamaño
				this.toWizard.hideAllButCurrent(form);
			}
		},
		textelements:function(element){
			if ($.fn.watermark) {
				$(element).watermark({attr:'placeholder'});
			}
			$(element).bind({
				focusin: function() {
					$(this).toggleClass('ui-state-focus');
				},
				focusout: function() {
					$(this).toggleClass('ui-state-focus');
				}
			});
		},
		buttons:function(element){
			if($(element).is(":submit")){
				$(element).addClass("ui-priority-primary ui-corner-all hover");
				if (this.options.captcha.captcha) {
					$(element).addClass("ui-state-disabled");
					$(element).bind("click",function(event){
						event.preventDefault();
					});
				}
			} else if($(element).is(":reset")) {
				$(element).addClass("ui-priority-secondary ui-corner-all hover");
			} else if($(element).is(":button")) {
				$(element).addClass("ui-corner-all hover");
			}
			$(element).bind('mousedown mouseup', function() {
				$(this).toggleClass('ui-state-active');
			});
			$(element).css('cursor','pointer');
		},
		checkboxes:function(element){
			$(element).parent("label").after("<span />");
			var innerSpan =  $(element).parent("label").next();
			$(element).addClass("ui-helper-hidden");
			innerSpan.css({width:16,height:16,display:"block"});

			var margin=$(element).css("margin-top")+" "+$(element).css("margin-right")+" "+$(element).css("margin-bottom")+" "+$(element).css("margin-left");
			var padding=$(element).css("padding-top")+" "+$(element).css("padding-right")+" "+$(element).css("padding-bottom")+" "+$(element).css("padding-left");
			//Problema, getComputedStyle (en lo que se basará css de jquery, probablemente) devuelve valores diferentes, 0 en FF, auto en Chrome y el valor esperado en IE y Opera
			innerSpan.wrap("<span class='ui-state-default ui-corner-all' style='"+
				"margin:"+margin+";"+
				"padding:"+padding+";"+
				"display:inline-block;width:16px;height:16px; vertical-align:middle;'/>");
			innerSpan.parent().addClass('hover');
			innerSpan.parent("span").click(function(event){
				$(this).toggleClass("ui-state-active");
				innerSpan.toggleClass("ui-icon ui-icon-check");

				//Ver: http://www.bennadel.com/blog/1525-jQuery-s-Event-Triggering-Order-Of-Default-Behavior-And-triggerHandler-.htm
				//Al llamar a $(element).click(); saltan primero los manejadores y luego el evento del navegador
				//al reves que si el evento se produce de manera natural (sin llamar al evento desde codigo), así que
				//para simular el comporatmiento natural, cambiamos el estado del checkbox a mano y llamamos solo a los
				//manejadores (no al evento por defecto)
				//$(element).click();
				$(element).attr('checked', !$(element).attr('checked'));
				$(element).triggerHandler("click");
				event.stopPropagation();
			});
			//20110510 Fragmento añadido para que los checks obedezcan al atributo "checked"
			if ($(element).attr('checked')) {
				innerSpan.parent("span").toggleClass("ui-state-active");
				innerSpan.toggleClass("ui-icon ui-icon-check");
			}
			//20110525 Fragmento añadido para que los checks obedezcan al atributo disabled
			if ($(element).attr('disabled')) {
				innerSpan.parent("span").toggleClass("ui-state-disabled");
				innerSpan.parent("span").unbind('click');
			}
		},
		radio:function(element){
			$(element).parent("label").after("<span />");
			var innerSpan =  $(element).parent("label").next();
			$(element).addClass("ui-helper-hidden");
			innerSpan.addClass("ui-icon ui-icon-radio-off");
			innerSpan.wrap("<span class='ui-state-default ui-corner-all' style='display:inline-block;width:16px;height:16px;margin-right:5px;'/>");
			innerSpan.parent().addClass('hover');
			innerSpan.parent("span").click(function(event){
				//20110714 Remodelado para que se deschecken todos los demas radios del mismo [name] al pinchar en uno no checked
				if (!$(element).attr('checked')) {
					$("input[name='"+$(element).attr('name')+"']").each(function () {
						var suInnerSpan=$(this).parent("label").next().children();
						suInnerSpan.parent().removeClass("ui-state-active");
						suInnerSpan.removeClass("ui-icon-bullet");
						suInnerSpan.addClass("ui-icon-radio-off");
					});
					$(this).toggleClass("ui-state-active");
					innerSpan.toggleClass("ui-icon-radio-off ui-icon-bullet");
					//Ver: http://www.bennadel.com/blog/1525-jQuery-s-Event-Triggering-Order-Of-Default-Behavior-And-triggerHandler-.htm
					//Al llamar a $(element).click(); saltan primero los manejadores y luego el evento del navegador
					//al reves que si el evento se produce de manera natural (sin llamar al evento desde codigo), así que
					//para simular el comporatmiento natural, cambiamos el estado del checkbox a mano y llamamos solo a los
					//manejadores (no al evento por defecto)
					//$(element).click();
					$(element).attr('checked', !$(element).attr('checked'));
					$(element).triggerHandler("click");
					event.stopPropagation();
				}
				//
			});
			//20110510 Fragmento añadido para que los radio obedezcan al atributo "checked"
			if ($(element).attr('checked')) {
				innerSpan.parent("span").toggleClass("ui-state-active");
				innerSpan.toggleClass("ui-icon-radio-off ui-icon-bullet");
			}
			//20110525 Fragmento añadido para que los radio obedezcan al atributo disabled, está sin probar
			if ($(element).attr('disabled')) {
				innerSpan.parent("span").toggleClass("ui-state-disabled");
				innerSpan.parent("span").unbind('click');
			}
		},
		selector:function(element){
			var options = $.extend(true,{},this.options.multiselects,this.options.multiselects[$(element).attr('id')]);
			if ($(element).attr('multiple') !== undefined && $(element).attr('multiple') !== false) {
				//lista de seleccion multiple
				$.extend(true,options,{
					multiple: true
				});
			} else {
				//lista de seleccion simple
				$.extend(true,options,{
					multiple: false,
					selectedList: 1
				});
			}
			
			if (options.filter) {
				//con filtro
				$.extend(true,options,{
					header: true
				});
				$(element).multiselect(options).multiselectfilter(this.options.multiselectfilter);
			} else {
				//sin filtro
				$(element).multiselect(options);
			}
		},
		/* 20110704 Sustituimos el selector original (muy simple) por el de ehynds (parece muy completo)
		selector:function(element){
			var parent = $(element).parent();
			parent.css({"display":"block","cursor":"pointer",width:$(element).css('width'),height:$(element).css('height')}).addClass("ui-state-default ui-corner-all");
			parent.append("<span id='labeltext"+$(element).attr('id')+"' style='float:left; font-size:"+$(element).css('font-size')+";'></span><span style='float:right;display:inline-block;' class='ui-icon ui-icon-triangle-1-s' ></span>");
	
			console.log('-----------------------');
			console.log('elto:'+$(element).attr('id')+'\nwidth: '+$(element).width()+'\ncssWidth:'+$(element).css('width'));
			console.log('parentW/cssW: '+parent.width()+'/'+parent.css('width'));
			console.log('eltoOW/parentOW:'+$(element).outerWidth()+'/'+parent.outerWidth());
			console.log('-----------------------');
	
			parent.after("<ul class=' ui-helper-reset ui-widget-content ui-helper-hidden' style='position:absolute;z-index:50;overflow:auto; max-height:200px; width:"+parent.css('width')+"; font-size:"+$(element).css('font-size')+";' ></ul>");
			$.each($(element).find("option"),function(){
				//20110510 Añadido cursor:default para que no salga el cursor de texto
				$(parent).next("ul").append("<li class='hover' selectValue='"+$(this).attr('value')+"' style='cursor:pointer;'>"+$(this).html()+"</li>");
				//20110510 Fragmento añadido para que los selects obedezcan al atributo "selected"
				//if ($(this).attr("selected")) {
				//	$("#labeltext").html($(this).html());
				//}
				//20110518 Fragmento eliminado pq de esto se encarga el evento change, pq lo disparamos al declararlo
			});
			$(parent).next("ul").find("li").click(function(){
				$("#labeltext"+$(element).attr('id')).html($(this).html());
				$(element).val($(this).attr('selectValue'));
				$(parent).click();//20110510 añadido para que los selects se cierren al escoger
				$(element).change();//20110704 añadido para al escoger se dispare el change del select
			});
			$(parent).click(function(event){
				$(this).next().slideToggle('fast');
				$(this).children('span').next().toggleClass('ui-icon-triangle-1-s');
				$(this).children('span').next().toggleClass('ui-icon-triangle-1-n');
				event.preventDefault();
			});
			//20110518 Fragmento añadido para q el select cambie de valor se cambia el select subyacente
			//NOTA, al cambiar por código el valor del select subyacente debemos disparar el evento change del select
			$(element).change(function(event){
				//alert($('option:selected',this).text());
				$("#labeltext"+$(element).attr('id')).html($('option:selected',this).text());
			}).change();
			$(element).addClass("ui-helper-hidden");
		},
		*/
		captcha:function() {
				var form=this.element;
				var no = Math.ceil(Math.random() * 4);
				var fieldset = jQuery("<fieldset />")
				.addClass("ui-widget-content");
				var legend = jQuery("<legend />",{
					text:this.options.captcha.title
				}).addClass("ui-widget-header ui-corner-all");
				var drag = jQuery("<div />",{
					css:{
						width:20,height:20,
						margin:10,textAlign:'center',
						cursor:'pointer',
						float:'left'
					}
				}).addClass("ui-state-default ui-corner-all drag");
				var dropZone = jQuery("<div />",{
					id:form.attr('id')+'_droppable',
					text:"Arrastra el "+no+" aquí",
					css:{width:150,height:50,fontWeight:'bold',textAlign:'center',float:'right'}
				}).addClass('ui-state-default ui-corner-all');
				var clear = jQuery("<div />",{
					css:{
						clear:'both'
					}
				});
				var submitsZone = jQuery('<div class="submitsZone" />');
				var captcha = jQuery('<div />').addClass('captcha');
	
				var submits = form.find(":reset,:submit");
				$.each(submits,function(){
					submitsZone.append($(this));
				});
	
				captcha.append(clear);
				for(var i=1;i<5;i++) {
					captcha.append(drag.clone().html(i).attr("id",form.attr('id')+'_'+i));
				}
				captcha.append(dropZone);
				captcha.append(clear.clone());
				captcha.append(submitsZone);
	
				if (this.options.captcha.fieldset) {
					fieldset.append(legend);
					fieldset.append(captcha);
					form.find("fieldset:last").after(fieldset);
				} else {
					var title = jQuery('<div />',{
						text:this.options.captcha.title
					});
					captcha.prepend(title);
					form.find("fieldset:last").append(captcha);
				}
	
				$(".drag").draggable({containment: 'parent'});
				$("#"+this.element.attr('id')+'_'+"droppable").droppable({
					accept:'#'+form.attr('id')+'_'+no,
					drop: function(event, ui) {
						$(this).addClass('ui-state-highlight').html("Muy bien!!, eres persona.");
						form.append('<input type="hidden" name="captcha" value="valido">');
						form.find(":submit").removeClass('ui-state-disabled').unbind('click');
						form.trigger('captchaSolved');
					}
				});
		},
		toWizard:{
			init:function(form, options) {
				//options = $.extend({  
				//	submitButton: ""
				//}, options);
				//var element = this;
				//var form = this.element;
				
				//var steps = $(form).children("fieldset");//cambiado find por children para que sea posible anidar fieldsets
				var steps = $(form).children("fieldset");
				var count = steps.size();
				//var submmitButtonName = "#" + this.options.submitButton;
				//$(submmitButtonName).hide();
		
				// 2
				//$(form).before('<ul id="'+form.attr('id')+'_steps" class="steps"></ul>');
				$(form).prepend('<ul id="'+form.attr('id')+'_steps" class="steps"></ul>');
		
				steps.each(function(i) {
					$(this).contents().not("legend").wrapAll('<div id="'+form.attr('id')+'_step' + i + '_scroll" style="height:'+options.wizard.height+'; overflow:auto;"></div>');
					$(this).wrap('<div id="'+form.attr('id')+'_step' + i + '"></div>');
					$(this).append('<p id="'+form.attr('id')+'_step' + i + 'commands" class="stepCommands"></p>');
					
					// 2
					var name = $(this).find("legend").html();
					$("#"+form.attr('id')+"_steps").append('<li id="'+form.attr('id')+'_stepDesc' + i + '">Paso ' + (i + 1) + '<span>' + name + '</span></li>');
					
					if (i == 0) {
						if (options.wizard.alwaysShowPrevNext) {
							createPrevButton(i,options.wizard.PrevText,false);
						}
						createNextButton(i,options.wizard.NextText,true);
					}
					else if (i == count - 1) {
						//$("#"+form.attr('id')+"_step" + i).hide();
						createPrevButton(i,options.wizard.PrevText,true);
						if(options.wizard.submitIsLastNext) {
							var submits = form.find(":reset,:submit");
							$.each(submits,function(){
								var stepName = form.attr('id')+"_step" + i;
								$("#" + stepName + "commands").append($(this));
							});
						} else {
							if (options.wizard.alwaysShowPrevNext) {
								createNextButton(i,options.wizard.NextText,false);
							}
						}
					}
					else {
						//$("#"+form.attr('id')+"_step" + i).hide();
						createPrevButton(i,options.wizard.PrevText,true);
						createNextButton(i,options.wizard.NextText,true);
					}
				});
				selectStep(0);
		
				function createPrevButton(i,text,enabled) {
					var stepName = form.attr('id')+"_step" + i;
					$("#" + stepName + "commands").append('<input type="button" id="' + stepName + 'Prev" value="'+text+'" />');
		
					if (enabled) {
						$("#" + stepName + "Prev").bind("click", function(e) {
							$("#" + stepName).hide();
							$("#"+form.attr('id')+"_step" + (i - 1)).show();
							//$(submmitButtonName).hide();
							selectStep(i - 1);
						});
					} else {
						$("#" + stepName + "Prev").addClass('ui-state-disabled');
					}
				}
		
				function createNextButton(i,text,enabled) {
					var stepName = form.attr('id')+"_step" + i;
					$("#" + stepName + "commands")
					.append('<input type="button" id="' + stepName + 'Next" value="'+text+'" />');
		
					if (enabled) {
						$("#" + stepName + "Next").bind("click", function(e) {
							$("#" + stepName).hide();
							$("#"+form.attr('id')+"_step" + (i + 1)).show();
							//if (i + 2 == count)
							//	$(submmitButtonName).show();
							selectStep(i + 1);
						});
					} else {
						$("#" + stepName + "Next").addClass('ui-state-disabled');
					}
				}
		
				function selectStep(i) {
					//$("#"+form.attr('id')+"_steps li").removeClass("current");
					//$("#"+form.attr('id')+"_stepDesc" + i).addClass("current");
					$("#"+form.attr('id')+"_steps li").addClass("ui-state-disabled");
					$("#"+form.attr('id')+"_stepDesc" + i).removeClass("ui-state-disabled")
					$(form).trigger('stepSelected',i);
				}
			},
			hideAllButCurrent: function (form) {
				var steps = $(form).find("fieldset");
				steps.each(function(i) {
					if (i!=0) {
						$("#"+form.attr('id')+"_step" + i).hide();
					}
				});
			}
		}//Fin objeto toWizard
	});
}(jQuery));