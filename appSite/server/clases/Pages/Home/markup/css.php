<?if (false) {?><style><?}?>
<?="\n/*".get_class()."*/\n"?>
/* Nav & Yamm */
	/* Multi-level dropdowns */
	.caret-right {
		border-bottom: 4px solid transparent;
		border-top: 4px solid transparent;
		border-left: 4px solid;
		display: inline-block;
		height: 0;
		margin-left: 2px;
		vertical-align: middle;
		width: 0;
	}
	.dropdown .dropdown .caret-right {
		float:right;
		position: relative;
		top:-1em;
	}
	.dropdown .dropdown {
		position: relative;
	}
	.dropdown-menu .dropdown-menu {
		position:absolute;
		top:0px;
		left:100%;
	}
	/*Yamm open on hover: necesita que se elimine del marcado el data-toggle="dropdown", para que no queden abiertos al hacer click;
		ul.nav li.dropdown:hover > ul.dropdown-menu {
			display: block;
		}
	/**/
	.grid-demo [class*="col-"] {
		background-color: #e5e1ea;
		border: 1px solid #d1d1d1;
		font-size: 1em;
		line-height: 2;
		margin-bottom: 5px;
		margin-top: 5px;
		text-align: center;
	}
/**/
/* letterpress */
	.letterpress {
		font-size: 3.33em;
		text-shadow: 0px 2px 3px #555;
	}
/**/
/* embossed */
	.embossed {
		text-shadow: -1px -1px 1px #fff, 1px 1px 1px #000;
	}
/**/
/* Efecto cortina: Necesita que menu, header y content se coloquen a la altura adecuada en $(window).resize(); */
	/*.menu obsoleto, ahora lo tiene el .navbar de yamm
		.menu {
			position:fixed;
			z-index:30;
			width:100%;
		}
	/**/
	.mainLinks {
		position:fixed;
		z-index:30;
		width:100%;
		text-align:center;
	}
	.header {
		position:fixed;
		z-index:10;
		width:100%;
		height:250px;
	}
	.header:after {
		content: "";
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		opacity: 0.3;
		z-index: -1;
		background-size: cover;
		background-image: url(./<?=FILE_APP?>?MODULE=IMAGES&almacen=LOREMPIXEL&ancho=600&alto=250);
	}

	.content {
		position:relative;
		z-index: 20;
	}
	.fragment {
		background-color: #FFF;
		position: relative;
		z-index: 1;
	}
	.sobreSiguiente:after {
		display:block;
		content:"";
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		z-index: -1;
		box-shadow: 0px 2px 2px 1px black;
	}
	.sobreAnterior {
		box-shadow: 0px -2px 2px 1px black;
	}
	.bajoAnterior:before {
		display:block;
		content:"";
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 10px;
		z-index: 1030;
		box-shadow: 0px 8px 6px -6px black inset;
	}
	/*Usamos after para poder regular la opacidad del fondo sin afectar a la opacidad del contenido de .fragment*/
		.fragment:after {
			content: "";
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			opacity: 0.3;
			z-index: -1;
			background-size: cover;
			background-attachment: fixed;
		}
		.fragment:nth-child(n):after {
			background-image: url(./<?=FILE_APP?>?MODULE=IMAGES&almacen=LOREMPIXEL&ancho=600&alto=600&formato=jpg);
		}
		.fragment:nth-child(2n):after {
			background-image: url(./<?=FILE_APP?>?MODULE=IMAGES&almacen=LOREMPIXEL&ancho=600&alto=601&formato=jpg);
		}
		.fragment:nth-child(3n):after {
			background-image: url(./<?=FILE_APP?>?MODULE=IMAGES&almacen=LOREMPIXEL&ancho=600&alto=602&formato=jpg);
		}
	/**/
	.fragment .titulo {
		background-color: rgba(255,192,128,0.3);
		font-size: xx-large;
		text-align: center;
		height:150px;
	}
