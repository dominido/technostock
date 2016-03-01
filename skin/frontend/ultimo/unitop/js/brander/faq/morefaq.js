if(typeof Morefaq=='undefined') {
    var Morefaq = {};
}

Morefaq.Config = Class.create();
Morefaq.Config.prototype = {
    initialize: function(config) {
        this.listSelector = '#faq-items ul.faq-list';
        this.postUrl = config.url_faq;
        this.limit = config.limit_faq;
        this.maxfaq = config.max_faq_size;
        this.page = 1;
        this.increment = parseInt(config.limit_faq)+1;
        Event.observe($('show-more-faq'), 'click', this.requestMoreFaq.bind(this));
    },

    requestMoreFaq: function(event) {
        event.preventDefault();
        var thisObj = this,
            btnMore = jQuery('#show-more-faq').addClass("spinner");
            thisObj.setNextPage();
            new Ajax.Request(
                this.postUrl, {
                    method:'post',
                    asynchronous:true,
                    evalScripts:false,
                    parameters: {
                        page: this.page,
                        increment : this.increment
                    },
                    onSuccess:function(transport) {
                        try{
                            var response =  transport.responseText.trim();
                            $$(thisObj.listSelector)[0].insert(response);
                            thisObj.increment += parseInt(thisObj.limit);
                            thisObj.setMoreCount();
                            btnMore.removeClass('spinner');
                            jQuery(".faq-question").nextOpener();
                        } catch(e) {
                            btnMore.removeClass('spinner');
                        }

                    }
                }
            );

    },

    setNextPage: function() {
        this.page = this.page + 1;
    },

    setMoreCount: function() {
        var next = this.maxfaq - this.limit*this.page;
        if (next > this.limit && next > 0) {
            $('faq-more-qty').update(this.limit);
        } else if (next > 0) {
            $('faq-more-qty').update(next);
        } else {
            $('show-more-faq').remove();
        }
    }
};