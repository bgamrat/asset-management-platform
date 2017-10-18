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
    var timeSpanFilteringSelect = [];
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
        var d, index = timeSpanFilteringSelect.length;
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
        startInput[index].on("change", function (evt) {
            endInput[index].set('value', this.value);
        });

        dijit = new TimeTextBox({
            placeholder: schedule.start_time,
            trim: true,
            required: false
        }, base + "-start-time");
        dijit.startup();
        startTimeInput.push(dijit);
        startTimeInput[index].on("change", function (evt) {
            endTimeInput[index].set('value', this.value);
        });

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
        if( id !== null ) {
            item = timeSpanFilteringSelect.splice(id, 1);
            item[0].destroyRecursive();
            item = startInput.splice(id, 1);
            item[0].destroyRecursive();
            item = startTimeInput.splice(id, 1);
            item[0].destroyRecursive();
            item = endInput.splice(id, 1);
            item[0].destroyRecursive();
            item = endTimeInput.splice(id, 1);
            item[0].destroyRecursive();
            item = commentInput.splice(id, 1);
            item[0].destroyRecursive();
        } else {
            timeSpanFilteringSelect.pop().destroyRecursive();
            startInput.pop().destroyRecursive();
            startTimeInput.pop().destroyRecursive();
            endInput.pop().destroyRecursive();
            endTimeInput.pop().destroyRecursive();
            commentInput.pop().destroyRecursive();
        }
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
        prototypeContent = dataPrototype.replace(/__time_span__/g, timeSpanFilteringSelect.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");

        createDijits();

        addOneMoreControl = query('.time-spans .add-one-more-row');

        addOneMoreControl.on("click", function (event) {
            cloneNewNode();
            createDijits();
        });

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var id = parseInt(targetParent.id.replace(/\D/g, ''));
            destroyRow(id, target.closest(".form-row.time-span"));
        });

    }

    function getData() {
        var i, returnData = [], st, stt, en, ent;
        for( i = 0; i < timeSpanFilteringSelect.length; i++ ) {
            stt = startTimeInput[i].get('value');
            st = startInput[i].get('value');
            lib.addTimeToDate(st,stt);
            ent = endTimeInput[i].get('value');
            en = endInput[i].get('value');
            lib.addTimeToDate(en,ent);
            returnData.push(
                    {
                        "type": parseInt(timeSpanFilteringSelect[i].get("value")),
                        "start": st === null ? "" : st,
                        "end": en === null ? "" : en,
                        "comment": commentInput[i].get('value')
                    });

        }
        return returnData;
    }

    function setData(timeSpans) {
        var i, timestamp;

        query(".form-row.time-span", prototypeNode.parentNode).forEach(function (node, index) {
            destroyRow(null, node);
        });

        if( typeof timeSpans === "object" && timeSpans !== null && timeSpans.length > 0 ) {
            for( i = 0; i < timeSpans.length; i++ ) {
                cloneNewNode();
                createDijits();
                timeSpanFilteringSelect[i].set("displayedValue", timeSpans[i].name);
                if( timeSpans[i].start !== null ) {
                    timestamp.setTime(timeSpans[i].start.timestamp * 1000);
                    startInput.set('value', timestamp);
                    startTimeInput.set('value', timestamp);
                } else {
                    startInput.set('value', null);
                    startTimeInput.set('value', null);
                }
                if( timeSpans[i].end !== null ) {
                    timestamp.setTime(timeSpans[i].end.timestamp * 1000);
                    endInput.set('value', timestamp);
                    endTimeInput.set('value', timestamp);
                } else {
                    endInput.set('value', null);
                    endTimeInput.set('value', null);
                }
                commentInput[i].set('value', timeSpans[i].comment);

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