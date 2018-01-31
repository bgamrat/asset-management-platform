define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    'dojo/store/JsonRest',
    "dijit/form/ValidationTextBox",
    "dijit/form/FilteringSelect",
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/schedule",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on, query,
        JsonRest,
        ValidationTextBox, FilteringSelect,
        core, schedule) {
    //"use strict";

    var dataPrototype;
    var prototypeNode, prototypeContent;
    var rentalFilteringSelect = [], commentInput = [];
    var rentalStore;
    var divIdInUse = 'event_rentals';
    var addOneMoreControl = null;

    function getDivId() {
        return divIdInUse;
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__rental__/g, rentalFilteringSelect.length);
        domConstruct.place(prototypeContent, prototypeNode, "last");
    }

    function createDijits() {
        var dijit;
        var base = prototypeNode.id + "_" + rentalFilteringSelect.length + "_";
        dijit = new FilteringSelect({
            store: rentalStore,
            labelAttr: "name",
            searchAttr: "name",
            placeholder: schedule.rental,
            pageSize: 25
        }, base + "rental");
        rentalFilteringSelect.push(dijit);
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
        var rental;

        rental = rentalFilteringSelect.splice(id, 1);
        rental[0].destroyRecursive();
        rental = commentInput.splice(id, 1);
        rental[0].destroyRecursive();
        domConstruct.destroy(target);
    }

    function run() {

        prototypeNode = dom.byId(getDivId());
        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__rental__/g, '0');

        rentalStore = new JsonRest({
            target: '/api/store/barcodes',
            useRangeHeaders: false,
            idProperty: 'id'});

        addOneMoreControl = query('.rentals .add-one-more-row');

        addOneMoreControl.on("click", function (event) {
            cloneNewNode();
            createDijits();
        });

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var id = parseInt(targetParent.id.replace(/\D/g, ''));
            destroyRow(id, target.closest(".form-row.event-rental"));
        });
    }

    function getData() {
        var i, l = rentalFilteringSelect.length, returnData = [];
        for( i = 0; i < l; i++ ) {
            returnData.push(
                    {
                        "rental": rentalFilteringSelect[i].get('value'),
                        "comment": commentInput[i].get('value')
                    });
        }
        return returnData;
    }

    function setData(rentals) {
        var i, l, obj, nodes;

        nodes = query(".form-row.event-rental", "rentals");
        nodes.forEach(function (node, index) {
            destroyRow(0, node);
        });
        if( typeof rentals === "object" && rentals !== null ) {
            l = rentals.length;
            for( i = 0; i < l; i++ ) {
                cloneNewNode();
                createDijits();
                obj = rentals[i];
                rentalFilteringSelect[i].set('displayedValue', obj.name);
                commentInput[i].set('value', obj.comment);
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