// Title: Timestamp picker
// Description: See the demo at url
// URL: http://us.geocities.com/tspicker/
// Script featured on: http://javascriptkit.com/script/script2/timestamp.shtml
// Version: 1.0
// Date: 12-05-2001 (mm-dd-yyyy)
// Author: Denis Gritcyuk <denis@softcomplex.com>; <tspicker@yahoo.com>
// Notes: Permission given to use this script in any kind of applications if
//    header lines are left unchanged. Feel free to contact the author
//    for feature requests and/or donations

function show_calendar(str_target, str_datetime,imgpath) {
	var arr_months = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin",
		"Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
	var week_days = ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"];
	var n_weekstart = 1; // day week starts from (normally 0 or 1)

	var dt_datetime = (str_datetime == null || str_datetime =="" ?  new Date() : str2dt(str_datetime));
	var dt_prev_month = new Date(dt_datetime);
	dt_prev_month.setMonth(dt_datetime.getMonth()-1);
	var dt_next_month = new Date(dt_datetime);
	dt_next_month.setMonth(dt_datetime.getMonth()+1);
	var dt_firstday = new Date(dt_datetime);
	dt_firstday.setDate(1);
	dt_firstday.setDate(1-(7+dt_firstday.getDay()-n_weekstart)%7);
	var dt_lastday = new Date(dt_next_month);
	dt_lastday.setDate(0);
	
	// html generation (feel free to tune it for your particular application)
	// print calendar header
	var str_buffer = new String (
		"<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">"+
		"<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\" lang=\"fr\">" +
		"<head>\n"+
		"	<title>Calendar</title>\n"+
		"</head>\n"+
		"<body style=\"padding:0;margin:0;\">\n"+
		"<div>"+
		"<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n"+
		"<tr>\n	<td style=\"background-color:#4682B4;\"><a href=\"javascript:window.opener.show_calendar('"+
		str_target+"', '"+ dt2dtstr(dt_prev_month)+"','"+imgpath+"');\">"+
		"←</a></td>\n"+
		"	<td style=\"background-color:#4682B4;\" colspan=\"5\">"+
		"<span color=\"white\" >"
		+arr_months[dt_datetime.getMonth()]+" "+dt_datetime.getFullYear()+"</span></td>\n"+
		"	<td style=\"background-color:#4682B4;\" align=\"right\"><a href=\"javascript:window.opener.show_calendar('"
		+str_target+"', '"+dt2dtstr(dt_next_month)+"','"+imgpath+"');\">"+
		"→</a></td>\n</tr>\n"
	);//+document.cal.time.value//+document.cal.time.value

	var dt_current_day = new Date(dt_firstday);
	// print weekdays titles
	str_buffer += "<tr>\n";
	for (var n=0; n<7; n++)
		str_buffer += "	<td style=\"background-color:#87CEFA;\">"+
		"<span color=\"white\">"+
		week_days[(n_weekstart+n)%7]+"</span></td>\n";
	// print calendar table
	str_buffer += "</tr>\n";
	while (dt_current_day.getMonth() == dt_datetime.getMonth() ||
		dt_current_day.getMonth() == dt_firstday.getMonth()) {
		// print row heder
		str_buffer += "<tr>\n";
		for (var n_current_wday=0; n_current_wday<7; n_current_wday++) {
				if (dt_current_day.getDate() == dt_datetime.getDate() &&
					dt_current_day.getMonth() == dt_datetime.getMonth())
					// print current date
					str_buffer += "	<td style=\"background-color:#FFB6C1;\" align=\"right\">";
				else if (dt_current_day.getDay() == 0 || dt_current_day.getDay() == 6)
					// weekend days
					str_buffer += "	<td style=\"background-color:#DBEAF5;\" align=\"right\">";
				else
					// print working days of current month
					str_buffer += "	<td style=\"background-color:white;\" align=\"right\">";

				if (dt_current_day.getMonth() == dt_datetime.getMonth())
					// print days of current month   +document.cal.time.value modif laurent
					str_buffer += "<a href=\"javascript:window.opener."+str_target+
					".value='"+dt2dtstr(dt_current_day)+"'; window.close();\">"+
					"<span color=\"black\">";
				else 
					// print days of other months +document.cal.time.value
					str_buffer += "<a href=\"javascript:window.opener."+str_target+
					".value='"+dt2dtstr(dt_current_day)+"'; window.close();\">"+
					"<span color=\"gray\">";
				str_buffer += dt_current_day.getDate()+"</span></a></td>\n";
				dt_current_day.setDate(dt_current_day.getDate()+1);
		}
		// print row footer
		str_buffer += "</tr>\n";
	}
	// print calendar footer
	str_buffer +=
		"</table>\n" +
		"</div>\n" +
		"</body>\n" +
		"</html>\n";

	var vWinCal = window.open("", "Calendar", 
		"width=400,height=120,status=no,resizable=no,top=200,left=200");
	vWinCal.opener = self;
	var calc_doc = vWinCal.document;
	calc_doc.write (str_buffer);
	calc_doc.close();
}
// datetime parsing and formatting routimes. modify them if you wish other datetime format
function str2dt (str_datetime) {
	var re_date = /^(\d+)\/(\d+)\/(\d+)$/;//\s+(\d+)\:(\d+)\:(\d+)
	if (!re_date.exec(str_datetime))
		return alert("Invalid Datetime format: "+ str_datetime);
	return (new Date (RegExp.$3, RegExp.$2-1, RegExp.$1, RegExp.$4, RegExp.$5, RegExp.$6));
}
function dt2dtstr (dt_datetime) {
	return (new String (
			dt_datetime.getDate()+"/"+(dt_datetime.getMonth()+1)+"/"+dt_datetime.getFullYear()));
}
function dt2tmstr (dt_datetime) {
	return (new String (
			dt_datetime.getHours()+":"+dt_datetime.getMinutes()+":"+dt_datetime.getSeconds()));
}

