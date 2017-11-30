define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/form/FilteringSelect",
    "dijit/form/ValidationTextBox",
    "dijit/form/CurrencyTextBox",
    'dojo/store/JsonRest',
    "dojo/i18n!app/nls/core",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on,
        query,
        FilteringSelect, ValidationTextBox, CurrencyTextBox,
        JsonRest,
        core) {
    //"use strict";
    var dataPrototype, prototypeNode, prototypeContent;
    var categoryStore;

    var categoryFilteringSelect = [], quantityInput = [], valueInput = [], commentInput = [];

    function cloneNewNode() {
        var prototypeContent = dataPrototype.replace(/__name__/g, categoryFilteringSelect.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits() {
        var dijit, index = categoryFilteringSelect.length;
        var base = prototypeNode.id + "_" + index + "_";

        var dijit = new FilteringSelect({
            store: categoryStore,
            labelAttr: "name",
            searchAttr: "name",
            pageSize: 25,
            required: true
        }, base + "category");
        categoryFilteringSelect.push(dijit);
        dijit.startup();
        dijit = new ValidationTextBox({
            trim: true,
            pattern: "[0-9]+",
            required: true,
            placeholder: core.quantity
        }, base + "quantity");
        quantityInput.push(dijit);
        dijit.startup();
        dijit = new CurrencyTextBox({
            placeholder: core.value,
            trim: true,
            required: false
        }, base + "value");
        valueInput.push(dijit);
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
        if( id !== null ) {
            item = categoryFilteringSelect.splice(id, 1);
            item[0].destroyRecursive();
            item = quantityInput.splice(id, 1);
            item[0].destroyRecursive();
            item = valueInput.splice(id, 1);
            item[0].destroyRecursive();
            item = commentInput.splice(id, 1);
            item[0].destroyRecursive();
        } else {
            categoryFilteringSelect.pop().destroyRecursive();
            quantityInput.pop().destroyRecursive();
            valueInput.pop().destroyRecursive();
            commentInput.pop().destroyRecursive();
        }
        domConstruct.destroy(target);
    }

    function run() {
        var addOneMoreControl = null;
        categoryStore = new JsonRest({
            target: '/api/store/categories?value',
            useRangeHeaders: false,
            idProperty: 'id'});

        prototypeNode = dom.byId("event_category_quantities");
        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        addOneMoreControl = query('.category-quantities.add-one-more-row');

        addOneMoreControl.on("click", function (event) {
            cloneNewNode();
            createDijits();
        });

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var idPieces = targetParent.id.split('-');
            destroyRow(idPieces[1], targetParent.parentNode);
        });
    }

    function getData() {
        var i, returnData = [];
        for( i = 0; i < categoryFilteringSelect.length; i++ ) {
            returnData.push({
                    "category" : categoryFilteringSelect[i].get("value"),
                    "quantity" : quantityInput[i].get("value"),
                    "value" : valueInput[i].get("value"),
                    "comment" : commentInput[i].get("value")
                })
        }
        return returnData;
    }

    function setData(categoryQuantities) {
        var i,l;

        query(".form-row.category-quantity", prototypeNode.parentNode).forEach(function (node, index) {
            destroyRow(null, node);
        });

        if( typeof categoryQuantities === "object" && categoryQuantities !== null && categoryQuantities.length > 0 ) {
            l = categoryQuantities.length;
            for( i = 0; i < l; i++ ) {
                cloneNewNode();
                createDijits();
                categoryFilteringSelect[i].set("displayedValue", categoryQuantities[i].category.fullName);
                quantityInput[i].set("value", categoryQuantities[i].quantity);
                valueInput[i].set("value", categoryQuantities[i].value);
                commentInput[i].set("value", categoryQuantities[i].comment);
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
//# sourceURL=categoryQuantities.js