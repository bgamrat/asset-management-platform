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
    function run() {
        var firstnameInput = new ValidationTextBox({readOnly: true}, "person_firstname");
        firstnameInput.startup();
        var middleInitialInput = new ValidationTextBox({readOnly: true}, "person_middleinitial");
        middleInitialInput.startup();
        var lastnameInput = new ValidationTextBox({readOnly: true}, "person_lastname");
        lastnameInput.startup();
    }
    return {
        run: run
    }
}
);