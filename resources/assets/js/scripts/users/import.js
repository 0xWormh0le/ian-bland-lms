$(document).ready(function () {
  $("#upload").on("click", function () {
    $("#file").click();
  });
  
  $("#file").on('change', function () {
    $(this).closest("form").submit();
  });

});
function downloadInnerHtml(filename, elId, mimeType) {
  var elHtml = document.getElementById(elId).innerText;
  var link = document.createElement('a');
  mimeType = mimeType || 'text/plain';

  link.setAttribute('download', filename);
  link.setAttribute('href', 'data:' + mimeType + ';charset=utf-8,' + encodeURIComponent(elHtml));
  link.click();
}

$('#downloadLog').click(function(){
  downloadInnerHtml('log' + new Date().getTime() / 1000+'.txt', 'log','text/plain');
});
