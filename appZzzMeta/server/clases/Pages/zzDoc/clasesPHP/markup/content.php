<?="\n<!-- ".get_class()." -->\n"?>
<div class="fragment bajoAnterior">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-8">
				<div class="article">
					<div class="title">
						<a name="Page"></a>
						<strong class="embossed">IPage / Page</strong>
						<span>Interfaz de página y clase abstracta de página</span>
					</div>
					<div class="meta">Raiz de la jerarquia<span></span></div>
					<div class="body">
						<p>
							La clase <code>Page</code>, que implementa la interfaz <code>IPage</code>,
							es la raiz de la jerarquia de las clases de página, de ella descienden todas
							las clases que formarán las páginas de cada aplicación y sobreescribirán los
							metodos que	necesiten para realizar sus tareas.
						</p>
						<div class="col2">
							<?=$this->ulClass(SKEL_ROOT_DIR.'./includes/server/clases/Page.php','IPage')?>
							<?=$this->ulClass(SKEL_ROOT_DIR.'./includes/server/clases/Page.php','Page')?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!---->
<div class="fragment bajoAnterior">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-8">
				<div class="article">
					<div class="title">
						<a name="User"></a>
						<strong class="embossed">IUser / User</strong>
						<span>Interfaz de usuario y clase abstracta de usuario</span>
					</div>
					<div class="meta">Raiz de la jerarquia<span></span></div>
					<div class="body">
						<p>
							La clase <code>User</code>, que implementa la interfaz <code>IUser</code>,
							es la raiz de la jerarquia de las clases de usuario, de ella descienden todas
							las clases que representarán a los diversos tipos de usuarios que acceden
							a la aplicación y deben implementar las funciones que definen el nivel de acceso
							de cada tipo de usuario.
						</p>
						<div class="col2">
							<?=$this->ulClass(SKEL_ROOT_DIR.'./includes/server/clases/User.php','IUser')?>
							<?=$this->ulClass(SKEL_ROOT_DIR.'./includes/server/clases/User.php','User')?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!---->
<div class="fragment bajoAnterior">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-8">
				<div class="article">
					<div class="title">
						<a name="MysqliDB"></a>
						<strong class="embossed">MysqliDB/cDb</strong>
						<span>Clase de acceso a MySQL</span>
					</div>
					<div class="meta"><span></span></div>
					<div class="body">
						<p>
							La clase <code>MysqliDB</code> y la clase <code>cDb</code> (que extiende a la primera), proporcionan
							métodos para realizar el acceso a <em>MySQL</em>.
						</p>
						<div class="col2">
							<?=$this->ulClass(SKEL_ROOT_DIR.'./includes/server/clases/MysqliDB.php','MysqliDB')?>
							<?=$this->ulClass(SKEL_ROOT_DIR.'./includes/server/clases/MysqliDB.php','cDb')?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!---->
<div class="fragment bajoAnterior">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-8">
				<div class="article">
					<div class="title">
						<a name="Fecha"></a>
						<strong class="embossed">Fecha</strong>
						<span>Clase de tratamiento de fechas</span>
					</div>
					<div class="meta"><span></span></div>
					<div class="body">
						<p>
							La clase <code>Fecha</code> proporciona métodos para el trabajo con fechas.
						</p>
						<div class="col2">
							<?=$this->ulClass(SKEL_ROOT_DIR.'./includes/server/clases/Fecha.php','*')?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!---->
<div class="fragment bajoAnterior">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-8">
				<div class="article">
					<div class="title">
						<a name="Imagen"></a>
						<strong class="embossed">Imagen</strong>
						<span>Clase de tratamiento de imagenes</span>
					</div>
					<div class="meta"><span></span></div>
					<div class="body">
						<p>
							La clase <code>Imagen</code> proporciona metodos para el tratamiento de imagenes.
						</p>
						<div class="col2">
							<?=$this->ulClass(SKEL_ROOT_DIR.'./includes/server/clases/Imagen.php','*')?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?="\n<!-- /".get_class()." -->\n"?>
