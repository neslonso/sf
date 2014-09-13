<?if (false) {?><style><?}?>
<?="\n/*".get_class()."*/\n"?>
/* Nav & Yamm */
	/* Multi-level dropdowns: necesita un fragmento de JS para que se abran y cierren bien los subniveles */
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
/* Efecto cortina: Necesita que menu, foto y content se coloquen a la altura adecuada en $(window).resize(); */
	/*.menu obsoleto, ahora lo tiene el .navbar de yamm
		.menu {
			position:fixed;
			z-index:30;
			width:100%;
		}
	/**/
	.foto {
		position:fixed;
		z-index:10;
		width:100%;
		opacity: 0.3;
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
			background-image: url(./<?=FILE_APP?>?MODULE=IMAGES&almacen=LOREMPIXEL&ancho=200&alto=200);
		}
		.fragment:nth-child(2n):after {
			background-image: url(./<?=FILE_APP?>?MODULE=IMAGES&almacen=LOREMPIXEL&ancho=400&alto=400);
		}
		.fragment:nth-child(3n):after {
			background-image: url(./<?=FILE_APP?>?MODULE=IMAGES&almacen=LOREMPIXEL&ancho=600&alto=600);
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
		width:40%;
		margin: auto;
	}
	.article .title{
		font-size: 2.5em;
		font-family: Georgia;
		letter-spacing: 0.1em;
		color: rgb(142,11,0);
		text-shadow: 1px 1px 1px rgba(255,255,255,0.6);
	}
	.article .title span{
		display: block;
		margin-top: 0.5em;
		font-family: Verdana;
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
	.article .body>p{
		font-family: Verdana;
		-moz-column-count: 2;
		-moz-column-gap: 1em;
		-webkit-column-count: 2;
		-webkit-column-gap: 1em;
		column-count: 2;
		column-gap: 1em;
		line-height: 1.5em;
		color: rgb(69,54,37);
	}
	.article .body p:first-child{
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
	}
/**/
