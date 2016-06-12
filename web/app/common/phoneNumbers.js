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
    var dataPrototype, prototypeNode, prototypeContent;
    var store;
    var typeSelect = [], numberInput = [], commentInput = [];
    var divIdInUse = null;
    var addOneMoreControl = null;

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
        divIdInUse = divId + '_phone_numbers';
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__phone__/g, phoneNumberId);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits() {
        var base = getDivId() + '_' + phoneNumberId + '_';
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
        }, base + "phone_number");
        numberInput[phoneNumberId].startup();
        commentInput[phoneNumberId] = new ValidationTextBox({
            placeholder: core.comment,
            trim: true,
            required: false
        }, base + "comment");
        commentInput[phoneNumberId].startup();
        phoneNumberId++;
    }

    function destroyRow(id, target) {
        typeSelect.pop().destroyRecursive();
        numberInput.pop().destroyRecursive();
        commentInput.pop().destroyRecursive();
        domConstruct.destroy(target);
        phoneNumberId--;
    }

    function run() {

        var base, select, data, storeData, d, memoryStore;
        if( arguments.length > 0 ) {
            setDivId(arguments[0]);
        }

        prototypeNode = dom.byId(getDivId());
        if( prototypeNode === null ) {
            setDivId(arguments[0] + '_0');
            prototypeNode = dom.byId(getDivId());
        }

        if( prototypeNode === null ) {
            lib.textError(getDivId() + " not found");
            return;
        }

        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__phone__/g, phoneNumberId);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");

        base = prototypeNode.id + "_" + phoneNumberId;
        select = base + "_type";

        if( dom.byId(select) === null ) {
            lib.textError(base + " not found");
            return;
        }

        data = JSON.parse(domAttr.get(select, "data-options"));
        // Convert the data to an array of objects
        storeData = [];
        storeData.push({value:"",label:core.type.toLowerCase()});
        for( d in data ) {
            storeData.push(data[d]);
        }
        memoryStore = new Memory({
            idProperty: "value",
            data: storeData});
        store = new ObjectStore({objectStore: memoryStore});

        createDijits();

        addOneMoreControl = query('.phone-numbers .add-one-more-row');

        addOneMoreControl.on("click", function (event) {
            cloneNewNode();
            createDijits();
            if( typeSelect.length >= lib.constant.MAX_PHONE_NUMBERS ) {
                addOneMoreControl.addClass("hidden");
            }
        });

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var id = parseInt(targetParent.id.replace(/\D/g, ''));
            destroyRow(id, targetParent.parentNode);
            if( typeSelect.length <= lib.constant.MAX_PHONE_NUMBERS ) {
                addOneMoreControl.removeClass("hidden");
            }
        });
    }

    function getData() {
        var i, returnData = [];
        for(i = 0; i < phoneNumberId; i++ ) {
            returnData.push(
                    {
                        "type": typeSelect[i].get('value'),
                        "phone_number": numberInput[i].get('value'),
                        "comment": commentInput[i].get('value')
                    });
        }
        return returnData;
    }

    function setData(phoneNumbers) {
        var i, p, obj;

        query(".form-row.phone-number", prototypeNode.parentNode).forEach(function (node, index) {
            if( index !== 0 ) {
                destroyRow(index, node);
            }
        });

        if( typeof phoneNumbers === "object" && phoneNumbers !== null && phoneNumbers.length > 0 ) {

            phoneNumberId = 1;
            for( i = 0; i < phoneNumbers.length; i++ ) {
                if( i !== 0 ) {
                    cloneNewNode();
                    createDijits();
                }
                obj = phoneNumbers[i];
                typeSelect[i].set('value', obj.type);
                numberInput[i].set('value', obj.phone_number);
                commentInput[i].set('value', obj.comment);
            }
        } else {
            typeSelect[0].set('value', "");
            numberInput[0].set('value', "");
            commentInput[0].set('value', "");
        }
    }

    return {
        run: run,
        getData: getData,
        setData: setData
    }
});