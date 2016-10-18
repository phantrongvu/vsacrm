/**
 * Created with JetBrains PhpStorm.
 * User: phantrongvu
 * Date: 3/01/13
 * Time: 7:47 PM
 * To change this template use File | Settings | File Templates.
 */

$(function() {
  $('.search-query').keyup(function(e) {
    if(e.keyCode == 13) {
      $('.btn-search').trigger('click');
    }
  });

  $('.btn-search').click(function() {
    var type = $('.select-type').val();
    window.location.href = VSACRM.base_url + 'people/search/' + type + '/' + $('.search-query').val();
  });

  $('.confirm-delete').click(function(e) {
    e.preventDefault();

    var id = $(this).data('id');
    var type = $(this).data('type');

    $('#modal-delete-person').data('id', id).data('type', type).modal('show');
  });

  $('#modal-delete-person').on('show', function() {
    var id = $(this).data('id'),
      removeBtn = $(this).find('.btn-danger'),
      type = $(this).data('type');

    removeBtn.attr('href', VSACRM.base_url + 'people/delete/' + type +  '/' + id);
  }).modal({ backdrop: true, show: false });
});
