Date.DIAS=["Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado"];

Date.MESES=["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

Date.msegPORDIA=1000*60*60*24;

Date.prototype.DIAS=Date.DIAS;

Date.prototype.MESES=Date.MESES;

Date.prototype.msPERDAY=Date.msegPORDIA;


// JavaScript Document
//Esta no va metida en el prototipo para llamarla de manera estatica
Date.fromMysql=function (mysqlStamp) {
	//function parses mysql datetime string and returns javascript Date object
	//input has to be in this format: 2007-06-05 15:26:02
	//TODO: Bug: La regex no acepta solo fecha, tiene que ser fecha y hora con "-" y ":" de separadores (YYYY-MM-DD HH:II:SS)
	//var regex=/^([09]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/;
	//var parts=mysqlStamp.replace(regex,"$1 $2 $3 $4 $5 $6").split(' ');
	//return new Date(parts[0],parts[1]-1,parts[2],parts[3],parts[4],parts[5]);

	//20140822: Reescrita sin bug, ahora puede recibir mysql timestamp con o sin hora
	var parts = mysqlStamp.split(/[- :]/);
	if (parts.length<4) {
		parts[3]=parts[4]=parts[5]=0;
	}
	return new Date(parts[0],parts[1]-1,parts[2],parts[3],parts[4],parts[5]);
}
Date.fromStringES=function (stringES) {
	//Recibimos una fecha con formato ES (DD/MM/YYYY HH:MM:SS)
		//Dias, meses, horas, minutos y segundo pueden tener 1 o 2 cifras
		//años pueden tener 2 o 4 cfiras.
		//El separador de fecha es / o - y el de hora :

	//"#([0-9]{1,2})[/|-]([0-9]{1,2})[/|-]([0-9]{2,4})(?: ([0-9]{0,2}):([0-9]{0,2}):([0-9]{0,2}))*#"
	//	-> Captura fecha y hora o solo fecha (En caso de solo fecha los indices de arrPreg correspondientes a la Hora viene vacios)
	var regex=/([0-9]{1,2})[\/|-]([0-9]{1,2})[\/|-]([0-9]{2,4})(?: ([0-9]{0,2}):([0-9]{0,2}):([0-9]{0,2}))*/;
	var parts=stringES.replace(regex,"$1 $2 $3 $4 $5 $6").split(' ');
	console.log(parts);
	var anio=parts[2];
	var mes=parts[1]-1;
	var dia=parts[0];
	var hora=parts[3];
    var minuto=parts[4];
	var segundo=parts[5];
	var milisec=0;
	return new Date(anio,mes,dia,hora,minuto,segundo, milisec);
}

Date.prototype.toUnix=function () {
	return Math.round(this.getTime() / 1000);
}

Date.prototype.toStringES=function (conHora) {
	var result;
	if (conHora) {
		result=this.format("conHoraES");
	} else {
		result=this.format("stringES");
	}
	return result;
}.defaults(true);

Date.prototype.toMysql=function (conHora) {
	var result;
	result=this.format("mysqlstamp");
	return result;
}.defaults(true);


/* Más funciones de fecha para ir revisando
Date.DAYNAMES = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

Date.MONTHNAMES = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

Date.msPERDAY = 1000 * 60 * 60 * 24;

Date.prototype.DAYNAMES = Date.DAYNAMES;

Date.prototype.MONTHNAMES = Date.MONTHNAMES;

Date.prototype.msPERDAY = Date.msPERDAY;

Date.prototype.copy = function () {
	return new Date( this.getTime() );
};

Date.prototype.getFullDay = function() {
	return this.DAYNAMES[this.getDay()];
};

Date.prototype.getDayAbbr = function() {
	return this.getFullDay().slice(0, 3);
};

Date.prototype.getFullMonth = function() {
	return this.MONTHNAMES[this.getMonth()];
};

Date.prototype.getMonthAbbr = function() {
	return this.getFullMonth().slice(0, 3);
};

Date.prototype.to12HourTimeString = function () {
	var h = this.getHours();
	var m = "0" + this.getMinutes();
	var s = "0" + this.getSeconds();
	var ap = "am";
	if (h >= 12) {
		ap = "pm";
		if (h >= 13)
			h -= 12;
	} else if (h == 0) {
		h = 12;
	}
	h = "0" + h;
	return h.slice(-2) + ":" + m.slice(-2) + ":" + s.slice(-2) + " " + ap;
};

Date.prototype.to24HourTimeString = function () {
	var h = "0" + this.getHours();
	var m = "0" + this.getMinutes();
	var s = "0" + this.getSeconds();
	return h.slice(-2) + ":" + m.slice(-2) + ":" + s.slice(-2);
};

Date.prototype.lastday = function() {
	var d = new Date(this.getFullYear(), this.getMonth() + 1, 0);
	return d.getDate();
};

Date.prototype.getDaysBetween = function(d) {
	var tmp = d.copy();
	tmp.setHours(this.getHours(), this.getMinutes(), this.getSeconds(), this.getMilliseconds());
	var diff = tmp.getTime() - this.getTime();
	return diff/this.msPERDAY;
};

Date.prototype.getDayOfYear = function() {
	var start = new Date(this.getFullYear(), 0, 0);
	return this.getDaysBetween(start) * -1;
};

Date.prototype.addDays = function(d) {
	this.setDate( this.getDate() + d );
};

Date.prototype.addWeeks = function(w) {
	this.addDays(w * 7);
};

Date.prototype.addMonths= function(m) {
	var d = this.getDate();
	this.setMonth(this.getMonth() + m);
	if (this.getDate() < d)
		 this.setDate(0);
};

Date.prototype.addYears = function(y) {
	var m = this.getMonth();
	this.setFullYear(this.getFullYear() + y);
	if (m < this.getMonth()) {
		this.setDate(0);
	}
};

Date.prototype.addWeekDays = function(d) {
	var startDay = this.getDay();  //current weekday 0 thru 6
	var wkEnds = 0;                //# of weekends needed
	var partialWeek = d % 5;       //# of weekdays for partial week
	if (d < 0) {                 //subtracting weekdays
		wkEnds = Math.ceil(d/5); //negative number weekends
		switch (startDay) {
			case 6:                  //start Sat. 1 less weekend
				if (partialWeek == 0 && wkEnds < 0)
				wkEnds++;
				break;
			case 0:                   //starting day is Sunday
				if (partialWeek == 0)
					d++;              //decrease days to add
				else
					d--;              //increase days to add
				break;
			default:
				if (partialWeek <= -startDay)
					wkEnds--;
		}
	} else if (d > 0) {            //adding weekdays
		wkEnds = Math.floor(d/5);
		var w = wkEnds;
		switch (startDay) {
			case 6:
				// If staring day is Sat. and
				 //* no partial week one less day needed
				 //* if partial week one more day needed

				if (partialWeek == 0)
					d--;
				else
					d++;
				break;
			case 0:        //Sunday
				if (partialWeek == 0 && wkEnds > 0)
				wkEnds--;
				break;
			default:
				if (5 - day < partialWeek)
					wkEnds++;
		}
	}
	d += wkEnds * 2;
	this.addDays(d);
};

Date.prototype.getWeekDays = function(d) {
	var wkEnds = 0;
	var days = Math.abs(this.getDaysBetween(d));
	var startDay = 0, endDay = 0;
	if (days) {
		if (d < this) {
			startDay = d.getDay();
			endDay = this.getDay();
		} else {
			startDay = this.getDay();
			endDay = d.getDay();
		}
		wkEnds = Math.floor(days/7);
		if (startDay != 6 && startDay > endDay)
			wkEnds++;
		if (startDay != endDay && (startDay == 6 || endDay == 6) )
			days--;
		days -= (wkEnds * 2);
	}
	return days;
};

Date.prototype.getMonthsBetween = function(d) {
	var sDate, eDate;
	var d1 = this.getFullYear() * 12 + this.getMonth();
	var d2 = d.getFullYear() * 12 + d.getMonth();
	var sign;
	var months = 0;
	if (this == d) {
		months = 0;
	} else if (d1 == d2) { //same year and month
		months = (d.getDate() - this.getDate())/this.lastday();
	} else {
		if (d1 <  d2) {
			sDate = this;
			eDate = d;
			sign = 1;
		} else {
			sDate = d;
			eDate = this;
			sign = -1;
		}
		var sAdj = sDate.lastday() - sDate.getDate();
		var eAdj = eDate.getDate();
		var adj = (sAdj + eAdj)/sDate.lastday() -1;
		months = Math.abs(d2 - d1) + adj;
		months = (months * sign);
	}
	return months;
};

Date.prototype.getYearsBetween = function(d) {
	var months = this.getMonthsBetween(d);
	return months/12;
};

Date.prototype.getAge = function() {
	var today = new Date();
	return this.getYearsBetween(today).toFixed(2);
};

Date.prototype.sameDayEachWeek = function (day, date) {
	var aDays = new Array();
	var eDate, nextDate, adj;
	if (this > date) {
		eDate = this;
		nextDate = date.copy();
	} else {
		eDate = date;
		nextDate = this.copy();
	}
	adj = (day - nextDate.getDay() + 7) %7;
	nextDate.setDate(nextDate.getDate() + adj);
	while (nextDate < eDate) {
		aDays[aDays.length] = nextDate.copy();
		nextDate.setDate(nextDate.getDate() + 7);
	}
	return aDays;
};

Date.toDate = function(d) {
	var newDate;
	if (arguments.length == 0) {
		newDate = new Date();
	} else if (d instanceof Date) {
		newDate = new Date(d.getTime());
	} else if (typeof d == "string") {
		newDate = new Date(d);
	} else if (arguments.length >= 3) {
		var dte = [0, 0, 0, 0, 0, 0];
		for (var i = 0; i < arguments.length && i < 7; i++) {
			dte[i] = arguments[i];
		}
		newDate = new Date(dte[0], dte[1], dte[2], dte[3], dte[4], dte[5]);
	} else if (typeof d == "number") {
		newDate = new Date(d);
	} else {
		newDate = null;
	}
	if (newDate == "Invalid Date")
		return null;
	else
		return newDate;
};
*/