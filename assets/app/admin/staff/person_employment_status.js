define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/form/SimpleTextarea",
    "dijit/form/DateTextBox",
    "dijit/form/TextBox",
    "dijit/form/Select",
    "dojo/data/ObjectStore",
    "dojo/store/Memory",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on,
        query,
        SimpleTextarea, DateTextBox, TextBox,
        Select, ObjectStore, Memory,
        lib, core) {
    //"use strict";

    var dataPrototype, prototypeNode, prototypeContent;
    var employmentStatusSelect = [], startInput = [], endInput = [];
    var divIdInUse = 'person_employment_statuses';
    var addOneMoreControl = null;
    var employmentStatusStore;

    function getDivId() {
        return divIdInUse;
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__employment_status__/g, employmentStatusSelect.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "first");
    }

    function createDijits(showDate) {
        var dijit;
        var base = getDivId() + '_' + employmentStatusSelect.length + '_';
        dijit = new Select({
            store: employmentStatusStore,
            placeholder: core.type,
            required: true,
            "class": "type-select"
        }, base + "employment_status");
        dijit.startup();
        employmentStatusSelect.push(dijit);
        dijit = new DateTextBox({
            placeholder: core.start,
            trim: true,
            required: false,
        }, base + "start");
        dijit.startup();
        startInput.push(dijit);
        dijit = new DateTextBox({
            placeholder: core.end,
            trim: true,
            required: false,
        }, base + "end");
        dijit.startup();
        endInput.push(dijit);
    }

    function destroyRow(id, target) {
        var i, l, item, kid;

        l = employmentStatusSelect.length;
        for( i = 0; i < l; i++ ) {
            kid = employmentStatusSelect[i].id.replace(/\D/g, '');
            if( kid == id ) {
                id = i;
                break;
            }
        }

        item = employmentStatusSelect.splice(id, 1);
        item[0].destroyRecursive();
        item = startInput.splice(id, 1);
        item[0].destroyRecursive();
        item = endInput.splice(id, 1);
        item[0].destroyRecursive();
        domConstruct.destroy(target);
    }

    function run() {
        var select, d, data, storeData, memoryStore;
        
        prototypeNode = dom.byId(getDivId());
        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__employment_status__/g, employmentStatusSelect.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "first");
        select = getDivId() + "_0_employment_status";

        if( dom.byId(select) === null ) {
            lib.textError(select + " not found");
            return;
        }

        data = JSON.parse(domAttr.get(select, "data-options"));
        // Convert the data to an array of objects
        storeData = [];
        for( d in data ) {
            storeData.push(data[d]);
        }

        memoryStore = new Memory({
            idProperty: "value",
            data: storeData});
        employmentStatusStore = new ObjectStore({objectStore: memoryStore});

        createDijits(false);

        addOneMoreControl = query('.employment-statuses .add-one-more-row');

        addOneMoreControl.on("click", function (event) {
            cloneNewNode();
            createDijits();
        });

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var id = parseInt(targetParent.id.replace(/\D/g, ''));
            destroyRow(id, target.closest(".form-row.person-employment-status"));
        });
    }

    function getData() {
        var i, l = employmentStatusSelect.length, returnData = [], st, en;
        for( i = 0; i < l; i++ ) {
            if( employmentStatusSelect[i].get('value') !== "" ) {
                st = startInput[i].get('value');
                en = endInput[i].get('value');
                returnData.push(
                        {
                            "employment_status": employmentStatusSelect[i].get('value'),
                            "start": st === null ? "" : lib.formatDate(st,false),
                            "end": en === null ? "" : lib.formatDate(en,false)
                        });
            }
        }
        return returnData.length > 0 ? returnData : null;
    }

    function setData(employment_statuses) {
        var i, l, timestamp, obj;

        query(".form-row.person-employment-status", prototypeNode.parentNode).forEach(function (node, index) {
            destroyRow(0, node);
        });

        timestamp = new Date();
        if( typeof employment_statuses === "object" && employment_statuses !== null ) {
            l = employment_statuses.length;
            for( i = 0; i < l; i++ ) {
                cloneNewNode();
                createDijits(true);
                obj = employment_statuses[i];
                employmentStatusSelect[i].set("value", obj.employmentStatus.id);
                if( obj.start !== null ) {
                    timestamp.setTime(obj.start.timestamp * 1000);
                    startInput[i].set('value', timestamp);
                } else {
                    startInput[i].set('value', null);
                }
                if( obj.end !== null ) {
                    timestamp.setTime(obj.end.timestamp * 1000);
                    endInput[i].set('value', timestamp);
                } else {
                    endInput[i].set('value', null);
                }
            }
        }
    }

    return {
        run: run,
        getData: getData,
        setData: setData
    }
});