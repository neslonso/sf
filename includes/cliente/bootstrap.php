<?
/*<!--bootstrap-->*/
?>
<link href="<?=PROTOCOL.":"?>//netdna.bootstrapcdn.com/bootstrap/<?=BOOTSTRAP?>/css/bootstrap.min.css" rel="stylesheet">
<script src="<?=PROTOCOL.":"?>//netdna.bootstrapcdn.com/bootstrap/<?=BOOTSTRAP?>/js/bootstrap.min.js"></script>
<?
/**/
?>
<?
/*<!--bootswatch-->*/

if (defined('BOOTSWATCH_THEME')) {
	if (BOOTSWATCH_THEME) {
?>
<link href="<?=PROTOCOL.":"?>//netdna.bootstrapcdn.com/bootswatch/<?=BOOTSTRAP?>/<?=BOOTSWATCH_THEME?>/bootstrap.min.css" rel="stylesheet">
<?
	}
}
/**/
?>
