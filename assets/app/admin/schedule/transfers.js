define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dojo/request/xhr",
    "app/lib/common",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on,
        query, xhr, lib) {
    "use strict";
    var transfersIn, transfersOut;

    function run() {
        transfersIn = document.getElementById("transfers-in");
        transfersOut = document.getElementById("transfers-out");
    }

    function setData(eventId, venue) {

        domConstruct.empty(transfersIn);
        domConstruct.empty(transfersOut);
        if (eventId !== null) {
            xhr.get('/api/store/events/' + eventId + '/transfers', {
                handleAs: "json"
            }).then(function (data) {
                var target, rowTemplate;
                var i, l, obj;

                l = data.length;
                for(i = 0; i < l; i++) {
                obj = data[i];
                rowTemplate = `
                        <div class = "table-row">
                        <span>${obj.id}</span><span>${obj.status}</span><span>${obj.source_location_text}</span><span>${obj.destination_location_text}</span><span>${obj.amount}</span>
                        </div>
                        `;
                        target = (obj.destination_location_text.indexOf(venue) !== -1) ? transfersIn : transfersOut;
                domConstruct.place(rowTemplate, target, "last");
                }
            });
        }

    }

    return {
        run: run,
        setData: setData
    }
}
);
//# sourceURL=trailer.js