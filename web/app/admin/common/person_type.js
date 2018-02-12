define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/form/ValidationTextBox",
    "dijit/form/Button",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct,
        on, query,
        ValidationTextBox, Button,
        lib, core) {
    //"use strict";

    var dataPrototype, prototypeNode, prototypeContent;
    var typeForm, typeInput = [];
    var addOneMoreControl = null;
    var divId = "person_types_types";

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__type__/g, typeInput.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits() {
        var dijit, index = typeInput.length;
        var base = divId + '_' + index + '_';
        dijit = new ValidationTextBox({
            placeholder: core.type,
            trim: true,
            pattern: "[a-zA-Z0-9x\.\,\ \+\(\)-]{2,24}",
            required: true,
            name: "person_types[types][" + index + "][type]",
            value: document.getElementById(base + "type").value
        }, base + "type");
        typeInput.push(dijit);
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

        typeForm = document.getElementById("type-form");
        on(typeForm, "click", function (evt) {
            var formRow = evt.target.closest(".form-row");
            var id = evt.target.id.replace(/\D/g, '');
            var i, l = typeInput.length;
            var idInput, item;
            idInput = document.getElementById("person_types_types_" + id + "_id");
            domConstruct.destroy(idInput);
            for( i = 0; i < l; i++ ) {
                if( typeInput[i].get("id").replace(/\D/g, '') === id ) {
                    id = i;
                    break;
                }
            }
            item = typeInput.splice(id, 1);
            item[0].destroyRecursive();
            domConstruct.destroy(formRow);
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