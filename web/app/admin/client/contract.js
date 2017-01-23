define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/form/Form",
    "dijit/form/TextBox",
    "dijit/form/ValidationTextBox",
    "dijit/form/DateTextBox",
    "dijit/form/CurrencyTextBox",
    "dijit/form/SimpleTextarea",
    "dijit/form/CheckBox",
    "dijit/form/RadioButton",
    "dijit/form/Button",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct,
        on, query,
        Form, TextBox, ValidationTextBox, DateTextBox, CurrencyTextBox, SimpleTextarea, CheckBox, RadioButton, Button,
        lib, core) {
    //"use strict";

    var dataPrototype, prototypeNode, prototypeContent;
    var nameInput, commentInput, activeCheckBox;
    var startInput, endInput, containerCheckBox;
    var saveBtn;
    var addOneMoreControl = null;

        nameInput = new ValidationTextBox({
            placeholder: core.name,
            trim: true,
            pattern: "[a-zA-Z0-9x\.\,\ \+\(\)-]{2,24}",
            required: true
        }, "contract_name" );
        nameInput.startup();
        commentInput = new SimpleTextarea({
            placeholder: core.comment,
            trim: true,
            required: false
        }, "contract_comment");
        commentInput.startup();
        activeCheckBox = new CheckBox({'checked': true}, "contract_active");
        activeCheckBox.startup();
        startInput = new DateTextBox({
            placeholder: core.start,
            trim: true,
            required: false
        }, "contract_start");
        startInput.startup();
        endInput = new DateTextBox({
            placeholder: core.end,
            trim: true,
            required: false
        }, "contract_end");
        endInput.startup();
        valueInput = new CurrencyTextBox({
            placeholder: core.value,
            trim: true,
            required: false
        }, "contract_value");
        valueInput.startup();
        var saveBtn = new Button({
            label: core.save
        }, 'contract-save-btn');
        saveBtn.startup();
    lib.pageReady();

    return;
    var divId = "asset_statuses_statuses";

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__status__/g, nameInput.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits(newRow) {
        var dijit, index = nameInput.length;
        var base = divId + '_' + index + '_';
        var checked = false;
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
        if( dom.byId(base + "default").checked === true ) {
            checked = true;
        }
        dijit.startup();
        dijit = new RadioButton({
        }, base + "default");
        dijit.set("checked", checked);
        dijit.startup();
        defaultRadioButton.push(dijit);
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

        on(dom.byId("asset_statuses_statuses"), "click", function (event) {
            var target = event.target;
            var id = target.id;
            if( target.checked && id.indexOf("default") !== -1 ) {
                id = id.replace(/^.*(\d+).*$/, '$1');
                target.name = 'asset_statuses[statuses][' + id + '][default]';
            } else {
                target.removeAttribute("name");
            }
        });

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
