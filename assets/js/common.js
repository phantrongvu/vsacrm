/**
 * disable submit buttons once form is submitting
 */
$(document).ready(function() {
  $("form").on("submit", function () {
    $(this).find(":submit").prop("disabled", true);
  });
});
