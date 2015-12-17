define([], function() {
    "use strict";

    function     isEmpty(obj) {
        // Thanks to: http://stackoverflow.com/a/32108184/2182349
        if (typeof obj.keys !== "undefined") {
            return obj.keys({}).length !== 0;
        } else {
            for (var prop in obj) {
                if (obj.hasOwnProperty(prop))
                    return false;
            }
            return true;
        }
    }
    function  xhrError(err) {
        console.log(err);
    }
    return {
        isEmpty: isEmpty,
        xhrError: xhrError
    }
});