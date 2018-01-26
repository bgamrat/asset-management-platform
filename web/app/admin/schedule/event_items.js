define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    'dojo/store/JsonRest',
    "dijit/form/ValidationTextBox",
    "dijit/form/FilteringSelect",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on, query,
        JsonRest,
        ValidationTextBox, FilteringSelect,
        core) {
    //"use strict";

    var dataPrototype;
    var prototypeNode, prototypeContent;
    var itemFilteringSelect = [], commentInput = [];
    var itemStore;
    var divIdInUse = 'event_items';
    var addOneMoreControl = null;

    function getDivId() {
        return divIdInUse;
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__item__/g, itemFilteringSelect.length);
        domConstruct.place(prototypeContent, prototypeNode, "last");
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
            placeholder: core.comment,
            trim: true,
            required: false
        }, base + "comment");
        commentInput.push(dijit);
        dijit.startup();
    }

    function destroyRow(id, target) {
        var item;

        item = itemFilteringSelect.splice(id, 1);
        item[0].destroyRecursive();
        item = commentInput.splice(id, 1);
        item[0].destroyRecursive();
        domConstruct.destroy(target);
    }

    function run() {

        prototypeNode = dom.byId(getDivId());
        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__item__/g, '0');

        itemStore = new JsonRest({
            target: '/api/store/barcodes',
            useRangeHeaders: false,
            idProperty: 'id'});

        addOneMoreControl = query('.items .add-one-more-row');

        addOneMoreControl.on("click", function (event) {
            cloneNewNode();
            createDijits();
        });

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var id = parseInt(targetParent.id.replace(/\D/g, ''));
            destroyRow(id, target.closest(".form-row.event-item"));
        });
    }

    function getData() {
        var i, l = itemFilteringSelect.length, returnData = [];
        for( i = 0; i < l; i++ ) {
            returnData.push(
                    {
                        "item": itemFilteringSelect[i].get('value'),
                        "comment": commentInput[i].get('value')
                    });
        }
        return returnData;
    }

    function setData(items) {
        var i, l, obj, nodes;

        nodes = query(".form-row.event-item", "items");
        nodes.forEach(function (node, index) {
            destroyRow(0, node);
        });
        if( typeof items === "object" && items !== null ) {
            l = items.length;
            for( i = 0; i < l; i++ ) {
                cloneNewNode();
                createDijits();
                obj = items[i];
                itemFilteringSelect[i].set('displayedValue', obj.name);
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