define([
    'put-selector/put',
    "dojo/domReady!"
], function (put) {
    "use strict";

    function renderGridCheckbox(object, value, td) {
        if( value === false ) {
            put(td, "span.dijit.dijitReset.dijitInline.dijitCheckBox", "");
        } else {
            put(td, "span.dijit.dijitReset.dijitInline.dijitCheckBox.dijitCheckBoxChecked.dijitChecked", "");
        }
    };

    return {
        renderGridCheckbox: renderGridCheckbox
    };
});