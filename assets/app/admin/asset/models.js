define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/form/FilteringSelect",
    "dojo/store/JsonRest",
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
    var modelStore, modelFilteringSelect = [];
    var divIdInUse = "model_models";
    var addOneMoreControl = null;


    function setDivId(divId) {
        divIdInUse = divId + "_models";
    }

    function getDivId() {
        return divIdInUse;
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__model__/g, modelFilteringSelect.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits() {
        var dijit;
        dijit = new FilteringSelect({
            store: modelStore,
            labelAttr: "name",
            searchAttr: "name",
            pageSize: 25
        }, getDivId() + "_" + modelFilteringSelect.length);
        dijit.startup();
        modelFilteringSelect.push(dijit);
    }

    function destroyRow(id, target) {
        var item;
        item = modelFilteringSelect.splice(id, 1);
        item[0].destroyRecursive();
        domConstruct.destroy(target);
    }

    function run(id) {

        if (typeof id !== "undefined") {
            setDivId(id);
        }

        prototypeNode = dom.byId(getDivId());
        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__model__/g, modelFilteringSelect.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");

        modelStore = new JsonRest({
            target: "/api/store/models",
            useRangeHeaders: false,
            idProperty: "id"});

        createDijits();

        addOneMoreControl = query(".models .add-one-more-row");

        addOneMoreControl.on("click", function (event) {
            cloneNewNode();
            createDijits();
        });

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var id = parseInt(targetParent.id.replace(/\D/g, ""));
            destroyRow(id, targetParent.parentNode);
        });
    }

    function getData() {
        var i, l = modelFilteringSelect.length, returnData = [];
        for( i = 0; i < l; i++ ) {
            if( modelFilteringSelect[i].get("value") !== "" ) {
                returnData.push(
                        modelFilteringSelect[i].get("value")
                        );
            }
        }
        return returnData.length > 0 ? returnData : null;
    }

    function setData(models) {
        var i, l, obj;

        query(".form-row.models", prototypeNode.parentNode).forEach(function (node, index) {
            if( index !== 0 ) {
                destroyRow(index, node);
            }
        });
        modelFilteringSelect[0].set("displayedValue", "");
        if( typeof models === "object" && models !== null ) {
            l = models.length;
            for( i = 0; i < l; i++ ) {
                if (i !== 0) {
                    cloneNewNode();
                    createDijits();
                }
                obj = models[i];
                modelFilteringSelect[i].set("displayedValue", obj.name);
            } 
        }
    }

    return {
        run: run,
        getData: getData,
        setData: setData
    }
});

