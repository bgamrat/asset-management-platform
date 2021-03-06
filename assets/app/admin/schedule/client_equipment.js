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
    "dojo/i18n!app/nls/schedule",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on, query,
        JsonRest,
        ValidationTextBox, FilteringSelect,
        core, schedule) {
    //"use strict";

    var dataPrototype;
    var prototypeNode, prototypeContent;
    var quantityInput = [], categoryFilteringSelect = [], commentInput = [];
    var categoryStore;
    var divIdInUse = 'event_client_equipment';
    var addOneMoreControl = null;

    function getDivId() {
        return divIdInUse;
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__client_equipment__/g, categoryFilteringSelect.length);
        domConstruct.place(prototypeContent, prototypeNode, "last");
    }

    function createDijits() {
        var dijit;
        var base = prototypeNode.id + "_" + categoryFilteringSelect.length + "_";
        dijit = new FilteringSelect({
            store: categoryStore,
            labelAttr: "name",
            searchAttr: "name",
            pageSize: 25,
            required: true
        }, base + "category");
        dijit.startup();
        categoryFilteringSelect.push(dijit);
        dijit = new ValidationTextBox({
            trim: true,
            pattern: "[0-9]+",
            required: true,
            placeholder: core.quantity
        }, base + "quantity");
        quantityInput.push(dijit);
        dijit.startup();
        quantityInput.push(dijit);
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

        item = categoryFilteringSelect.splice(id, 1);
        item[0].destroyRecursive();
        item = quantityInput.splice(id, 1);
        item[0].destroyRecursive();
        item = commentInput.splice(id, 1);
        item[0].destroyRecursive();
        domConstruct.destroy(target);
    }

    function run() {

        prototypeNode = dom.byId(getDivId());
        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__client_equipment__/g, '0');

        categoryStore = new JsonRest({
            target: '/api/store/categories',
            useRangeHeaders: false,
            idProperty: 'id'});

        addOneMoreControl = query('.client-equipment .add-one-more-row');

        addOneMoreControl.on("click", function (event) {
            cloneNewNode();
            createDijits();
        });

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var id = parseInt(targetParent.id.replace(/\D/g, ''));
            destroyRow(id, target.closest(".form-row.client-equipment"));
        });
    }

    function getData() {
        var i, l = categoryFilteringSelect.length, returnData = [];
        for( i = 0; i < l; i++ ) {
            returnData.push(
                    {
                        "category": categoryFilteringSelect[i].get('value'),
                        "quantity": quantityInput[i].get('value'),
                        "comment": commentInput[i].get('value')
                    });
        }
        return returnData;
    }

    function setData(clientEquipment) {
        var i, l, obj, nodes;

        nodes = query(".form-row.client-equipment", "client-equipment");
        nodes.forEach(function (node, index) {
            destroyRow(0, node);
        });
        if( typeof clientEquipment === "object" && clientEquipment !== null ) {
            l = clientEquipment.length;
            for( i = 0; i < l; i++ ) {
                cloneNewNode();
                createDijits();
                obj = clientEquipment[i];
                categoryFilteringSelect[i].set('displayedValue', obj.category.fullName);
                quantityInput[i].set('value', obj.quantity);
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