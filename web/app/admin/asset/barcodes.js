define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/form/ValidationTextBox",
    "dijit/form/RadioButton",
    "dijit/form/Button",
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/asset",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on,
        query,
        ValidationTextBox, RadioButton,
        core, asset) {
    //"use strict";

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
            pattern: "[a-zA-Z0-9]{1,15}",
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
        dijit.startup();
        if (barcodeInput.length === 1) {
            dijit.set('checked',true);
        }
        activeRadioButton.push(dijit);
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

        var data, d;
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