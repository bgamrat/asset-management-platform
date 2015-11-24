require([
    "dojo/_base/declare",
    "dojo/dom",
    "dojo/request/xhr",
    "dojo/dom-attr",
    "dojo/json",
    "dijit/registry",
    "dijit/form/TextBox",
    "dijit/form/CheckBox",
    "dijit/form/Button",
    "dijit/Dialog",
    'dstore/RequestMemory',
    'dgrid/OnDemandGrid',
    "dgrid/Selection",
    'dgrid/Editor',
    "dojo/query",
    "dojo/domReady!"
], function (declare, dom, xhr, domAttr, json, registry, TextBox, CheckBox, 
        Button, Dialog, RequestMemory, OnDemandGrid, Selection, Editor) {

    var emailInput = new TextBox({}, "user_email");
    var usernameInput = new TextBox({}, "user_username");
    var enabledCheckBox = new CheckBox({}, "user_enabled");
    
    var saveBtn = new Button({
        label: "Save"
    }, 'save-btn');
    saveBtn.startup();

    var userViewDialog = new Dialog({
        title: "View"
    }, "user-view-dialog");
    userViewDialog.on("cancel", function (event) {
        grid.clearSelection();
    });
    userViewDialog.startup();

    var grid = new (declare([OnDemandGrid, Selection, Editor]))({
        collection: new RequestMemory({target: '/api/admin/user/list'}),
        columns: {
            username: 'Username',
            email: 'Email',
            enabled: 'Enabled',
            remove: {
                width: "10%",
                editor: 'checkbox',
                label: 'Remove',
                autoSave: true,
                sortable: false
            }
        },
        selectionMode: "single"
    }, 'grid');

    grid.startup();

    grid.on("dgrid-select", function (event) {
        var username = event.rows[0].data.username;
        xhr("/api/admin/user/" + username, {
            handleAs: "json"
        }).then(function (data) {
            userViewDialog.show();
            domAttr.set("user_username", "value", data.username);
            domAttr.set("user_email", "value", data.email);
            domAttr.set("user_enabled", "checked", data.enabled === true);
        });

    });

});