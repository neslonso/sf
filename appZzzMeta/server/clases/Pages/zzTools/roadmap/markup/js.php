<?if (false) {?><script><?}?>
<?="\n/*".get_class()."*/\n"?>
$(document).ready(function() {
	$('.attrInput').change(function (e) {
		var attrValue;
		switch($(this).attr('type')) {
			case 'text':attrValue=$(this).val();break;
			case 'checkbox':attrValue=$(this).prop('checked');break;
		}
		updateAttrValue($(this).data('xpath'),$(this).attr('name'),attrValue);
	});
});

function updateAttrValue (xpath,attrName,attrValue) {
		//activar overlay
		var $overlay = $('<div></div>').css({
			'position': 'fixed','top': '0','left': '0','width': '100%','height': '100%',
			'background-color': '#000',
			'filter':'alpha(opacity="50")','-moz-opacity':'0.5','-khtml-opacity': '0.5','opacity': '0.5',
			'z-index': '99999'
		});
		$overlay.appendTo(document.body);
		$.ajax({
			type: 'POST',
			url: '<?=BASE_URL.FILE_APP?>',
			data: {
				'MODULE':'actions',
				'acClase':'roadmap',
				'acMetodo':'acUpdateAttr',
				'acTipo':'ajaxAssoc',
				'acReturnURI':'',
				'xpath':xpath,
				'attrName':attrName,
				'attrValue':attrValue
			},
			success: function (data, textStatus, jqXHR) {
				/*console.log ('Callback: success');
				console.log ('jqXHR:')
				console.log (jqXHR);
				console.log ('data:')
				console.log (data);
				console.log ('textStatus: '+textStatus);*/
			},
			error: function (jqXHR, textStatus, errorThrwon) {
				console.log ('Callback: error');
				console.log ('jqXHR:')
				console.log (jqXHR);
				console.log ('textStatus: '+textStatus);
			},
			complete: function (jqXHR, textStatus) {
				/*console.log ('Callback: complete');
				console.log ('jqXHR:')
				console.log (jqXHR);
				console.log ('textStatus: '+textStatus);*/
				//Eliminar overlay
				$overlay.remove();
			},
			dataType: 'json'
		});
}