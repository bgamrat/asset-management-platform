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
    "app/common/phoneNumbers",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/domReady!"
], function (declare, lang, dom, domAttr, domConstruct, on,
        registry, TextBox, ValidationTextBox, phoneNumbers,
        lib, core) {
    "use strict";
    
    var firstnameInput = new ValidationTextBox({
        trim: true,
        properCase: true,
        pattern: "^[A-Za-z\.\,\ \'-]{2,64}$"
    }, "user_person_firstname");
    var middleInitialInput = new ValidationTextBox({
        trim: true,
        uppercase: true,
        pattern: "^[A-Z]$",
        required: false
    }, "user_person_middleinitial");
    var lastnameInput = new ValidationTextBox({
        trim: true,
        pattern: "^[A-Za-z\.\,\ \'-]{2,64}$"
    }, "user_person_lastname");
    
    function run() {  
        firstnameInput.startup();       
        middleInitialInput.startup();
        lastnameInput.startup();
        phoneNumbers.run('user_person_phone_numbers');
    }
    function getData() {
        return {
            "firstname": firstnameInput.get('value'),
            "middleinitial": middleInitialInput.get('value'),
            "lastname": lastnameInput.get('value'),
            "phone_numbers": phoneNumbers.getData()
        }
    }
    function setData(person) {
        if (typeof person === "object") {
            if (person === null) {
                person = {};
            }
            person = lang.mixin({firstname: '', middleinitial: '', lastname: ''}, person);
            firstnameInput.set('value', person.firstname);
            middleInitialInput.set('value', person.middleinitial);
            lastnameInput.set('value', person.lastname);
            if (typeof person.phonenumbers !== "undefined") {
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