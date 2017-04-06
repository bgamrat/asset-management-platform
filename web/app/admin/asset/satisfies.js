define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/form/FilteringSelect",
    'dojo/store/JsonRest',
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on,
        query,
        FilteringSelect, JsonRest,
        lib, core) {
    //"use strict";

    var dataPrototype, prototypeNode, prototypeContent;
    var categoryId = [], categoryStore, categoryFilteringSelect = [];
    var divIdInUse = 'model_satisfies';
    var addOneMoreControl = null;

    function getDivId() {
        return divIdInUse;
    }

    function cloneNewNode() {
        categoryId.push(null);
        prototypeContent = dataPrototype.replace(/__satisfies__/g, categoryId.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits() {
        var dijit;
        dijit = new FilteringSelect({
            store: categoryStore,
            labelAttr: "name",
            searchAttr: "name",
            pageSize: 25
        }, getDivId() + '_' + categoryId.length);
        dijit.startup();
        categoryFilteringSelect.push(dijit);
    }

    function destroyRow(id, target) {
        var i, l = categoryId.length, item;

        for( i = 0; i < l; i++ ) {
            if( categoryId[i] === id ) {
                id = i;
                break;
            }
        }
        categoryId.splice(id, 1);
        item = categoryFilteringSelect.splice(id, 1);
        item[0].destroyRecursive();
        domConstruct.destroy(target);
    }

    function run() {

        prototypeNode = dom.byId(getDivId());
        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__satisfies__/g, categoryId.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");

        categoryStore = new JsonRest({
            target: '/api/store/categories',
            useRangeHeaders: false,
            idProperty: 'id'});

        createDijits();

        addOneMoreControl = query('.satisfies.add-one-more-row');

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
        var i, l = categoryId.length, returnData = [];
        for( i = 0; i < l; i++ ) {
            if( categoryInput[i].get('value') !== "" ) {
                returnData.push(
                        {
                            "id": categoryId[i],
                            "category": categoryInput[i].get('value')
                        });
            }
        }
        return returnData.length > 0 ? returnData : null;
    }

    function setData(categories) {
        var i, l, obj;

        query(".form-row.satisfies", prototypeNode.parentNode).forEach(function (node, index) {
            if( index !== 0 ) {
                destroyRow(index, node);
            }
        });

        if( typeof categories === "object" && categories !== null ) {
            l = categories.length;
            for( i = 0; i < l; i++ ) {
                cloneNewNode();
                createDijits(true);
                obj = categories[i];
                categoryId[i] = obj.id;
                categoryInput[i].set('value', obj.category);
            }
        } else {
            categoryId[0] = null;
            categoryFilteringSelect[0].set('value', '');
        }
    }

    return {
        run: run,
        getData: getData,
        setData: setData
    }
});

