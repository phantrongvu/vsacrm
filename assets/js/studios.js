/**
 * Created with JetBrains PhpStorm.
 * User: phantrongvu
 * Date: 5/01/13
 * Time: 2:16 PM
 * To change this template use File | Settings | File Templates.
 */

$(function() {
  $('.confirm-delete').click(function(e) {
    e.preventDefault();

    var id = $(this).data('id');

    $('#modal-delete-studio').data('id', id).modal('show');
  });

  $('#modal-delete-studio').on('show', function() {
    var id = $(this).data('id'),
      removeBtn = $(this).find('.btn-danger');

    removeBtn.attr('href', VSACRM.base_url + 'admin/studio/' + id + '/delete');
  }).modal({ backdrop: true, show: false });
});
