<?="\n<!-- ".get_class()." -->\n"?>
<div class="fragment bajoAnterior">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-8">
				<div class="article">
					<div class="title">
						<a name="actions"></a>
						<strong class="embossed">Actions</strong>
						<span>Módulo de acciones de servidor</span>
					</div>
					<div class="meta">Funciones y <span>parámetros</span></div>
					<div class="body">
						<p>
							El módulo <code>actions</code> se encarga de enrutar las peticiones realizadas desde las páginas
							de las diferentes aplicaciones hacia los metodos que ejecutan la lógica correspondiente.
						</p>
						<div class="col2">
							<span class="skelName">S!nt@x</span> implementa el patrón
							<a href="http://es.wikipedia.org/wiki/Post/Redirect/Get">PRG</a> mediante el módulo actions, encargado
							de llamar al metodo correspondiente a la acción que se deseea ejecutar y realizar una redirección,
							evitando de este modo reenvios de formularios o avisos relativos al reenvio de datos por parte del
							agente de usuario (navegador). El módulo actions se encuentra estrechamente ligado al método
							<code>accionValida</code> de la interfaz <code>IPage</code>, ya que para que un método sea invocado
							por el módulo <code>actions</code>, la función accionVálida debe delvolver <code>true</code> para
							ese método en particular.<br />
							El módulo actions debe recibir al menos los siguientes parámetros:
							<ul>
								<li>
									<code>acClase</code> (POST): Nombre de la clase que se encargará de responder a la acción.
								</li>
								<li>
									<code>acMetodo</code> (POST): Nombre del metodo que contiene la lógica de la acción.
								</li>
								<li>
									<code>acTipo</code> (POST): Tipo de acción, define como se debe llamar al método encargado de
									responder a la acción y si se debe o no redirigir el navegador a una nueva URL para
									evitar que el navegador registre en su historial una URL resultado de una petición POST y
									garantizar que la acción no vuelva a ser ejecutada al recorrer el historial del navegador.
									Debe ser uno de los siguientes valores:
									<ul>
										<li><code>std</code>: Los parametros se pasan al metodo uno por uno, cuando la acción termina se
											redirige el navegador a <code>acReturnURI</code></li>
										<li><code>ajax</code>: Los parametros se pasan al metodo uno por uno y cuando la acción termina se
											devuelve un objeto <em>JSON</em> con la siguiente estrcutura:
											<ul>
												<li><code>exito</code>: Boolean que indica si la acción se completó o no.</li>
												<li><code>msg</code>: Cadena Vacia si exito es <code>true</code>, mensaje de error si exito es <code>false</code></li>
												<li><code>data</code>: valor devuelto por la acción si exito es <code>true</code>, cadena Vacia si exito es <code>false</code></li>
											</ul>
										</li>
										<li><code>plain</code>: Los parametros se pasan al metodo uno por uno, cuando la acción termina no hace nada más</li>
										<li><code>stdAssoc</code>, ajaxAssoc o plainAssoc: iguales que sus equivalentes "no Assoc", con la salvedad de que el método
											es llamado con un solo parametro, un array asociativo que contiene a todos los parametros.</li>
									</ul>
								</li>
								<li>
									<code>acReturnURI</code> (POST): URL a la que redirigir el navegador una vez concluida la acción. El método encargado
									del tratamiento de la acción puede cambiar este valor dependiendo del resultado de su ejecución.
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-2"></div>
		</div>
	</div>
</div>
<!---->
<div class="fragment sobreAnterior">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-8">
				<div class="article">
					<div class="title">
						<a name="api"></a>
						<strong class="embossed">Api</strong>
						<span>Módulo de comunicación</span>
					</div>
					<div class="meta">Funciones y <span>parámetros</span></div>
					<div class="body">
						<p>
							El módulo <code>api</code> se encarga exponer servicios disponibles para la comunicación con
							aplicaciones exteriores.
						</p>
						<div class="col2">
							El módulo <code>api</code> expone servicios, públicos o privados, orientados a su acceso por terceros.
							Este módulo debe ser llamado con los parametros <code>service</code> y <code>key</code>, siendo
							el primero obligatorio y el segundo optativo.
							Los servicios accesibles a través del módulo <code>api</code> son especificados en el fichero
							<code>./server/appApi.php</code> por medio de la constante <code>ARR_API_SERVICES</code> que contiene un
							array asociativo, donde cada elemento tiene por clave el valor del parametro <code>service</code>
							que debe ser usado para invocar el servicio en cuestión, y como valor, un array con los siguientes
							elementos:
							<ul>
								<li>
									<code>active</code>: Valor boolean que indica si el servicio puede ser ejecutado. Si su valor es <code>false</code>
									el servicio nunca será ejecutado y las llamadas realizadas devolveran un mensaje de error.
								</li>
								<li>
									<code>keys</code>: Un array que contiene las claves válidas para el acceso al servicio. Si la clave suministrada
									en el parametro <code>key</code> figura en este array, el servicio será ejecutado. Debe tenerse en
									cuenta que si este array no tiene elementos o alguna de las claves es igual a <code>""</code>
									(cadena vacia), cualquier key, o incluso la ausencia del parametro key, provocará la ejecución del
									servicio.
								</li>
								<li>
									<code>comando</code>: Cadena de caracteres que será pasada a la función <code>eval</code> para realizar
									la ejecución de las tareas asociadas al servicio.
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-2"></div>
		</div>
	</div>
