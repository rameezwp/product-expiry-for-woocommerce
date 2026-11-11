jQuery(document).ready(function($) {
	$('.woope-form').submit(function(event) {
	    event.preventDefault();
	    var formData = $(this).serialize();

	    Swal.fire({
	        title: 'Saving...',
	        allowOutsideClick: false,
	        didOpen: () => { Swal.showLoading(); }
	    });

	    $.post(ajaxurl, formData, function(response) {
	        if (response.success) {
	            Swal.fire({
	                icon: 'success',
	                title: 'Done!',
	                text: response.data, // This is the string from wp_send_json_success
	                timer: 2000,
	                showConfirmButton: false
	            });
	        } else {
	            Swal.fire({
	                icon: 'warning',
	                title: 'Notice',
	                text: response.data // This is the string from wp_send_json_error
	            });
	        }
	    }).fail(function() {
	        Swal.fire({
	            icon: 'error',
	            title: 'Server Error',
	            text: 'Connection lost or server error. Please refresh.'
	        });
	    });
	});

	$('.woope-hook-selector').on('change', function() {
        var val = $(this).val();
        var target = $('#' + $(this).data('target'));
        var input = target.find('input');

        if (val === 'custom') {
            target.show();
        } else {
            target.hide();
            input.val(val); // Update hidden/text input to the standard hook value
        }
    });
    // Toggle the custom-text row for the "after expiry" display setting.
    $('#woope_expired_date_display').on('change', function() {
        $('#woope_expired_custom_row').toggle($(this).val() === 'custom');
    });

    // Click-to-copy the Pro coupon code.
    $(document).on('click', '.woope-coupon-code', function() {
        var code = $(this).data('code');
        var $label = $(this).find('.woope-coupon-copy');
        var done = function() {
            var prev = $label.text();
            $label.text('Copied!');
            setTimeout(function() { $label.text(prev); }, 1500);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(code).then(done).catch(done);
        } else {
            var t = $('<textarea>').val(code).appendTo('body').select();
            try { document.execCommand('copy'); } catch (e) {}
            t.remove();
            done();
        }
    });
});