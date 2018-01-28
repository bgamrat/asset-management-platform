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

    function run() {

    }

    function setData(eventId) {
        {
            xhr.get('/api/events/' + eventId + '/transfers', {
                handleAs: "json"
            }).then(function (data) {
                console.log(data);
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