jQuery(document).ready(function($) {
    $('#additional-info-form').on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        formData.append('action', 'custom_checkout_file_upload');
        formData.append('security', $('#custom_checkout_nonce_field').val());

        $.ajax({
            url: customCheckout.ajax_url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if(response.success) {
                    alert('Informações enviadas com sucesso!');
                } else {
                    alert('Houve um erro ao enviar as informações.');
                }
            },
            error: function(response) {
                alert('Houve um erro ao enviar as informações.');
            }
        });
    });
});
