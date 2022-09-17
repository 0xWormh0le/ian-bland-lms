$(document).ready(function () {

  function checkConfig() {
    var driver = $("#mail_driver").val();
    $("#smtp-conf").hide();
    $("#mailgun-conf").hide();
    $("#sparkpost-conf").hide();

    if (driver == 'smtp')
      $("#smtp-conf").show();
    else if (driver == 'mailgun')
      $("#mailgun-conf").show();
    else if (driver == 'sparkpost')
      $("#sparkpost-conf").show();
  }

  $("#mail_driver").on("change", function () {
    checkConfig();
  });

  checkConfig();

});