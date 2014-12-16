OFC = {};

var jq = jQuery.noConflict();

OFC.jQuery = {
	name : "jQuery",
	version : function(src) {
		return jq('#' + src)[0].get_version();
	},
	rasterize : function(src, dst) {
		jq('#' + dst).replaceWith(OFC.jQuery.image(src));
	},
	image : function(src) {
		return "<img src='data:image/png;base64,"
				+ jq('#' + src)[0].get_img_binary() + "' />";
	},
	popup : function(src) {
		var img_win = window.open('', 'Charts: Export as Image');
		with (img_win.document) {
			write('<html><head><title>Charts: Export as Image<\/title><\/head><body>'
					+ OFC.jQuery.image(src) + '<\/body><\/html>');
		}
		// stop the 'loading...' message
		img_win.document.close();
	}
};

function save_image(id) {

	OFC.jQuery.rasterize(id, id);
}

if (typeof (Control == "undefined")) {
	var Control = {
		OFC : OFC.jQuery
	};
}

function popupWindow(url, title, width, height) {
	var left = parseInt((screen.availWidth / 2) - (width / 2));
	var top = parseInt((screen.availHeight / 2) - (height / 2) - 100);
	var windowFeatures = "width="
			+ width
			+ ",height="
			+ height
			+ ",toolbar=0,location=0,directories=0,status=0,scrollbars=0,menubar=0,resizable=0,modal=yes,left="
			+ left + ",top=" + top + "screenX=" + left + ",screenY=" + top;
	window.open(url, title, windowFeatures);
}

function replaceUTF8Umlaute(string) {

	string = string.replace(/[\u00c4]/g, 'Ae');
	string = string.replace(/[\u00e4]/g, 'ae');
	string = string.replace(/[\u00dc]/g, 'Ue');
	string = string.replace(/[\u00fc]/g, 'ue');
	string = string.replace(/[\u00d6]/g, 'Oe');
	string = string.replace(/[\u00f6]/g, 'oe');
	string = string.replace(/[\u00df]/g, 'ss');

	return string;
}

function test_click(id, key, parm3, host) {

	title = "Parameters via OFC";
	if (key != undefined)
		var tmp = '_' + key;
	else
		var tmp = "_popup";

	tmp = tmp.split(' ').join('');
	tmp = tmp.replace(/^\s+|\s+$/g, "");
	tmp = replaceUTF8Umlaute(tmp);
	tmp = tmp.replace(/[^A-Za-z0-9\-_]/g, '-');

	content = '\nUse this chartid for modal window:  '
			+"<br><b> "+ id
			+ tmp
			+"</b>";
	info_box(title, content);
}

function click_parms(parm1, parm2, parm3, parm4) {

	title = "Parameters via OFC";
	content = "<h5>got parameters:</h5><br><b>parm1:</b> " + parm1 
	+ "<br><b>parm2:</b> " + parm2 + "<br><b>parm3:</b> "
	+ parm3 + "<br><b>parm4:</b> " + parm4;
	info_box(title, content);

}

function print_page(id) {

	if (navigator.appName == "Microsoft Internet Explorer")
		window.print();
	else {
		save_image(id);
		setTimeout("window.print()", 2000);
	}

}

function show_modalchart(id, key, parm3, host)
{ 
	var target = "#modal_" +id +"_" +key;
	jQuery(target).modal('show'); 
}

function alert_box(title, content) {
	jQuery.msgBox({
		title : title,
		content : content,
		autoClose : false,
		timeOut : 6000,
		opacity : 0.5
	});
	return false;
}

function info_box(title, content) {	
	jQuery.msgBox({
		title : title,
		type : 'info',
		autoClose : true,
		timeOut : 5000,
		content : content,
		opacity : 0.2
	});
}

function set_mouse_pointer(obj)
{
   obj.style.cursor='pointer';
}

/*
 * AjaxRequest used for flashchart Applications (implements XMLHttpRequest)
 */
function getHTTPObject() {
	var xhr = false;
	if (window.XMLHttpRequest) {
		xhr = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		try {
			xhr = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {
				xhr = false;
			}
		}
	}
	return xhr;
}

function requestFlashchartData(url) {
	var request = getHTTPObject();
	if (request) {
		request.onreadystatechange = function() {
			parseFlashchartRequestResponse(request);
		};
		request.open("GET", url, true);
		request.send(null);
	}
}

function parseFlashchartRequestResponse(request) {
	if (request.readyState == 4) {
		if (request.status == 200 || request.status == 304) {
			loadFlaschartContent(request.responseText);
		} else
			alert_box('HTTP-Error', request.responseText);
	}
}

