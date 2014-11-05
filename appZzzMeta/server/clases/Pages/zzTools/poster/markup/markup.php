<?="\n<!-- ".get_class()." -->\n"?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Poster</title>
		<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<script type="text/javascript">
			function doPost() {
				var arrFuncionPost=new Array;
				var arrNames=document.getElementsByName('name');
				var arrValues=document.getElementsByName('value');
				console.log(arrNames);
				for (var i = 0; i < arrNames.length; i++) {
					if (arrNames[i].value!="") {
						arrFuncionPost.push(arrNames[i].value);
						arrFuncionPost.push(arrValues[i].value);
					}
				};
				Post.apply(this,arrFuncionPost);
			}

			// JavaScript Document
			function Post() {
				var action=(document.getElementById('action')!="")?document.getElementById('action').value:"<?=$_SERVER['PHP_SELF']?>";
				form=document.createElement("form");
				form.setAttribute("action",action);
				form.setAttribute("method","post");
				form.setAttribute("target","_blank");

				for (var i=0; i<arguments.length; i+=2) {
					switch (arguments[i]) {
					case "action":
						form.setAttribute("action",arguments[i+1]);
						break;
					case "method":
						form.setAttribute("method",arguments[i+1]);
						break;
					default:
						input=document.createElement("input");
						input.setAttribute("type","hidden");
						input.setAttribute("name",arguments[i]);
						input.setAttribute("value",arguments[i+1]);
						form.appendChild(input);
					}
				}
				document.body.appendChild(form);
				form.submit();
			}
		</script>
		<style type="text/css">
			input {
				width: 300px;
			}
		</style>
	</head>
	<body>
		Action: <input type="text" id="action"><br />

		<input type="text" name="name" placeholder="name" value="APP" /> : <input type="text" name="value" placeholder="value" value="appZzzMeta" /><br />
		<input type="text" name="name" placeholder="name" value="acClase" /> : <input type="text" name="value" placeholder="value" value="" /><br />
		<input type="text" name="name" placeholder="name" value="acMetodo" /> : <input type="text" name="value" placeholder="value" value="" /><br />
		<input type="text" name="name" placeholder="name" value="acTipo" /> : <input type="text" name="value" placeholder="value" value="stdAssoc" /><br />
		<input type="text" name="name" placeholder="name" value="acReturnURI" /> : <input type="text" name="value" placeholder="value" value="<?=$_SERVER["REQUEST_URI"]?>" /><br />
<?
for ($i=0; $i<10; $i++) {
?>
		<input type="text" name="name" placeholder="name" /> : <input type="text" name="value" placeholder="value" /><br />
<?
}
?>
		<button onclick="doPost()">Post</button>
		<pre>
<?
var_dump($_POST);
?>
		</pre>
	</body>
</html><?="\n<!-- /".get_class()." -->\n"?>
