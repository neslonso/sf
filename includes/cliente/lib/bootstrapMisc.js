//Biblioteca de pequeños plugins y funciones globales

function muestraMsgModalBootstrap3(title, msg) {
	var $div=$([
		'<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">',
		'	<div class="modal-dialog">',
		'		<div class="modal-content">',
		'			<div class="modal-header">',
		'				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>',
		'				<h4 class="modal-title">'+title+'</h4>',
		'			</div>',
		'			<div class="modal-body">'+msg+'</div>',
		'			<div class="modal-footer">',
		'				<button type="button" class="btn btn-default" data-dismiss="modal">Aceptar</button>',
		'			</div>',
		'		</div>',
		'	</div>',
		'</div>'
	].join(''));

	$('body').append($div);
	$div.modal({
		backdrop:'static'
	});
}
