(function($) {
  $('.mm-template').each(function(idx) {
    var container = $(this).data('target');
    var template = $(this).html();
    var url = $(this).data('url');

    $.getJSON(url, function(data) {
      $.each(data.items, function(idx, item) {
        item['formatted_published_at'] = moment(item['published_at']).fromNow();
        var html = Mustache.to_html(template, item);

        $('#'+container).append(html);
      });

    }).done(function() {
    }).fail(function() {
      $('body').append('<h1>Error Connecting to Media Magnet</h1>');
    });

  });

})(jQuery);
