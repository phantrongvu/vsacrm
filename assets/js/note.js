/**
 * Created with JetBrains PhpStorm.
 * User: phantrongvu
 * Date: 20/01/13
 * Time: 12:48 PM
 * To change this template use File | Settings | File Templates.
 */

$(function() {
  // student auto complete
  $('.student-autocomplete').autocomplete({
    source: VSACRM.base_url + 'people/search_json/student',
    select: function(event, ui) {
      $('.student-autocomplete').val(ui.item.label);
      $('input[name="student"]').val(ui.item.value);
      return false;
    }
  });
});
