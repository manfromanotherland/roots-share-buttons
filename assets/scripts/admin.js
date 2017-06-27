jQuery(document).ready(function() {

  jQuery('#buttons-sort').sortable({
    stop: function(event, ui) {
      var buttonOrder = jQuery(this).sortable('toArray').toString().replace(/sort_/g, '').replace('googleplus', 'google_plus');
      jQuery('#roots_share_buttons_button_order').val(buttonOrder);
    }
  });

});