</div>
<!---->
<div class="fragment sobreAnterior">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-8">
				<div class="article">
					<div class="title">
						<a name="auto"></a>
						<strong class="embossed">Auto</strong>
						<span>Módulo de tareas programadas</span>
					</div>
					<div class="meta">Funciones y <span>parámetros</span></div>
					<div class="body">
						<p>
							El módulo <code>auto</code> se encarga de realizar la ejecución de las tareas programadas
							de cada aplicación.
						</p>
						<div class="col2">
							El módulo <code>auto</code> se ejecuta cada minuto y revisa el array <code>ARR_CRON_JOBS</code>, definido
							en el fichero <code>./server/appAuto.php</code>, en busca de tareas programados que deban ser invocadas.
							Cada elemento del array <code>ARR_CRON_JOBS</code> es, a su vez, un array con la siguiente estructura:
							<ul>
								<li>
									<code>activado</code>: Valor boolean que indica si la tareadebe ser ejecutada. Si su valor es <code>false</code>
									la tarea nunca será ejecutada.
								</li>
								<li>
									<code>minuto</code>, <code>hora</code>, <code>diaMes</code>, <code>mes</code> y <code>diaSemana</code>:
									Valores de la expresión CRON que determinan en que momento(s) debe ser ejecutada la tarea, analizados
									mediante la fabulosa biblioteca <a href="https://github.com/mtdowling/cron-expression">cron-expression</a>
									de <a href=" http://mtdowling.com/">Michael Dowling</a>
								</li>
								<li>
									<code>comando</code>: Cadena de caracteres que será pasada a la función <code>eval</code> para realizar
									la ejecución de las tarea.
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-2"></div>
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
						<a name="css"></a>
						<strong class="embossed">Css</strong>
						<span>Módulo de empaquetado de CSS</span>
					</div>
					<div class="meta">Funciones y <span>parámetros</span></div>
					<div class="body">
						<p>
							El módulo <code>css</code> se encarga de recopilar y servir todo el código CSS necesario para
							cada página.
						</p>
						<div class="col2">
							El módulo <code>css</code> revisa el array <code>ARR_CLIENT_LIBS</code>, definido
							en el fichero <code>/includes/server/clientLibs.php</code>, que contiene las rutas de los
							ficheros que serán incorporados mediante <code>require</code> (el código de servidor
							que pudieran contener será ejecutado). Todas las etiquetas <code>&lt;link&gt;</code> contenidas
							en la salida de los ficheros referenciados en el array <code>ARR_CLIENT_LIBS</code> serán
							analizadas y el codigo al que hagan referencia incorporado como parte de la respuesta del módulo
							<code>css</code>. Para que sea posible hacer referencia a ficheros remotos y
							<a href="http://es.wikipedia.org/wiki/Red_de_entrega_de_contenidos">CDNs</a>, el módulo
							<code>css</code> se encarga de convertir las URLs relativas en absolutas. El módulo <code>css</code>
							debe recibir el parametro <code>page</code>, que contiene el nombre de la clase de página
							para la que se desea obtener el código CSS.
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-2"></div>
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
						<a name="images"></a>
						<strong class="embossed">Images</strong>
						<span>Módulo de obtención de imagenes</span>
					</div>
					<div class="meta">Funciones y <span>parámetros</span></div>
					<div class="body">
						<p>
							El módulo <code>images</code> es el encargado de suministrar imagenes
						</p>
						<div class="col2">
							El módulo <code>images</code> recibe los siguientes parámetros:
							<ul>
								<li>
									<code>almacen</code>: Cadena de caracteres. Nombre de una constante que alamcena la ruta de servidor hasta el
									directorio que contiene la imagen.
									<div class="bs-callout bs-callout-info">
										<h4>Almacenes especiales</h4>
										<p>
											El párametro almacen puede los siguientes valores especiales que identifican almacenes
											de imagnes predeterminados y modulan el significado del parametro fichero:
										</p>
										<ul>
											<li>
												LOREM_PIXEL: este valor indica que la imagen será obtenida del servicio ofrecido por
												el sitio web <a href="http://lorempixel.com/">lorempixel.com</a>. En este caso,
												el parámetro fichero especifica el parametro categoría del mencionado servicio
											</li>
											<li>
												DB: este valor indica que la imagen será extraida de base de datos. El parametro
												fichero contendra la especificación de los parametros necesario para la consulta,
												concatenados y separados por un el caracter "<code>.</code>"
												(<code>tabla.campoId.valorID.cmapoData</code>):
												<ul>
													<li>tabla: nombre de la tabla que contiene la imagen.</li>
													<li>campoId: nombre de campo que es clave primaria de la tabla.</li>
													<li>valorID: valor de la clave primaria para la imagen deseada.</li>
													<li>campoData: nombre del campo que contiene los datos de la imagen.</li>
												</ul>
											</li>
										</ul>
									</div>
								</li>
								<li>
									<code>fichero</code>: Cadena de caracteres. Su significado depende del valor del parametro <code>almacen</code>.
									Si el parametro <code>almacen</code> no hace referencia a ninguno de los almacenes especiales mencionados, este
									parametro contendrá el nombre del fichero de la imagen deseada.
								</li>
								<li>
									<code>ancho</code> y <code>alto</code>: Enteros. Dimensiones en pixeles a las que se desea recuperar la imagen.
								</li>
								<li>
									<code>modo</code>: Entero. Valor que indica el tratamiento que se desea aplicar a la imagen, uno de los siguientes:
									<ul>
