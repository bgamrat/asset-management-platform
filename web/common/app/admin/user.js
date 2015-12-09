require([
    "dojo/_base/declare",
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/request/xhr",
    "dojo/json",
    "dojo/aspect",
    "dojo/query",
    "dijit/registry",
    "dijit/form/ValidationTextBox",
    "dijit/form/CheckBox",
    "dijit/form/Select",
    "dijit/form/Button",
    "dijit/Dialog",
    'dstore/RequestMemory',
    'dgrid/OnDemandGrid',
    "dgrid/Selection",
    'dgrid/Editor',
    "dojo/i18n!app/nls/core",
    "app/lib/common",
    "dojo/domReady!"
], function (declare, dom, domAttr, domConstruct, on, xhr, json, aspect, query,
        registry, ValidationTextBox, CheckBox, Select, Button, Dialog,
        RequestMemory, OnDemandGrid, Selection, Editor, core, lib) {

    var newBtn = new Button({
        label: core["new"]
    }, 'new-btn');
    newBtn.startup();

    var removeBtn = new Button({
        label: core.remove
    }, 'remove-btn');
    removeBtn.startup();

    var emailInput = new ValidationTextBox({pattern: "^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$" 
}, "user_email");
    emailInput.startup();

    var usernameInput = new ValidationTextBox({}, "user_username");
    usernameInput.startup();

    var enabledCheckBox = new CheckBox({}, "user_enabled");
    enabledCheckBox.startup();
    
    var lockedCheckBox = new CheckBox({}, "user_locked");
    lockedCheckBox.startup();
    
    var userGroupsSelect = new Select({}, "user_groups");
    userGroupsSelect.startup();

    var saveBtn = new Button({
        label: core.save
    }, 'save-btn');
    saveBtn.startup();

    var userViewDialog = new Dialog({
        title: core.view
    }, "user-view-dialog");
    userViewDialog.startup();
    userViewDialog.on("cancel", function (event) {
        grid.clearSelection();
    });

    var grid = new (declare([OnDemandGrid, Selection, Editor]))({
        collection: new RequestMemory({target: '/api/admin/user/list'}),
        columns: {
            username: {
                label: core.username
            },
            email: {
                label: core.email
            },
            enabled: {
                label: core.enabled,
                editor: CheckBox,
                sortable: false
            },
            locked: {
                label: core.locked,
                editor: CheckBox,
                sortable: false
            },
            remove: {
                editor: CheckBox,
                label: core.remove,
                sortable: false,
                className: "remove-cb",
                renderHeaderCell: function (node) {
                    var inp = domConstruct.create("input", {id: "cb-all", type: "checkbox"});
                    return inp;
                }
            }
        },
        selectionMode: "none"
    }, 'grid');
    grid.startup();

    grid.on(".dgrid-row:click", function (event) {
        var checkBoxes = [ "enabled", "locked", "remove" ];
        var options = {handleAs: "json"};
        var row = grid.row(event);
        var cell = grid.cell(event);
        var username = row.data.username;
        if( checkBoxes.indexOf(cell.column.field) === -1 ) {
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
        }
    });

    var cbAll = new CheckBox({}, "cb-all");
    cbAll.startup();
    cbAll.on("click", function (event) {
        var state = this.checked;
        query(".dgrid-row .remove-cb input").forEach(function (node) {
            registry.byId(node.id).set("checked", state);
        });
    });

    removeBtn.on("click", function (event) {
        if( confirm(core.areyousure) ) {
            query(".dgrid-row .remove-cb input").forEach(function (node) {
                var row = grid.row(node);
                xhr("/api/admin/user/" + row.data.username, {handleAs: "json", method: "DELETE"}).then(function (data) {
                    grid.removeRow(row);
                });
            });
        }
    });

    aspect.before(grid, "removeRow", function (rowElement) {
        // Destroy the checkbox widgets
        var e, elements = [grid.cell(rowElement, "remove").element, grid.cell(rowElement, "enabled"), grid.cell(rowElement, "locked")];
        var widget;
        for (e in elements) {
            widget = (e.contents || e).widget;
            if( widget ) {
                widget.destroyRecursive();
            }
        }
    });


});