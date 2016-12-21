define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/form/FilteringSelect",
    'dojo/store/JsonRest',
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on,
        query,
        FilteringSelect,
        JsonRest) {
    "use strict";

    function RelationshipObj(relationship, dataPrototype, prototypeNode, trailerFilteringSelect) {
        this.relationship = relationship;
        this.dataPrototype = dataPrototype;
        this.prototypeNode = prototypeNode;
        this.trailerFilteringSelect = [];
    }

    var relationshipObjs = {
        "extends": null,
        "requires": null,
        "extended_by": null,
        "required_by": null
    };

    var trailerStore;

    function getDivId() {
        return divIdInUse;
    }

    function setDivId(divId) {
        divIdInUse = 'trailer_' + divId;
    }

    function cloneNewNode() {
        var prototypeContent = this.dataPrototype.replace(/__name__/g, this.trailerFilteringSelect.length);
        domConstruct.place(prototypeContent, this.prototypeNode.parentNode, "last");
    }

    function createDijit() {
        var base = this.prototypeNode.id + "_" + this.trailerFilteringSelect.length;
        var dijit = new FilteringSelect({
            store: trailerStore,
            labelAttr: "name",
            searchAttr: "name",
            pageSize: 25
        }, base);
        dijit.startup();
        this.trailerFilteringSelect.push(dijit);
    }

    function destroyRow(id, target) {
        var item;
        if (id !== null) {
            item = this.trailerFilteringSelect.splice(id, 1);
            item[0].destroyRecursive();
        } else {
            this.trailerFilteringSelect.pop().destroyRecursive();
        }
        domConstruct.destroy(target);
    }

    function run() {
        var r, addOneMoreControl = null;
        trailerStore = new JsonRest({
            target: '/api/store/trailers',
            useRangeHeaders: false,
            idProperty: 'id'});
        var prototypeNode, dataPrototype;
        for( r in relationshipObjs ) {
            prototypeNode = dom.byId("trailer_" + r);
            dataPrototype = domAttr.get(prototypeNode, "data-prototype");

            addOneMoreControl = query('#' + r + ' .add-one-more-row');

            relationshipObjs[r] = new RelationshipObj(r, dataPrototype, prototypeNode);
            addOneMoreControl.on("click", function (event) {
                var dataType = domAttr.get(event.target, "data-type");
                cloneNewNode.call(relationshipObjs[dataType]);
                createDijit.call(relationshipObjs[dataType]);
            });

            on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
                var target = event.target;
                var targetParent = target.parentNode;
                var idPieces = targetParent.id.split('-');
                destroyRow.call(relationshipObjs[idPieces[0]], idPieces[1], targetParent.parentNode);
            });

        }
    }

    function getData(relationship) {
        var i, returnData = [], trailerFilteringSelect;
        trailerFilteringSelect = relationshipObjs[relationship].trailerFilteringSelect;
        for( i = 0; i < trailerFilteringSelect.length; i++ ) {
            returnData.push(
                    parseInt(trailerFilteringSelect[i].get("value")));
        }
        return returnData;
    }

    function setData(relationship, trailers) {
        var i, rObj = relationshipObjs[relationship];

        query(".form-row." + relationship, rObj.prototypeNode.parentNode).forEach(function (node, index) {
            destroyRow.call(rObj, null, node);
        });

        if( typeof trailers === "object" && trailers !== null && trailers.length > 0 ) {
            for( i = 0; i < trailers.length; i++ ) {
                cloneNewNode.call(rObj);
                createDijit.call(rObj);
                rObj.trailerFilteringSelect[i].set("value", trailers[i].id);
                rObj.trailerFilteringSelect[i].set("displayedValue", trailers[i].name);
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
//# sourceURL=trailer_relationships.js