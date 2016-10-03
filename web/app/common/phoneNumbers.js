define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dojo/data/ObjectStore",
    "dojo/store/Memory",
    "dijit/form/ValidationTextBox",
    "dijit/form/Select",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on,
        query, ObjectStore, Memory,
        ValidationTextBox, Select,
        lib, core) {
    "use strict";

    var dataPrototype, prototypeNode, prototypeContent;
    var store;
    var typeSelect = [], numberInput = [], commentInput = [];
    var divIdInUse = null;
    var addOneMoreControl = null;

    function getDivId() {
        return divIdInUse;
    }

    function setDivId(divId) {
        divIdInUse = divId + '_phone_numbers';
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__phone__/g, numberInput.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits() {
        var dijit;
        var base = getDivId() + '_' + numberInput.length + '_';
        dijit = new Select({
            store: store,
            placeholder: core.type,
            required: true
        }, base + "type");
        typeSelect.push(dijit);
        dijit.startup();
        dijit = new ValidationTextBox({
            placeholder: core.phone_number,
            trim: true,
            pattern: "[0-9x\.\,\ \+\(\)-]{2,24}",
            required: true
        }, base + "phone_number");
        numberInput.push(dijit);
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
        typeSelect.pop().destroyRecursive();
        numberInput.pop().destroyRecursive();
        commentInput.pop().destroyRecursive();
        domConstruct.destroy(target);
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
        prototypeContent = dataPrototype.replace(/__phone__/g, numberInput.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");

        base = prototypeNode.id + "_" + numberInput.length;
        select = base + "_type";

        if( dom.byId(select) === null ) {
            lib.textError(base + " not found");
            return;
        }

        data = JSON.parse(domAttr.get(select, "data-options"));
        // Convert the data to an array of objects
        storeData = [];
        storeData.push({value: "", label: core.type.toLowerCase()});
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
            if( numberInput.length >= lib.constant.MAX_PHONE_NUMBERS ) {
                addOneMoreControl.addClass("hidden");
            }
        });

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var id = parseInt(targetParent.id.replace(/\D/g, ''));
            destroyRow(id, targetParent.parentNode);
            if( numberInput.length <= lib.constant.MAX_PHONE_NUMBERS ) {
                addOneMoreControl.removeClass("hidden");
            }
        });
    }

    function getData() {
        var i, returnData = [];
        for( i = 0; i < numberInput.length; i++ ) {
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

            for( i = 0; i < numberInput.length; i++ ) {
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