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

    function RelationshipObj(relationship,dataPrototype, prototypeNode, prototypeContent, modelFilteringSelect) {
        this.relationship = relationship;
        this.dataPrototype = dataPrototype;
        this.prototypeNode = prototypeNode;
        this.prototypeContent = prototypeContent;
        this.modelFilteringSelect = modelFilteringSelect;
    }

    var relationshipObjs = {
        "extends": null,
        "requires": null,
        "supports": null
    };

    var modelStore;

    function getDivId() {
        return divIdInUse;
    }

    function setDivId(divId) {
        divIdInUse = 'model_' + divId;
    }

    function cloneNewNode() {
        this.prototypeContent = this.dataPrototype.replace(/__name__/g, this.modelFilteringSelect.length);
        domConstruct.place(this.prototypeContent, this.prototypeNode, "after");
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
        var i;

        for( i = 0; i < this.modelFilteringSelect.length; i++ ) {
            if( this.modelFilteringSelect[i].get("id").indexOf(id) !== -1 ) {
                id = i;
                break;
            }
        }
        this.modelFilteringSelect.splice(id, 1);
        domConstruct.destroy(target);
    }

    function run() {
        var r, addOneMoreControl = null;
        modelStore = new JsonRest({
            target: '/api/model/select',
            useRangeHeaders: false,
            idProperty: 'id'});
        var prototypeNode, dataPrototype, prototypeContent, modelFilteringSelect = [];
        for( r in relationshipObjs ) {
            prototypeNode = dom.byId("model_" + r);
            dataPrototype = domAttr.get(prototypeNode, "data-prototype");
            prototypeContent = dataPrototype.replace(/__name__/g, 0);
            //domConstruct.place(prototypeContent, prototypeNode, "after");

            addOneMoreControl = query('#' + r + ' .add-one-more-row');

            relationshipObjs[r] = new RelationshipObj(r,dataPrototype, prototypeNode, prototypeContent, modelFilteringSelect);
            addOneMoreControl.on("click", function (event) {
                var dataType = domAttr.get(event.target, "data-type");
                cloneNewNode.call(relationshipObjs[dataType]);
                createDijit.call(relationshipObjs[dataType]);
            });
            on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
                var target = event.target;
                var targetParent = target.parentNode;
                var idPieces = targetParent.id.split('-');
                var id;
                destroyRow.call(relationshipObjs[idPieces[0]], parseInt(idPieces[1]), targetParent.parentNode);
            });

        }
    }

    function getData(relationship) {
        var i, returnData = [];
        modelFilteringSelect = relationshipObjs[relationship];
        for( i = 0; i < modelFilteringSelect.length; i++ ) {
            returnData.push(
                    {
                        "model": modelFilteringSelect[i].get("value")
                    });
        }

        return returnData;
    }

    function setData(models) {
        var i, p, obj, nodes;

        nodes = query(".form-row." + relationship, relationship);
        nodes.forEach(function (node, index) {
            destroyRow.call(relationshipObjs[r],index, node);
        });

        if( typeof models === "object" && models !== null && models.length > 0 ) {
            for( i = 0; i < models.length; i++ ) {
                cloneNewNode();
                createDijit();
                modelFilteringSelect.set("displayedValue", models[i].model);
                modelFilteringSelect.set("value", models[i].model_id)
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