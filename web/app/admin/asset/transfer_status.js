define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/form/ValidationTextBox",
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
        ValidationTextBox, CheckBox, RadioButton, Button,
        lib, core) {
    //"use strict";

    var dataPrototype, prototypeNode, prototypeContent;
    var nameInput = [], commentInput = [], noneCheckBox = [], inTransitCheckBox = [],
            locationDestinationCheckBox = [], locationUnknownCheckBox = [], activeCheckBox = [], defaultRadioButton = [];
    var addOneMoreControl = null;
    var divId = "transfer_statuses_statuses";

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__status__/g, nameInput.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits(newRow) {
        var dijit, index = nameInput.length, none;
        var base = divId + '_' + index + '_';
        var checked = false;
        dijit = new ValidationTextBox({
            placeholder: core.name,
            trim: true,
            pattern: "[a-zA-Z0-9x\.\,\ \+\(\)-]{2,24}",
            required: true,
            name: "transfer_statuses[statuses][" + index + "][name]",
            value: document.getElementById(base + "name").value
        }, base + "name");
        nameInput.push(dijit);
        dijit.startup();
        dijit = new ValidationTextBox({
            placeholder: core.comment,
            trim: true,
            required: false,
            name: "transfer_statuses[statuses][" + index + "][comment]",
            value: document.getElementById(base + "comment").value
        }, base + "comment");
        commentInput.push(dijit);
        dijit.startup();
        
        none = !document.getElementById(base + "in_transit").checked &&
                !document.getElementById(base + "location_destination").checked &&
                !document.getElementById(base + "location_unknown").checked;
        dijit = new RadioButton({'checked': none, name: "transfer_statuses[statuses][" + index + "][location]"}, base + "none");
        noneCheckBox.push(dijit);
        dijit.startup();
        dijit = new RadioButton({'checked': document.getElementById(base + "in_transit").checked,
            "data-name": "transfer_statuses[statuses][" + index + "][in_transit]",
            name: "transfer_statuses[statuses][" + index + "][location]"}, base + "in_transit");
        inTransitCheckBox.push(dijit);
        dijit.startup();
        dijit = new RadioButton({'checked': document.getElementById(base + "location_destination").checked,
            "data-name": "transfer_statuses[statuses][" + index + "][location_destination]",
            name: "transfer_statuses[statuses][" + index + "][location]"}, base + "location_destination");
        locationDestinationCheckBox.push(dijit);
        dijit.startup();
        dijit = new RadioButton({'checked': document.getElementById(base + "location_unknown").checked,
            "data-name": "transfer_statuses[statuses][" + index + "][location_unknown]",
            name: "transfer_statuses[statuses][" + index + "][location]"}, base + "location_unknown");
        locationUnknownCheckBox.push(dijit);
        dijit.startup();
        dijit = new CheckBox({'checked': document.getElementById(base + "active").checked || newRow === true,
            name: "transfer_statuses[statuses][" + index + "][active]"}, base + "active");
        activeCheckBox.push(dijit);
        dijit.startup();
        if( dom.byId(base + "default").checked === true ) {
            checked = true;
        }
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
            type: "button"
        }, 'statuses-save-btn');
        saveBtn.startup();
        saveBtn.on("click", function (event) {
            var checks = query('[type="radio"]');
            checks.forEach(function (node, index) {
                var id;
                if( node.checked ) {
                    if( node.id.indexOf("default") !== -1 ) {
                        id = node.id.replace(/^.*(\d+).*$/, '$1');
                        node.name = 'transfer_statuses[statuses][' + id + '][default]';
                    } else {
                        if( domAttr.has(node, "data-name") ) {
                            node.name = domAttr.get(node, "data-name");
                        } else {
                            node.removeAttribute("name");
                        }
                    }
                } else {
                    node.removeAttribute("name");
                }
            });
            document.getElementById("status-type-form").submit();
        });
        lib.pageReady();
    }

    return {
        run: run
    }
});