define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/form/Button",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on, query,
        Button,
        lib, core) {
    function run() {

        var testBtn = new Button({
            label: 'Test',
            type: "submit"
        }, 'test-btn');
        testBtn.on("click",function(evt){
            lib.textError("Yay!"); 
        });
        testBtn.startup();
        lib.pageReady();
    }
    return {
        run: run
    }
});