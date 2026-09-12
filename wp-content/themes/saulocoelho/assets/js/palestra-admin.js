(function ($) {
  $(function () {
    var pick = $('#sc-palestra-bg-pick');
    if (pick.length && typeof wp !== 'undefined' && wp.media) {
      pick.on('click', function (e) {
        e.preventDefault();
        var frame = wp.media({ title: 'Imagem de fundo', multiple: false });
        frame.on('select', function () {
          var att = frame.state().get('selection').first().toJSON();
          $('#sc-palestra-bg-id').val(att.id);
          $('#sc-palestra-bg-preview').html('<img src="' + att.url + '" alt="" style="max-width:100%;height:auto;" />');
        });
        frame.open();
      });
      $('#sc-palestra-bg-clear').on('click', function (e) {
        e.preventDefault();
        $('#sc-palestra-bg-id').val('');
        $('#sc-palestra-bg-preview').empty();
      });
    }

    $('#sc-palestra-add-extra').on('click', function (e) {
      e.preventDefault();
      var $tbody = $('#sc-palestra-extras tbody');
      var i = $tbody.find('tr').length;
      var row =
        '<tr>' +
        '<td><input type="text" name="sc_palestra_extras[' + i + '][label]" class="widefat" /></td>' +
        '<td><input type="text" name="sc_palestra_extras[' + i + '][key]" class="widefat" placeholder="empresa" /></td>' +
        '<td><select name="sc_palestra_extras[' + i + '][type]"><option value="text">Texto</option><option value="email">E-mail</option><option value="tel">Telefone</option></select></td>' +
        '<td style="text-align:center;"><input type="checkbox" name="sc_palestra_extras[' + i + '][required]" value="1" /></td>' +
        '<td><button type="button" class="button sc-palestra-remove-row">&times;</button></td>' +
        '</tr>';
      $tbody.append(row);
    });

    $(document).on('click', '.sc-palestra-remove-row', function (e) {
      e.preventDefault();
      $(this).closest('tr').remove();
    });
  });
})(jQuery);
