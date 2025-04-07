(function ($) {
  'use strict';

  $(document).ready(function () {
    // 新しい行を追加
    $('#add-new-row').on('click', function () {
      var template = $('#row-template').html();
      var index = $('#replacement-rows tr').length;
      template = template.replace(/{{index}}/g, index);
      $('#replacement-rows').append(template);
    });

    // 行を削除
    $(document).on('click', '.remove-row', function () {
      $(this).closest('tr').remove();
    });
  });
})(jQuery);
