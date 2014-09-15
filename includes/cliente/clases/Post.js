// JavaScript Document
function Post() {
	form=document.createElement("form");
	form.setAttribute("action",window.location);
	form.setAttribute("method","post");

	for (var i=0; i<arguments.length; i+=2) {
		switch (arguments[i]) {
		case "action":
			form.setAttribute("action",arguments[i+1]);
			break;
		case "method":
			form.setAttribute("method",arguments[i+1]);
			break;
		default:
			if (arguments[i+1] instanceof Array ||
				arguments[i+1] instanceof Object) {
				for (var indexOrProperty in arguments[i+1]) {
					var value=arguments[i+1][indexOrProperty];
					input=document.createElement("input");
					input.setAttribute("type","hidden");
					input.setAttribute("name",arguments[i]+"[]");
					input.setAttribute("value",value);
					form.appendChild(input);
				}
			} else {
				input=document.createElement("input");
				input.setAttribute("type","hidden");
				input.setAttribute("name",arguments[i]);
				input.setAttribute("value",arguments[i+1]);
				form.appendChild(input);
			}
		}
	}
	document.body.appendChild(form);
	//console.log(form);
	form.submit();
}
