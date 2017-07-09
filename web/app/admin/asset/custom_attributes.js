define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/form/ValidationTextBox",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on,
        query,
        ValidationTextBox,
        lib, core) {
    //"use strict";

    var dataPrototype, prototypeNode, prototypeContent;
    var keyInput = [], valueInput = [];
    var divIdInUse = 'model_custom_attributes';
    var addOneMoreControl = null;

    function getDivId() {
        return divIdInUse;
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__name__/g, keyInput.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "first");
    }

    function createDijits() {
        var dijit;
        var base = getDivId() + '_' + keyInput.length + '_';
        dijit = new ValidationTextBox({
            placeholder: core.key,
            trim: true,
            required: true
        }, base + "key");
        keyInput.push(dijit);
        dijit.startup();
        dijit = new ValidationTextBox({
            placeholder: core.value,
            trim: true,
            required: true
        }, base + "value");
        valueInput.push(dijit);
        dijit.startup();
    }

    function destroyRow(id, target) {
        item = keyInput.splice(id, 1);
        item[0].destroyRecursive();
        item = valueInput.splice(id, 1);
        item[0].destroyRecursive();
        domConstruct.destroy(target);
    }

    function run() {

        prototypeNode = dom.byId(getDivId());
        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__name__/g, 0);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "first");

        createDijits();

        addOneMoreControl = query('.custom-attributes.add-one-more-row');

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
        var i, l = keyInput.length, returnData = [];
        for( i = 0; i < l; i++ ) {
            if( keyInput[i].get('value') !== "" ) {
                returnData.push(
                        {
                            key: keyInput[i].get("value"),
                            value: valueInput[i].get('value')
                        });
            }
        }
        return returnData;
    }

    function setData(attributes) {
        var i, l, keys;

        query(".form-row.custom-attribute", prototypeNode.parentNode).forEach(function (node, index) {
            destroyRow(index, node);
        });

        if( typeof attributes === "object" && attributes !== null ) {
            keys = attributes.keys();
            l = keys.length;
            for( i = 0; i < l; i++ ) {
                cloneNewNode();
                createDijits(true);
                keyInput[i].set('value', keys[i]);
                valueInput[i].set('value', attributes[keys[i]]);
            }
        }
    }

    return {
        run: run,
        getData: getData,
        setData: setData
    }
});