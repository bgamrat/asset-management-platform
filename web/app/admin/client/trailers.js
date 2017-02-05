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
    function TrailerObj(trailer, dataPrototype, prototypeNode) {
        this.trailer = trailer;
        this.dataPrototype = dataPrototype;
        this.prototypeNode = prototypeNode;
        this.trailerFilteringSelect = [];
        this.quantityInput = [];
        this.valueInput = [];
        this.commentInput = [];
    }

    var trailerObjs = {
        "requires_trailers": null,
        "available_trailers": null,
    };

    var trailerStore;

    function cloneNewNode() {
        var prototypeContent = this.dataPrototype.replace(/__name__/g, this.trailerFilteringSelect.length);
        domConstruct.place(prototypeContent, this.prototypeNode.parentNode, "last");
    }

    function createDijits() {
        var dijit, index = this.trailerFilteringSelect.length;
        var base = this.prototypeNode.id + "_" + index + '_';
        var type = this.prototypeNode.id.replace(/contract_/, '');
        var dijit = new FilteringSelect({
            store: trailerStore,
            labelAttr: "name",
            searchAttr: "name",
            pageSize: 25,
            name: "contract[" + type + "][" + index + "][trailer]",
            displayedValue: document.getElementById(base + "trailer").value,
            required: true
        }, base + "trailer");
        this.trailerFilteringSelect.push(dijit);
        dijit.startup();
        dijit = new CurrencyTextBox({
            placeholder: core.value,
            trim: true,
            required: false,
            name: "contract[" + type + "][" + index + "][value]",
            value: document.getElementById(base + "value").value
        }, base + "value");
        this.valueInput.push(dijit);
        dijit.startup();
        dijit = new ValidationTextBox({
            placeholder: core.comment,
            trim: true,
            required: false,
            name: "contract[" + type + "][" + index + "][comment]",
            value: document.getElementById(base + "comment").value
        }, base + "comment");
        this.commentInput.push(dijit);
        dijit.startup();
    }

    function destroyRow(id, target) {
        var item;
        if( id !== null ) {
            item = this.trailerFilteringSelect.splice(id, 1);
            item[0].destroyRecursive();
            item = this.quantityInput.splice(id, 1);
            item[0].destroyRecursive();
            item = this.valueInput.splice(id, 1);
            item[0].destroyRecursive();
            item = this.commentInput.splice(id, 1);
            item[0].destroyRecursive();
        } else {
            this.trailerFilteringSelect.pop().destroyRecursive();
            this.quantityInput.pop().destroyRecursive();
            this.valueInput.pop().destroyRecursive();
            this.commentInput.pop().destroyRecursive();
        }
        domConstruct.destroy(target);
    }

    function run() {
        var existingRows, c, addOneMoreControl = null;
        trailerStore = new JsonRest({
            target: '/api/store/trailers',
            useRangeHeaders: false,
            idProperty: 'id'});
        var prototypeNode, dataPrototype;
        for( c in trailerObjs ) {
            prototypeNode = dom.byId("contract_" + c);
            dataPrototype = domAttr.get(prototypeNode, "data-prototype");
            addOneMoreControl = query('#contract-' + c + ' .add-one-more-row');

            trailerObjs[c] = new TrailerObj(c, dataPrototype, prototypeNode);
            existingRows = query('.form-row.' + c);
            existingRows = existingRows.length;

            for( i = 0; i < existingRows; i++ ) {
                createDijits.call(trailerObjs[c]);
            }
            addOneMoreControl.on("click", function (event) {
                var dataType = domAttr.get(event.target, "data-type");
                cloneNewNode.call(trailerObjs[dataType]);
                createDijits.call(trailerObjs[dataType]);
            });

            on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
                var target = event.target;
                var targetParent = target.parentNode;
                var idPieces = targetParent.id.split('-');
                destroyRow.call(trailerObjs[idPieces[0]], idPieces[1], targetParent.parentNode);
            });

        }
    }

    return {
        run: run
    }
}
);
//# sourceURL=model_trailers.js