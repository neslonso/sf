<?="\n<!-- ".get_class()." -->\n"?>
<h1>ROADMAP</h1>
<hr />
<div class="versions">
<?
$arrColorClases=array('verde','azul','crema');
$i=0;
foreach ($dDocRoadmap->getElementsByTagName('version') as $dNodeVersion) {
	$sXmlEltoVersion=simplexml_import_dom($dNodeVersion);
?>
	<div class="version <?=$arrColorClases[$i % (count($arrColorClases))]?>">
		<div class="name">
			<?=$sXmlEltoVersion->name?>
		</div>
		<div class="features">
<?
	foreach ($dNodeVersion->getElementsByTagName('feature') as $dNodeFeature) {
		$sXmlEltoFeature=simplexml_import_dom($dNodeFeature);
?>
			<div class="feature">
				<div class="name" title="">
					<?=$sXmlEltoFeature->name?>
				</div>
				<div class="description">
					<?=trim($sXmlEltoFeature->description)?>
				</div>
				<div class="controls">
<?
		foreach ($dNodeFeature->attributes as $attr) {
			$attrName = $attr->nodeName;
    		$attrValue = $attr->nodeValue;
			switch ($attrValue) {
				case 'true':
				case 'false':
					$checked=($attrValue=='true')?'checked="checked"':'';
					$xhtmlCode='<label>'.$attrName.'</label>: <input class="attrInput" type="checkbox" id="'.$dNodeFeature->getNodePath().'/@'.$attrName.'" name="'.$attrName.'" data-xpath="'.$dNodeFeature->getNodePath().'" value="true" '.$checked.' />';
					break;
				default:
					$xhtmlCode='<label>'.$attrName.'</label>: <input class="attrInput" type="text" id="'.$dNodeFeature->getNodePath().'/@'.$attrName.'" name="'.$attrName.'" data-xpath="'.$dNodeFeature->getNodePath().'" value="'.$attrValue.'" />';
					break;
			}
?>
					<?=$xhtmlCode?>
<?
		}
?>
				</div>
			</div>

<?
	}
?>
		</div>
	</div>
<?
	$i++;
}
?>
</div>
<?="\n<!-- /".get_class()." -->\n"?>
