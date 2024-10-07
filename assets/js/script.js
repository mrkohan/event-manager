jQuery(document).ready(function($) {
    $('#event-rsvp-form').on('submit', function(e) {
        e.preventDefault();

        var event_id = $(this).data('event-id');

        $.ajax({
            url: em_rsvp_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'event_rsvp',
                nonce: em_rsvp_obj.nonce,
                event_id: event_id
            },
            success: function(response) {
                $('#rsvp-response').html('<p>' + response.data + '</p>');
            },
            error: function(response) {
                $('#rsvp-response').html('<p>' + response.responseJSON.data + '</p>');
            }
        });
    });
});
