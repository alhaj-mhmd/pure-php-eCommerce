$(function() {
  "user strict";
  // hide placeholder on hover

  // add astrisk on required field
  $("input").each(function() {
    if ($(this).attr("required") === "required") {
      $(this).after('<span class="astrisk">*</span>');
    }
  });
// convert password input to text input
  var passfield = $(".password");

  $(".show-pass").hover(
    function() {
      passfield.attr("type", "text");
    },
    function() {
      passfield.attr("type", "password");
    }
  );
// confirmation message on button delete
$('.confirm').click(
    function(){return confirm('Delete');}
);

 });
