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
    var typeSelect = [], numberInput = [], commentInput = [];
    var divIdInUse = null;

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__name__/g, phoneNumberId);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits() {
        var base = prototypeNode.id + "_" + phoneNumberId + "_";
        typeSelect[phoneNumberId] = new Select({
            store: store,
            placeholder: core.type,
            required: true
        }, base + "type");
        typeSelect[phoneNumberId].startup();
        numberInput[phoneNumberId] = new ValidationTextBox({
            placeholder: core.phone_number,
            trim: true,
            pattern: "^[0-9x\.\,\ \+\(\)-]{2,24}$",
            required: true
        }, base + "phonenumber");
        numberInput[phoneNumberId].startup();
        commentInput[phoneNumberId] = new ValidationTextBox({
            placeholder: core.comment,
            trim: true,
            required: false
        }, base + "comment");
        commentInput[phoneNumberId].startup();
        phoneNumberId++;
    }

    function run() {

        var phoneLine, base, select, data, storeData, d, memoryStore;
        if( arguments.length > 0 ) {
            setDivId(arguments[0]);
        }

        prototypeNode = dom.byId(getDivId());
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
                cloneNewNode();
            }
            createDijits();
        });

        query('.phone-numbers .add-one-more-row').on("click", function (event) {
            var target = event.target.parentNode;
            cloneNewNode();
            createDijits();
        });

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var id = parseInt(target.id.replace(/\D/g, ''));
            destroyRow(id, targetParent);
        });
    }

    function getData() {
        var i, returnData = [];
        for( i in typeSelect ) {
            returnData.push(
                    {
                        "type": typeSelect[i].get('value'),
                        "phonenumber": numberInput[i].get('value'),
                        "comment": commentInput[i].get('value')
                    });
        }
        return returnData;
    }

    function getDivId() {
        var q;
        if( divIdInUse !== null ) {
            q = divIdInUse;
        } else {
            q = query('[id$="phone_numbers"]');
            if( q.length > 0 ) {
                q = q[0];
            }
        }
        return q;
    }

    function setDivId(divId) {
        divIdInUse = divId;
    }

    function setData(phoneNumbers) {
        var i, p, obj;

        query(".form-row.phone-number", prototypeNode.parentNode).forEach(function (node, index) {
            if( index !== 0 ) {
                destroyRow(index, node);
            }
        });

        if( typeof phoneNumbers === "object" && phoneNumbers !== null && phoneNumbers.length > 0 ) {

            i = 0;
            phoneNumberId = 1;
            for( p in phoneNumbers ) {
                if( i !== 0 ) {
                    cloneNewNode();
                    createDijits();
                }
                obj = phoneNumbers[p];
                typeSelect[i].set('value', obj.type);
                numberInput[i].set('value', obj.phonenumber);
                commentInput[i].set('value', obj.comment);
                i++;
            }
        } else {
            typeSelect[0].set('value', "");
            numberInput[0].set('value', "");
            commentInput[0].set('value', "");
        }
    }
    
    function destroyRow(id, target) {
        typeSelect[id].destroyRecursive();
        typeSelect.splice(id, 1);
        numberInput[id].destroyRecursive();
        numberInput.splice(id, 1);
        commentInput[id].destroyRecursive();
        commentInput.splice(id, 1);
        domConstruct.destroy(target);
    }
    
    return {
        getData: getData,
        run: run,
        setData: setData
    }
}
);