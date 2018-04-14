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
    "app/admin/client/trailers",
    "app/admin/client/category_quantities",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct,
        on, query,
        Form, TextBox, ValidationTextBox, DateTextBox, CurrencyTextBox, SimpleTextarea, CheckBox, RadioButton, Button,
        trailers, categoryQuantities, lib, core) {
    //"use strict";
    function run() {
        var nameInput, commentInput, activeCheckBox;
        var startInput, endInput, valueInput, containerCheckBox;
        var d;
        var saveBtn;

        nameInput = new ValidationTextBox({
            placeholder: core.name,
            trim: true,
            pattern: "[a-zA-Z0-9x\.\,\ \+\(\)-]{2,24}",
            required: true,
            name: "contract[name]",
            value: document.getElementById("contract_name").value
        }, "contract_name");
        nameInput.startup();
        commentInput = new SimpleTextarea({
            placeholder: core.comment,
            trim: true,
            required: false,
            name: "contract[comment]",
            value: document.getElementById("contract_comment").value
        }, "contract_comment");
        commentInput.startup();
        activeCheckBox = new CheckBox({
            name: "contract[active]",
            'checked': document.getElementById("contract_active").checked
        }, "contract_active");
        activeCheckBox.startup();
        d = document.getElementById("contract_start").value;
        startInput = new DateTextBox({
            placeholder: core.start,
            trim: true,
            required: false,
            name: "contract[start]",
            value: (d === "") ? null : d
        }, "contract_start");
        startInput.startup();
        d = document.getElementById("contract_end").value;
        endInput = new DateTextBox({
            placeholder: core.end,
            trim: true,
            required: false,
            name: "contract[end]",
            value: (d === "") ? null : d
        }, "contract_end");
        endInput.startup();
        valueInput = new CurrencyTextBox({
            placeholder: core.value,
            trim: true,
            required: false,
            name: "contract[value]",
            value: document.getElementById("contract_value").value
        }, "contract_value");
        valueInput.startup();
        containerCheckBox = new CheckBox({
            name: "contract[container]",
            'checked': document.getElementById("contract_container").checked
        }, "contract_container");
        containerCheckBox.startup();

        var saveBtn = new Button({
            label: core.save,
            type: "submit"
        }, 'contract-save-btn');
        saveBtn.startup();
        lib.pageReady();
    }
    trailers.run();
    categoryQuantities.run();
    lib.pageReady();

    return {
        run: run
    }
});
