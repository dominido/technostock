AjaxForm = Class.create();
AjaxForm.prototype = new VarienForm();

AjaxForm.prototype.initialize = (function(superConstructor) {
    return function(formId, firstFieldFocus) {
        superConstructor.call(this, formId, firstFieldFocus);
        if (this.form) {
            this.responseBlock = null;
            this.loadingBlock  = $(this.form.id + '-ajax');
            if (this.loadingBlock) {
                this.loadingBlock.hide();
            }
            this.form.observe('submit', this.submit.bindAsEventListener(this))
            return false;
        }
    };
})(VarienForm.prototype.initialize);

AjaxForm.prototype.submit = function(e) {
    if(this.validator && this.validator.validate()) {
        this._submit(this.form.getAttribute('action'));
    }
    Event.stop(e);
    return false;
};

AjaxForm.prototype._submit = function(url) {
    if (this.loadingBlock) {
        this.form.hide();
        this.loadingBlock.show();
    }
    new Ajax.Request(url, {
        method: this.form.getAttribute('method') || 'get',
        parameters: this.form.serialize(),
        onComplete: this._processResult.bind(this),
        onFailure: function() {
            this.setResponseMessage('error', Translator.translate('Sorry, something went wrong...'));
        }
    });
};

AjaxForm.prototype.setResponseMessage = function(type, msg) {
    if (!this.responseBlock) {
        Element.insert(this.form, { before: '<div></div>' });
        this.responseBlock = this.form.previous('div');
    }
    if (msg != 'success') {
        this.responseBlock.update(msg.join ? msg.join("<br />") : msg);
        this.responseBlock.className = type;
        this.responseBlock.id = 'ajax-form-answer';
        return this;
    } else {
        return this;
    }
};

AjaxForm.prototype._processResult = function(transport){

    var response = '';
    try {
        response = transport.responseText.evalJSON();
    } catch (e) {
        response = transport.responseText;
    }

    if (response.error) {
        if (this.loadingBlock) {
            this.loadingBlock.hide();
            this.form.show();
        }
        this.setResponseMessage('error', response.error);
    } else if(response.success) {
        this.setResponseMessage('success', response.success);
        if (response.form_visibility == 'hide') {
            this.loadingBlock.hide();
            this.form.hide();
        }
    } else {
        this.setResponseMessage('error', Translator.translate('Sorry, something went wrong...'));
    }

    if (response.redirect) {
        location.href = response.redirect;
    }
};
