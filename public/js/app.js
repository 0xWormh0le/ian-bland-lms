$(document).ready(function () {

  /**
   * Show alert on Click Delete
   */
  $(document).on('click', '.delete', function (e) {
    e.preventDefault();
    var form = $(this).parents('form:first');
    swal({
      title: "Are you sure to delete this?",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
      if (result.value) {
        form.submit();
      }
    });
  });

    /**
     * Allow Number (0-9) only 
     */
    $("body").on('keydown', '.numberonly', function (e) {
      // Allow: backspace, delete, tab, escape, enter and .
      if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
        // Allow: Ctrl+A, Command+A
        (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
        // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
        // let it happen, don't do anything
        return;
      }
      // Ensure that it is a number and stop the keypress
      if ((e.shiftKey || (e.keyCode < 40 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
      }
    });

    /**
     * Not allow keypress
     */
    $('.noedit').keydown(function (event) {
      return false;
    });

    /**
     * Input Masking hour:min
     * 00:00 number only
     */
    $('.hour-min').keydown(function (e) {
      var key = e.which || e.charCode || e.keyCode || 0;
      $this = $(this);

      // Auto-format- do not expose the mask as the user begins to type
      if (key !== 8 && key !== 9) {
        if ($this.val().length === 2) {
          $this.val($this.val() + ':');
        }
      }
      // Allow numeric (and tab, backspace, delete) keys only
      return (key == 8 ||
        key == 9 ||
        key == 46 ||
        (key >= 48 && key <= 57) ||
        (key >= 96 && key <= 105));
    })
    .bind('focus click', function () {
      $this = $(this);
      if ($this.val().length === 0) {
        $this.val('');
      }
      else {
        var val = $this.val();
        $this.val('').val(val); // Ensure cursor remains at the end
      }
    })
    .blur(function () {
      $this = $(this);

      if ($this.val() === ':') {
        $this.val('');
      }
    });


    // Preview uploaded image
    function readURL(input) {

      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
          $('.img-preview').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
      }
    }

    $(".img-upload").change(function () {
      readURL(this);
    });

});

  