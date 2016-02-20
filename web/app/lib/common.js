define([
    "dijit/Dialog",
    "dijit/ConfirmDialog",
    "dijit/form/Button",
    "dojo/i18n!app/nls/core",
    "dojo/domReady!"
], function (Dialog, ConfirmDialog, Button, core) {
    "use strict";

    var confirmDialog = new ConfirmDialog({
        title: core.confirm
    });
    confirmDialog.startup();

    var errorDialog = new Dialog({
        title: core.error
    });
    errorDialog.startup();

    function confirmAction(text, callbackFn) {
        confirmDialog.set("execute", callbackFn);
        confirmDialog.set("content", text);
        confirmDialog.show();
    }
    function isEmpty(obj) {
        // Thanks to: http://stackoverflow.com/a/32108184/2182349
        if( typeof obj.keys !== "undefined" ) {
            return obj.keys({}).length !== 0;
        } else {
            for( var prop in obj ) {
                if( obj.hasOwnProperty(prop) )
                    return false;
            }
            return true;
        }
    }
    function textError(msg) {
        errorDialog.set("content", msg);
        errorDialog.show();
    }
    function xhrError(err) {
        var errObj = JSON.parse(err.response.text);
        errorDialog.set("content", errObj.message);
        errorDialog.show();
    }
    return {
        confirmAction: confirmAction,
        isEmpty: isEmpty,
        textError: textError,
        xhrError: xhrError
    };
});