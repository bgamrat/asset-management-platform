define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/form/FilteringSelect",
    'dojo/store/JsonRest',
    "app/lib/common",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on,
        query,
        FilteringSelect,
        JsonRest, lib) {
    "use strict";

    var dataPrototype, prototypeNode, prototypeContent;
    var divIdInUse, contractStore, contractFilteringSelect = [];

    function getDivId() {
        return divIdInUse;
    }

    function setDivId(divId) {
        divIdInUse = divId;
    }

    function cloneNewNode() {
        var prototypeContent = dataPrototype.replace(/__contract__/g, contractFilteringSelect.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijit() {
        var base = prototypeNode.id + "_" + contractFilteringSelect.length;
        var dijit = new FilteringSelect({
            store: contractStore,
            labelAttr: "name",
            searchAttr: "name",
            pageSize: 25
        }, base);
        dijit.startup();
        contractFilteringSelect.push(dijit);
    }

    function destroyRow(id, target) {
        var item;
        if( id !== null ) {
            item = contractFilteringSelect.splice(id, 1);
            item[0].destroyRecursive();
        } else {
            contractFilteringSelect.pop().destroyRecursive();
        }
        domConstruct.destroy(target);
    }

    function run() {
        var addOneMoreControl = null;

        contractStore = new JsonRest({
            target: '/api/store/contracts',
            useRangeHeaders: false,
            idProperty: 'id'});

        if( arguments.length > 0 ) {
            setDivId(arguments[0]);
        }

        prototypeNode = dom.byId(getDivId());
        if( prototypeNode === null ) {
            setDivId(arguments[0] + '_0');
            prototypeNode = dom.byId(getDivId());
        }

        if( prototypeNode === null ) {
            lib.textError(getDivId() + " not found");
            return;
        }

        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__contract__/g, contractFilteringSelect.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");

        createDijit();

        addOneMoreControl = query('.contracts .add-one-more-row');

        addOneMoreControl.on("click", function (event) {
            var dataType = domAttr.get(event.target, "data-type");
            cloneNewNode();
            createDijit();
        });

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var idPieces = targetParent.id.split('-');
            destroyRow(idPieces[1], targetParent.parentNode);
        });

    }

    function getData(relationship) {
        var i, returnData = [];
        for( i = 0; i < contractsFilteringSelect.length; i++ ) {
            returnData.push(
                    parseInt(contractsFilteringSelect[i].get("value")));
        }
        return returnData;
    }

    function setData(relationship, models) {
        var i;

        query(".form-row.contract", prototypeNode.parentNode).forEach(function (node, index) {
            destroyRow(null, node);
        });

        if( typeof models === "object" && models !== null && models.length > 0 ) {
            for( i = 0; i < models.length; i++ ) {
                cloneNewNode();
                createDijit();
                contractFilteringSelect[i].set("value", contracts[i].id);
                contractFilteringSelect[i].set("displayedValue", contracts[i].name);
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
//# sourceURL=contract.js