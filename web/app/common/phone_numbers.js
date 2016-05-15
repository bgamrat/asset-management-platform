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
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (declare, lang, dom, domAttr, domConstruct, on,
        query, ObjectStore, Memory,
        registry, TextBox, ValidationTextBox, Select, Button,
        lib, core) {
    "use strict";

    var phoneLineId = 0;



    query('[id$="phone_numbers"]').forEach(function (node, index, array) {
        var dataPrototype = domAttr.get(node, "data-prototype");
        dataPrototype = dataPrototype.replace(/__name__/g, phoneLineId)
        var phoneLine = domConstruct.place(dataPrototype, node, "after");
        var base = node.id + "_" + phoneLineId + "_";
        var select = base + "type";
        var data = JSON.parse(domAttr.get(select, "data-options"));
        // Convert the data to an array of objects
        var storeData = [];
        var d;
        for (d in data) {
            storeData.push(data[d]);
        }
        var store = new Memory({
            idProperty: "value",
            data: storeData });
        var os = new ObjectStore({objectStore: store});
        var typeSelect = new Select({
            store: os
        }, select);
        typeSelect.startup();
        var numberInput = new ValidationTextBox({}, base + "number");
        numberInput.startup();
        var commentInput = new ValidationTextBox({}, base + "comment");
        commentInput.startup();
        phoneLineId++;
    });



    function run() {
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