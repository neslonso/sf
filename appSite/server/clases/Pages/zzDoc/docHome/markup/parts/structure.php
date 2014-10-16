<div class="container-fluid">
	<div class="row">
		<div class="col-sm-2"></div>
		<div class="col-sm-8">
			<div class="article">
				<div class="title">
					<a name="structure"></a>
					<strong class="embossed">Structure</strong>
					<span>Organización y ficheros</span>
				</div>
				<div class="meta">Estructura de <span class="skelName">S!nt@x</span> y <span>APPs</span></div>
				<div class="body">
					<p>
						<span class="skelName">S!nt@x</span> consta de varios directorios donde almacenar sus componentes y otros
						elementos comunes, así como los directorios de aplicaciones. Estos últimos
						contienen todas las páginas y lógica de cada aplicación y, aunque pueden tener
						cualquier nombre, se recomienda añadirles el prefijo <em>app</em>
					</p>
					<div class="bs-callout bs-callout-info">
						<h4><span class="skelName">S!nt@x</span></h4>
<pre><?=\Filesystem::array2list($arrSintaxFiles);?></pre>
					</div>
					<div class="col2">
						Espcial mención merece el directorio <code>appZzShared/</code>, que contiene el código de cliente
						(JS / CSS) y servidor (PHP) común a todas las aplicaciones. En la ruta
						<code>./appZzShared/server/sharedDefines.php</code>	se encuentran las constantes PHP globales a
						todas las aplicaciones.<br />
						El directorio <code>binaries/</code> esta destinado a contener todos los recursos binarios
						(imagenes, audio, video, documentos, ficheros subidos...) susceptibles de ser servidos
						directamente por el servidor web sin pasar por ninguno de los modulos de <span class="skelName">S!nt@x</span>.<br />
						<code>includes/</code> contiene los ficheros de configuración de <span class="skelName">S!nt@x</span>, así como todas
						las bibliotecas y clases de cliente y servidor, locales (<code>lib</code> y <code>clases</code>)
						y de terceros (<code>vendor</code>).<br />
						<code>zCache/</code> contiene los ficheros de recopilación de código JS y CSS. Estós ficheros son
						creados por los módulos <code>js</code> y <code>css</code> para poder entregar todo el código JS y
						CSS en respuesta a una sola petición del navegador.<br />
						<code>zzModules/</code> contiene los módulos princiaples de <span class="skelName">S!nt@x</span>.
					</div>
					<p>

					</p>
					<div class="bs-callout bs-callout-info">
						<h4>Esqueleto de aplicación</h4>
<pre><?=\Filesystem::array2list($arrAppFiles);?></pre>
					</div>
				</div>
				<!---->
				<div class="title">
					<span>Estructura lógica</span>
				</div>
				<div class="meta"><span>APPs</span> y puntos de entrada</div>
				<div class="body">
					<p>
						<span class="skelName">S!nt@x</span> se encuentra organizado mediante puntos de entrada que proporcionan acceso a las
						diversas aplicaciones de un proyecto. Estos puntos de entrada deben estar asociados a la
						ruta de	la <em>APP</em> a la que dan acceso y deben recibir como parametro el módulo que
						se desea ejecutar.
					</p>
					<p>
						Los puntos de entrada son ficheros <code>.php</code> almacenados en el raiz de la estrcutura
						de ficheros de <span class="skelName">S!nt@x</span>, pueden tener cualquier nombre, ser el índice el directorio
						(<a href="httpd.apache.org/docs/current/mod/mod_dir.html#DirectoryIndex">DirectoryIndex</a>)
						y su URL puede ser reescrita para hacerla más amigable. Todos los puntos de entrada son ficheros
						idénticos y su función principal es la de enrutar hacia una u otra aplicación de las registradas
						en el array constante <code>APPS</code> del fichero <code>./includes/server/start.php</code>
					</p>
					<div class="bs-callout bs-callout-danger">
						<h4>Array APPS</h4>
						<p>
							El array <code>APPS</code> define la correspondencia entre puntos de entrada y rutas de
							<em>APPs</em>. Cada punto de entrada debe definir al menos los valores <code>FILE_APP</code>
							y <code>RUTA_APP</code>, que serán convertidos a las constantes <code>FILE_APP</code>
							y <code>RUTA_APP</code> que quedarán disponibles para su uso en la APP.
						</p>
