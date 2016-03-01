var customerCallbacks = (function($){

    var actionStop;

    var callbackForm;

    var private_methods = {

        animateLoader: function(action) {
            if(action == 'start') {
                $('.callbacks_loader').fadeIn();
            } else {
                $('.callbacks_loader').fadeOut();
            }
        },
        setMessage: function(msg) {
            $('.callbacks_message').html(msg);
        },
        clearFields: function() {
            $('#callbacks_name').val('');
            $('#callbacks_phone').val('');
            $('#callbacks_comment').val('');
            var phone = $('#callbacks_phone');
            phone.data('bfhphone', null);
            phone.bfhphone(phone.data())
        },
        hideForm: function() {
            $('.callbacks-welcome-message').fadeOut();
            $('.webforms-fields-username').fadeOut();
            $('.webforms-fields-phonenumber').fadeOut();
            $('.webforms-fields-comment').fadeOut();
            $('.webforms-callback .buttons-set').fadeOut();
        },
        callAjaxControllerLogin: function(url){
            if (actionStop != true){

                actionStop = true;
                private_methods.animateLoader('start');
                private_methods.setMessage('');
                var name = $('#callbacks_name').val();
                var phone = $('#callbacks_phone').val();
                var comment = $('#callbacks_comment').val();
                var current_url = $('#callbacks_current_url').val();

                jQuery.post( url, { name: name, phone: phone, comments: comment, current_url: current_url})
                    .done(function(msg) {
                        if (msg.error){
                            private_methods.setMessage(msg.error);
                        } else if(msg.success) {
                            private_methods.clearFields();
                            private_methods.hideForm();
                            private_methods.setMessage(msg.success);
                        }
                        actionStop = false;
                        private_methods.animateLoader('stop');
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        actionStop = false;
                        private_methods.animateLoader('stop');
                    });
            }
        }

    }

    var public_methods = {

        callbackInit: function(){
            callbackForm = new VarienForm('form_customercallbacks');
            actionStop = false;
        },
        submitForm: function () {
            $('#form_customercallbacks').submit(function(){
                if(callbackForm.validator.validate()) {
                    private_methods.callAjaxControllerLogin($('#form_customercallbacks').attr('action'));
                }
                return false;
            });
        }
    }

    return public_methods;
})(jQuery);














/*


jQuery.each( [ "get", "post" ], function( i, method ) {
    jQuery[ method ] = function( url, data, callback, type ) {
        // shift arguments if data argument was omitted
        if ( jQuery.isFunction( data ) ) {
            type = type || callback;
            callback = data;
            data = undefined;
        }

        return jQuery.ajax({
            type: method,
            url: url,
            data: data,
            success: callback,
            dataType: type
        });
    };
});




jQuery(function($){

    var getOverlay = function(){
        return $("<div/>").addClass("sp-overlay");
    }

    $("#callback_form").on("submit",function(e){
        e.preventDefault();
        var form = $(this), note = form.find(".note").hide(), form_callback = form.find(".form_call");

        var spinner = getOverlay().appendTo(form.parent());
        if(!form.find('input').hasClass('validation-failed')){
            var url = form.attr("action");
            $.post(url,form.serialize(),function(data){
                if (data.success === true) {
                    note.fadeIn().html(data.message);
                    form_callback.fadeOut();
                }
                if (data.success === false) {
                    note.fadeIn().html(data.message);
                }
            },'json')
                .complete(function(){spinner.remove()});
        }
    });
});
*/