function loadFlaschartContent(content) {

	if (content == null || content == undefined) {
		alert("no data received");
		return false;
	} else {
		try {
			var data = JSON.parse(content);
			var script_text = "";

			switch (data.data_type) {

			case "error_data":
				var error_title = JSON.stringify(data.error_title).replace(/['"]/g,'');
				var error_msg = JSON.stringify(data.error_msg).replace(/['"]/g,'');
				alert_box(error_title, error_msg);
				break;

			case 'flashcharts':
				for ( var i = 0; i < data.flashcharts.length; i++) {					
					if (data.flashcharts[i].type == 'chart') {
						var chart_id = JSON.stringify(data.flashcharts[i].chart_id).replace(/['"]/g,'');
						var div_element = document.getElementById(data.flashcharts[i].chart_id);
						div_element.style.width = data.flashcharts[i].width;
						div_element.style.height = data.flashcharts[i].height;
						
						var tmp_text = JSON.stringify(data.flashcharts[i].swfobject);
						var strlen = tmp_text.length - 2;
						script_text += tmp_text.substr(1, strlen);
						script_text += "\n var data_" + chart_id + " = ";
						script_text += JSON.stringify(data.flashcharts[i].chartdata) + ";";
					}						
					if (data.flashcharts[i].type == 'script') {
						
						var scriptname = JSON.stringify(data.flashcharts[i].scriptname).replace(/['"]/g,'');
						// swfobject script:
						var tmp_text = JSON.stringify(data.flashcharts[i].script);
						var strlen = tmp_text.length - 2;
						script_text += "\n" + tmp_text.substr(1, strlen);
	
						script_text += "\n function get_data_" + scriptname + "() { "; 
						script_text += " \n var data_" + scriptname +" = "; 
						script_text += JSON.stringify(data.flashcharts[i].chartdata) + ";";
						script_text += "\n return JSON.stringify(data_" + scriptname +" ); }";
						//alert_box ("test", script_text);
					}
					
					if (data.flashcharts[i].type == 'chart'	|| data.flashcharts[i].type == 'script') {
						var script_element = document.getElementById('flashchart_scripts');
						if (script_element == undefined)
						{
							script_element = document.createElement("div");
							script_element.id = 'flashchart_scripts';
							document.head.appendChild(script_element);
						}	
						if (script_element.innerHTML != "")
							script_element.innerHTML = "";
						var newScript = document.createElement("script");
						newScript.type = "text/javascript";
						newScript.text = script_text;
						script_element.appendChild(newScript);
					}
					if (data.flashcharts[i].type == 'html')
					{
					   var node = JSON.stringify(data.flashcharts[i].node).replace(/['"]/g,	'');
					   var div_element = document.getElementById(node);
					   div_element.innerHTML = "";
					   var html_text = JSON.stringify(data.flashcharts[i].html_text);
					   var strlen = html_text.length - 2;
					   html_text = html_text.substr(1, strlen);
					   div_element.innerHTML = html_text;
					}	
				}
				break;

			case "html_data":
				if (data.node == undefined)
				   var div_element = document.getElementById('html_text');
				else
					var div_element = document.getElementById(data.node);	
				div_element.innerHTML = "";
				var html_text = JSON.stringify(data.html_text);
				var strlen = html_text.length - 2;
				html_text = html_text.substr(1, strlen);
				div_element.innerHTML = html_text;
				// alert(html_text);
				break;
			}

		} catch (err) {
			// if error, show content and alert:
			var html_element = document.getElementById('html_text');
			html_element.innerHTML = "";
			html_element.innerHTML = "<br /><b>Processing Error! </b>Ajax Request got this content: <br />"
					+ content;
			alert_box('Processing Error', err.message);
		}
	}
}

function requestContent(node, url) {
	var request = getHTTPObject();
	if (request) {
		request.onreadystatechange = function() {
			parseContentResponse(request, node);
		};
		request.open("GET", url, true);
		request.send(null);
	}
}

function parseContentResponse(request, node) {
	if (request.readyState == 4) {
		if (request.status == 200 || request.status == 304) {
			loadPageContent(request.responseText, node);
		}
	}
}

function loadPageContent(content, node) {
	
	if (content == null || content == undefined)
		alert("no data received");
	
	var content_container = document.getElementById(node);
	if (content_container != undefined)
	 {
		content_container.innerHTML = "";
		content_container.innerHTML = content;
		
	 }
	else
	 alert ("no div element for " +node);	
	
}	
