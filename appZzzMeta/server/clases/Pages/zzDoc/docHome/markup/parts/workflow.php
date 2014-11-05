<div class="container-fluid">
	<div class="row">
		<div class="col-sm-2"></div>
		<div class="col-sm-8">
			<div class="article">
				<div class="title">
					<a name="workflow"></a>
					<strong class="embossed">Workflow</strong>
					<span>MySQL Workbench</span>
				</div>
				<div class="meta"><span>Modelado</span>, <span>forward engineer</span> y sincronización</div>
				<div class="body">
					<p>
						Modelar nuestra base de datos con una herramienta CASE nos ofrece una serie de ventajas
						cuyo detalle queda fuera del ambito que nos ocupa, a continuación tan solo reflejar la
						configuración que utilizamos.
					</p>
					<p>
						Para configurar las opciones a usar en nuestros modelos accederemos al diálogo <code>Model Option</code>
						a través de la opción del mismo nombre en el menú <code>Model</code>.
						<br />
						En la pestaña <code>Model</code> utilizaremos los siguientes valores:
					</p>
					<div class="bs-callout bs-callout-info" style="overflow-x:auto;">
						<h4>Valores pestaña <em>Model</em></h4>
						<table class="MWconfig"><tr>
							<td class="option">PK Column Name:</td><td class="value">id</td>
							<td class="option">PK Column Type:</td><td class="value">INT</td>
						</tr><tr>
							<td class="option">Column Name:</td><td class="value">%table%col</td>
							<td class="option">Column Type:</td><td class="value">VARCHAR(255)</td>
						</tr><tr>
							<td class="option">FK Name:</td><td class="value">%dcolumn</td>
							<td class="option">Column Name:</td><td class="value">%column%%table|capitalize%</td>
						</tr><tr>
							<td class="option">ON UPDATE:</td><td class="value">NO ACTION</td>
							<td class="option">ON DELETE:</td><td class="value">NO ACTION</td>
						</tr><tr>
							<td class="option">Associative Table Name:</td><td class="value" colspan="99">%stable%VARIOS%dtable%</td>
						</tr></table>
					</div>
					<p>
						A mayores, en la pestaña <code>Model:MySQL</code> configuraremos <code>Target MySQL Version</code> con
						el número de versión de MySQL de nuestro servidor y como
						<code>Default Storage Engine</code> elegiremos <code>InnoDB</code>. Para descargar MySQL Workbench ve a
						<a href="http://dev.mysql.com/downloads/workbench/">Mysql Workbench downloads</a>.<br />
						Modelar con MySQL Workbench dará como resultado la creación/sincronización del esquema de la aplicación,
						base donde aplicar la herramienta de creación incorporada en <span class="skelName">S!nt@x</span> para comenza a crear las primeras
						páginas.
					</p>
				</div>
				<!---->
				<div class="title">
					<span>Tools/Creacion</span>
				</div>
				<div class="meta"><span>Blank, CRUD y DBdataTable</span></div>
				<div class="body">
					<p>
						Olvídate de picar una y otra vez listas interminables de campos, inputs y selects, crea prototipos rápidos
						y no vuelvas a empezar de cero.
					</p>
					<p>
						<span class="skelName">S!nt@x</span> incorpora una herramienta de creación de Pages, capaz de crear los ficheros que componen una Page y
						colocarlos en su lugar, dejando todo listo para comenzar a escribir el código. Está herramienta se encuentra
						en la URL <a href="./Creacion"><code>./Creacion</code></a> y permite la creación de tres tipos de páginas:
						Blank, CRUD y DBdataTable.<br />
						<strong>Blank</strong> es una plantilla de página en blanco, la herramienta creará el directorio de la Page,
						el fichero de definición de la clase y los ficheros de código de cliente (css, js y html).<br />
						<strong>CRUD</strong> (Create, Read, Update, Delete) es un formulario basado en <em>bootstrap</em>, que
						incorpora validación de datos mediante <em>bootstrap validator</em>. Para la creación de un CRUD será
						necesario seleccionar en que tabla de la base de datos debe basarse, así como que columnas deben ser excluidas
						del formulario y los validadores a aplicar a cada columna. Como parte de la creación de una Page CRUD,
						se creará una clase de acceso a datos para la tabla/entidad de la base de datos correspondiente y se almacenará
						en <code><?=RUTA_APP?>server/clases/Logic</code>.<br />
						<strong>DBdataTable</strong> es un vista de tabla de datos basada en <em>datatables</em> de JqueryUI. Para
						su creación será necesario seleccionar que columnas deben ser excluidas de la tabla. La tabla resultante esta
						basada en servidor, y recupera los datos de páginas y busquedas mediante AJAX. Como parte de la creación de una Page CRUD,
						se creará una clase de acceso a datos para la tabla/entidad de la base de datos correspondiente y se almacenará en
						<code><?=RUTA_APP?>server/clases/Logic</code>.<br />
					</p>
					<div class="bs-callout bs-callout-warning">
						<h4>Dependencias</h4>
						Cada uno de los tipos de página hace uso de bibliotecas y plugins a su propia discrección, por lo que deben
						estar incluidos en el proyecto, si no lo están la herramienta de creación creará una página de tipo Blank,
						que no depende de ninguna biblioteca ni plugin.
					</div>
				</div>
				<!---->
				<div class="title">
					<span>Page</span>
				</div>
				<div class="meta"><span>Clase</span> y ficheros anexos</div>
				<div class="body">
					<p>
						El resultado de la herramienta de creación es un directorio que contiene el fichero de definicion de una clase
						clase de página y los ficheros anexos a la misma.
					</p>
					<p>
						Cada página es una clase PHP que extiende directa o indirectamente a la clase abstracta <code>Page</code>, que
						implementa la interfaz <code>IPage</code>. Para facilitar la organización y extensibilidad de las páginas, es
						recomendable dividar los diversos elementos en diferentes ficheros, aunque esta estrcutura puede ser extendida
						o reducida de acuerdo con las necesidades de la	página en cuestión:
					</p>
					<div class="bs-callout bs-callout-info">
						<h4>Estructura de ficheros de una <em>Page</em></h4>
						<ul class="filesStruct">
							<li>
								samplePage/ <span><i class="fa fa-long-arrow-right"></i> Directorio raiz</span>
								<ul>
									<li>
										markup/ <span><i class="fa fa-long-arrow-right"></i> Directorio de ficheros de generación de codigo de cliente</span>
										<ul>
											<li>css.php <span><i class="fa fa-long-arrow-right"></i> Fichero de generación de CSS</span></li>
											<li>head.php <span><i class="fa fa-long-arrow-right"></i> Fichero de inyección de código en <code><?=htmlspecialchars('<head></head>')?></code></span></li>
											<li>js.php <span><i class="fa fa-long-arrow-right"></i> Fichero de generación de JS</span></li>
											<li>markup.php <span><i class="fa fa-long-arrow-right"></i> Fichero de generación de marcado HTML</span></li>
										</ul>
									</li>
									<li>
										samplePage.php <span><i class="fa fa-long-arrow-right"></i> <code>class samplePage extends Page implements IPage {...</code></span>
									</li>
								</ul>
							</li>
						</ul>
					</div>
					<p>
						Los ficheros anexos serán requeridos (vía <code>require</code>) por los diversos metodos de la clase de página,
						metodos que serán, a su vez, llamados por los diferentes módulos de <span class="skelName">S!nt@x</span> para preparar y emsamblar los
						tres tipos de codigo a enviar al cliente, HTML, CSS y JS.<br />
					</p>
					<div class="bs-callout bs-callout-info">
						<h4>Comonentes principales</h4>
						<span class="skelName">S!nt@x</span> se basa en 7 módulos principales, cada uno de ellos encargado de una tarea concreta:
						<em>actions</em>, <em>api</em>, <em>auto</em>, <em>css</em>, <em>images</em>, <em>js</em> y <em>render</em>
					</div>
				</div>
				<div class="meta">La interfaz <span>IPage</span></div>
				<div class="body">
					<p>
					 	La interfaz IPage define las funciones necesarias para la representación de la página y la implementación
					 	de su lógica de negocio.
					</p>
					<p>
						Todas las clases de página deben implementar la interfaz <code>IPage</code>, que incluye metodos para
						la validación de acceso y ejecución de código de servidor y la generación de las diversas partes del
						código de cliente (HTML, CSS, JS) y su inyección en las partes correspondientes del documento web
						resultante. Cualquier clase de página se puede extender con los metodos necesarios para implementar
						su lógica de negocio y suministrar datos a las vistas que genera.
					</p>
					<div class="bs-callout bs-callout-info">
						<h4>Intefaz <em>IPage</em></h4>
<pre style="white-space: pre-wrap;">
<?=Sintax\Pages\clasesPHP::ulClass(SKEL_ROOT_DIR.'./includes/server/clases/Page.php','IPage',true,false)?>
</pre>
					</div>
					<div class="col2">
						<strong>__construct:</strong> El constructor de una clase de  página puede recibir el parametro
						<code>$objUsr</code> con el fin de que toda la lógica de la página esté orientada en torno al
						usuario que la solicita. El constructor de la clase abstracta <code>Page</code> almacena el
						objeto de clase <code>Usuario</code> (o descendientes de la misma) recibido, en el atributo
						protegido <code>$objUsr</code>.<br />
						<strong>pageValida:</strong> Metodo destinado a comprobar si está permitido mostrar la página
						en función de la situación actual de la aplicación (login de usuario, permisos, origen del acceso...).
						Está función debe devolver true si el acceso está permitido o una cadena conteniendo el nombre de la
						página a la que se debe redireccionar en caso contrario.<br />
						<div class="bs-callout bs-callout-danger">
							<h4>Comprobación de la validez de la jerarquía</h4>
							Cuando una página extiende a otras, pageValida no debe devolver <code>true</code> a la ligera,
							ya que es posible que la cadena de páginas a la que la actual extiende tengan sus propios
							requisitos de validez, por lo que es recomendable devolver <code>parent::pageValida()</code>
						</div>
						<strong>accionValida:</strong> Este método recibe el nombre de un <em>metodo de acción</em> y debe
						devolver <code>true</code> o un <code>string</code> indicando el mensaje para el usuario en función
						de si la ejecución de la acción está permitida o no.<br />
						<strong>title:</strong> Devuelve el contenido de la etiqueta <code><?=htmlspecialchars('<title></title>')?></code>.<br />
						<strong>metaTags:</strong> El valor de retorno de este método es inyectado al principio del contenido de la etiqueta
						<code><?=htmlspecialchars('<head></head>')?></code>. Su tarea es generar las etiquetas
						<code><?=htmlspecialchars('<mata />')?></code> adecuadas para la página.<br />
						<strong>head:</strong> El valor de retorno de este método es inyectado al final del contenido de la etiqueta
						<code><?=htmlspecialchars('<head></head>')?></code>.<br />
						<strong>js:</strong> Este método es llamado por el modulo <em>js</em> y su valor de retorno incorporado al final,
						(junto con lás bibliotecas de cliente y plugins) de la recopilación de código javascript realizada por <span class="skelName">S!nt@x</span>
						con el fin de que solo sea necesaria una petición del cliente para conseguir todo el código javascript necesario
						para la página en cuestión. Debido a que el código se incorpora al final de la recopilación, es posible utilizar
						cualqiuer biblioteca de cliente o plugin de los incorporados mediante <code>./includes/server/clientLibs.php</code>.<br />
						<strong>css:</strong> Este método es llamado por el modulo <em>css</em> y su valor de retorno incorporado al final,
						(junto con lás bibliotecas de cliente) de la recopilación de código css realizada por <span class="skelName">S!nt@x</span>
						con el fin de que solo sea necesaria una petición del cliente para conseguir todo el código css necesario
						para la página en cuestión.<br />
						<div class="bs-callout bs-callout-warning">
							<h4>Head vs js vs css</h4>
							A la hora de incorporar recursos <em>javascript</em> y/o <em>css</em> a una página, cabe la posibilidad de hacerlo insertando etiquetas
							<code><?=htmlspecialchars('<script></script>')?></code> o <code><?=htmlspecialchars('<style></style>')?></code>
							mediante el metodo <code>head</code>. Debido a los mecanismos de cache de los navegadores, elegir un modo u otro
							de hacerlo tiene varias implicaciones. Se debe tener en cuenta:
							<ul>
								<li>
									El código incorporado mediante los modulos <em>js</em> o <em>css</em> es cacheado por el navegador,
									lo que acelera la aplicación pero dificulta el refresco del código si este cambia, por lo que se deben
									utilizar los modulos <em>js</em> o <em>css</em> para todo el código cuyo cambio no sea critico para
									el funcionamiento de la aplicación (99% del código).
								</li>
								<li>
									El código incorporado mediante head es refrescado cada vez que se recarga la página, por lo que no
									es recomendable para inyectar un gran volumen de código, ya que no es cacheado por el navegador,
									sino que es	adecuado para código que pudiera cambiar en cada carga de la página, como código dedicado
									a mostrar mensajes de estado o condiciones de error. Los mecanismos de <code>returnInfo</code> utilizan
									head para inyectar su código.
								</li>
							</ul>
						</div>
						<strong>markup:</strong> El valor de retorno de este método es utilizado por el módulo <code>render</code> e inyectado
						dentro de la etiqueta <code><?=htmlspecialchars('<body></body>')?></code>. Constituye todo el marcado de la página.<br />
					</div>
					<div class="bs-callout bs-callout-info">
						<h4><em>IPage</em> y estructura de ficheros</h4>
						Como hemos visto, los métodos de la interfaz <code>IPage</code> devuelven el código de las diversas partes de la página.
						Si bien todo esto puede ser realizado dentro del fichero de definición de la clase de página, por motivos de organización
						y limpieza de código, en la mayoría de los casos, los métodos de la interfaz <code>IPage</code> se limitarán a requerir
						(<code>require</code>) un fichero que devuelva el código correspondiente. Estos ficheros se encuentran ubicados en la carpeta
						<code>markup/</code> y tienen el mismo nombre que el método al que corresponden.
					</div>
					<date>Creado: 13 de septiembre, 2014</date>
				</div>

			</div>
		</div>
		<div class="col-sm-2"></div>
	</div>
</div>