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
    var serviceId = [], nameInput = [], commentInput = [], activeCheckBox = [], defaultRadioButton = [];
    var addOneMoreControl = null;
    var divId = "carrier_services";

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__services__/g, serviceId.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
        serviceId.push(null);
    }

    function createDijits(newRow) {
        var dijit, index = nameInput.length;
        var base = divId + '_' + index + '_';
        var checked = false;
        if( index === 0 ) {
            checked = true;
        }
        dijit = new RadioButton({
        }, base + "default");
        dijit.set("checked", checked);
        dijit.startup();
        defaultRadioButton.push(dijit);
        dijit = new ValidationTextBox({
            placeholder: core.name,
            trim: true,
            pattern: "[a-zA-Z0-9x\.\,\ \+\(\)-]{2,24}",
            required: true,
            name: "carrier_services[services][" + index + "][name]",
            value: document.getElementById(base + "name").value
        }, base + "name");
        nameInput.push(dijit);
        dijit.startup();
        dijit = new ValidationTextBox({
            placeholder: core.comment,
            trim: true,
            required: false,
            name: "carrier_services[services][" + index + "][comment]",
            value: document.getElementById(base + "comment").value
        }, base + "comment");
        commentInput.push(dijit);
        dijit.startup();
        dijit = new CheckBox({'checked': document.getElementById(base + "active").value === "1" || document.getElementById(base + "active").checked || newRow === true,
            name: "carrier_services[services][" + index + "][active]"}, base + "active");
        activeCheckBox.push(dijit);
        dijit.startup();
    }

    function run() {

        prototypeNode = dom.byId(divId);
        if( prototypeNode === null ) {
            prototypeNode = dom.byId(divId + '_0');
        }

        if( prototypeNode === null ) {
            lib.textError(divId + " not found");
            return;
        }

        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__services__/g, nameInput.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");

        createDijits();

        on(dom.byId("carrier_services"), "click", function (event) {
            var target = event.target;
            var id = target.id;
            if( target.checked && id.indexOf("default") !== -1 ) {
                id = id.replace(/^.*(\d+).*$/, '$1');
                target.name = 'carrier[services][' + id + '][default]';
            } else {
                target.removeAttribute("name");
            }
        });

        addOneMoreControl = query('.services .add-one-more-row');

        addOneMoreControl.on("click", function (event) {
            cloneNewNode();
            createDijits(true);
        });
    }

    function getData() {
        var i, l = serviceId.length, returnData = [];
        for( i = 0; i < l; i++ ) {
            returnData.push(
                    {
                        "id": serviceId[i],
                        "default": defaultRadioButton[i].get('value'),
                        "name": nameInput[i].get('value'),
                        "comment": commentInput[i].get('value'),
                        "active": activeCheckBox[i].get("checked"),
                    });
        }
        return returnData;
    }

    function setData(services) {
        var i, p, obj;

        query(".form-row.services", prototypeNode.parentNode).forEach(function (node, index) {
            if( index !== 0 ) {
                destroyRow(index, node);
            }
        });

        if( typeof services === "object" && services !== null && services.length > 0 ) {

            for( i = 0; i < services.length; i++ ) {
                if( i !== 0 ) {
                    cloneNewNode();
                    createDijits();
                }
                obj = services[i];
                serviceId[i] = obj.id;
                nameInput[i].set('value', obj.name);
                commentInput[i].set('value', obj.comment);
                activeCheckBox[i].set('value', obj.active);
                defaultRadioButton[i].set('checked', obj.default);
            }
        } else {
            serviceId[0] = null;
            nameInput[0].set('value', "");
            commentInput[0].set('value', "");
            activeCheckBox[0].set('value', "");
            defaultRadioButton[0].set('value', "");
        }
    }

    return {
        run: run,
        getData: getData,
        setData: setData
    }
});