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
				<div class="meta"><span>Qué</span>, donde y como</div>
				<div class="body">
					<p>
<?
	$excludingRexEx=array (
		"/",
		"vendor",
		"|",
		"aaReferences",
		"|",
		"(css|jsMin)\.(.+)\.(css|js)",
		"|",
		"zzWorkspace",
		"|",
		"google",
		"/"
	);
	$arr=self::path2array("./",implode('',$excludingRexEx));
	echo self::array2list($arr);
?>
					</p>
					<date>Creado: 13 de septiembre, 2014</date>
				</div>
			</div>
		</div>
		<div class="col-sm-2"></div>
	</div>
</div>