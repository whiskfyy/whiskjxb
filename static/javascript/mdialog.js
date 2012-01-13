/**
* @author moufer<moufer@163.com>
* @copyright www.modoer.com
*/
var DLOGID = "MODOER_DIALOG";
var SRCEENID = 'MODOER_SCREENOVER';
var MSGID = "MODOER_MESSAGE";
var SRARTMOVE = 0;
var MOVEX = 0;
var MOVEY = 0;
var MSGTIME = 0;

loadcss('mdialog');

function dlgOpen(title, body, width, height) {
	if($("#"+DLOGID)[0] != null) {
		return;
	}
	srceenOpen();
	var dlg = $("<div></div>").attr("id",DLOGID);
	var p = winAxis(width, height);
	dlg.css({ 
		top:p.y, left:p.x, display:"block", margin:"0px", padding:"0px",
		width:width, height:height, position:"absolute", zIndex:"1000"
	});
	dlg.addClass("mdialog");
	dlg.append($("<div></div>").html("<em></em><span></span>"));
	dlg.find("div").addClass("mheader");
	dlg.find("div").mousedown(dlgDown).mousemove(dlgMove).mouseup(dlgUp).mouseout(dlgOut);
	dlg.find("div > span").text(title);
	dlg.find("div > em").click(dlgClose);
	dlg.append($("<div></div>").addClass("mbody").append(body));
	if($.browser.msie && $.browser.version.substr(0,1)=='6' ) {
		$("select").each(function(i) { 
			this.style.visibility="hidden";
		});
	}
	if($.browser.msie) {
		$("embed").each(function(i) {
			this.style.visibility="hidden";
		});
	}
	//dlg.css('display','none');
	$(document.body).append(dlg);
	//dlg.fadeIn('fast');
}

function dlgClose() {
	if(arguments[0]!='1') srceenClose();
	if($("#"+DLOGID)[0]!=null) {
		var ifme = $("#"+DLOGID).find('iframe');
		if(ifme[0]!=null) ifme.remove();
		$("#"+DLOGID).remove();
	}
	if($.browser.msie && $.browser.version.substr(0,1)=='6' ) {
		$("select").each(function(i) { 
			this.style.visibility = "";
		});
	}
	if($.browser.msie) {
		$("embed").each(function(i) {
			this.style.visibility = "";
		});
	}
}

function winAxis(width, height) {
	var dde = document.documentElement;
	if (window.innerWidth) {
		var ww = window.innerWidth;
		var wh = window.innerHeight;
		var bgX = window.pageXOffset;
		var bgY = window.pageYOffset;
	} else {
		var ww = dde.offsetWidth;
		var wh = dde.offsetHeight;
		var bgX = dde.scrollLeft;
		var bgY = dde.scrollTop;
	}
	x = bgX + ((ww - width)/2);
	y = bgY + ((wh - height)/2);
	return {x:x, y:y};
}

function dlgDown() {
	SRARTMOVE = true;
}

function dlgMove(e) {
	if(!SRARTMOVE) return;
	var dlg = $("#"+DLOGID);
	var bar = $("#"+DLOGID).find("div > span");
	var left = parseInt(dlg.css("left").replace('px','')); // x
	var top = parseInt(dlg.css("top").replace('px',''));   // y
	var mX = MOVEX == 0 ? 0 : MOVEX - e.pageX;
	var mY = MOVEX == 0 ? 0 : MOVEY - e.pageY;
	var newleft = left - mX;
	var newtop = top - mY;
	
	//bar.text(e.pageX+','+e.pageY+'|'+top+','+left+'|'+newleft+','+newtop);

	MOVEX = e.pageX;
	MOVEY = e.pageY;

	dlg.css({"top":newtop+"px", "left":newleft+"px"});
	
}

function dlgUp() {
	//settimeout
	SRARTMOVE = false;
	MOVEX = MOVEY = 0;
	//setTimeout('endDrag()',5);
}

function dlgOut() {
	dlgUp();
}

function endDrag() {
	var dlg = $("#"+DLOGID);
	var left = parseInt(dlg.css("left").replace('px','')); // x
	var top = parseInt(dlg.css("top").replace('px',''));   // y
	var right = left + parseInt(dlg.css("width").replace('px','')); // x
	var bottom = top + parseInt(dlg.css("height").replace('px',''));   // y
}

function getPosition(e) {
	var left = 0;
	var top  = 0;
	while (e.offsetParent){
		left += e.offsetLeft + (e.currentStyle?(parseInt(e.currentStyle.borderLeftWidth)).NaN0():0);
		top  += e.offsetTop  + (e.currentStyle?(parseInt(e.currentStyle.borderTopWidth)).NaN0():0);
		e     = e.offsetParent;
	}
	left += e.offsetLeft + (e.currentStyle?(parseInt(e.currentStyle.borderLeftWidth)).NaN0():0);
	top  += e.offsetTop  + (e.currentStyle?(parseInt(e.currentStyle.borderTopWidth)).NaN0():0);
	return {x:left, y:top};
}

function msgOpen() {
	
	if(arguments.length == 0) return;

	var body = arguments[0];
	var width = typeof(arguments[1]) == 'undefined' ? 250 : arguments[1];
	var height = typeof(arguments[2]) == 'undefined' ? 50 : arguments[2];

	if($("#"+MSGID)[0] != null) {
		return;
	}
	var msg = $("<div></div>").attr("id",MSGID);
	var p = winAxis(width, height);
	msg.css({ 
		top:p.y, left:p.x, display:"block", margin:"0px", padding:"0px",
		width:width+'px', height:height+'px', position:"absolute", zIndex:"1005",
		"line-height":height+'px'
	});
	msg.addClass("mmessage");
	msg.append($("<div></div>").addClass("mbody").append(body)).click(msgClose);

	if($.browser.msie && $.browser.version.substr(0,1)=='6' ) {
		$("select").each(function(i) {
			this.style.visibility="hidden";
		});
	}

	$(document.body).append(msg);
	MSGTIME = window.setTimeout("msgClose();", 3000);
}

function msgClose() {
	if($("#"+MSGID)[0]!=null) {
		$("#"+MSGID).remove();
	}
	if($.browser.msie && $.browser.version.substr(0,1)=='6' ) {
		$("select").each(function(i) { 
			this.style.visibility = "";
		});
	}
	window.clearTimeout(MSGTIME);
}

function srceenOpen() {
	if($('#'+SRCEENID)[0]!=null) return;
	var wh = "100%";
	if (document.documentElement.clientHeight && document.body.clientHeight) {
		if (document.documentElement.clientHeight > document.body.clientHeight) {
			wh = document.documentElement.clientHeight + "px";
		} else {
			wh = document.body.clientHeight + "px";
		}
	} else if (document.body.clientHeight) {
		wh = document.body.clientHeight + "px";
	} else if (window.innerHeight) {
		wh = window.innerHeight + "px";
	}
	var o = 40/100;
	var scr = $('<div></div>').attr("id", SRCEENID).css({
			display:"block", top:"0px", left:"0px", margin:"0px", padding:"0px",
			width:"100%", height:wh, position:"absolute", zIndex:999, background:"#8A8A8A",
			filter:"alpha(opacity=25)", opacity:o, MozOpacity:o, display:"none"
	});
	$(document.body).append(scr);
	$('#'+SRCEENID).fadeIn("fast");
}

function srceenClose() {
	$('#'+SRCEENID).fadeOut("fast", function() {
		$('#'+SRCEENID).remove();
	});
}