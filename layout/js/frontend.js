$(function() {
  "user strict";
  // hide placeholder on hover

  // add astrisk on required field
  $("input").each(function() {
    if ($(this).attr("required") === "required") {
      $(this).after('<span class="astrisk">*</span>');
    }
  });

// confirmation message on button delete
$('.confirm').click(
    function(){return confirm('Delete');}
);

 });
