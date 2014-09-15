<div class="container-fluid">
	<div class="row">
		<div class="col-sm-2"></div>
		<div class="col-sm-8">
			<div class="article">
				<div class="title">
					<a name="quickstart"></a>
					<strong class="embossed">Quickstart</strong>
					<span>Preparación</span>
				</div>
				<div class="meta"><span>Ficheros</span> y estructura básica</div>
				<div class="body">
					<p>
						Descarga el código desde <a href="#" onclick="return scrollToName('download');">aqui</a>, descomprímelo un en directorio accesible de tu servidor web
						y edita el fichero de marcado de la página principal para comenzar.
					</p>
					<p>
						Descarga S!nt@x en un paquete zip, dentro encontrarás una estructura de ficheros que debe ir colocada en el
						directorio raiz de tu proyecto. El único requisito es que este directorio raiz sea accesible mediante Apache,
						el funcionamiento de S!nt@x es independiente de su ruta y no necesita ninguna configuración fuera del directorio
						donde se instale.
						La página principal de cada aplicación se llama <code>Home</code>, y se encuentra almacenada en el directorio
						<code><?=RUTA_APP?>server/clases/Pages/Home/</code>. A la página principal le correspoden
						las URLs <code>./</code>, <code>./Home</code> o <code>./?page=Home</code>. El fichero de marcado
						se encuentra dentro del directorio de la página, en <code>./markup/markup.php</code>
					</p>
					<div class="bs-callout bs-callout-danger">
						<h4>Ruta en .htaccess</h4>
						<p>
							El fichero .htaccess del directorio raiz de S!nt@x necesita conocer la ruta donde está instalado, relativa
							al raiz del servidor web. Si instalas S!nt@x en un directorio diferente del raiz de tu servidor web, será
							necesario que edites el fichero .htaccess y cambies la siguiente línea para que refleje tu configuración actual:
							<pre>RewriteBase /</pre>
						</p>
					</div>
					<div class="bs-callout bs-callout-info">
						<h4>Estructura de ficheros</h4>
						<p>
							S!nt@x se divide en <code>APPs</code> y cada una de ellas en <code>Pages</code>. Cada <code>Page</code>
							se compone de uno o varios ficheros y se almacena en un directorio con el mismo nombre que la Page.<br />
							Cada APP contiene un directorio Pages destinado a contener el directorio de cada Page.
							Cada Page consiste en al menos un fichero que define una clase PHP. Es recomendable que la clase, el fichero que la
							contiene y la carpeta donde se almacena tengan el mismo nombre.
						</p>
					</div>
				</div>
				<!---->
				<div class="title">
					<span>MySQL</span>
				</div>
				<div class="meta">Conexión a <span>MySQL</span>. La clase <span>mysqliDB</span></div>
				<div class="body">
					<p>
						S!nt@x proporciona clases para la conexión a MySQL, tanto para su uso mediante diversas instancias, como para su uso
						mediante el patrón <strong>Singleton</strong>. Ambos metodos son útiles dependiendo de la naturaleza del proyecto a
						desarrollar y se encuentran implementados como una jerarquia de clases.
					</p>
					<p>
					S!nt@x proporciona una clase de acceso a datos para MySQL llamada <code>mysqliDB</code>, que extiende a la clase nativa
					<code>mysqli</code>. A su vez, esta clase es extendida por la clase <code>cDb</code>, que implementa el patron
					<strong>Singleton</strong>. La implementación <strong>Singleton</strong> de la clase <code>cDb</code> debe ser inicializada
					antes de su uso, mediante una llamada a su metodo estatico <code>conf</code>. Este metodo tambien puede ser usado para
					desconectar de la base de datos activa y preparar la clase para una nueva conexión.
					</p>
					<div class="bs-callout bs-callout-info">
						<h4>Conexión a MySQL</h4>
						<p>
							Aunque se proporcionan clases para la conexión a MySQL, S!nt@x no realiza ninguna conexión, por lo que es
							necesario que las APPs realicen sus propias conexiones bien instanciando la clase <code>mysqliDB</code>
							directamente o bien mediante una llamada al metodo estático <code>conf</code> de la clase <code>cDb</code>
<pre>
$db=new mysqliDB ($host, $user, $pass, $db);
$mysqli_result=$db->query('SELECT * FROM tabla');
cDb::conf($host, $user, $pass, $db);
cDb::getInstance()->query('SELECT * FROM tabla');
	// o usando el alias gI:
	cDb::gI()->query('SELECT * FROM tabla');
