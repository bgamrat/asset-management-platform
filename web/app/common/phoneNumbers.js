define([
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dojo/data/ObjectStore",
    "dojo/store/Memory",
    "dijit/registry",
    "dijit/form/TextBox",
    "dijit/form/ValidationTextBox",
    "dijit/form/Select",
    "dijit/form/Button",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (declare, lang, dom, domAttr, domConstruct, on,
        query, ObjectStore, Memory,
        registry, TextBox, ValidationTextBox, Select, Button,
        lib, core) {
    "use strict";

    var phoneNumberId = 0;
    var dataPrototype;
    var prototypeNode, prototypeContent;
    var base, select, store;

    function cloneNewNode(node) {
        var typeSelect;
        prototypeContent = dataPrototype.replace(/__name__/g, phoneNumberId);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits() {
        var base = prototypeNode.id + "_" + phoneNumberId + "_";
        var typeSelect = new Select({
            store: store,
            placeholder: core.type
        }, base + "type");
        typeSelect.startup();
        var numberInput = new ValidationTextBox({"placeholder":core.phone_number}, base + "number");
        numberInput.startup();
        var commentInput = new ValidationTextBox({"placeholder":core.comment}, base + "comment");
        commentInput.startup();
        phoneNumberId++;
    }

    function run() {

        var q, phoneLine, base, select, data, storeData, d, memoryStore;
        q = query('[id$="phone_numbers"]');
        if( q.length > 0 ) {

            prototypeNode = q[0];
            dataPrototype = domAttr.get(prototypeNode, "data-prototype");
            prototypeContent = dataPrototype.replace(/__name__/g, phoneNumberId);
            base = prototypeNode.id + "_" + phoneNumberId + "_";
            select = base + "type";
            domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
            data = JSON.parse(domAttr.get(select, "data-options"));
            // Convert the data to an array of objects
            storeData = [];
            for( d in data ) {
                storeData.push(data[d]);
            }
            memoryStore = new Memory({
                idProperty: "value",
                data: storeData});
            store = new ObjectStore({objectStore: memoryStore});

            query('[id$="phone_numbers"]').forEach(function (node, index) {
                if( index !== 0 ) {
                    cloneNewNode(node);
                }
                createDijits();
            });

            query('.phone-numbers .add-one-more-row').on("click", function (event) {
                var target = event.target.parentNode;
                cloneNewNode(target);
                createDijits();
            });
            
            on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
                var target = event.target.parentNode;
                domConstruct.destroy(target);
            });
        }
    }

    function getData() {
        return {
        }
    }
    function setData(phoneNumbers) {
        if( typeof phoneNumbers === "object" ) {
            if( phoneNumbers === null ) {
                phoneNumbers = {};
            }
            phoneNumbers = lang.mixin({type: '', number: '', comment: ''}, phoneNumbers);
        } else {
        }
    }
    return {
        getData: getData,
        run: run,
        setData: setData
    }
}
);