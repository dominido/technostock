var openDialogWindow = {
    overlayShowEffectOptions : null,
    overlayHideEffectOptions : null,
    refreshUrl: null,
    open : function(url) {
        var width = 1000,
            left = (screen.width - width)/2;
            
        var newWindow = window.open(url, "edit","width=" + width + ",height=700,left=" + left + ",status=0,toolbar=0,location=0,scrollbars=1");
        this.checkWindow(newWindow);
        /*if (url) {
            new Ajax.Request(url, {
                parameters: {
                    //element_id: elementId+'_editor',
                },
                onSuccess: function(transport) {
                    try {
                        this.openDialogWindow(transport.responseText);
                    } catch(e) {
                        alert(e.message);
                    }
                }.bind(this)
            });
        }*/
        return false;
    },
    onClosed: function() {
        updateContent(this.refreshUrl);
    },
    checkWindow: function(window) {
        var self = this;
        setTimeout(function() {
            if (window.closed) {
                self.onClosed();
            } else {
                self.checkWindow(window);
            }
        }, 100);
    },
    openDialogWindow : function(content) {
        this.overlayShowEffectOptions = Windows.overlayShowEffectOptions;
        this.overlayHideEffectOptions = Windows.overlayHideEffectOptions;
        Windows.overlayShowEffectOptions = {duration:0};
        Windows.overlayHideEffectOptions = {duration:0};

        Dialog.confirm(content, {
            draggable:true,
            resizable:true,
            closable:true,
            className:"magento",
            windowClassName:"popup-window",
            title:'WYSIWYG Editor',
            width:950,
            height:555,
            zIndex:1000,
            recenterAuto:false,
            hideEffect:Element.hide,
            showEffect:Element.show,
            id:"catalog-wysiwyg-editor",
            buttonClass:"form-button",
            okLabel:"Submit",
            ok: this.okDialogWindow.bind(this),
            cancel: this.closeDialogWindow.bind(this),
            onClose: this.closeDialogWindow.bind(this)//,
            //firedElementId: elementId
        });

        content.evalScripts.bind(content).defer();

        //$(elementId+'_editor').value = $(elementId).value;
    },
    okDialogWindow : function(dialogWindow) {
        /*if (dialogWindow.options.firedElementId) {
            wysiwygObj = eval('wysiwyg'+dialogWindow.options.firedElementId+'_editor');
            wysiwygObj.turnOff();
            if (tinyMCE.get(wysiwygObj.id)) {
                $(dialogWindow.options.firedElementId).value = tinyMCE.get(wysiwygObj.id).getContent();
            } else {
                if ($(dialogWindow.options.firedElementId+'_editor')) {
                    $(dialogWindow.options.firedElementId).value = $(dialogWindow.options.firedElementId+'_editor').value;
                }
            }
        }*/
        this.closeDialogWindow(dialogWindow);
    },
    closeDialogWindow : function(dialogWindow) {
        // remove form validation event after closing editor to prevent errors during save main form
        if (typeof varienGlobalEvents != undefined && editorFormValidationHandler) {
            varienGlobalEvents.removeEventHandler('formSubmit', editorFormValidationHandler);
        }

        //IE fix - blocked form fields after closing
        //$(dialogWindow.options.firedElementId).focus();

        //destroy the instance of editor
        dialogWindow.close();
        Windows.overlayShowEffectOptions = this.overlayShowEffectOptions;
        Windows.overlayHideEffectOptions = this.overlayHideEffectOptions;
    }
};
