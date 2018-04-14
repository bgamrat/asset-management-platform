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
    var divIdInUse, personStore, eventRoleStore;
    var personSelector = [], eventRoleFilteringSelect = [];
    var startInput = [], startTimeInput = [], endInput = [], endTimeInput = [], commentInput = [];

    function getDivId() {
        return divIdInUse;
    }

    function setDivId(divId) {
        divIdInUse = divId;
    }

    function cloneNewNode() {
        var prototypeContent = dataPrototype.replace(/__role__/g, eventRoleFilteringSelect.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits() {
        var d, index = eventRoleFilteringSelect.length;
        var base = prototypeNode.id + "_" + eventRoleFilteringSelect.length;

        var dijit = new FilteringSelect({
            required: true,
            "class": "name",
            store: personStore,
            searchAttr: "name",
            placeholder: core.lastname
        }, base + "_person");
        dijit.startup();
        personSelector.push(dijit);

        dijit = new FilteringSelect({
            store: eventRoleStore,
            labelAttr: "name",
            searchAttr: "name",
            pageSize: 25
        }, base + "_role");
        dijit.startup();
        eventRoleFilteringSelect.push(dijit);

        dijit = new DateTextBox({
            placeholder: schedule.start_date,
            trim: true,
            required: true,
            name: "event_role[" + index + "][start]"
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
            name: "event_role[" + index + "][end]"
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
            name: "event_role[" + index + "][comment]"
        }, base + "_comment");
        commentInput.push(dijit);
        dijit.startup();
    }

    function destroyRow(id, target) {
        var item;
        if( id !== null ) {
            item = personSelector.splice(id, 1);
            item[0].destroyRecursive();
            item = eventRoleFilteringSelect.splice(id, 1);
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
            personSelector.pop().destroyRecursive();
            eventRoleFilteringSelect.pop().destroyRecursive();
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

        personStore = new JsonRest({
            target: '/api/store/staff',
            useRangeHeaders: false,
            idProperty: 'name'});

        eventRoleStore = new JsonRest({
            target: '/api/store/eventroles',
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
        prototypeContent = dataPrototype.replace(/__role__/g, eventRoleFilteringSelect.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");

        createDijits();

        addOneMoreControl = query('.event-roles .add-one-more-row');

        addOneMoreControl.on("click", function (event) {
            cloneNewNode();
            createDijits();
        });

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var id = parseInt(targetParent.id.replace(/\D/g, ''));
            destroyRow(id, target.closest(".form-row.event-role"));
        });

    }

    function getData() {
        var i, returnData = [], st, stt, en, ent;
        for( i = 0; i < eventRoleFilteringSelect.length; i++ ) {
            stt = startTimeInput[i].get('value');
            st = startInput[i].get('value');
            lib.addTimeToDate(st, stt);
            ent = endTimeInput[i].get('value');
            en = endInput[i].get('value');
            lib.addTimeToDate(en, ent);
            returnData.push(
                    {
                        "person": parseInt(personSelector[i].get("value")),
                        "role": parseInt(eventRoleFilteringSelect[i].get("value")),
                        "start": st === null ? "" : st,
                        "end": en === null ? "" : en,
                        "comment": commentInput[i].get('value')
                    });

        }
        return returnData;
    }

    function setData(eventRoles) {
        var i, timestamp = new Date(), obj;

        query(".form-row.event-role", prototypeNode.parentNode).forEach(function (node, index) {
            destroyRow(null, node);
        });

        if( typeof eventRoles === "object" && eventRoles !== null && eventRoles.length > 0 ) {
            for( i = 0; i < eventRoles.length; i++ ) {
                cloneNewNode();
                createDijits();
                obj = eventRoles[i];
                personSelector[i].set("displayedValue", (obj.person !== null) ? obj.person.fullName : null);
                eventRoleFilteringSelect[i].set("displayedValue", obj.role.name);
                if( obj.start !== null ) {
                    timestamp.setTime(obj.start.timestamp * 1000);
                    startInput[i].set('value', timestamp);
                    startTimeInput[i].set('value', timestamp);
                } else {
                    startInput[i].set('value', null);
                    startTimeInput[i].set('value', null);
                }
                if( obj.end !== null ) {
                    timestamp.setTime(obj.end.timestamp * 1000);
                    endInput[i].set('value', timestamp);
                    endTimeInput[i].set('value', timestamp);
                } else {
                    endInput[i].set('value', null);
                    endTimeInput[i].set('value', null);
                }
                commentInput[i].set('value', obj.comment);

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
//# sourceURL=event_role.js