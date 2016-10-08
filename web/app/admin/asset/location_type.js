define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
        "dojo/data/ObjectStore",
    "dojo/store/Memory",
        "dijit/form/Select",
    "dijit/form/ValidationTextBox",
    "dijit/form/CheckBox",
    "dijit/form/Button",
    "lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on,
        query, ObjectStore, Memory,
        Select, ValidationTextBox, CheckBox, Button,
        lib, core) {
    //"use strict";

    var dataPrototype, prototypeNode, prototypeContent;
    var nameInput = [], entitySelect = [], urlInput = [], activeCheckBox = [];
    var addOneMoreControl = null;
    var divId = "location_types_types";
    var store;

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__type__/g, nameInput.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits(newRow) {
        var dijit, index = nameInput.length;
        var base = divId + '_' + index + '_';

        dijit = new ValidationTextBox({
            placeholder: core.name,
            trim: true,
            pattern: "[a-zA-Z0-9x\.\,\ \+\(\)-]{2,24}",
            required: true,
            name: "location_types[types][" + index + "][name]",
            value: document.getElementById(base + "name").value
        }, base + "name");
        nameInput.push(dijit);
        dijit.startup();
        dijit = new Select({
            store: store,
            placeholder: core.entity,
            name: "location_types[types][" + index + "][entity]",
            required: true,
            value: domAttr.get(dom.byId(base + "entity"),'data-selected')
        }, base + "entity");
        dijit.startup();
        entitySelect.push(dijit);
        dijit = new ValidationTextBox({
            placeholder: core.url,
            trim: true,
            required: false,
            name: "location_types[types][" + index + "][url]",
            value: document.getElementById(base + "url").value
        }, base + "url");
        urlInput.push(dijit);
        dijit.startup();
        dijit = new CheckBox({'checked': document.getElementById(base + "active").value === "1" || document.getElementById(base + "active").checked || newRow === true,
            name: "location_types[types][" + index + "][active]"}, base + "active");
        activeCheckBox.push(dijit);
        dijit.startup();
    }

    function run() {
        var base, i, existingLocationRows;

        prototypeNode = dom.byId(divId);

        if( prototypeNode === null ) {
            lib.textError(divId + " not found");
            return;
        }
        
        var select = "location_types_types_0_entity";
        var data = JSON.parse(domAttr.get(select, "data-options"));
        // Convert the data to an array of objects
        var entityStoreData = [], d;
        for( d in data ) {
            entityStoreData.push(data[d]);
        }
        var entityMemoryStore = new Memory({
            idProperty: "value",
            data: entityStoreData});
        store = new ObjectStore({objectStore: entityMemoryStore});

        existingLocationRows = query('.location-types .form-row.type');
        existingLocationRows = existingLocationRows.length;

        for( i = 0; i < existingLocationRows; i++ ) {
            createDijits(false);
        }

        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__type__/g, nameInput.length);

        addOneMoreControl = query('.location-types .add-one-more-row');

        addOneMoreControl.on("click", function (event) {
            cloneNewNode();
            createDijits(true);
        });

        var saveBtn = new Button({
            label: core.save,
            type: "submit"
        }, 'location-types-save-btn');
        saveBtn.startup();

        lib.pageReady();
    }

    return {
        run: run
    }
});