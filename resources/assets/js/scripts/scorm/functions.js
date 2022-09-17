function doLaunch(url, scorm, user) {
	
	window.open("courses/lms/scorm/loader.html?p=1&s=" + scorm + "&u=" + user + "&url=" + url, "windowname1", "width=980, height=710");
}