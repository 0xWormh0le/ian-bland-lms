<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
 <head>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
  	<title> Contents {{$version}} </title>
    <style>
        body { margin:0; padding:20px; }
        #top,#bottom,#left,#right { position:fixed; background:#DFE5EA; }
        #top,#bottom { left:0; width:100%; height:16px; }
        #top { top:0; }
        #bottom { bottom:0; }
        #left,#right { top:0; height:100%; width:16px; }
        #left { left:0; }
        #right { right:0; }
        #content { text-align:center; width:100%; color:#555555; font-family:'Lucida Grande','Helvetica Neue',Helvetica,Arial,sans-serif;}
        #content p { font-size:1.2em; width:70%; margin:auto; }
        #LaunchScoButton {margin-top: 15px;}
        #MessageAreaWrapper {
            text-align: center;
            margin-top: 15%;
            margin-left: auto;
            margin-right: auto;
            max-width: 80%;
            width: 60em;
        }
        #my_iframe {
          position: absolute;
          left: 0;
          top: 0;
          width: 100%;
          height: 100%;
        }
    </style>

  <script src="{{ asset('assets/js/scorm/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/js/scorm/jquery-url-min.js') }}"></script>
  <script src="{{ asset('assets/js/scorm/purl.js') }}"></script>

  @if (stripos($version,'12') !== false)
    <script src="{{ asset('assets/js/scorm/api.js') }}"></script>
  @elseif (stripos($version,'13') !== false)
    <script src="{{ asset('assets/js/scorm/api_13.js') }}"></script>
  @else
    <script src="{{ asset('assets/js/scorm/api.js') }}"></script>
  @endif

  <script>
    var $version = {!! json_encode($version) !!};
    var $scorm = {!! json_encode($scorm) !!};
    var $user = {!! json_encode($user) !!};
    var $sco = {!! json_encode($sco) !!};
    var $attempt = {!! json_encode($attempt) !!};
  </script>

  <script src="{{ asset('assets/js/scorm/main.js') }}"></script>


 </head>

<body id="launchBody" marginwidth="0" marginheight="0">
    <div id="content">


  <iframe id="my_iframe" src="{{$src}}" frameborder="0" scrolling="yes">
			Your browser doesn't support iframes
  </iframe>
 
 <script>
    var my_h = (screen.height-110);
	if(my_h<0) my_h = my_h * (-1);
 	document.getElementById('my_iframe').height = my_h;
 </script>
<?php
	  exit();
?>

    <div id="MessageAreaWrapper">
        <div id="PleaseClick" style="display: none;">
            <h3><a id="PleaseClickLink">Click here to launch the lesson.</a></h3>
        </div>
        <div id="PopupBlocked" style="display: block; visibility: visible;">
            <h3 id="PopupBlockedHeaderMessage">Popup Blocked</h3>
            <p id="PopupBlockedMessage">We attempted to launch your course in a new window, but a popup blocker is preventing it from opening. Please disable popup blockers for this site.</p>
            <button id="LaunchScoButton" onclick="launchCourse('{{$src}}', 'SCORM Viewer', 0, 0, '{{ URL::asset('assets/js/scorm/closer.html') }}')">Launch Course</button>
        </div>
        <div id="Message" style="display: none;">
            <h3 id="CourseLaunchedMessage">Your course has been launched in a new window.</h3>
        </div>
        <div id="PossiblePopupBlockerMessage" style="display: none;">We launched your course in a new window but if you do not see it, a popup blocker may be preventing it from opening. Please disable popup blockers for this site.</div>
    </div>
    </div>

    <div id="top"></div>
    <div id="bottom"></div>
    <div id="left"></div>
    <div id="right"></div>

      <script src="{{ asset('assets/js/scorm/launch.js') }}"></script>

</body>
</html>