/**/
/* Ejemplo Tipografía */
	.article {
		width:90%;
		margin: auto;
	}
	.article .title{
		font-size: 2.5em;
		font-family: Verdana;
		letter-spacing: 0.1em;
		color: rgb(142,11,0);
		text-shadow: 1px 1px 1px rgba(255,255,255,0.6);
	}
	.article .title span{
		display: block;
		margin-top: 0.5em;
		font-family: Georgia;
		font-size: 0.6em;
		font-weight: normal;
		letter-spacing: 0em;
		text-shadow: none;
	}
	.article .meta{
		font-family: Georgia;
		color: rgba(69,54,37,0.6);
		font-size: 0.85em;
		font-style: italic;
		letter-spacing: 0.25em;
		border-bottom: 1px solid rgba(69,54,37,0.2);
		padding-bottom: 0.5em;
	}
	.article .meta span{
		text-transform: capitalize;
		font-style: normal;
		color: rgba(69,54,37,0.8);
	}
	.article .body>p,
	.article .body>.col2{
		font-family: Verdana;
		-moz-column-count: 2;
		-moz-column-gap: 3em;
		-webkit-column-count: 2;
		-webkit-column-gap: 3em;
		column-count: 2;
		column-gap: 3em;
		-moz-column-rule: solid #000 1px;
		-webkit-column-rule: solid #000 1px;
		column-rule: solid #000 1px;
		line-height: 1.5em;
		color: rgb(69,54,37);
	}
	.article .body>p:first-child,
	.article .body>.col2:first-child{
		font-size: 1.25em;
		font-family: Georgia;
		font-style: italic;
		-moz-column-count: 1;
		-webkit-column-count: 1;
		column-count: 1;
		letter-spacing: 0.1em;
	}
	.article .body p:first-child:first-line{
		font-weight: bold;
	}

	.article date{
		font-family: Georgia;
		color: rgba(69,54,37,0.6);
		font-size: 0.75em;
		font-style: italic;
		letter-spacing: 0.25em;
		border-top: 1px solid rgba(69,54,37,0.2);
		display: block;
		padding-top: 0.5em;
		margin-top: 2em;
		text-align: right;
	}
/**/
/* Estilos para los contenidos */
	.mainLinks .btn-group-lg .btn {
		font-size: large;
		width:150px;
	}
	@media only screen and (max-width: 767px) {
		.mainLinks .btn-group-lg .btn {
			width:100%;
		}
	}
	.mainLinks a:link {color: #FFF;text-decoration: none;}
	.mainLinks a:visited {color: #FFF;}
	.mainLinks a:hover, .mainLinks a:focus {color: #000;}
	.mainLinks a:active {color: #000;}

	.sobreSiguiente>.btn {
		border-radius: 0;
	}
	/* Quickstart */
		table.features {
			width:100%;
			text-align: center;
		}
		table.features td {
			padding: 1em;
		}
		.fa-icon-big {
			cursor: default;
			color:#c0c0c0;
			font-size: 7em;
			text-shadow: -1px -1px 1px #fff, 1px 1px 1px #000;
		}
	/**/
	/* Workflow */
		table.MWconfig {
			width:100%;
			margin-bottom: 10px;
			border:double #000 1px;
		}
		table.MWconfig tr:nth-child(odd){
			background-color: rgba(128,128,128,0.5);
		}
		table.MWconfig tr:nth-child(even){
			background-color: rgba(128,128,128,0.25);
		}
		table.MWconfig td.option{
			text-align: right;
			padding-right:1em;
		}
		table.MWconfig td.value{
			font-weight: bold;
		}

		ul.filesStruct li {
			font-weight: bold;
		}
		ul.filesStruct li>span {
			font-weight: normal;
		}
	/**/
	/* Structure */
		ul.ulFiles {
			list-style-type:none;
		}
	/**/
	/* Como no tenemos selector para ".fragment que preceda a .sobreAnterior" se lo ponemos a .fragment */
		.fragment {
			padding-bottom: 7px;
		}
	/**/
/**/

/* Estilos de bootstrap callouts */
	/* Base styles (regardless of theme) */
	.bs-callout {
		margin: 20px 0;
		padding: 15px 30px 15px 15px;
		border-left: 5px solid #eee;
	}
	.bs-callout h4 {
		margin-top: 0;
	}
	.bs-callout p:last-child {
		margin-bottom: 0;
	}
	.bs-callout code,
	.bs-callout .highlight {
		background-color: #fff;
	}

	/* Themes for different contexts */
	.bs-callout-danger {
		background-color: #fcf2f2;
		background-color: rgba(252, 242, 242, 0.5);
		border-color: #dFb5b4;
	}
	.bs-callout-warning {
		background-color: #fefbed;
		background-color: rgba(254, 251, 237, 0.5);
		border-color: #f1e7bc;
	}
	.bs-callout-info {
		background-color: #f0f7fd;
		background-color: rgba(240, 247, 253, 0.5);
		border-color: #d0e3f0;
	}
	.bs-callout-danger h4 {
		color: #B94A48;
	}
	.bs-callout-warning h4 {
		color: #C09853;
	}

	.bs-callout-info h4 {
		color: #3A87AD;
	}
/**/