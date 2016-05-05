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
    function setData(user) {
        if (typeof user === "object") {
            if (user === null) {
                user = {};
            }
            user = lang.mixin({firstname: '', middleinitial: '', lastname: ''}, user);
            firstnameInput.set('value', user.firstname);
            middleInitialInput.set('value', user.middleinitial);
            lastnameInput.set('value', user.lastname);
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