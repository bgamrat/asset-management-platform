define([
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dojo/html",
    "dojo/data/ObjectStore",
    "dojo/store/Memory",
    "dijit/registry",
    "dijit/form/TextBox",
    "dijit/form/ValidationTextBox",
    "dijit/form/RadioButton",
    "dijit/form/Select",
    "dijit/form/Button",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/asset",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (declare, lang, dom, domAttr, domConstruct, on,
        query, html, ObjectStore, Memory,
        registry, TextBox, ValidationTextBox, RadioButton, Select, Button,
        lib, core, asset) {
    "use strict";

    var dataPrototype;
    var prototypeNode, prototypeContent;
    var store;
    var barcodeId = [], barcodeInput = [], commentInput = [], activeRadioButton = [];
    var divIdInUse = null;
    var addOneMoreControl = null;

    function getDivId() {
        return divIdInUse;
    }

    function setDivId(divId) {
        divIdInUse = divId + '_barcodes';
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__barcode__/g, barcodeInput.length);
        domConstruct.place(prototypeContent, prototypeNode, "after");
        barcodeId.push(null);
    }

    function createDijits() {
        var dijit;
        var base = prototypeNode.id + "_" + barcodeInput.length + "_";
        dijit = new ValidationTextBox({
            placeholder: asset.barcode,
            required: false,
            pattern: "^[a-zA-Z0-9]{1,15}$",
            trim: true
        }, base + "barcode");
        barcodeInput.push(dijit);
        dijit.startup();
        dijit = new ValidationTextBox({
            placeholder: core.comment,
            trim: true,
            required: false
        }, base + "comment");
        commentInput.push(dijit);
        dijit.startup();
        dijit = new RadioButton({}, base + "active");
        activeRadioButton.push(dijit);
        dijit.startup();
    }

    function destroyRow(id, target) {
        var i, item;

        for( i = 0; i < barcodeInput.length; i++ ) {
            if( barcodeInput[i].get("id").indexOf(id) !== -1 ) {
                id = i;
                break;
            }
        }
        barcodeId.splice(id, 1);
        item = barcodeInput.splice(id, 1);
        item[0].destroyRecursive();
        item = commentInput.splice(id, 1);
        item[0].destroyRecursive();
        item = activeRadioButton.splice(id, 1);
        item[0].destroyRecursive();
        domConstruct.destroy(target);
    }

    function run() {

        var base, data, d;
        if( arguments.length > 0 ) {
            setDivId(arguments[0]);
        }

        prototypeNode = dom.byId(getDivId());
        if( prototypeNode === null ) {
            setDivId(arguments[0] + '_0');
            prototypeNode = dom.byId(getDivId());
        }
        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__barcode__/g, barcodeInput.length);
        base = prototypeNode.id + "_" + barcodeInput.length;

        domConstruct.place(prototypeContent, prototypeNode, "after");

        createDijits();

        addOneMoreControl = query('.barcodes .add-one-more-row');

        addOneMoreControl.on("click", function (event) {
            cloneNewNode();
            createDijits();
        });

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var id = parseInt(targetParent.id.replace(/\D/g, ''));
            destroyRow(id, targetParent.parentNode);
        });
    }

    function getData() {
        var i, returnData = [];
        for( i = 0; i < barcodeInput.length; i++ ) {
            returnData.push(
                    {
                        "id": barcodeId[i],
                        "barcode": barcodeInput[i].get('value'),
                        "comment": commentInput[i].get('value'),
                        "active": activeRadioButton[i].get("checked"),
                    });
        }
        return returnData;
    }

    function setData(barcodes) {
        var i, p, obj, nodes;

        nodes = query(".form-row.barcode", "barcodes");
        nodes.forEach(function (node, index) {
            destroyRow(index, node);
        });

        if( typeof barcodes === "object" && barcodes !== null && barcodes.length > 0 ) {
            for( i = 0; i < barcodes.length; i++ ) {
                cloneNewNode();
                createDijits();
                obj = barcodes[i];
                barcodeId[i] = obj.id;
                barcodeInput[i].set('value', obj.barcode);
                commentInput[i].set('value', obj.comment);
                activeRadioButton[i].set('checked', obj.active);
            }
        } else {
            barcodeId[0] = null;
            barcodeInput[0].set('value', "");
            commentInput[0].set('value', "");
        }
    }

    function getActive() {
        var i, activeSet = false;
        for( i = 0; i < activeRadioButton.length; i++ ) {
            if( activeRadioButton[i].get("checked") === true ) {
                activeSet = true;
                break;
            }
        }
        return activeSet ? barcodeInput[i].get("value") : null;
    }

    return {
        run: run,
        getData: getData,
        setData: setData,
        getActive: getActive
    }
}
);