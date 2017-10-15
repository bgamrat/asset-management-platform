define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/form/DateTextBox",
    "dijit/form/TimeTextBox",
    "dijit/form/ValidationTextBox",
    "dijit/form/FilteringSelect",
    'dojo/store/JsonRest',
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/schedule",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on,
        query,
        DateTextBox, TimeTextBox, ValidationTextBox, FilteringSelect,
        JsonRest,
        lib, core, schedule) {
    "use strict";

    var dataPrototype, prototypeNode, prototypeContent;
    var divIdInUse, timeSpanStore;
    var timeSpanId = [], timeSpanFilteringSelect = [];
    var startInput = [], startTimeInput = [], endInput = [], endTimeInput = [], commentInput = [];

    function getDivId() {
        return divIdInUse;
    }

    function setDivId(divId) {
        divIdInUse = divId;
    }

    function cloneNewNode() {
        var prototypeContent = dataPrototype.replace(/__time_span__/g, timeSpanFilteringSelect.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits() {
        var d, index = timeSpanId.length;
        var base = prototypeNode.id + "_" + timeSpanFilteringSelect.length;
        var dijit = new FilteringSelect({
            store: timeSpanStore,
            labelAttr: "name",
            searchAttr: "name",
            pageSize: 25
        }, base + "_type");
        dijit.startup();
        timeSpanFilteringSelect.push(dijit);

        dijit = new DateTextBox({
            placeholder: schedule.start_date,
            trim: true,
            required: true,
            name: "time_span[" + index + "][start]"
        }, base + "_start");
        dijit.startup();
        startInput.push(dijit);

        dijit = new TimeTextBox({
            placeholder: schedule.start_time,
            trim: true,
            required: false
        }, base + "-start-time");
        dijit.startup();
        startTimeInput.push(dijit);

        dijit = new DateTextBox({
            placeholder: schedule.end_date,
            trim: true,
            required: true,
            name: "time_span[" + index + "][end]"
        }, base + "_end");
        dijit.startup();
        endInput.push(dijit);

        dijit = new TimeTextBox({
            placeholder: schedule.end_time,
            trim: true,
            required: false
        }, base + "-end-time");
        dijit.startup();
        endTimeInput.push(dijit);

        dijit = new ValidationTextBox({
            placeholder: core.comment,
            trim: true,
            required: false,
            name: "time_span[" + index + "][comment]"
        }, base + "_comment");
        commentInput.push(dijit);
        dijit.startup();
    }

    function destroyRow(id, target) {
        var item;
        timeSpanId.splice(id, 1);
        item = nameInput.splice(id, 1);
        item[0].destroyRecursive();
        item = commentInput.splice(id, 1);
        item[0].destroyRecursive();
        item = activeCheckBox.splice(id, 1);
        item[0].destroyRecursive();
        item = startInput.splice(id, 1);
        item[0].destroyRecursive();
        item = endInput.splice(id, 1);
        item[0].destroyRecursive();
        item = valueInput.splice(id, 1);
        item[0].destroyRecursive();
        domConstruct.destroy(target);
    }

    function run() {
        var addOneMoreControl = null;

        timeSpanStore = new JsonRest({
            target: '/api/store/timespantypes',
            useRangeHeaders: false,
            idProperty: 'id'});

        if( arguments.length > 0 ) {
            setDivId(arguments[0]);
        }

        prototypeNode = dom.byId(getDivId());
        if( prototypeNode === null ) {
            setDivId(arguments[0] + '_0');
            prototypeNode = dom.byId(getDivId());
        }

        if( prototypeNode === null ) {
            lib.textError(getDivId() + " not found");
            return;
        }

        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__time_span__/g, timeSpanId.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");

        createDijits();

        addOneMoreControl = query('.time_spans .add-one-more-row');

        addOneMoreControl.on("click", function (event) {
            cloneNewNode();
            createDijits();
        });

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var idPieces = targetParent.id.split('-');
            destroyRow(idPieces[1], targetParent.parentNode);
        });

    }

    function getData(relationship) {
        var i, returnData = [];
        for( i = 0; i < timespanFilteringSelect.length; i++ ) {
            returnData.push(
                    parseInt(timespanFilteringSelect[i].get("value")));
        }
        return returnData;
    }

    function setData(relationship, models) {
        var i;

        query(".form-row.time_span", prototypeNode.parentNode).forEach(function (node, index) {
            destroyRow(null, node);
        });

        if( typeof models === "object" && models !== null && models.length > 0 ) {
            for( i = 0; i < models.length; i++ ) {
                cloneNewNode();
                createDijits();
                timeSpanFilteringSelect[i].set("value", timespan[i].id);
                timeSpanFilteringSelect[i].set("displayedValue", timespan[i].name);
            }
        } else {
            cloneNewNode();
            createDijits();
        }
    }

    return {
        run: run,
        getData: getData,
        setData: setData
    }
}
);
//# sourceURL=time_span.js