<?
										$broker = new TokenReflection\Broker(new TokenReflection\Broker\Backend\Memory());
										$broker->processFile('./includes/server/clases/Imagen.php');
										foreach ($broker->getClasses() as $nombreClase => $objReflectionClass) {
											foreach ($objReflectionClass->getConstantReflections() as $objReflectionConstant) {
												$objReflectionAnnotation=new TokenReflection\ReflectionAnnotation($objReflectionConstant,$objReflectionConstant->getDocComment());
												echo "<li>".$objReflectionConstant->getShortName().": ".
													$objReflectionAnnotation->getAnnotation(' short_description')
												."</li>";
											}
										}
?>
									</ul>
									Si no se especifica se utilizará <code>OUTPUT_MODE_SCALE</code>
								</li>
								<li>
									<code>formato</code>: Cadena de caracteres. Valor que indica el formato en el que se desea la imagen,
									puede valer <code>gif</code>, <code>jpg</code>, <code>png</code> o <code>wbmp</code>. Si no se especifica
									se utilizará <code>png</code>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-2"></div>
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
						<a name="js"></a>
						<strong class="embossed">Js</strong>
						<span>Módulo de empaquetado de JS</span>
					</div>
					<div class="meta">Funciones y <span>parámetros</span></div>
					<div class="body">
						<p>
							El módulo <code>js</code> se encarga de recopilar y servir todo el código JS necesario para
							cada página.
						</p>
						<div class="col2">
							El módulo <code>js</code> revisa el array <code>ARR_CLIENT_LIBS</code>, definido
							en el fichero <code>/includes/server/clientLibs.php</code>, que contiene las rutas de los
							ficheros que serán incorporados mediante <code>require</code> (el código de servidor
							que pudieran contener será ejecutado). Todas las etiquetas <code>&lt;script&gt;</code> contenidas
							en la salida de los ficheros referenciados en el array <code>ARR_CLIENT_LIBS</code> serán
							analizadas y el codigo al que hagan referencia incorporado como parte de la respuesta del módulo
							<code>js</code>. Debe recibir el parametro <code>page</code>, que contiene el nombre de la
							clase de página para la que se desea obtener el código.
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-2"></div>
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
						<a name="render"></a>
						<strong class="embossed">Render</strong>
						<span>Módulo de representación de páginas</span>
					</div>
					<div class="meta">Funciones y <span>parámetros</span></div>
					<div class="body">
						<p>
							El módulo <code>render</code> es el encargado de llamar a los metodos
							correspondientes de la clase de página solicitada para obtener el
							marcado necesario para su representación en el navegador.
						</p>
						<div class="col2">
							El módulo render debe recibir el parametro <code>page</code>, que
							contiene el nombre de la clase de página para la que se desea obtener
							el código. La clase de página deberá extender a las clase abstracta
							<code>Page</code> e implementar la interfaz <code>IPage</code> y, a
							través de los métodos de la interfaz <code>IPage</code>, devolver todo
							el marcado necesario para generar la página solicitada. En caso de no
							recibir el parametro <code>page</code>, se utilizará la clase de página
							<code>Home</code>.
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-2"></div>
		</div>
	</div>
</div>
<?="\n<!-- /".get_class()." -->\n"?>
