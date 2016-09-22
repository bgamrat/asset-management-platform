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
    "app/common/addresses",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/domReady!"
], function (declare, lang, dom, domAttr, domConstruct, on,
        registry, TextBox, ValidationTextBox, Textarea, Select,
        ObjectStore, Memory,
        emails, phoneNumbers, addresses,
        lib, core) {

    "use strict";
    var divIdInUse = "user_person";
    var firstnameInput, middleInitialInput, lastnameInput;
    var typeSelect, commentInput;
    var dataPrototype;
    var prototypeNode, prototypeContent;
    var store;
    var personId = 0;

    function setDivId(divId) {
        divIdInUse = divId + '_person';
    }

    function getDivId() {
        return divIdInUse;
    }

    function createDijits() {
        var base = getDivId() + '_';
        if( prototypeNode !== null ) {
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
            pattern: "[A-Za-z\.\,\ \'-]{2,64}",
            "class": "name",
            placeholder: core.firstname
        }, base + "firstname");
        middleInitialInput = new ValidationTextBox({
            trim: true,
            uppercase: true,
            pattern: "[A-Z]",
            maxLength: 1,
            required: false,
            placeholder: core.mi,
            "class": "xshort"
        }, base + "middleinitial");
        lastnameInput = new ValidationTextBox({
            required: true,
            trim: true,
            pattern: "[A-Za-z\.\,\ \'-]{2,64}",
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
            base = prototypeNode.id + "_0_";
        } else {
            base = getDivId() + '_';
        }

        select = base + "type";

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
        return {
            "type": typeSelect.get('value'),
            "firstname": firstnameInput.get('value'),
            "middleinitial": middleInitialInput.get('value'),
            "lastname": lastnameInput.get('value'),
            "comment": commentInput.get('value'),
            "emails": emails.getData(),
            "phone_numbers": phoneNumbers.getData(),
            "addresses": addresses.getData()
        }
    }

    function setData(person) {
        if( typeof person === "object" ) {
            if( person === null ) {
                person = {};
            }
            person = lang.mixin({firstname: '', middleinitial: '', lastname: ''}, person);
            typeSelect.set('value', person.type);
            firstnameInput.set('value', person.firstname);
            middleInitialInput.set('value', person.middleinitial);
            lastnameInput.set('value', person.lastname);
            commentInput.set('value', person.comment);
            if( typeof person.phone_numbers !== "undefined" ) {
                phoneNumbers.setData(person.phone_numbers);
            } else {
                phoneNumbers.setData(null);
            }
            if( typeof person.emails !== "undefined" ) {
                emails.setData(person.emails);
            } else {
                emails.setData(null);
            }
            if( typeof person.addresses !== "undefined" ) {
                addresses.setData(person.addresses);
            } else {
                addresses.setData(null);
            }
        } else {
            typeSelect.set('value', '');
            firstnameInput.set('value', '');
            middleInitialInput.set('value', '');
            lastnameInput.set('value', '');
            commentInput.set('value', '');
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