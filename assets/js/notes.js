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
    var sid = $(this).data('sid');

    $('#modal-delete-note').data('id', id).data('sid', sid).modal('show');
  });

  $('#modal-delete-note').on('show', function() {
    var id = $(this).data('id'),
      sid = $(this).data('sid'),
      removeBtn = $(this).find('.btn-danger');

    removeBtn.attr('href', VSACRM.base_url + 'note/manage/' + id + '/delete?sid=' + sid);
  }).modal({ backdrop: true, show: false });
});
