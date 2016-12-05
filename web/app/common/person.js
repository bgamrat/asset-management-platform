define([
    "dojo/_base/lang",
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/form/ValidationTextBox",
    "dijit/form/Textarea",
    "dijit/form/Select",
    "dojo/data/ObjectStore",
    "dojo/store/Memory",
    "app/common/emails",
    "app/common/phoneNumbers",
    "app/common/addresses",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/domReady!"
], function (lang, dom, domAttr, domConstruct, on, query,
        ValidationTextBox, Textarea, Select,
        ObjectStore, Memory,
        emails, phoneNumbers, addresses,
        lib, core) {

    "use strict";
    var divIdInUse = "user_person";
    var firstnameInput = [], middlenameInput = [], lastnameInput = [];
    var typeSelect = [], commentInput = [];
    var dataPrototype;
    var prototypeNode, prototypeContent;
    var store;
    var personId = [];

    function setDivId(divId) {
        divIdInUse = divId;
    }

    function getDivId() {
        return divIdInUse;
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__person__/g, personId.length);
        domConstruct.place(prototypeContent, prototypeNode, "after");
    }

    function createDijits() {
        var dijit, base = getDivId() + '_';
        if( prototypeNode !== null ) {
            base += personId.length + '_';
        }
        personId.push(null);
        dijit = new Select({
            store: store,
            placeholder: core.type,
            required: true,
            "class": "type-select"
        }, base + "type");
        dijit.startup();
        typeSelect.push(dijit);
        dijit = new ValidationTextBox({
            trim: true,
            properCase: true,
            pattern: "[A-Za-z\.\,\ \'-]{2,64}",
            "class": "name",
            placeholder: core.firstname
        }, base + "firstname");
        dijit.startup();
        firstnameInput.push(dijit);
        dijit = new ValidationTextBox({
            trim: true,
            properCase: true,
            pattern: "[A-Za-z\.\,\ \'-]{2,64}",
            maxLength: 64,
            required: false,
            "class": "name",
            placeholder: core.middlename,
        }, base + "middlename");
        dijit.startup();
        middlenameInput.push(dijit);
        dijit = new ValidationTextBox({
            required: true,
            trim: true,
            pattern: "[A-Za-z\.\,\ \'-]{2,64}",
            "class": "name",
            placeholder: core.lastname
        }, base + "lastname");
        dijit.startup();
        lastnameInput.push(dijit);
        dijit = new Textarea({
            placeholder: core.comment,
            trim: true,
            required: false
        }, base + "comment");
        dijit.startup();
        commentInput.push(dijit);
    }

    function destroyRow(id, target) {
        var item;
        personId.splice(id, 1);
        item = typeSelect.splice(id, 1);
        item[0].destroyRecursive();
        item = firstnameInput.splice(id, 1);
        item[0].destroyRecursive();
        item = middlenameInput.splice(id, 1);
        item[0].destroyRecursive();
        item = lastnameInput.splice(id, 1);
        item[0].destroyRecursive();
        item = commentInput.splice(id, 1);
        item[0].destroyRecursive();
        //domConstruct.destroy(target);
    }

    function run() {
        var base, d, data;
        var select, storeData, memoryStore;

        if( arguments.length > 0 ) {
            setDivId(arguments[0]);
        }
        base = getDivId();
        prototypeNode = dom.byId(getDivId());
        if( prototypeNode !== null ) {
            dataPrototype = domAttr.get(prototypeNode, "data-prototype");
            if( dataPrototype !== null ) {
                cloneNewNode();
                base += "_0";
            } else {
                prototypeNode = null;
            }
        }

        select = base + "_type";

        if( dom.byId(select) === null ) {
            lib.textError(select + " not found");
            return;
        }

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

        createDijits();

        phoneNumbers.run(getDivId());
        emails.run(getDivId());
        addresses.run(getDivId());
    }

    function getData() {
        var i, returnData = [];
        for( i = 0; i < personId.length; i++ ) {
            if (lastnameInput[i].get('value') !== "") {
                returnData.push({
                    "id": personId[i],
                    "type": parseInt(typeSelect[i].get('value')),
                    "type_text": typeSelect[i].get('displayedValue'),
                    "firstname": firstnameInput[i].get('value'),
                    "middlename": middlenameInput[i].get('value'),
                    "lastname": lastnameInput[i].get('value'),
                    "name": firstnameInput[i].get('value') + " " + middlenameInput[i].get('value') + " " + lastnameInput[i].get('value'),
                    "comment": commentInput[i].get('value'),
                    "emails": emails.getData(),
                    "phones": phoneNumbers.getData(),
                    "addresses": addresses.getData()
                });
            }
        }
        return returnData.length > 0 ? returnData : null;
    }

    function setData(person) {
        var i, p, obj, nodes;

        nodes = query(".form-row.person,.form-row.contacts");
        nodes.forEach(function (node, index) {
            if (index !== 0) {
                destroyRow(index, node);
            }
        });

        if( typeof person === "object" && person !== null ) {    
            if (!person.hasOwnProperty('length')) {
                person = [person];
            }
            for( i = 0; i < person.length; i++ ) {
                if (i !== 0) {
                    cloneNewNode();
                    createDijits();
                }
                obj = person[i];
                personId[i] = obj.id;
                typeSelect[i].set('value', obj.type);
                firstnameInput[i].set('value', obj.firstname);
                middlenameInput[i].set('value', obj.middlename);
                lastnameInput[i].set('value', obj.lastname);
                commentInput[i].set('value', obj.comment);
                if( typeof obj.phones !== "undefined" ) {
                    phoneNumbers.setData(obj.phones);
                } else {
                    phoneNumbers.setData(null);
                }
                if( typeof obj.emails !== "undefined" ) {
                    emails.setData(obj.emails);
                } else {
                    emails.setData(null);
                }
                if( typeof obj.addresses !== "undefined" ) {
                    addresses.setData(obj.addresses);
                } else {
                    addresses.setData(null);
                }
            }
        } else {
            personId[0] = null;
            typeSelect[0].set('value', '');
            firstnameInput[0].set('value', '');
            middlenameInput[0].set('value', '');
            lastnameInput[0].set('value', '');
            commentInput[0].set('value', '');
            phoneNumbers.setData(null);
            emails.setData(null);
            addresses.setData(null);
        }
    }
    return {
        run: run,
        getData: getData,
        setData: setData
    }
}
);