define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dojo/aspect",
    'dojo/store/JsonRest',
    "dijit/form/CurrencyTextBox",
    "dijit/form/ValidationTextBox",
    "dijit/form/FilteringSelect",
    "dijit/form/Select",
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/schedule",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on, query, aspect,
        JsonRest,
        CurrencyTextBox, ValidationTextBox, FilteringSelect, Select,
        core, schedule) {
    //"use strict";

    var dataPrototype;
    var prototypeNode, prototypeContent;
    var billToId = [], clientId = [], clientFilteringSelect = [], eventId = [], eventFilteringSelect = [], amountInput = [], commentInput = [];
    var clientStore, eventStore;
    var divIdInUse = null;
    var addOneMoreControl = null;
    var currentRowIndex = 0;

    function getDivId() {
        return divIdInUse;
    }

    function setDivId(divId) {
        divIdInUse = divId + '_bill_tos';
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__bill_to__/g, clientFilteringSelect.length);
        domConstruct.place(prototypeContent, prototypeNode, "after");
        billToId.push(null);
    }

    function createDijits() {
        var dijit;
        var base = prototypeNode.id + "_" + clientFilteringSelect.length + "_";
        dijit = new FilteringSelect({
            store: clientStore,
            labelAttr: "name",
            searchAttr: "name",
            placeholder: core.client,
            required: false,
            pageSize: 25
        }, base + "client");
        dijit.on("change", function () {
            var clientId = this.get('value');
            if( !isNaN(clientId) ) {
                eventStore.target = eventStore.target.replace(/\d*$/, clientId);
            }
        });
        clientFilteringSelect.push(dijit);
        dijit.startup();
        dijit = new FilteringSelect({
            store: eventStore,
            labelAttr: "name",
            searchAttr: "name",
            placeholder: schedule.event,
            required: false,
            pageSize: 25
        }, base + "event");
        eventFilteringSelect.push(dijit);
        dijit.startup();
        dijit = new CurrencyTextBox({
            placeholder: core.amount,
            trim: true,
            required: false
        }, base + "amount");
        amountInput.push(dijit);
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
        var i, item;

        for( i = 0; i < billToId.length; i++ ) {
            if( billToId[i] === id ) {
                id = i;
                break;
            }
        }

        billToId.splice(id, 1);
        item = clientFilteringSelect.splice(id, 1);
        item[0].destroyRecursive();
        item = eventFilteringSelect.splice(id, 1);
        item[0].destroyRecursive();
        item = amountInput.splice(id, 1);
        item[0].destroyRecursive();
        item = commentInput.splice(id, 1);
        item[0].destroyRecursive();
        domConstruct.destroy(target);

    }

    function run() {

        if( arguments.length > 0 ) {
            setDivId(arguments[0]);
        } else {
            throw new Error('No divId');
        }

        prototypeNode = dom.byId(getDivId());
        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__bill_to__/g, '0');

        domConstruct.place(prototypeContent, prototypeNode, "after");

        clientStore = new JsonRest({
            target: '/api/store/clients?',
            useRangeHeaders: false,
            idProperty: 'id'});

        eventStore = new JsonRest({
            target: '/api/store/events?client=',
            useRangeHeaders: false,
            idProperty: 'id'});

        createDijits();

        addOneMoreControl = query('.bill-tos .add-one-more-row');

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
        var i, l = billToId.length, returnData = [];
        for( i = 0; i < l; i++ ) {
            returnData.push(
                    {
                        "id": billToId[i],
                        "client": clientFilteringSelect[i].get('value'),
                        "event": eventFilteringSelect[i].get('value'),
                        "amount": parseFloat(amountInput[i].get("value")),
                        "comment": commentInput[i].get('value')
                    });
        }
        return returnData;
    }

    function setData(items) {
        var i, l, obj, nodes;

        nodes = query(".form-row.bill-to", "bill-tos");
        nodes.forEach(function (node, index) {
            destroyRow(index, node);
        });
        l = items.length;
        if( typeof items === "object" && items.length !== 0 ) {
            for( i = 0; i < l; i++ ) {
                cloneNewNode();
                createDijits();
                currentRowIndex = i;
                obj = items[i];
                billToId[i] = obj.id;
                clientFilteringSelect[i].set('displayedValue', obj.client.name);
                amountInput[i].set("value", obj.amount);
                commentInput[i].set('value', obj.comment);
                if( typeof items[i].event !== "undefined" && typeof items[i].event.name !== "undefined" ) {
                    eventStore.target = eventStore.target.replace(/\d*$/, obj.client.id);
                    eventFilteringSelect[i].set('displayedValue', obj.event.name);
                } else {
                    eventFilteringSelect[i].reset();
                }
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