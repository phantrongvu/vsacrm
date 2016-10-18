/**
 * Created with JetBrains PhpStorm.
 * User: phantrongvu
 * Date: 8/01/13
 * Time: 11:18 PM
 * To change this template use File | Settings | File Templates.
 */

$(function() {
  // time
  $('#scheduled_time_from').change(function() {
    var from = parseFloat($(this).val());
    var to = parseFloat($('#scheduled_time_to').val());

    $('#scheduled_time_to').val(from + 0.5);
  });

  //$('#scheduled_time_to').attr('readonly', 'readonly');
  $('#scheduled_time_to').val(parseFloat($('#scheduled_time_from').val()) + 0.5);

  /*
  NOT NEED TO SET TO FIELD, since it's set to disabled
  $('#scheduled_time_to').change(function() {
    var to = parseFloat($(this).val());
    var from = parseFloat($('#scheduled_time_from').val());

    if(from > to) {
      $('#scheduled_time_from').val(to);
    }
  });
  */

  // student auto complete
  $('.student-autocomplete').autocomplete({
    source: VSACRM.base_url + 'people/search_json/student',
    select: function(event, ui) {
      $('.student-autocomplete').val(ui.item.label);
      $('input[name="student"]').val(ui.item.value);
      return false;
    }
  });

  // date picker
  $('#scheduled_date').datepicker({
    dateFormat: 'dd/mm/yy',
    //minDate: 0,
    showOtherMonths: true
  });
});
