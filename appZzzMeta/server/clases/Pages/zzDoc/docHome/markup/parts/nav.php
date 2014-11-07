<nav class="navbar navbar-inverse navbar-fixed-top yamm sobreSiguiente" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<button class="navbar-toggle" data-target="#navbar-collapse-1" data-toggle="collapse" type="button">
				<span class="icon-bar"></span><span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?=BASE_URL?>docHome" data-toggle="tooltip" data-placement="bottom" title="PHP from artisans to artisans">
				<span class="letterpress">
					S!nt@x<br />
				</span>
			</a>
		</div>
		<div class="navbar-collapse collapse" id="navbar-collapse-1">
			<ul class="nav navbar-nav">
				<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Módulos<span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li class="dropdown"><a href="<?=BASE_URL?>modulos#actions">actions</a></li>
						<li class="dropdown"><a href="<?=BASE_URL?>modulos#api">api</a></li>
						<li class="dropdown"><a href="<?=BASE_URL?>modulos#auto">auto</a></li>
						<li class="dropdown"><a href="<?=BASE_URL?>modulos#css">css</a></li>
						<li class="dropdown"><a href="<?=BASE_URL?>modulos#images">images</a></li>
						<li class="dropdown"><a href="<?=BASE_URL?>modulos#js">js</a></li>
						<li class="dropdown"><a href="<?=BASE_URL?>modulos#render">render</a></li>
					</ul>
				</li>
				<!-- Clases -->
				<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Clases<span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">PHP<span class="caret-right"></span></a>
							<ul class="dropdown-menu">
								<li><a href="<?=BASE_URL?>clasesPHP#Page" tabindex="-1"> Page</a></li>
								<li><a href="<?=BASE_URL?>clasesPHP#User" tabindex="-1"> User</a></li>
								<li><a href="<?=BASE_URL?>clasesPHP#MysqliDB" tabindex="-1"> MysqliDB/cDb</a></li>
								<li><a href="<?=BASE_URL?>clasesPHP#Fecha" tabindex="-1"> Fecha</a></li>
								<li><a href="<?=BASE_URL?>clasesPHP#Imagen" tabindex="-1"> Imagen</a></li>
							</ul>
						</li>
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">JS<span class="caret-right"></span></a>
							<ul class="dropdown-menu">
								<li><a href="<?=BASE_URL?>clasesJS#log" tabindex="-1"> Log </a></li>
								<li><a href="<?=BASE_URL?>clasesJS#fecha" tabindex="-1"> Fecha </a></li>
							</ul>
						</li>
					</ul>
				</li>
				<!-- Plugins -->
				<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Plugins<span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">JS<span class="caret-right"></span></a>
							<ul class="dropdown-menu">
								<li><a href="<?=BASE_URL?>js#DBdataTable" tabindex="-1"> DBdataTable</a></li>
								<li><a href="<?=BASE_URL?>js#pila" tabindex="-1"> Pila</a></li>
								<li><a href="<?=BASE_URL?>js#raty" tabindex="-1"> Raty</a></li>
								<li class="divider"></li>
							</ul>
						</li>
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">CSS<span class="caret-right"></span></a>
							<ul class="dropdown-menu">
								<li><a href="<?=BASE_URL?>css#ulMenu" tabindex="-1"> .ulMenu</a></li>
								<li><a href="<?=BASE_URL?>css#stdDataTable" tabindex="-1"> .stdDataTable</a></li>
								<li><a href="<?=BASE_URL?>css#cssCheckbox" tabindex="-1"> csscheckbox</a></li>
								<li><a href="<?=BASE_URL?>css#cssCheckbox" tabindex="-1"> yamm 3</a></li>
								<li class="divider"></li>
							</ul>
						</li>
					</ul>
				</li>
<?
$dir=RUTA_APP."server/clases/Pages/";
?>
				<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Pages<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li>
							<!-- Content container to add padding -->
							<div class="yamm-content">
								<div class="row">
									Pages de la aplicación (<?=str_replace($_SERVER['DOCUMENT_ROOT'].BASE_DIR, '', $dir);?>):
									<?$this->ulPages($dir)?>
								</div>
							</div>
						</li>
					</ul>
				</li>
				<!-- Tools -->
				<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Tools<span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="<?=BASE_URL?>creacion" tabindex="-1"> Creación </a></li>
						<li class="divider"></li>
						<li><a href="<?=BASE_URL?>phpinfo" tabindex="-1"> Phpinfo </a></li>
						<li><a href="<?=BASE_URL?>poster" tabindex="-1"> Poster </a></li>
						<li class="divider"></li>
						<li>
							<a href="<?=BASE_URL?>codes" tabindex="-1"> Usefull code fragments </a>
							simplexml_load_file("http://www.lipsum.com/feed/xml?amount=1&amp;what=paras&amp;start=0")->lipsum;
							<br />
							lorempixel
							<br />
							index.php.noCache.redirRaiz
							<br />
							.htaccess.deny.all
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</nav>
