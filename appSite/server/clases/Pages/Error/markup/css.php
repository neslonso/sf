<?
/*COLORES DATATABLES*/
$GLOBALS['colores']['datatables']['colorPares']="#E1ECF6";
$GLOBALS['colores']['datatables']['colorImpares']="#91BBE0";
$GLOBALS['colores']['datatables']['colorParesHover']="#FF9932";
$GLOBALS['colores']['datatables']['colorImparesHover']="#FF9932";
$GLOBALS['colores']['datatables']['colorOrdenPares']=  array ('#92BADE','#4388C6','#26547D');//Colores de las columnas que participan en el orden por orden de seleccion
$GLOBALS['colores']['datatables']['colorOrdenImpares']=array ('#4189C9','#24547F','#0D2030');
$GLOBALS['colores']['datatables']['colorOrdenParesHover']=  array ('#FFB266','#FFCC99','#FFE5CC');//Colores de las columnas que participan en el orden por orden de seleccion
$GLOBALS['colores']['datatables']['colorOrdenImparesHover']=array ('#FFB266','#FFCC99','#FFE5CC');

$colorPares=$GLOBALS['colores']['datatables']['colorPares'];
$colorImpares=$GLOBALS['colores']['datatables']['colorImpares'];
$colorParesHover=$GLOBALS['colores']['datatables']['colorParesHover'];
$colorImparesHover=$GLOBALS['colores']['datatables']['colorImparesHover'];
$colorOrdenPares=$GLOBALS['colores']['datatables']['colorOrdenPares'];
$colorOrdenImpares=$GLOBALS['colores']['datatables']['colorOrdenImpares'];
$colorOrdenParesHover=$GLOBALS['colores']['datatables']['colorOrdenParesHover'];
$colorOrdenImparesHover=$GLOBALS['colores']['datatables']['colorOrdenImparesHover'];
?>
<?if (false) {//No sacamos la etiqueta, solo está para que los editores coloreen bien el fichero?><style><?}?>
<?="\n/*".get_class()."*/\n"?>
/*Standard datatables styles*/
.stdDataTable {cursor:pointer;}

/*
.stdDataTable tbody tr.even:hover, .stdDataTable tbody tr.even td.highlighted {background-color: <?=$colorPares?>;}
.stdDataTable tbody tr.odd:hover, .stdDataTable tbody tr.odd td.highlighted {background-color: <?=$colorImpares?>;}
*/

.stdDataTable tr.even {background-color:  <?=$colorPares?>;}
.stdDataTable tr.even td.sorting_1 {background-color: <?=$colorOrdenPares[0]?>;}
.stdDataTable tr.even td.sorting_2 {background-color: <?=$colorOrdenPares[1]?>;}
.stdDataTable tr.even td.sorting_3 {background-color: <?=$colorOrdenPares[2]?>;}
.stdDataTable tr.even:hover {background-color:  <?=$colorParesHover?>;}
.stdDataTable tr.even:hover td.sorting_1 {background-color: <?=$colorOrdenParesHover[0]?>;}
.stdDataTable tr.even:hover td.sorting_2 {background-color: <?=$colorOrdenParesHover[1]?>;}
.stdDataTable tr.even:hover td.sorting_3 {background-color: <?=$colorOrdenParesHover[2]?>;}

.stdDataTable tr.odd {background-color: <?=$colorImpares?>;}
.stdDataTable tr.odd td.sorting_1 {background-color: <?=$colorOrdenImpares[0]?>;}
.stdDataTable tr.odd td.sorting_2 {background-color: <?=$colorOrdenImpares[1]?>;}
.stdDataTable tr.odd td.sorting_3 {background-color: <?=$colorOrdenImpares[2]?>;}

.stdDataTable tr.odd:hover {background-color: <?=$colorImparesHover?>;}
.stdDataTable tr.odd:hover td.sorting_1 {background-color: <?=$colorOrdenImparesHover[0]?>;}
.stdDataTable tr.odd:hover td.sorting_2 {background-color: <?=$colorOrdenImparesHover[1]?>;}
.stdDataTable tr.odd:hover td.sorting_3 {background-color: <?=$colorOrdenImparesHover[2]?>;}
/**/
