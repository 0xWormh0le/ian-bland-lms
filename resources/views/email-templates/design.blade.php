<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>
        {{ $title }} -
        {{ config('app.name', 'LMS') }}
    </title>
    <link rel="stylesheet" href="https://grapesjs.com/stylesheets/grapes.min.css?v0.14.25">
    <link href="{{asset('vendors/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/simple-line-icons/css/simple-line-icons.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('vendors/grapesjs/material.css')}}">
    <link rel="stylesheet" href="{{asset('vendors/grapesjs/tooltip.css')}}">
    <link rel="stylesheet" href="{{asset('vendors/grapesjs/toastr.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendors/grapesjs/grapesjs-preset-newsletter.css')}}">
    <link rel="stylesheet" href="{{asset('vendors/grapesjs/theme.css')}}">

    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="{{asset('vendors/grapesjs/grapes.min.js')}}"></script>
    <script src="{{asset('vendors/grapesjs/grapesjs-preset-newsletter.min.js')}}"></script>
    <script src="{{asset('vendors/grapesjs/toastr.min.js')}}"></script>
    <script src="{{asset('vendors/grapesjs/ajaxable.min.js')}}"></script>
  </head>
  <body>

    <div id="gjs" style="height:0px; overflow:hidden">
      @if($data->content)
         {!! $data->content !!}
      @else
        <table class="main-body">
          <tr class="row">
            <td class="main-body-cell">
              <table class="container">
                <tr>
                  <td class="container-cell">
                    <table class="c1766">
                      <tr>
                        <td class="cell c1769">
                          <img class="c926" src="http://artf.github.io/grapesjs/img/grapesjs-logo.png" alt="Logo"/>
                        </td>
                        <td class="cell c1776">
                          <div class="c1144">@PORTAL @COMPANY
                            <br/>
                          </div>
                        </td>
                      </tr>
                    </table>
                    <table class="card">
                      <tr>
                        <td class="card-cell">
                          <table class="table100 c1357">
                            <tr>
                              <td class="card-content">
                                <h1 class="card-title">Hi @FIRSTNAME @LASTNAME
                                  <br/>
                                </h1>
                                <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                </p>
                                <table class="c1542">
                                  <tr>
                                    <td class="card-footer" id="c1545">
                                      <a class="button" href="@URL">Click Here
                                      </a>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                    <table class="footer">
                      <tr>
                        <td class="footer-cell">
                          <div class="c2421">
                            POWERED BY <a class="link" href="https://scormdispatch.co.uk">SCORM Dispatch</a>
                            <p>
                          </div>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        <style>
          .link {
            color: rgb(217, 131, 166);
          }
          .row{
            vertical-align:top;
          }
          .main-body{
            min-height:150px;
            padding: 5px;
            width:100%;
            height:100%;
            background-color:rgb(234, 236, 237);
          }
          .c926{
            color:rgb(158, 83, 129);
            width:100%;
            font-size:50px;
          }
          .cell.c849{
            width:11%;
          }
          .c1144{
            padding: 10px;
            font-size:17px;
            font-weight: 300;
          }
          .card{
            min-height:150px;
            padding: 5px;
            margin-bottom:20px;
            height:0px;
          }
          .card-cell{
            background-color:rgb(255, 255, 255);
            overflow:hidden;
            border-radius: 3px;
            padding: 0;
            text-align:center;
          }
          .card.sector{
            background-color:rgb(255, 255, 255);
            border-radius: 3px;
            border-collapse:separate;
          }
          .c1271{
            width:100%;
            margin: 0 0 15px 0;
            font-size:50px;
            color:rgb(120, 197, 214);
            line-height:250px;
            text-align:center;
          }
          .table100{
            width:100%;
          }
          .c1357{
            min-height:150px;
            padding: 5px;
            margin: auto;
            height:0px;
          }
          .darkerfont{
            color:rgb(65, 69, 72);
          }
          .button{
            font-size:12px;
            padding: 10px 20px;
            background-color:rgb(217, 131, 166);
            color:rgb(255, 255, 255);
            text-align:center;
            border-radius: 3px;
            font-weight:300;
          }
          .table100.c1437{
            text-align:left;
          }
          .cell.cell-bottom{
            text-align:center;
            height:51px;
          }
          .card-title{
            font-size:25px;
            font-weight:300;
            color:rgb(68, 68, 68);
          }
          .card-content{
            font-size:13px;
            line-height:20px;
            color:rgb(111, 119, 125);
            padding: 10px 20px 0 20px;
            vertical-align:top;
          }
          .container{
            font-family: Helvetica, serif;
            min-height:150px;
            padding: 5px;
            margin:auto;
            height:0px;
            width:90%;
            max-width:550px;
          }
          .cell.c856{
            vertical-align:middle;
          }
          .container-cell{
            vertical-align:top;
            font-size:medium;
            padding-bottom:50px;
          }
          .c1790{
            min-height:150px;
            padding: 5px;
            margin:auto;
            height:0px;
          }
          .table100.c1790{
            min-height:30px;
            border-collapse:separate;
            margin: 0 0 10px 0;
          }
          .browser-link{
            font-size:12px;
          }
          .top-cell{
            text-align:right;
            color:rgb(152, 156, 165);
          }
          .table100.c1357{
            margin: 0;
            border-collapse:collapse;
          }
          .c1769{
            width:30%;
          }
          .c1776{
            width:70%;
          }
          .c1766{
            margin: 0 auto 10px 0;
            padding: 5px;
            width:100%;
            min-height:30px;
          }
          .cell.c1769{
            width:11%;
          }
          .cell.c1776{
            vertical-align:middle;
          }
          .c1542{
            margin: 0 auto 10px auto;
            padding:5px;
            width:100%;
          }
          .card-footer{
            padding: 20px 0;
            text-align:center;
          }
          .c2280{
            height:150px;
            margin:0 auto 10px auto;
            padding:5px 5px 5px 5px;
            width:100%;
          }
          .c2421{
            padding:10px;
          }
          .c2577{
            padding:10px;
          }
          .footer{
            margin-top: 50px;
            color:rgb(152, 156, 165);
            text-align:center;
            font-size:11px;
            padding: 5px;
            width: 100%;
          }
          .quote {
            font-style: italic;
          }

          .list-item{
            height:auto;
            width:100%;
            margin: 0 auto 10px auto;
            padding: 5px;
          }
          .list-item-cell{
            background-color:rgb(255, 255, 255);
            border-radius: 3px;
            overflow: hidden;
            padding: 0;
          }
          .list-cell-left{
            width:30%;
            padding: 0;
          }
          .list-cell-right{
            width:70%;
            color:rgb(111, 119, 125);
            font-size:13px;
            line-height:20px;
            padding: 10px 20px 0px 20px;
          }
          .list-item-content{
            border-collapse: collapse;
            margin: 0 auto;
            padding: 5px;
            height:150px;
            width:100%;
          }
          .list-item-image{
            color:rgb(217, 131, 166);
            font-size:45px;
            width: 100%;
          }

          .grid-item-image{
            line-height:150px;
            font-size:50px;
            color:rgb(120, 197, 214);
            margin-bottom:15px;
            width:100%;
          }
          .grid-item-row {
            margin: 0 auto 10px;
            padding: 5px 0;
            width: 100%;
          }
          .grid-item-card {
            width:100%;
            padding: 5px 0;
            margin-bottom: 10px;
          }
          .grid-item-card-cell{
            background-color:rgb(255, 255, 255);
            overflow: hidden;
            border-radius: 3px;
            text-align:center;
            padding: 0;
          }
          .grid-item-card-content{
            font-size:13px;
            color:rgb(111, 119, 125);
            padding: 0 10px 20px 10px;
            width:100%;
            line-height:20px;
          }
          .grid-item-cell2-l{
            vertical-align:top;
            padding-right:10px;
            width:50%;
          }
          .grid-item-cell2-r{
            vertical-align:top;
            padding-left:10px;
            width:50%;
          }
        </style>
      @endif
    </div>

    <form id="submitForm" class="test-form" action="{{route('email-setup.update', [$data->id, $language])}}" method="POST" style="display:none">
      @csrf()
      @method('put')
      <input type="hidden" name="body">
    </form>

    <script type="text/javascript">
      var host = '{{url('/')}}';
      var images = [
        host + '/img/logo-symbol.png',
      ];

      // Set up GrapesJS editor with the Newsletter plugin
      var editor = grapesjs.init({
        height: '100%',
        //noticeOnUnload: 0,
        storageManager:{
          autoload: 0,
        },
        assetManager: {
          assets: images,
          upload: false,
          uploadText: 'Uploading is not available in this demo',
          dropzone: false,
        },
        container : '#gjs',
        fromElement: true,
        plugins: ['gjs-preset-newsletter'],
        pluginsOpts: {
          'gjs-preset-newsletter': {
            modalLabelImport: 'Paste all your code here below and click import',
            modalLabelExport: 'Copy the code and use it wherever you want',
            codeViewerTheme: 'material',
            //defaultTemplate: templateImport,
            importPlaceholder: '<table class="table"><tr><td class="cell">Hello world!</td></tr></table>',
            cellStyle: {
              'font-size': '12px',
              'font-weight': 300,
              'vertical-align': 'top',
              color: 'rgb(111, 119, 125)',
              margin: 0,
              padding: 0,
            }
          }
        }
      });

      // Let's add in this demo the possibility to test our newsletters
      var mdlClass = 'gjs-mdl-dialog-sm';
      var pnm = editor.Panels;
      var cmdm = editor.Commands;
      var testContainer = document.getElementById("submitForm");
      var contentEl = testContainer.querySelector('input[name=body]');
      var md = editor.Modal;
      cmdm.add('send-test', {
        run(editor, sender) {
          sender.set('active', 0);
          var modalContent = md.getContentEl();
          var cmdGetCode = cmdm.get('gjs-get-inlined-html');
          contentEl.value = cmdGetCode && cmdGetCode.run(editor);
          $("#submitForm").submit();
        }
      });
      cmdm.add('go-back', {
        run(editor, sender) {
          var check = confirm("Are you sure you want to leave?");
          if (check == true) {
            window.open("{{route('email-setup.index')}}", "_self");
          }
        }
      });
      pnm.addButton('options', {
        id: 'send-test',
        className: 'fa fa-save',
        command: 'send-test',
        attributes: {
          'title': 'Save Design',
          'data-tooltip-pos': 'bottom',
        },
      });
      pnm.addButton('options', {
        id: 'go-back',
        className: 'fa fa-home',
        command: 'go-back',
        attributes: {
          'title': 'Previous Page',
          'data-tooltip-pos': 'bottom',
        },
      });

      // Simple warn notifier
      var origWarn = console.warn;
      toastr.options = {
        closeButton: true,
        preventDuplicates: true,
        showDuration: 250,
        hideDuration: 150
      };
      console.warn = function (msg) {
        toastr.warning(msg);
        origWarn(msg);
      };


      $(document).ready(function () {
        // Beautify tooltips
        $('*[title]').each(function () {
          var el = $(this);
          var title = el.attr('title').trim();
          if(!title)
            return;
          el.attr('data-tooltip', el.attr('title'));
          el.attr('title', '');
        });
      });
    </script>
  </body>
</html>
