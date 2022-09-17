
$("body").on("change", ".attachment-file", function(e){
  var fileName = e.target.files[0].name;
  if (fileName.length > 24) fileName = fileName.substring(0, 24)+'...';
  $(this).parents('.chat-attachment').find('.chat-upload').html(fileName);
  $(this).parents('.row').show();
})

$("body").on("click", ".add_chat_files", function (e) {
  var files = $('.chat-attachment-group .chat-attachment-row').length;
  if(files <= 4)
  {
    var $target = $('.chat-attachment-group').find('.chat-attachment-row').first();
    if ($target.is(":hidden")) {
      $target.find('.attachment-file').click();
    }
    else {
      var $clone = $target.clone();
      $clone.find('.attachment-file').val(null);
      $clone.find('.chat-upload').html("Click to upload");
      $clone.find('.attachment-file').click();
      $clone.appendTo(".chat-attachment-group");
    }
  }
})

$("body").on("click", ".rm-attach", function (e) {
  var files = $('.chat-attachment-group .chat-attachment-row').length;
  if (files > 1) {
    $(this).parents('.chat-attachment-row').remove();
  }else{
    $(this).parents('.chat-attachment-row').find('attachment-file').val(null);
    $(this).parents('.chat-attachment-row').hide();
  }
})
