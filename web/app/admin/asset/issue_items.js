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
    var itemId = [], itemFilteringSelect = [], statusSelect = [], commentInput = [];
    var itemStore, statusStoreData, statusMemoryStore, statusStore;
    var divIdInUse = 'issue_items';
    var addOneMoreControl = null;

    function getDivId() {
        return divIdInUse;
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__item__/g, itemFilteringSelect.length);
        domConstruct.place(prototypeContent, prototypeNode, "last");
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
        dijit = new Select({
            store: statusStore,
            placeholder: asset.status,
            value: domAttr.get(base + "status", 'data-selected')
        }, base + "status");
        dijit.startup();
        statusSelect.push(dijit);
        dijit = new ValidationTextBox({
            placeholder: core.comment,
            trim: true,
            required: false
        }, base + "comment");
        commentInput.push(dijit);
        dijit.startup();
    }

    function destroyRow(id, target) {
        var i, l, item, kid;

        l = itemFilteringSelect.length;
        for( i = 0; i < l; i++ ) {
            kid = itemFilteringSelect[i].id.replace(/\D/g, '');
            if( kid == id ) {
                id = i;
                break;
            }
        }

        itemId.splice(id, 1);
        item = itemFilteringSelect.splice(id, 1);
        item[0].destroyRecursive();
        item = statusSelect.splice(id, 1);
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

        domConstruct.place(prototypeContent, prototypeNode, "last");

        itemStore = new JsonRest({
            target: '/api/store/barcodes',
            useRangeHeaders: false,
            idProperty: 'id'});

        data = JSON.parse(domAttr.get('issue_items_0_status', "data-options"));
        // Convert the data to an array of objects
        statusStoreData = []
        for( d in data ) {
            statusStoreData.push(data[d]);
        }
        statusMemoryStore = new Memory({
            idProperty: "value",
            data: statusStoreData});
        statusStore = new ObjectStore({objectStore: statusMemoryStore});

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
            destroyRow(id, target.closest(".form-row.issue-item"));
        });
    }

    function getData() {
        var i, l = itemId.length, returnData = [];
        for( i = 0; i < l; i++ ) {
            returnData.push(
                    {
                        //"id": itemId[i],
                        "item": itemFilteringSelect[i].get('value'),
                        "status": statusSelect[i].get('value'),
                        "comment": commentInput[i].get('value')
                    });
        }
        return returnData;
    }

    function setData(items) {
        var i, l, obj, nodes;

        nodes = query(".form-row.issue-item", "items");
        nodes.forEach(function (node, index) {
            destroyRow(0, node);
        });
        if( typeof items === "object" && items !== null ) {
            l = items.length;
            for( i = 0; i < l; i++ ) {
                cloneNewNode();
                createDijits();
                obj = items[i];
                itemId[i] = obj.id;
                itemFilteringSelect[i].set('displayedValue', obj.name);
                statusSelect[i].set('value', obj.asset.status.id);
                commentInput[i].set('value', obj.comment);
            }
        }
    }

    return {
        run: run,
        getData: getData,
        setData: setData
    }
}
);