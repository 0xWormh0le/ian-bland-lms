$(document).ready(function () {

  function launch(url) {
    var leftPosition, topPosition;
    var width = 1024;
    var height = 768;
  
    leftPosition = (window.screen.width / 2) - ((width / 2) + 10);
    topPosition = (window.screen.height / 2) - ((height / 2) + 50);

    var style = `fullscreen=yes, resizeable=no, menubar=no, location=no, titlebar=no, toolbar=no, status=no, width=${width}, height=${height}, screenX=${leftPosition}, screenY=${topPosition}`;
    var w = window.open(url, '_blank', style);

    setTimeout(function timeout() {
      if (w.closed) {
        window.location.reload();
      } else {
        setTimeout(timeout, 100);
      }
    }, 100);
  }
  
  
  $(".radio-module").click(function () {
    $(".module-select-section")
      .removeClass("bg-primary")
      .find(".launch-button-section")
      .hide();
    
    $(this).parents(".module-select-section")
      .addClass("bg-primary")
      .find(".launch-button-section")
      .show();
  })
  
  $(".radio-module").first().click();

  $('body').on('click', '.launch', function () {

    var $this = $(this),
        url = $this.data('url'),
        id = $this.data('id');
        type = $this.data('type');
        module_id = $this.data('moduleid');

    $this.find('span').html('. . . . . .');
    $this.prop('disabled','disabled');

    $.ajax({
      url: url,
      method: "POST",
      dataType: "JSON",
      data: {
        '_token': $('meta[name="csrf-token"]').attr('content'),
        'id': id,
        'module_id': module_id
      }
    }).done(function (res) {
      if (res.status == 'success') {
        if(type == 'Elearning')
        {
          win = window.open(res.url, '_self');
          if (win) {
            win.focus();
          } else {
            alert(trans('js.allow_popup'));
          }
        }
      }else{
        swal({
          title: res.msg,
          text:'',
          type:'error',
          timer: 2000,
          showConfirmButton: false,
        });
      }
      $this.find('span').html('Launch');
      $this.prop('disabled', false);
    });

    launch($this.data('open-url'));
  });


  $('body').on('click', '.streamlaunch', function () {
    $.ajax({
      url: $(this).data("url"),
      method: "POST",
      dataType: "JSON",
      data: {
        '_token': $('meta[name="csrf-token"]').attr('content'),
        'module_id': $(this).data("moduleid"),
      }
    }).done(function (res) {
      if(res.presenter == true){
        $("#launch_presenter").attr("disabled", true).css("cursor", "not-allowed");
      }
      else{
        $("#launch_presenter").attr("disabled", false).css("cursor", "pointer");
      }
      $("#stream_modal").modal("show");
    });
  });

  $("#launch_presenter").on("click", function(){
    $("#stream_modal").modal("hide");
    $("#presenter_modal").modal("show");
  });

  $(".launch-stream").on("click", function(){
    var $this = $(this),
        url = $this.data('url'),
        id = $this.data('id'),
        type = $this.data('type'),
        module_id = $this.data('moduleid');

    $.ajax({
      url: url,
      method: "POST",
      dataType: "JSON",
      data: {
        '_token': $('meta[name="csrf-token"]').attr('content'),
        'id': id,
        'module_id': module_id,
        'type': type,
      },
      async: false
    }).done(function (res) {
      if (res.status == 'success') {
        if(res.launchUrl !== '')
        {
          target = '_self';
          winScorm = window.open(res.launchUrl, target);
          if (winScorm)
            winScorm.focus();
          else
            alert(trans('js.allow_popup'));
        }


      }

      else {
        swal({
          title: res.msg,
          text: '',
          type: 'error',
          timer: 2000,
          showConfirmButton: false,
        });
      }
      $("#stream_modal").modal("hide");
      $("#presenter_modal").modal("hide");
    });
  })

});
