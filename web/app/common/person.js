define([
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dijit/registry",
    "dijit/form/TextBox",
    "dijit/form/ValidationTextBox",
    "dijit/form/Textarea",
    "dijit/form/Select",
    "dojo/data/ObjectStore",
    "dojo/store/Memory",
    "app/common/emails",
    "app/common/phoneNumbers",
    "app/common/address",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/domReady!"
], function (declare, lang, dom, domAttr, domConstruct, on,
        registry, TextBox, ValidationTextBox, Textarea, Select,
        ObjectStore, Memory,
        emails, phoneNumbers, address,
        lib, core) {

    "use strict";
    var divIdInUse = "user_person";
    var firstnameInput, middleInitialInput, lastnameInput;
    var typeSelect, commentInput;
    var dataPrototype;
    var prototypeNode, prototypeContent;
    var store;
    var personId = 0;

    function createDijits() {
        var base = getDivId() + '_';
        if (prototypeNode !== null) {
            base += personId + '_';
        }
        typeSelect = new Select({
            store: store,
            placeholder: core.type,
            required: true
        }, base + "type");
        typeSelect.startup();
        firstnameInput = new ValidationTextBox({
            trim: true,
            properCase: true,
            pattern: "^[A-Za-z\.\,\ \'-]{2,64}$",
            "class": "name",
            placeholder: core.firstname
        }, base + "firstname");
        middleInitialInput = new ValidationTextBox({
            trim: true,
            uppercase: true,
            pattern: "^[A-Z]$",
            maxLength: 1,
            required: false,
            placeholder: core.mi,
            "class": "xshort"
        }, base + "middleinitial");
        lastnameInput = new ValidationTextBox({
            required: true,
            trim: true,
            pattern: "^[A-Za-z\.\,\ \'-]{2,64}$",
            "class": "name",
            placeholder: core.lastname
        }, base + "lastname");
        firstnameInput.startup();
        middleInitialInput.startup();
        lastnameInput.startup();
        commentInput = new Textarea({
            placeholder: core.comment,
            trim: true,
            required: false
        }, base + "comment");
        commentInput.startup();

    }

    function run() {
        var base, d, data;
        var select, storeData, memoryStore;

        if( arguments.length > 0 ) {
            setDivId(arguments[0]);
        }

        prototypeNode = dom.byId(getDivId());
        if( prototypeNode !== null ) {
            dataPrototype = domAttr.get(prototypeNode, "data-prototype");
            prototypeContent = dataPrototype.replace(/__person__/g, personId);
            domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
            base = prototypeNode.id + "_" + personId + "_";
        } else {
            base = getDivId()+'_';
        }

        select = base + "type";
        
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
        emails.run(getDivId());
        phoneNumbers.run(getDivId());
        address.run(getDivId());
    }

    function getData() {
        return {
            "type": typeSelect.get('value'),
            "firstname": firstnameInput.get('value'),
            "middleinitial": middleInitialInput.get('value'),
            "lastname": lastnameInput.get('value'),
            "emails": emails.getData(),
            "phone_numbers": phoneNumbers.getData(),
            "address": address.getData()
        }
    }

    function getDivId() {
        var q;
        if( divIdInUse !== null ) {
            q = divIdInUse;
        } else {
            q = query('[id$="person"]');
            if( q.length > 0 ) {
                q = q[0];
            }
        }
        return q;
    }

    function setDivId(divId) {
        divIdInUse = divId + 'person';
    }
    function setData(person) {
        if( typeof person === "object" ) {
            if( person === null ) {
                person = {};
            }
            person = lang.mixin({firstname: '', middleinitial: '', lastname: ''}, person);
            firstnameInput.set('value', person.firstname);
            middleInitialInput.set('value', person.middleinitial);
            lastnameInput.set('value', person.lastname);
            if( typeof person.phonenumbers !== "undefined" ) {
                phoneNumbers.setData(person.phonenumbers);
            } else {
                phoneNumbers.setData(null);
            }
        } else {
            firstnameInput.set('value', '');
            middleInitialInput.set('value', '');
            lastnameInput.set('value', '');
        }
    }
    return {
        getData: getData,
        run: run,
        setData: setData
    }
}
);