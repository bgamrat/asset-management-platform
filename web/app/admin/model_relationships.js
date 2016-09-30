define([
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-class",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/request/xhr",
    "dojo/query",
    "dijit/form/FilteringSelect",
    'dojo/store/JsonRest',
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/asset",
    "dojo/domReady!"
], function (declare, lang, dom, domAttr, domClass, domConstruct, on,
        xhr, query,
        FilteringSelect,
        JsonRest,
        lib, core, asset) {
    "use strict";

    function RelationshipObj(relationship, dataPrototype, prototypeNode, modelFilteringSelect) {
        this.relationship = relationship;
        this.dataPrototype = dataPrototype;
        this.prototypeNode = prototypeNode;
        this.modelFilteringSelect = [];
    }

    var relationshipObjs = {
        "extends": null,
        "requires": null,
        "extended_by": null,
        "required_by": null
    };

    var modelStore;

    function getDivId() {
        return divIdInUse;
    }

    function setDivId(divId) {
        divIdInUse = 'model_' + divId;
    }

    function cloneNewNode() {
        var prototypeContent = this.dataPrototype.replace(/__name__/g, this.modelFilteringSelect.length);
        domConstruct.place(prototypeContent, this.prototypeNode.parentNode, "last");
    }

    function createDijit() {
        var base = this.prototypeNode.id + "_" + this.modelFilteringSelect.length;
        var dijit = new FilteringSelect({
            store: modelStore,
            labelAttr: "name",
            searchAttr: "name",
            pageSize: 25
        }, base);
        dijit.startup();
        this.modelFilteringSelect.push(dijit);
    }

    function destroyRow(id, target) {
        var item;
        if (id !== null) {
            item = this.modelFilteringSelect.splice(id, 1);
            item[0].destroyRecursive();
        } else {
            this.modelFilteringSelect.pop().destroyRecursive();
        }
        domConstruct.destroy(target);
    }

    function run() {
        var r, addOneMoreControl = null;
        modelStore = new JsonRest({
            target: '/api/models',
            useRangeHeaders: false,
            idProperty: 'id'});
        var prototypeNode, dataPrototype;
        for( r in relationshipObjs ) {
            prototypeNode = dom.byId("model_" + r);
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
        var i, returnData = [], modelFilteringSelect;
        modelFilteringSelect = relationshipObjs[relationship].modelFilteringSelect;
        for( i = 0; i < modelFilteringSelect.length; i++ ) {
            returnData.push(
                    parseInt(modelFilteringSelect[i].get("value")));
        }
        return returnData;
    }

    function setData(relationship, models) {
        var i, rObj = relationshipObjs[relationship];

        query(".form-row." + relationship, rObj.prototypeNode.parentNode).forEach(function (node, index) {
            destroyRow.call(rObj, null, node);
        });

        if( typeof models === "object" && models !== null && models.length > 0 ) {
            for( i = 0; i < models.length; i++ ) {
                cloneNewNode.call(rObj);
                createDijit.call(rObj);
                rObj.modelFilteringSelect[i].set("value", models[i].id);
                rObj.modelFilteringSelect[i].set("displayedValue", models[i].name);
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
//# sourceURL=model_relationships.js