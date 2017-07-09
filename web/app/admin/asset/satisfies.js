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
    var categoryStore, categoryFilteringSelect = [];
    var divIdInUse = 'model_satisfies';
    var addOneMoreControl = null;

    function getDivId() {
        return divIdInUse;
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__satisfies__/g, categoryFilteringSelect.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits() {
        var dijit;
        dijit = new FilteringSelect({
            store: categoryStore,
            labelAttr: "name",
            searchAttr: "name",
            pageSize: 25
        }, getDivId() + '_' + categoryFilteringSelect.length);
        dijit.startup();
        categoryFilteringSelect.push(dijit);
    }

    function destroyRow(id, target) {
        var item;
        item = categoryFilteringSelect.splice(id, 1);
        item[0].destroyRecursive();
        domConstruct.destroy(target);
    }

    function run() {

        prototypeNode = dom.byId(getDivId());
        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__satisfies__/g, categoryFilteringSelect.length);
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
        var i, l = categoryFilteringSelect.length, returnData = [];
        for( i = 0; i < l; i++ ) {
            if( categoryFilteringSelect[i].get('value') !== "" ) {
                returnData.push(
                        categoryFilteringSelect[i].get('value')
                        );
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
        categoryFilteringSelect[0].set('displayedValue', '');
        if( typeof categories === "object" && categories !== null ) {
            l = categories.length;
            for( i = 0; i < l; i++ ) {
                if (i !== 0) {
                    cloneNewNode();
                    createDijits();
                }
                obj = categories[i];
                categoryFilteringSelect[i].set('displayedValue', obj.name);
            } 
        }
    }

    return {
        run: run,
        getData: getData,
        setData: setData
    }
});

