<?="\n<!-- ".get_class()." -->\n"?>
	<div>
		<div>
			<div style="width:40%; min-width: 320px; max-width: 768px; margin: auto; margin-top:37px; border:solid black 1px;">
				<div style="padding: 7px;  border:solid black 1px; background-color:#c0c0c0;">
					¡Operación no completada!
				</div>
				<div style="text-align:justify; padding:7px; ">
					Se ha producido un error mientras se realizaba la operación.<br />
					<?=$this->msg?>
				</div>
				<div style="text-align:center;">
					<a href="<?=BASE_DIR.FILE_APP?>">Volver al inicio</a>
				</div>
			</div>
		</div>
	</div>
<?="\n<!-- /".get_class()." -->\n"?>