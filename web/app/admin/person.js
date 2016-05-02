define([
    "dojo/_base/declare",
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
], function (declare, dom, domAttr, domConstruct, on,
        registry, TextBox, ValidationTextBox,
        lib, core) {
    "use strict";
    
    var firstnameInput = new ValidationTextBox({}, "person_firstname");
    var middleInitialInput = new ValidationTextBox({}, "person_middleinitial");
    var lastnameInput = new ValidationTextBox({}, "person_lastname");
    
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
        firstnameInput.set('value', user.firstname);
        middleInitialInput.set('value', user.middleinitial);
        lastnameInput.set('value', user.lastname);
    }
    return {
        getData: getData,
        run: run,
        setData: setData
    }
}
);