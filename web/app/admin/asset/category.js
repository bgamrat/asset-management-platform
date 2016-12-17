define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/form/ValidationTextBox",
    "dijit/form/CheckBox",
    "dijit/form/Button",
    "dijit/form/Select",
    "dojo/data/ObjectStore",
    "dojo/store/Memory",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on, query,
        ValidationTextBox, CheckBox, Button, Select, ObjectStore, Memory,
        lib, core) {
    //"use strict";

    var dataPrototype, prototypeNode, prototypeContent, store;
    var positionInput = [], parentSelect = [], nameInput = [], commentInput = [], activeCheckBox = [];
    var addOneMoreControl = null;
    var divId = "categories_categories";

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__category__/g, nameInput.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits(newRow) {
        var dijit, index = nameInput.length;
        var base = divId + '_' + index + '_';
        var readOnly = (index === 0);
        dijit = new ValidationTextBox({
            trim: true,
            readOnly: readOnly,
            pattern: "[0-9]+",
            required: !readOnly,
            name: "categories[categories][" + index + "][position]",
            value: document.getElementById(base + "position").value
        }, base + "position");
        positionInput.push(dijit);
        dijit.startup();
        parentSelect = new Select({
            store: store,
            readOnly: readOnly,
            placeholder: core.parent,
            name: "categories[categories][" + index + "][parent]",
            value: document.getElementById(base + "parent").getAttribute("data-selected"),
            required: true
        }, base + "parent");
        parentSelect.startup();
        dijit = new ValidationTextBox({
            placeholder: core.name,
            readOnly: readOnly,
            trim: true,
            pattern: "[a-zA-Z0-9x\.\,\ \+\(\)-]{2,24}",
            required: true,
            name: "categories[categories][" + index + "][name]",
            value: document.getElementById(base + "name").value
        }, base + "name");
        nameInput.push(dijit);
        dijit.startup();
        dijit = new ValidationTextBox({
            placeholder: core.comment,
            readOnly: readOnly,
            trim: true,
            required: false,
            name: "categories[categories][" + index + "][comment]",
            value: document.getElementById(base + "comment").value
        }, base + "comment");
        commentInput.push(dijit);
        dijit.startup();
        dijit = new CheckBox({
            readOnly: readOnly,
            'checked': document.getElementById(base + "active").value === "1" || document.getElementById(base + "active").checked || newRow === true,
            name: "categories[categories][" + index + "][active]"
        }, base + "active");
        activeCheckBox.push(dijit);
        dijit.startup();
    }

    function run() {
        var base, i, existingCategoryRows;

        prototypeNode = dom.byId(divId);

        if( prototypeNode === null ) {
            lib.textError(divId + " not found");
            return;
        }

        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__category__/g, nameInput.length);

        base = prototypeNode.id + "_" + nameInput.length;
        select = base + "_parent";

        if( dom.byId(select) === null ) {
            lib.textError(select + " not found");
            return;
        }

        data = JSON.parse(domAttr.get(select, "data-options"));
        // Convert the data to an array of objects
        storeData = [{value: null, label: ""}];
        for( d in data ) {
            storeData.push(data[d]);
        }

        memoryStore = new Memory({
            idProperty: "value",
            data: storeData});

        store = new ObjectStore({objectStore: memoryStore});
        existingCategoryRows = query('.categories .form-row.category');
        existingCategoryRows = existingCategoryRows.length;

        for( i = 0; i < existingCategoryRows; i++ ) {
            createDijits(false);
        }
        addOneMoreControl = query('.categories .add-one-more-row');

        addOneMoreControl.on("click", function () {
            cloneNewNode();
            createDijits(true);
        });

        var saveBtn = new Button({
            label: core.save,
            type: "submit"
        }, 'categories-save-btn');
        saveBtn.startup();

        lib.pageReady();
    }

    return {
        run: run
    }
});