</pre>
						</p>
					</div>
				</div>
				<!---->
				<div class="title">
					<span>Bibliotecas</span>
				</div>
				<div class="meta"><span>FirePHP, PHPMailer, JqueryUI, Bootstrap, Grid960</span> y plugins</div>
				<div class="body">
					<p>
						S!nt@x incorpora de serie varias bibliotecas PHP, Javascript y CSS, cargando desde sus respectivas CDNs
						aquellas que disponen de ella. Decide cuales quieres incluir en tu proyecto.
					</p>
					<div class="col2">
						La inclusion o exclusión de bibliotecas de servidor no afectará en gran medida al rendimiento de tu proyecto,
						(no es necesario enviarlas por la red) especialmente si no se trata de gran cantidad de ellas o si su volumen
						no es muy muy grande, por lo que S!nt@x simplifica el proceso de elección cargando unas pocas bibliotecas de
						<i>amplio espectro</i>, como PHPMailer. No obstante, la carga de las bibliotecas de servidor se regula a través
						del fichero <code>./includes/server/serverLibs.php</code>, compuesto unicamente de sentencias requiere.<br />

						En cuanto a bibvliotecas de cliente, la carga de las principales se regula desde el fichero
						<code>./includes/server/clientLibs.php</code> por medio de diversas constantes. Es recomendable que
						revises las bibliotecas que quieres utilizar en las aplicaciones de tu proyecto y sus versiones.
					</div>
					<div class="bs-callout bs-callout-warning">
						<h4>Dependencias de bibliotecas principales</h4>
						<p>
							Una biblioteca no se incluirá en el proyecto si depende de otra biblioteca no incluida, aunque
							existan constantes independientes para la inclusión de ambas, por ejemplo, Bootstrap no se incluirá
							si no se ha incluido Jquery.
						</p>
					</div>
					<div class="bs-callout bs-callout-info">
						<h4>Estructura de ficheros</h4>
						<p>
							Las constantes de las bibliotecas se utilizan para incluir o excluir distintos ficheros que contienen
							las URLs y rutas de cada biblioteca y los plugins que dependen de ella. Estos ficheros se encuentran en
							el directorio <code>./includes/cliente/</code>.
						</p>
					</div>
				</div>
				<!---->
				<div class="title">
					<span>Convenciones</span>
				</div>
				<div class="meta"><span>Nomenclatura</span>, <span>campos</span>, <span>ficheros</span> y <span>clases</span></div>
				<div class="body">
					<p>
						La metodología de trabajo detrás de S!nt@x (nesWork) se apoya en varias convenciones de nomeclatura de campos
						de base de datos, clases de acceso a datos, clases de página, constantes, variables y ficheros.
					</p>
					<p>
						Siguiendo las convenciones de nomeclatura conseguiremos tener que utilizar menos identificadores y más cortos,
						código más autodocumentado y una mayor facilidad a la hora de orientarnos y refactorizar y mantener nuestro código.
						Estas convenciones abarcan desde el nombrado de tablas y campos de la base de datos, hasta el nombre de los
						ficheros que compondrán cada clase de página (Page) de nuestras APPs.
					</p>
					<div class="bs-callout bs-callout-info">
						<h4>Convenciones de nomenclatura</h4>
						<ul>
							<li>Generales
								<ul>
									<li>Nombres comienzan en minuscula, las mayusculas o números separan palabras y/o conceptos</li>
								</ul>
							</li>
							<li>Base de datos
								<ul>
									<li>Nombres de tablas en singular y en minusculas (cliente no clientes)</li>
									<li>Clave primaria llamada id</li>
									<li>Claves foraneas id+[nombreTablaDestino]</li>
									<li>Nombres de tablas relacciones varios a varios [nombreTabla]VARIOS[nombreTabla]</li>
								</ul>
							</li>
							<li>Código
								<ul>
									<li>Nombres de clases comienzan en mayuscula</li>
									<li>Nombres de constantes todo mayusculas, el guión bajo ("_") separa palabras y/o conceptos</li>
									<li>
										Las clases de acceso a datos se llamarán igual que la tabla correspondiente de la base de datos
										(comenzando en mayuscula)
									</li>
									<li>
										Los nombres de métodos de acción de las clases de página comenzaran por "ac", seguido del nombre
										con la primera letra en mayusculas, por ejemplo <code>acGrabr</code>, <code>acBorrar</code>)
									</li>
								</ul>
							</li>
							<li>Ficheros
								<ul>
									<li>
										El fichero de definición de una clase de página se llamara igual que la clase con la extensión "php"
										e igual que el directorio que lo contiene, por ejemplo, <code>lsCLientes/</code>, <code>lsClintes.php</code>
										y <code>class LsCLientes extends ...</code>
									</li>
								</ul>
							</li>
						</ul>
					</div>
					<div class="bs-callout bs-callout-info">
						<h4>Campos</h4>
						<ul>
							<li>
								Todas las tablas contendrán los campos id (clave primaria), insert (momento del insert del registro)
								y update (momento del último update del registro). La creación de una tabla puede empezar a partir de
								la siguiente plantilla:
<pre>CREATE TABLE IF NOT EXISTS `[NOMBRE_SCHEMA]`.`[NOMBRE_TABLE]` (
	`id` INT NOT NULL,
	`insert` TIMESTAMP NULL DEFAULT NULL,
	`update` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)
ENGINE = InnoDB</pre>
							</li>
						</ul>
					</div>
					<date>Creado: 13 de septiembre, 2014</date>
				</div>
			</div>
		</div>
		<div class="col-sm-2"></div>
	</div>
</div>