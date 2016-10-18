/**
 * Created with JetBrains PhpStorm.
 * User: phantrongvu
 * Date: 5/01/13
 * Time: 3:16 PM
 * To change this template use File | Settings | File Templates.
 */

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

    $('#modal-delete-product').data('id', id).modal('show');
  });

  $('#modal-delete-product').on('show', function() {
    var id = $(this).data('id'),
      removeBtn = $(this).find('.btn-danger');

    removeBtn.attr('href', VSACRM.base_url + 'admin/product/' + id + '/delete');
  }).modal({ backdrop: true, show: false });
});
