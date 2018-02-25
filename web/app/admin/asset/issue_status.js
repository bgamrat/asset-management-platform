define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/form/ValidationTextBox",
    "dijit/form/NumberTextBox",
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
        ValidationTextBox, NumberTextBox, CheckBox, RadioButton, Button,
        lib, core) {
    //"use strict";

    var dataPrototype, prototypeNode, prototypeContent;
    var orderInput = [], nameInput = [], commentInput = [], inUseCheckBox = [], defaultRadioButton = [];
    var addOneMoreControl = null;
    var divId = "issue_statuses_statuses";

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__status__/g, nameInput.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits(newRow) {
        var dijit, index = nameInput.length;
        var base = divId + '_' + index + '_';
        var checked = false;
        if( dom.byId(base + "default").checked === true ) {
            checked = true;
        }
        dijit = new RadioButton({
        }, base + "default");
        dijit.set("checked", checked);
        dijit.startup();
        defaultRadioButton.push(dijit);
        dijit = new NumberTextBox({
            placeholder: core.order,
            trim: true,
            constraints: {min: 1, max: 200, places: 0},
            required: true,
            "class": "order",
            name: "issue_statuses[statuses][" + index + "][order]",
            value: document.getElementById(base + "order").value
        }, base + "order");
        orderInput.push(dijit);
        dijit.startup();
        dijit = new ValidationTextBox({
            placeholder: core.name,
            trim: true,
            pattern: "[a-zA-Z0-9x\.\,\ \+\(\)-]{2,24}",
            required: true,
            name: "issue_statuses[statuses][" + index + "][name]",
            value: document.getElementById(base + "name").value
        }, base + "name");
        nameInput.push(dijit);
        dijit.startup();
        dijit = new ValidationTextBox({
            placeholder: core.comment,
            trim: true,
            required: false,
            name: "issue_statuses[statuses][" + index + "][comment]",
            value: document.getElementById(base + "comment").value
        }, base + "comment");
        commentInput.push(dijit);
        dijit.startup();
        dijit = new CheckBox({'checked': document.getElementById(base + "in_use").value === "1" || document.getElementById(base + "in_use").checked || newRow === true,
            name: "issue_statuses[statuses][" + index + "][in_use]"}, base + "in_use");
        inUseCheckBox.push(dijit);
        dijit.startup();
    }

    function run() {
        var base, i, existingStatusRows;

        prototypeNode = dom.byId(divId);

        if( prototypeNode === null ) {
            lib.textError(divId + " not found");
            return;
        }

        existingStatusRows = query('.statuses .form-row.issue-status');
        existingStatusRows = existingStatusRows.length;

        for( i = 0; i < existingStatusRows; i++ ) {
            createDijits(false);
        }

        on(dom.byId("issue_statuses_statuses"), "click", function (event) {
            var target = event.target;
            var id = target.id;
            if( target.checked && id.indexOf("default") !== -1 ) {
                id = id.replace(/^.*(\d+).*$/, '$1');
                target.name = 'issue_statuses[statuses][' + id + '][default]';
            } else {
                target.removeAttribute("name");
            }
        });

        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__status__/g, nameInput.length);
        if (existingStatusRows === 0) {
            console.log('zero');
            domConstruct.place(prototypeContent, "issue_statuses_statuses", "last");
            createDijits(true);
        }

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