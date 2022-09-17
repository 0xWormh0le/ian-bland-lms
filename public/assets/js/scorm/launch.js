function launchCourse(url, name, width, height, redirect) {

    $('#PopupBlocked').css('display', 'none');
    $('#PossiblePopupBlockerMessage').css('display', 'block');

    if (width <= 100) {
        width = Math.round(screen.availWidth * width / 100);
    }
    if (height <= 100) {
        height = Math.round(screen.availHeight * height / 100);
    }
    var options = ",width=" + width + ",height=" + height;
    var windowobj = window.open(url, name, options);

    window = windowobj;

    setTimeout(function timeout() {

        if (windowobj.closed) {
          //  window.document.location.href = redirect;
        } else {
          setTimeout(timeout, 300);
        }
    }, 300);

    windowobj.focus();
    return windowobj;
}
