define([
    "dijit/Dialog",
    "dojo/i18n!app/nls/core",
    "dojo/domReady!"
], function (Dialog, core) {
    "use strict";
    
    var errorDialog = new Dialog({
        title: core.error
    });
    errorDialog.startup();

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
    function  xhrError(err) {
        var errObj = JSON.parse(err.response.text);
        errorDialog.set("content", errObj.message);
        errorDialog.show();
    }
    return {
        isEmpty: isEmpty,
        textError: textError,
        xhrError: xhrError
    }
});