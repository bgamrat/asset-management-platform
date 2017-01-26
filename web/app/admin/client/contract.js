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
    "app/admin/client/category_quantities",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct,
        on, query,
        Form, TextBox, ValidationTextBox, DateTextBox, CurrencyTextBox, SimpleTextarea, CheckBox, RadioButton, Button,
        categoryQuantities, lib, core) {
    //"use strict";
    function run() {

        var nameInput, commentInput, activeCheckBox;
        var startInput, endInput, valueInput, containerCheckBox;
        var saveBtn;


        nameInput = new ValidationTextBox({
            placeholder: core.name,
            trim: true,
            pattern: "[a-zA-Z0-9x\.\,\ \+\(\)-]{2,24}",
            required: true
        }, "contract_name");
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



        var saveBtn = new Button({
            label: core.save,
            type: "submit"
        }, 'statuses-save-btn');
        saveBtn.startup();


    }
    categoryQuantities.run();
    lib.pageReady();

    return {
        run: run
    }
});
