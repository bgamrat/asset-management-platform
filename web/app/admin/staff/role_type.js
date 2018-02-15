define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/form/ValidationTextBox",
    "dijit/form/CheckBox",
    "dijit/form/Button",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct,
        on, query,
        ValidationTextBox, CheckBox, Button,
        lib, core) {
    //"use strict";

    var dataPrototype, prototypeNode, prototypeContent;
    var typeForm, typeInput = [], commentInput = [], activeCheckBox = [];
    var addOneMoreControl = null;
    var divId = "role_types_types";

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__type__/g, typeInput.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits(newRow) {
        var dijit, index = typeInput.length;
        var base = divId + '_' + index + '_';
        dijit = new ValidationTextBox({
            placeholder: core.type,
            trim: true,
            pattern: "[a-zA-Z0-9x\.\,\ \+\(\)-]{2,24}",
            required: true,
            name: "role_types[types][" + index + "][type]",
            value: document.getElementById(base + "type").value
        }, base + "type");
        typeInput.push(dijit);
        dijit.startup();
        dijit = new ValidationTextBox({
            placeholder: core.comment,
            trim: true,
            required: false,
            name: "role_types[types][" + index + "][comment]",
            value: document.getElementById(base + "comment").value
        }, base + "comment");
        commentInput.push(dijit);
        dijit.startup();
        dijit = new CheckBox({'checked': document.getElementById(base + "active").checked || newRow === true,
            name: "role_types[types][" + index + "][active]"}, base + "active");
        activeCheckBox.push(dijit);
        dijit.startup();
    }

    function run() {
        var i, existingTypeRows;

        prototypeNode = dom.byId(divId);

        if( prototypeNode === null ) {
            lib.textError(divId + " not found");
            return;
        }

        existingTypeRows = query('.types .form-row.type');
        existingTypeRows = existingTypeRows.length;

        for( i = 0; i < existingTypeRows; i++ ) {
            createDijits(false);
        }

        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__type__/g, typeInput.length);

        addOneMoreControl = query('.types .add-one-more-row');
        addOneMoreControl.on("click", function (event) {
            cloneNewNode();
            createDijits(true);
        });

        var saveBtn = new Button({
            label: core.save,
            type: "submit"
        }, 'types-save-btn');
        saveBtn.startup();

        lib.pageReady();
    }

    return {
        run: run
    }
});