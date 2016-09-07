define([
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dojo/data/ObjectStore",
    "dojo/store/Memory",
    "dijit/registry",
    "dijit/form/TextBox",
    "dijit/form/ValidationTextBox",
    "dijit/form/Select",
    "dijit/form/Button",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/asset",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (declare, lang, dom, domAttr, domConstruct, on,
        query, ObjectStore, Memory,
        registry, TextBox, ValidationTextBox, Select, Button,
        lib, core, asset) {
    "use strict";

    var dataPrototype;
    var prototypeNode, prototypeContent;
    var store;
    var barcodeInput = [], commentInput = [];
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
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
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
    }
    
    function destroyRow(id, target) {
        barcodeInput.pop().destroyRecursive();
        commentInput.pop().destroyRecursive();
        domConstruct.destroy(target);
    }

    function run() {

        var base, data, d;
        if( arguments.length > 0 ) {
            setDivId(arguments[0]);
        }

        prototypeNode = dom.byId(getDivId());
        if (prototypeNode === null) {
            setDivId(arguments[0]+'_0');
            prototypeNode = dom.byId(getDivId());
        }
        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__barcode__/g, barcodeInput.length);
        base = prototypeNode.id + "_" + barcodeInput.length;
        
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
        
        createDijits();

        addOneMoreControl = query('.barcodes .add-one-more-row');
                
        addOneMoreControl.on("click", function (event) {
            cloneNewNode();
            createDijits();
            if (barcodeInput.length >= lib.constant.MAX_PHONE_NUMBERS) {
                addOneMoreControl.addClass("hidden");
            }
        });

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var id = parseInt(targetParent.id.replace(/\D/g, ''));
            destroyRow(id, targetParent.parentNode);
            if (barcodeInput.length <= lib.constant.MAX_PHONE_NUMBERS) {
                addOneMoreControl.removeClass("hidden");
            }
        });
    }

    function getData() {
        var i, returnData = [];
        for( i = 0; i < barcodeInput.length; i++ ) {
            returnData.push(
                    {
                        "barcode": barcodeInput[i].get('value'),
                        "comment": commentInput[i].get('value')
                    });
        }
        return returnData;
    }

    function setData(barcodes) {
        var i, p, obj;

        query(".form-row.barcode", prototypeNode.parentNode).forEach(function (node, index) {
            if( index !== 0 ) {
                destroyRow(index, node);
            }
        });

        if( typeof barcodes === "object" && barcodes !== null && barcodes.length > 0 ) {

            for( i = 0; i < barcodes.length; i++ ) {
                if( i !== 0 ) {
                    cloneNewNode();
                    createDijits();
                }
                obj = barcodes[i];
                barcodeInput[i].set('value', obj.barcode);
                commentInput[i].set('value', obj.comment);
            }
        } else {
            barcodeInput[0].set('value', "");
            commentInput[0].set('value', "");
        }
    }
    
    return {
        run: run,
        getData: getData,
        setData: setData
    }
}
);
