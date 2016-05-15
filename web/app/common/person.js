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
    "app/common/phone_numbers",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/domReady!"
], function (declare, lang, dom, domAttr, domConstruct, on,
        registry, TextBox, ValidationTextBox,
        lib, core) {
    "use strict";
    
    var firstnameInput = new ValidationTextBox({}, "user_person_firstname");
    var middleInitialInput = new ValidationTextBox({}, "user_person_middleinitial");
    var lastnameInput = new ValidationTextBox({}, "user_person_lastname");
    
    function run() {  
        firstnameInput.startup();       
        middleInitialInput.startup();
        lastnameInput.startup();
    }
    function getData() {
        return {
            "firstname": firstnameInput.get('value'),
            "middleinitial": middleInitialInput.get('value'),
            "lastname": lastnameInput.get('value')
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