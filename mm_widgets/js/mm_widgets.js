(function($) {
  var container = document.getElementById(Drupal.settings.mm_widgets.container_id);
  var template = document.getElementById(Drupal.settings.mm_widgets.template_id).innerHTML;
  var url = Drupal.settings.mm_widgets.url;

  $.getJSON(url, function(data) {
    $.each(data.items, function(idx, item) {
      item['formatted_published_at'] = moment(item['published_at']).fromNow();
      var html = Mustache.to_html(template, item);
      $(container).append(html);
    });
    }).done(function() {
    }).fail(function() {
    $('body').append('<h1>Error Connecting to Media Magnet</h1>');

    });

    $(document).on('click', '.filter', function() {
      var showItems = $('article.'+$(this).data('filter')).show();
      var hideItems = $('article').not('.'+$(this).data('filter')).hide();
     $(container).masonry('layout');
    });

})(jQuery);
