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
    var divIdInUse, trailerStore, trailerFilteringSelect = [];

    function getDivId() {
        return divIdInUse;
    }

    function setDivId(divId) {
        divIdInUse = divId;
    }

    function cloneNewNode() {
        var prototypeContent = dataPrototype.replace(/__trailer__/g, trailerFilteringSelect.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijit() {
        var base = prototypeNode.id + "_" + trailerFilteringSelect.length;
        var dijit = new FilteringSelect({
            store: trailerStore,
            labelAttr: "name",
            searchAttr: "name",
            pageSize: 25,
            required: false
        }, base);
        dijit.startup();
        trailerFilteringSelect.push(dijit);
        dijit.on("change", function () {
            var equipmentLink = dom.byId("trailer-equipment-link-" + this.id);
            equipmentLink.href = '/admin/trailer/' + this.displayedValue + '/equipment-by-category';
        });
    }

    function destroyRow(id, target) {
        var item;
        if( id !== null ) {
            item = trailerFilteringSelect.splice(id, 1);
            item[0].destroyRecursive();
        } else {
            trailerFilteringSelect.pop().destroyRecursive();
        }
        domConstruct.destroy(target);
    }

    function run() {
        var addOneMoreControl = null;

        trailerStore = new JsonRest({
            target: '/api/store/trailers',
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
        prototypeContent = dataPrototype.replace(/__trailer__/g, trailerFilteringSelect.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");

        createDijit();

        addOneMoreControl = query('.trailers .add-one-more-row');

        addOneMoreControl.on("click", function (event) {
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
        for( i = 0; i < trailerFilteringSelect.length; i++ ) {
            returnData.push(
                    parseInt(trailerFilteringSelect[i].get("value")));
        }
        return returnData;
    }

    function getText() {
        var i, returnData = [];
        for( i = 0; i < trailerFilteringSelect.length; i++ ) {
            returnData.push(
                    trailerFilteringSelect[i].get("displayedValue"));
        }
        return returnData.join(', ');
    }

    function setData(relationship, trailers) {
        var i;

        query(".form-row.trailer", prototypeNode.parentNode).forEach(function (node, index) {
            destroyRow(null, node);
        });

        if( typeof trailers === "object" && trailers !== null && trailers.length > 0 ) {
            for( i = 0; i < trailers.length; i++ ) {
                cloneNewNode();
                createDijit();
                trailerFilteringSelect[i].set("value", trailers[i].id);
                trailerFilteringSelect[i].set("displayedValue", trailers[i].name);
            }
        } else {
            cloneNewNode();
            createDijit();
        }
    }

    return {
        run: run,
        getData: getData,
        getText: getText,
        setData: setData
    }
}
);
//# sourceURL=trailer.js