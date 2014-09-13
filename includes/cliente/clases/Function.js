// JavaScript Document
//Se usa así:
/*
var foo = function(a, b)
{
  ...
}.defaults(42, 'default_b');
*/

//Extendemos el objeto Function para que acepte parametros por defecto
/*
Function.prototype.defaults = function() {
	var _f = this;
	var _a = Array(_f.length-arguments.length).concat(Array.prototype.slice.apply(arguments));
	return function() {
		return _f.apply(_f, Array.prototype.slice.apply(arguments).concat(_a.slice(arguments.length, _a.length)));
	}
}
*/
//No se puede usar en metodos de clases, porque entonces this se refiere a la funcion en si misma y no al objeto de la clase

//Nueva version, que funciona con metodos de clases
Function.prototype.defaults = function() {
	var _f = this;
	var _a = Array(_f.length-arguments.length).concat(Array.prototype.slice.apply(arguments));
	//console.log(_f.length+"--"+arguments.length+"--"+_a+"--"+Array.prototype.slice.apply(arguments));
	return function() {
		return _f.apply(this, Array.prototype.slice.apply(arguments).concat(_a.slice(arguments.length, _a.length)));
	}
}