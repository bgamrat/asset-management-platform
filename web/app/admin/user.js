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
    "dijit/form/Form",
    "dijit/form/TextBox",
    "dijit/form/ValidationTextBox",
    "dijit/form/CheckBox",
    "dijit/form/Select",
    "dijit/form/Button",
    "dijit/Dialog",
    'dstore/Rest',
    'dstore/SimpleQuery',
    'dstore/Trackable',
    'dgrid/OnDemandGrid',
    "dgrid/Selection",
    'dgrid/Editor',
    'put-selector/put',
    "app/lib/common",
    "app/lib/grid",
    "dojo/i18n!app/nls/core",
    "dojo/domReady!"
], function (declare, dom, domAttr, domConstruct, on, xhr, json, aspect, query,
        registry, Form, TextBox, ValidationTextBox, CheckBox, Select, Button, Dialog,
        Rest, SimpleQuery, Trackable, OnDemandGrid, Selection, Editor, put,
        lib, libGrid, core) {

    var action = null;

    var userViewDialog = new Dialog({
        title: core.view
    }, "user-view-dialog");
    userViewDialog.startup();
    userViewDialog.on("cancel", function (event) {
        grid.clearSelection();
    });

    var newBtn = new Button({
        label: core["new"]
    }, 'new-btn');
    newBtn.startup();
    newBtn.on("click", function (event) {
        usernameInput.set("value", "");
        usernameInput.set("readOnly", false);
        emailInput.set("value", "");
        enabledCheckBox.set("checked", true);
        lockedCheckBox.set("checked", false);
        userViewDialog.set("title", core["new"]).show();
        action = "new";
    });

    var removeBtn = new Button({
        label: core.remove
    }, 'remove-btn');
    removeBtn.startup();
    removeBtn.on("click", function (event) {
        var markedForDeletion = query(".dgrid-row .remove-cb input:checked");
        if( markedForDeletion.length > 0 ) {
            lib.confirmAction(core.areyousure, function () {
                markedForDeletion.forEach(function (node) {
                    var row = grid.row(node);
                    store.remove(row.data.username);
                });
            });
        }
    });

    var emailInput = new ValidationTextBox({required: true, pattern: "^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$"
    }, "user_email");
    emailInput.startup();

    var usernameInput = new ValidationTextBox({readOnly: true}, "user_username");
    usernameInput.startup();

    var enabledCheckBox = new CheckBox({}, "user_enabled");
    enabledCheckBox.startup();

    var lockedCheckBox = new CheckBox({}, "user_locked");
    lockedCheckBox.startup();

    var userGroupsCheckBoxes = {};
    query('[data-type="user-group-cb"] input[type="checkbox"]').forEach(function (node) {
        var label, cb;
        label = query('label[for="' + node.id + '"]')[0].textContent;
        cb = new CheckBox({label: label}, node.id);
        cb.startup();
        userGroupsCheckBoxes[node.id.replace(/^\w+_(\d+)$/, '$1')] = cb;
    });

    var userForm = new Form({}, "user-form");
    userForm.startup();

    var saveBtn = new Button({
        label: core.save
    }, 'save-btn');
    saveBtn.startup();
    saveBtn.on("click", function (event) {
        var beforeId, beforeIdFilter, filter, g, groups;
        if( userForm.validate() ) {
            groups = [];
            for( g in userGroupsCheckBoxes ) {
                if( userGroupsCheckBoxes[g].get("checked") === true ) {
                    groups.push(g);
                }
            }
            var data = {
                "username": usernameInput.get("value"),
                "email": emailInput.get("value"),
                "enabled": enabledCheckBox.get("checked"),
                "locked": lockedCheckBox.get("checked"),
                "groups": groups
            };
            if( action === "view" ) {
                grid.collection.put(data).then(function (data) {
                    userViewDialog.hide();
                }, lib.xhrError);
            } else {
                filter = new store.Filter();
                beforeIdFilter = filter.gt('username', data.username);
                store.filter(beforeIdFilter).sort('username').fetchRange({start: 0, end: 1}).then(function (results) {
                    beforeId = (results.length > 0) ? results[0].username : null;
                    grid.collection.add(data, {"beforeId": beforeId}).then(function (data) {
                        userViewDialog.hide();
                    }, lib.xhrError);
                });
            }
        } else {
            lib.textError(core.invalid_form)
        }
    });

    var filterInput = new TextBox({placeHolder: core.filter}, "filter-input");
    filterInput.startup();

    var TrackableRest = declare([Rest, SimpleQuery, Trackable]);
    var store = new TrackableRest({target: '/api/users', useRangeHeaders: true, idProperty: 'username'});
    var grid = new (declare([OnDemandGrid, Selection, Editor]))({
        collection: store,
        className: "dgrid-autoheight",
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
                editOn: "click",
                sortable: false,
                renderCell: libGrid.renderGridCheckbox
            },
            locked: {
                label: core.locked,
                editor: CheckBox,
                editOn: "click",
                sortable: false,
                renderCell: libGrid.renderGridCheckbox
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
    grid.collection.track();

    grid.on(".dgrid-row:click", function (event) {
        var checkBoxes = ["enabled", "locked", "remove"];
        var row = grid.row(event);
        var cell = grid.cell(event);
        var field = cell.column.field;
        var username = row.data.username;
        if( checkBoxes.indexOf(field) === -1 ) {
            if( typeof grid.selection[0] !== "undefined" ) {
                grid.clearSelection();
            }
            grid.select(row);
            grid.collection.get(username).then(function (user) {
                var g;
                action = "view";
                usernameInput.set("value", user.username);
                emailInput.set("value", user.email);
                enabledCheckBox.set("checked", user.enabled === true);
                lockedCheckBox.set("checked", user.locked === true);
                for( g in userGroupsCheckBoxes ) {
                    if( user.groups.indexOf(parseInt(g)) !== -1 ) {
                        userGroupsCheckBoxes[g].set("checked", true);
                    } else {
                        userGroupsCheckBoxes[g].set("checked", false);
                    }
                }
                userViewDialog.show();
            }, lib.xhrError);
        }
    });

    grid.on('.field-enabled:dgrid-datachange, .field-locked:dgrid-datachange', function (event) {
        var row = grid.row(event);
        var cell = grid.cell(event);
        var field = cell.column.field;
        var username = row.data.username;
        var value = event.value;
        switch( field ) {
            case "enabled":
            case "locked":
                xhr("/api/users/" + username, {
                    method: "PATCH",
                    handleAs: "json",
                    headers: {'Content-Type': 'application/json'},
                    data: JSON.stringify({"field": field,
                        "value": value})
                });
                break;
        }
    });

    var cbAll = new CheckBox({}, "cb-all");
    cbAll.startup();
    cbAll.on("click", function (event) {
        var state = this.checked;
        query(".dgrid-row .remove-cb").forEach(function (node) {
            registry.findWidgets(node)[0].set("checked", state);
        });
    });

    aspect.before(grid, "removeRow", function (rowElement) {
        // Destroy the checkbox widgets
        var e, elements = [grid.cell(rowElement, "remove").element, grid.cell(rowElement, "enabled"), grid.cell(rowElement, "locked")];
        var widget;
        for( e in elements ) {
            widget = (e.contents || e).widget;
            if( widget ) {
                widget.destroyRecursive();
            }
        }
    });

    on(dom.byId('grid-filter-form'), 'submit', function (event) {
        event.preventDefault();
        grid.set('collection', store.filter({
            // Pass a RegExp to Memory's filter method
            // Note: this code does not go out of its way to escape
            // characters that have special meaning in RegExps
            match: new RegExp(filterInput.get("value").replace(/\W/, ''), 'i')
        }));
    });
});