/**
 * Created with JetBrains PhpStorm.
 * User: Victor
 * Date: 23/01/13
 * Time: 3:06 PM
 * To change this template use File | Settings | File Templates.
 */

$(function() {
  $('#teacher_id').change(function () {
    var teacher_id = $(this).val();
    if(teacher_id) {
      window.location.href = VSACRM.base_url + 'report/timesheet/' + teacher_id;
    }
  });

  $('.btn-add-extra-item').click(function (e) {
    e.preventDefault();

    $('.ul-extra-items').append('<li class="clearfix"> \
      <input type="text" placeholder="Item" class="input-medium" name="extra_item_name[]" /> \
      <strong><input type="text" placeholder="0.00" class="input-small" name="extra_item_value[]" /></strong> \
      </li>');
  });
});
