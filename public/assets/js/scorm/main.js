var console, length, method, methods, noop, _results;
method = void 0;
noop = noop = function() {};
methods = ["assert", "clear", "count", "debug", "dir", "dirxml", "error", "exception", "group", "groupCollapsed", "groupEnd", "info", "log", "markTimeline", "profile", "profileEnd", "table", "time", "timeEnd", "timeStamp", "trace", "warn"];
length = methods.length;
console = (window.console = window.console || {});
_results = [];
while (length--) {
	method = methods[length];
	if (!console[method]) {
		_results.push(console[method] = noop);
	} else {
		_results.push(void 0);
	}
}

$(function(){
	var url = $.url(); window.url = url;
	var params = url.data.param.query;
	var API = null;
	var API_1484_11 = null;

	// Jun 12, 2017
	// Change: Point API_1484_11 (SCORM 2004 object) to new, separate API file for SCORM 2004; separate is necessary for certain packages with certain authoring tools to use the 2004 API object correctly 
  	// Author: John Doyle, jdoyle@syllametrics.com
	params.version = $version;
	params.scorm = $scorm;
	params.sco = $sco;
	params.user = $user;
	params.attempt = $attempt;

	if (typeof SCORMAPI !== 'undefined') {
		API = new SCORMAPI(params);
	} else if (typeof SCORMAPI13 !== 'undefined') {
		API_1484_11 = new SCORMAPI13(params);
	} else {
		API = new SCORMAPI(params);
	}
  	
	window.API = API;
	window.API_1484_11 = API_1484_11;
	$(window).on('beforeunload', function() {
	});
});