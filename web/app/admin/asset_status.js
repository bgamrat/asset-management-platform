define([
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/registry",
    "dijit/form/TextBox",
    "dijit/form/ValidationTextBox",
    "dijit/form/CheckBox",
    "dijit/form/Select",
    "dojo/data/ObjectStore",
    "dojo/store/Memory",
    "dijit/form/Button",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/asset",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (declare, lang, dom, domAttr, domConstruct, on,
        query,
        registry, TextBox, ValidationTextBox, CheckBox, Select, ObjectStore, Memory, Button,
        lib, core, asset) {
    //"use strict";

    var dataPrototype, prototypeNode, prototypeContent;
    var idInput = [], nameInput = [], commentInput = [], activeCheckBox = [];
    var divIdInUse = null;
    var addOneMoreControl = null;
    var divId = "asset_statuses_statuses";

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__status__/g, nameInput.length);
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
            name: "asset_statuses[statuses][" + index + "][name]",
            value: document.getElementById(base + "name").value
        }, base + "name");
        nameInput.push(dijit);
        dijit.startup();
        dijit = new ValidationTextBox({
            placeholder: core.comment,
            trim: true,
            required: false,
            name: "asset_statuses[statuses][" + index + "][comment]",
            value: document.getElementById(base + "comment").value
        }, base + "comment");
        commentInput.push(dijit);
        dijit.startup();
        dijit = new CheckBox({'checked': document.getElementById(base + "active").value === "1" || document.getElementById(base + "active").checked || newRow === true,
            name: "asset_statuses[statuses][" + index + "][active]"}, base + "active");
        activeCheckBox.push(dijit);
        dijit.startup();
    }

    function run() {
        var base, i, existingStatusRows;

        prototypeNode = dom.byId(divId);

        if( prototypeNode === null ) {
            lib.textError(divId + " not found");
            return;
        }

        existingStatusRows = query('.statuses .form-row.status');
        existingStatusRows = existingStatusRows.length;

        for( i = 0; i < existingStatusRows; i++ ) {
            createDijits(false);
        }

        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__status__/g, nameInput.length);
        //domConstruct.place(prototypeContent, "statuses_statuses", "last");

        addOneMoreControl = query('.statuses .add-one-more-row');

        addOneMoreControl.on("click", function (event) {
            cloneNewNode();
            createDijits(true);
        });

        var saveBtn = new Button({
            label: core.save,
            type: "submit"
        }, 'statuses-save-btn');
        saveBtn.startup();

        lib.pageReady();
    }

    return {
        run: run
    }
});