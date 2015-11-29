require([
    "dojo/_base/declare",
    "dojo/dom",
    "dojo/on",
    "dojo/request/xhr",
    "dojo/dom-attr",
    "dojo/json",
    "dijit/registry",
    "dijit/form/ValidationTextBox",
    "dijit/form/CheckBox",
    "dijit/form/Button",
    "dijit/Dialog",
    'dstore/RequestMemory',
    'dgrid/OnDemandGrid',
    "dgrid/Selection",
    'dgrid/Editor',
    "dojo/aspect",
    "dojo/query",
    "dojo/domReady!"
], function (declare, dom, on, xhr, domAttr, json, registry, ValidationTextBox, CheckBox,
        Button, Dialog, RequestMemory, OnDemandGrid, Selection, Editor, aspect, query) {

    var emailInput = new ValidationTextBox({}, "user_email");
    var usernameInput = new ValidationTextBox({}, "user_username");
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
            username: {
                label: 'Username'
            },
            email: {
                label: 'Email'
            },
            enabled: {
                label: 'Enabled'
            },
            remove: {
                width: "10%",
                editor: CheckBox,
                label: 'Remove',
                sortable: false
            }
        },
        selectionMode: "none"
    }, 'grid');


    grid.startup();
    grid.on(".dgrid-row:click", function (event) {
        console.log(event);
        var options = {handleAs: "json"};
        var row = grid.row(event);
        var cell = grid.cell(event);
        var username = row.data.username;
        if( cell.column.field !== "remove" ) {
            if( typeof grid.selection[0] !== "undefined" ) {
                grid.clearSelection();
            }
            grid.select(row);
            options.method = "GET";
            xhr("/api/admin/user/" + username, options).then(function (data) {
                userViewDialog.show();
                domAttr.set("user_username", "value", data.username);
                domAttr.set("user_email", "value", data.email);
                domAttr.set("user_enabled", "checked", data.enabled === true);
            });
        } else {
            if( confirm("Are you sure?") ) {
                options.method = "DELETE";
                xhr("/api/admin/user/" + username, options).then(function (data) {
                    grid.removeRow(row);
                });
            }
        }
    });

    aspect.before(grid, "removeRow", function (rowElement) {
        // Destroy the checkbox widgets
        var cellElement = grid.cell(rowElement, "remove").element,
                widget = (cellElement.contents || cellElement).widget;
        if( widget ) {
            widget.destroyRecursive();
        }
    });
});