<?
/*<!--font awesome-->*/
/**
 * TODO: Mejora: https, regex.
 * El css de font-awesome carga recursos mediante http, que serán bloqueados por el navegador si PROTOCOL es https.
 * Quiza se pueda solventar reescribiendo las url en el módulo CSS para que usen https cuando PROTOCOL valga https
 */
?>
<link href="<?=PROTOCOL.":"?>//netdna.bootstrapcdn.com/font-awesome/<?=FONT_AWESOME?>/css/font-awesome.min.css" rel="stylesheet">
<?
/**/
?>
