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

    function CategoryQuantityObj(categoryQuantity, dataPrototype, prototypeNode) {
        this.categoryQuantity = categoryQuantity;
        this.dataPrototype = dataPrototype;
        this.prototypeNode = prototypeNode;
        this.categoryFilteringSelect = [];
        this.quantityInput = [];
        this.valueInput = [];
        this.commentInput = [];
    }

    var categoryQuantityObjs = {
        "requires": null,
        "available": null,
    };

    var categoryStore;

    function cloneNewNode() {
        var prototypeContent = this.dataPrototype.replace(/__name__/g, this.categoryFilteringSelect.length);
        domConstruct.place(prototypeContent, this.prototypeNode.parentNode, "last");
    }

    function createDijits() {
        var dijit, index = this.categoryFilteringSelect.length;
        var base = this.prototypeNode.id + "_" + index + '_';
        var dijit = new FilteringSelect({
            store: categoryStore,
            labelAttr: "name",
            searchAttr: "name",
            pageSize: 25,
            name: "contract[requires][" + index + "][category]",
            value: document.getElementById(base + "category").getAttribute("data-selected"),
            required: true
        }, base + "category");
        this.categoryFilteringSelect.push(dijit);
        dijit.startup();
        dijit = new ValidationTextBox({
            trim: true,
            pattern: "[0-9]+",
            required: true,
            name: "contract[requires][" + index + "][quantity]",
            value: document.getElementById(base + "quantity").value
        }, base + "quantity");
        this.quantityInput.push(dijit);
        dijit.startup();
        dijit = new CurrencyTextBox({
            placeholder: core.value,
            trim: true,
            required: false
        }, base + "value");
        this.valueInput.push(dijit);
        dijit.startup();
        dijit = new ValidationTextBox({
            placeholder: core.comment,
            trim: true,
            required: false,
            name: "contract[requires][" + index + "][comment]",
            value: document.getElementById(base + "comment").value
        }, base + "comment");
        this.commentInput.push(dijit);
        dijit.startup();
    }

    function destroyRow(id, target) {
        var item;
        if( id !== null ) {
            item = this.categoryFilteringSelect.splice(id, 1);
            item[0].destroyRecursive();
            item = this.quantityInput.splice(id, 1);
            item[0].destroyRecursive();
            item = this.valueInput.splice(id, 1);
            item[0].destroyRecursive();
            item = this.commentInput.splice(id, 1);
            item[0].destroyRecursive();
        } else {
            this.categoryFilteringSelect.pop().destroyRecursive();
            this.quantityInput.pop().destroyRecursive();
            this.valueInput.pop().destroyRecursive();
            this.commentInput.pop().destroyRecursive();
        }
        domConstruct.destroy(target);
    }

    function run() {
        var c, addOneMoreControl = null;
        categoryStore = new JsonRest({
            target: '/api/store/categories',
            useRangeHeaders: false,
            idProperty: 'id'});
        var prototypeNode, dataPrototype;
        for( c in categoryQuantityObjs ) {
            prototypeNode = dom.byId("contract_" + c);
            dataPrototype = domAttr.get(prototypeNode, "data-prototype");

            addOneMoreControl = query('#contract-' + c + ' .add-one-more-row');

            categoryQuantityObjs[c] = new CategoryQuantityObj(c, dataPrototype, prototypeNode);
            addOneMoreControl.on("click", function (event) {
                var dataType = domAttr.get(event.target, "data-type");
                cloneNewNode.call(categoryQuantityObjs[dataType]);
                createDijits.call(categoryQuantityObjs[dataType]);
            });

            on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
                var target = event.target;
                var targetParent = target.parentNode;
                var idPieces = targetParent.id.split('-');
                destroyRow.call(categoryQuantityObjs[idPieces[0]], idPieces[1], targetParent.parentNode);
            });

        }
    }

    return {
        run: run
    }
}
);
//# sourceURL=model_categoryQuantitys.js