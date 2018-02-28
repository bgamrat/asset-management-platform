define([
    "dojo/_base/lang",
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/query",
    "dijit/form/ValidationTextBox",
    "dijit/form/Textarea",
    "dijit/form/Select",
    "dojo/data/ObjectStore",
    "dojo/store/Memory",
    "app/common/emails",
    "app/common/phones",
    "app/common/addresses",
        "app/admin/staff/person_roles",
    "app/admin/staff/person_employment_status",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/domReady!"
], function (lang, dom, domAttr, query,
        ValidationTextBox, Textarea, Select,
        ObjectStore, Memory,
        xemails, xphones, xaddresses, roles, employmentStatuses,
        lib, core) {

    "use strict";
    var divIdInUse = "person";
    var base;
    var firstnameInput, middlenameInput, lastnameInput, titleInput;
    var typeSelect, commentInput;
    var store;
    var emails = [], phones = [], addresses = [];

    function setDivId(divId) {
        divIdInUse = divId;
    }

    function getDivId() {
        return divIdInUse;
    }

    function createDijits() {
        var dijit, base = getDivId() + '_';

        dijit = new Select({
            store: store,
            placeholder: core.type,
            required: true,
            "class": "type-select"
        }, base + "type");
        dijit.startup();
        typeSelect = dijit;
        dijit = new ValidationTextBox({
            required: false,
            trim: true,
            pattern: "[A-Za-z\.\,\ \'-]{2,64}",
            "class": "name",
            placeholder: core.title
        }, base + "title");
        dijit.startup();
        titleInput = dijit;
        dijit = new ValidationTextBox({
            trim: true,
            properCase: true,
            pattern: "[A-Za-z\.\,\ \'-]{2,64}",
            "class": "name",
            placeholder: core.firstname
        }, base + "firstname");
        dijit.startup();
        firstnameInput = dijit;
        dijit = new ValidationTextBox({
            trim: true,
            properCase: true,
            pattern: "[A-Za-z\.\,\ \'-]{,64}",
            maxLength: 64,
            required: false,
            "class": "name",
            placeholder: core.middlename
        }, base + "middlename");
        dijit.startup();
        middlenameInput = dijit;
        dijit = new ValidationTextBox({
            required: true,
            trim: true,
            pattern: "[A-Za-z\.\,\ \'-]{2,64}",
            "class": "name",
            placeholder: core.lastname,
        }, base + "lastname");
        dijit.startup();
        lastnameInput = dijit;
        dijit = new Textarea({
            placeholder: core.comment,
            trim: true,
            required: false
        }, base + "comment");
        dijit.startup();
        commentInput = dijit;
        phones = xphones.run("person");
        emails = xemails.run("person");
        addresses = xaddresses.run("person");
    }

    function setPersonValues(obj) {
        typeSelect.set('value', obj.type.id);
        titleInput.set('value', obj.title);
        firstnameInput.set('value', obj.firstname);
        middlenameInput.set('value', obj.middlename);
        lastnameInput.set('value', obj.lastname);
        commentInput.set('value', obj.comment);
        phones.setData(obj.phones);
        emails.setData(obj.emails);
        addresses.setData(obj.addresses);
        roles.setData(obj.roles);
        employmentStatuses.setData(obj.employmentStatuses);
    }

    function run() {
        var select, d, data, storeData, memoryStore;

        if( arguments.length > 0 ) {
            setDivId(arguments[0]);
        }

        select = getDivId() + "_type";

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

        roles.run();
        employmentStatuses.run();
    }

    function getData() {

        return {
            "type": parseInt(typeSelect.get('value')),
            "type_text": typeSelect.get('displayedValue'),
            "title": titleInput.get('value'),
            "firstname": firstnameInput.get('value'),
            "middlename": middlenameInput.get('value'),
            "lastname": lastnameInput.get('value'),
            "name": firstnameInput.get('value') + " " + middlenameInput.get('value') + " " + lastnameInput.get('value'),
            "comment": commentInput.get('value'),
            "emails": emails.getData(),
            "phones": phones.getData(),
            "addresses": addresses.getData(),
            "roles": roles.getData(),
            "employment_statuses": employmentStatuses.getData()
        }

    }

    function setData(person) {
        if( typeof person === "object" && person !== null ) {
            setPersonValues(person);
        } else {
            typeSelect.set('value', '');
            titleInput.set('value', '');
            firstnameInput.set('value', '');
            middlenameInput.set('value', '');
            lastnameInput.set('value', '');
            commentInput.set('value', '');
            phones.setData(null);
            emails.setData(null);
            addresses.setData(null);
            roles.setData(null);
            employmentStatuses.setData(null);
        }
    }

    return {
        run: run,
        getData: getData,
        setData: setData
    };
}
);