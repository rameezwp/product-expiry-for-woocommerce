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
});