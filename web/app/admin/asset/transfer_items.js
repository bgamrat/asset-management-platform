define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dojo/data/ObjectStore",
    "dojo/store/Memory",
    'dojo/store/JsonRest',
    "dijit/form/ValidationTextBox",
    "dijit/form/FilteringSelect",
    "dijit/form/Select",
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/asset",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on, query,
        ObjectStore, Memory, JsonRest,
        ValidationTextBox, FilteringSelect, Select,
        core, asset) {
    //"use strict";

    var dataPrototype;
    var prototypeNode, prototypeContent;
    var itemId = [], itemFilteringSelect = [], rmaInput = [], commentInput = [];
    var itemStore;
    var divIdInUse = 'transfer_items';
    var addOneMoreControl = null;

    function getDivId() {
        return divIdInUse;
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__item__/g, itemFilteringSelect.length);
        domConstruct.place(prototypeContent, prototypeNode, "after");
        itemId.push(null);
    }

    function createDijits() {
        var dijit;
        var base = prototypeNode.id + "_" + itemFilteringSelect.length + "_";
        dijit = new FilteringSelect({
            store: itemStore,
            labelAttr: "name",
            searchAttr: "name",
            placeholder: core.item,
            pageSize: 25
        }, base + "item");
        itemFilteringSelect.push(dijit);
        dijit.startup();
        dijit = new ValidationTextBox({
            placeholder: asset.rma,
            trim: true,
            required: false
        }, base + "rma");
        rmaInput.push(dijit);
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
        var i, item;

        for( i = 0; i < itemFilteringSelect.length; i++ ) {
            if( itemFilteringSelect[i].get("id").indexOf(id) !== -1 ) {
                id = i;
                break;
            }
        }
        itemId.splice(id, 1);
        item = itemFilteringSelect.splice(id, 1);
        item[0].destroyRecursive();
        item = rmaInput.splice(id, 1);
        item[0].destroyRecursive();
        item = commentInput.splice(id, 1);
        item[0].destroyRecursive();
        domConstruct.destroy(target);
    }

    function run() {

        var data, d;

        prototypeNode = dom.byId(getDivId());
        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__item__/g, '0');

        domConstruct.place(prototypeContent, prototypeNode, "after");

        itemStore = new JsonRest({
            target: '/api/store/barcodes',
            useRangeHeaders: false,
            idProperty: 'id'});

        createDijits();

        addOneMoreControl = query('.items .add-one-more-row');

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
        var i, l = itemId.length, returnData = [];
        for( i = 0; i < l; i++ ) {
            returnData.push(
                    {
                        "id": itemId[i],
                        "item": itemFilteringSelect[i].get('value'),
                        "rma": rmaFilteringSelect[i].get('value'),
                        "comment": commentInput[i].get('value')
                    });
        }
        return returnData;
    }

    function setData(items) {
        var i, l, obj, nodes;

        nodes = query(".form-row.transfer-item", "items");
        nodes.forEach(function (node, index) {
            if( index !== 0 ) {
                destroyRow(index, node);
            }
        });

        if( typeof items === "object" && items !== null && l > 0 ) {
            l = items.length;
            for( i = 0; i < l; i++ ) {
                cloneNewNode();
                createDijits();
                obj = items[i];
                itemId[i] = obj.id;
                itemFilteringSelect[i].set('displayedValue', obj.name);
                rmaInput[i].set('value', obj.rma);
                commentInput[i].set('value', obj.comment);
            }
        } else {
            itemId[0] = null;
            itemFilteringSelect[0].set('value', "");
            rmaInput[0].set("value", '');
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