<pre>
define ('APPS', serialize(array(
	'index.php' => array(
		'FILE_APP' => 'index.php',
		'RUTA_APP' => './appSite/',
		'NOMBRE_APP' => 'Sitio web',
	),
)));
</pre>
					</div>
					<div class="bs-callout bs-callout-info">
						<h4>Código de un punto de entrada</h4>
<pre>
try {
	require_once "./includes/server/start.php";
	$module=(isset($_REQUEST['MODULE']))?strtolower($_REQUEST['MODULE']):"render";
	$arrModules=unserialize(MODULES);
	if (array_key_exists($module,$arrModules)) {
		require $arrModules[$module];
	} else {
		throw new Exception("El módulo '".$module."' no se encuentra.", 1);
	}
} catch (Exception $e) {
	$infoExc="Excepcion de tipo: ".get_class($e).". Mensaje: ".$e->getMessage().
		" en fichero ".$e->getFile()." en linea ".$e->getLine();
	error_log ($infoExc);
	header('HTTP/1.1 500 Internal Server Error',true,500);
}
</pre>
					</div>
				</div>
				<!---->
				<div class="title">
					<span>Módulos</span>
				</div>
				<div class="meta">Propósito y uso de los <span>módulos</span></div>
				<div class="body">
					<p>
						<span class="skelName">S!nt@x</span> funciona mediante un conjunto de módulos encargados cada uno de una determinada tarea. Los módulos son
						utilizados a través de los puntos de entrada de las aplicaciones. Cada vez que un punto de entrada es accedido,
						puede recibir un párametro <code>MODULE</code> via GET o POST, indicando el módulo que debe atender la petición.
					</p>
					<div class="col2">
						<span class="skelName">S!nt@x</span> consta de los siguientes módulos:
						<ul>
							<li>
								<a href="<?=BASE_URL?>modules/#actions">actions</a>: Encargado de enrutar las peticiones
								hacía los métodos de las clases de página que ejecutarán la lógica correspondiente a la acción
								invocada por el usuario, debe recibir al menos los parámetros POST
								<code>acClase</code>, <code>acMetodo</code>, <code>acTipo</code> y <code>acReturnURI</code>, además
								de los parámetros propios de la acción requerida.
							</li>
							<li>
								<a href="<?=BASE_URL?>modules/#api">api</a>: Interfaz de comunicación con aplicaciones de terceros,
								debe recibir al menos el parámetro <code>service</code>, en función del cual determinará las acciones
								a ejecutar.
							</li>
							<li>
								<a href="<?=BASE_URL?>modules/#auto">auto</a>: Módulo de tareas programadas, destinado a ejecutarse
									periodicamente y llamar a las acciones especificadas en el fichero de
									configuración de tareas	programadas de la aplicación. El fichero de configuración de tareas
									programadas se encuentra en <code>./server/appAuto.php</code>.<br />
								<div class="bs-callout bs-callout-danger" style="display: inline-block">
									<h4>Módulo <em>auto</em> y cron</h4>
									Cuando es ejecutado por primera vez, el módulo <code>auto</code>, se añade a si mismo
									al crontab del usuario que lo ejecuta, por lo que es necesaria una primera ejecucción
									manual del módulo para cada <em>APP</em>.
								</div>
							</li>
							<li>
								<a href="<?=BASE_URL?>modules/#css">css</a>: Recopilador de código css, llamado via etiqueta link desde
								el marcado generado por el módulo <span class="skelName">render</span>. Este módulo transforma el código
								<code>css</code> leído desde servidores remotos para ajustar las URLs relativas.
							</li>
							<li>
								<a href="<?=BASE_URL?>modules/#images">images</a>: Proveedor de imagenes desde diferentes
								almacenes (LOREMPIXEL, DB, Directorio...). Las rutas de servidor reales no quedan expuestas al cliente.
							</li>
							<li>
								<a href="<?=BASE_URL?>modules/#js">js</a>: Recopilador de código javascript, llamado via etiqueta script
								desde el marcado generado por el módulo <span class="skelName">render</span>.
							</li>
							<li>
								<a href="<?=BASE_URL?>modules/#render">render</a>: Renderer de páginas, recibe un parámetro GET
								llamado <code>page</code>, instancia la clase correspondioente para el usuario de la session
								y lanza los metodos de render del objeto Page (title, metaTags, head y markup).
							</li>
						</ul>
						<br /><br /><br />
					</div>
					<date>Creado: 13 de septiembre, 2014</date>
				</div>
			</div>
		</div>
		<div class="col-sm-2"></div>
	</div>
</div>