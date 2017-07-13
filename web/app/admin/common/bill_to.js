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
    var billToId = [], contactId = [], contactFilteringSelect = [], eventFilteringSelect = [], amountInput = [], commentInput = [];
    var contactStore, currentContact, eventStore;
    var divIdInUse = null;
    var addOneMoreControl = null;
    var currentContact;

    function getDivId() {
        return divIdInUse;
    }

    function setDivId(divId) {
        divIdInUse = divId + '_bill_tos';
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__bill_to__/g, contactFilteringSelect.length);
        domConstruct.place(prototypeContent, prototypeNode, "after");
        billToId.push(null);
    }

    function createDijits() {
        var dijit;
        var base = prototypeNode.id + "_" + contactFilteringSelect.length + "_";
        dijit = new FilteringSelect({
            store: contactStore,
            labelAttr: "name",
            labelType: "html",
            searchAttr: "name",
            placeholder: core.contact,
            required: false,
            pageSize: 25,
            intermediateChanges: true
        }, base + "contact");
        dijit.startup();
        dijit.on("change", function (evt) {
            var id = parseInt(this.id.replace(/\D/g, ''));
            var item = this.get('item');
            eventFilteringSelect[id].store.target = "/api/store/events?" + item.contact_type + "=" + item.contact_entity_id;
        });
        contactFilteringSelect.push(dijit);

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
        item = contactFilteringSelect.splice(id, 1);
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

        contactStore = new JsonRest({
            target: '/api/store/contacts?client&venue',
            useRangeHeaders: false,
            idProperty: 'id'});

        eventStore = new JsonRest({
            target: '/api/store/events?contact=',
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
            destroyRow(id, target.closest(".form-row.bill-to"));
        });
    }

    function getData() {
        var i, l = billToId.length, contact, returnData = [];
        for( i = 0; i < l; i++ ) {
            contact = contactFilteringSelect[i].get('item');
            returnData.push(
                    {
                        "id": billToId[i],
                        "contact": contact,
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
            destroyRow(0, node);
        });
        l = items.length;
        if( typeof items === "object" && items.length !== 0 ) {
            for( i = 0; i < l; i++ ) {
                cloneNewNode();
                createDijits();
                obj = items[i];
                billToId[i] = obj.id;
                contactFilteringSelect[i].set('item', obj.contact);
                amountInput[i].set("value", obj.amount);
                commentInput[i].set('value', obj.comment);
                if( typeof items[i].event !== "undefined" && items[i].event !== null && typeof items[i].event.name !== "undefined" ) {
                    eventStore.target = eventStore.target.replace(/\d*$/, obj.contact.id);
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