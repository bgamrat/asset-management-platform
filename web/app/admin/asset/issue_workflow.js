define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/form/CheckBox",
    "dijit/form/Button",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct,
        on, query,
        CheckBox, Button,
        lib, core) {
    //"use strict";

    var nextInput = [];
    var divId = "issue_workflow_next";

    function createDijits(issueStatusId, domId) {
        var dijit;
        var index = domId.replace(/.*_(\d+)_\d+$/,"$1");
        var value = domId.replace(/.*_(\d+)$/,"$1");

        dijit = new CheckBox({
            "name":"issue_workflow[next]["+index+"]["+value+"]",
            "value": value,
            "disabled": value === issueStatusId,
            "checked": dom.byId(domId).checked === true
        }, domId);
        dijit.startup();
    }

    function run() {
        query(".statuses .form-row.issue-workflow .next input").forEach(function(node) {
            var issueStatusId = query(node).closest("[data-id]");
            createDijits(domAttr.get(issueStatusId[0],"data-id"), node.id);
        });

        var saveBtn = new Button({
            label: core.save,
            type: "submit"
        }, 'statuses-save-btn');
        saveBtn.startup();

        lib.pageReady();
    }

    return {
        run: run
    }
});