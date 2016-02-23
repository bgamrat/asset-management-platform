define([
    'put-selector/put',
    "dojo/domReady!"
], function (put) {
    "use strict";

    function renderGridCheckbox(object, value, td) {
        if( value === true ) {
            put(td, "span.dijit.dijitReset.dijitInline.dijitCheckBox.dijitCheckBoxChecked.dijitChecked", "");
        } else {
            put(td, "span.dijit.dijitReset.dijitInline.dijitCheckBox", "");
        }
    };

    return {
        renderGridCheckbox: renderGridCheckbox
    };
});