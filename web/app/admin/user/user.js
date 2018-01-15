define([
    "dojo/_base/declare",
    "dojo/dom",
    "dojo/dom-construct",
    "dojo/html",
    "dojo/on",
    "dojo/request/xhr",
    "dojo/aspect",
    "dojo/query",
    "dijit/registry",
    "dijit/form/Form",
    "dijit/form/TextBox",
    "dijit/form/ValidationTextBox",
    "dijit/form/FilteringSelect",
    "dijit/form/CheckBox",
    "dijit/form/Button",
    "dijit/Dialog",
    "dijit/layout/TabContainer",
    "dijit/layout/ContentPane",
    'dojo/store/JsonRest',
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
], function (declare, dom, domConstruct, html, on, xhr, aspect, query,
        registry, Form, TextBox, ValidationTextBox, FilteringSelect, CheckBox, Button,
        Dialog, TabContainer, ContentPane,
        JsonRest, Rest, SimpleQuery, Trackable, OnDemandGrid, Selection, Editor, put,
        lib, libGrid, core) {
    //"use strict";
    function run() {
        var action = null;
        var viewUsername = dom.byId("user_username");

        var userViewDialog = new Dialog({
            title: core.view
        }, "user-view-dialog");
        userViewDialog.startup();
        userViewDialog.on("cancel", function (event) {
            grid.clearSelection();
        });

        var tabContainer = new TabContainer({
            style: "height: 500px; width: 100%;"
        }, "user-view-tabs");

        var groupsContentPane = new ContentPane({
            title: core.groups},
        "user-view-groups-tab"
                );
        tabContainer.addChild(groupsContentPane);

        var rolesContentPane = new ContentPane({
            title: core.roles},
        "user-view-roles-tab"
                );
        tabContainer.addChild(rolesContentPane);

        tabContainer.startup();

        var newBtn;
        if( dom.byId('user-new-btn') !== null ) {
            newBtn = new Button({
                label: core["new"]
            }, 'user-new-btn');
            newBtn.startup();
            newBtn.on("click", function (event) {
                usernameInput.set("value", "");
                usernameInput.set("readOnly", false);
                emailInput.set("value", "");
                enabledCheckBox.set("checked", true);
                lockedCheckBox.set("checked", false);
                personSelector.set("value",null);
                userViewDialog.set("title", core["new"]).show();
                action = "new";
            });
        }

        var removeBtn;
        if( dom.byId('user-remove-btn') !== null ) {
            removeBtn = new Button({
                label: core.remove
            }, 'user-remove-btn');
            removeBtn.startup();
            removeBtn.on("click", function (event) {
                var markedForDeletion = query(".dgrid-row .remove-cb input:checked", "user-grid");
                if( markedForDeletion.length > 0 ) {
                    lib.confirmAction(core.areyousure, function () {
                        markedForDeletion.forEach(function (node) {
                            var row = grid.row(node);
                            store.remove(row.data.username);
                        });
                    });
                }
            });
        }

        var personStore = new JsonRest({
            target: '/api/store/people?',
            useRangeHeaders: false,
            idProperty: 'id'});

        var personSelector = new FilteringSelect({
            required: true,
            "class": "name",
            store: personStore,
            searchAttr: "name",
            placeholder: core.lastname
        }, "user_person");
        personSelector.startup();

        var emailInput = new ValidationTextBox({
            required: true,
            pattern: "[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?",
            trim: true
        }, "user_email");
        emailInput.startup();

        var usernameInput = new ValidationTextBox({
            readOnly: true
        }, "user_username");
        usernameInput.startup();

        var enabledCheckBox;
        if( dom.byId("user_enabled") !== null ) {
            enabledCheckBox = new CheckBox({}, "user_enabled");
            enabledCheckBox.startup();
        }

        var lockedCheckBox;
        if( dom.byId("user_locked") !== null ) {
            lockedCheckBox = new CheckBox({}, "user_locked");
            lockedCheckBox.startup();
        }

        var userGroupsCheckBoxes = {};
        query('[data-type="user-group-cb"] input[type="checkbox"]').forEach(function (node) {
            var label, cb;
            label = query('label[for="' + node.id + '"]')[0].textContent;
            cb = new CheckBox({label: label}, node.id);
            cb.startup();
            userGroupsCheckBoxes[node.id.replace(/^\w+_(\d+)$/, '$1')] = cb;
        });

        var userRolesCheckBoxes = {};
        query('[data-type="user-role-cb"] input[type="checkbox"]').forEach(function (node) {
            var label, cb;
            label = query('label[for="' + node.id + '"]')[0].textContent;
            cb = new CheckBox({label: label}, node.id);
            cb.startup();
            userRolesCheckBoxes[label] = cb;
        });

        var userForm = new Form({"disabled": dom.byId("user-save-btn") === null}, '[name="user"]');
        userForm.startup();

        var saveBtn;
        if( dom.byId('user-save-btn') !== null ) {
            saveBtn = new Button({
                label: core.save
            }, 'user-save-btn');
            saveBtn.startup();
            saveBtn.on("click", function (event) {
                var g, groups, r, roles;
                if( userForm.validate() ) {
                    groups = [];
                    for( g in userGroupsCheckBoxes ) {
                        if( userGroupsCheckBoxes[g].get("checked") === true ) {
                            groups.push(parseInt(g));
                        }
                    }
                    roles = [];
                    for( r in userRolesCheckBoxes ) {
                        if( userRolesCheckBoxes[r].get("checked") === true ) {
                            roles.push(parseInt(userRolesCheckBoxes[r].get("id").replace(/.*(\d+)$/, "$1")));
                            //roles.push(r);
                        }
                    }
                    var data = {
                        "username": usernameInput.get("value"),
                        "email": emailInput.get("value"),
                        "enabled": enabledCheckBox.get("checked"),
                        "locked": lockedCheckBox.get("checked"),
                        "groups": groups,
                        "roles": roles,
                        "person": personSelector.get("value")
                    };
                    grid.collection.put(data).then(function (data) {
                        userViewDialog.hide();
                        store.fetch();
                        grid.refresh();
                    }, lib.xhrError);
                } else {
                    lib.textError(core.invalid_form)
                }

            });
        }

        var inviteBtn;
        if( dom.byId('user-invite-btn') !== null ) {
            var userInviteDialog = new Dialog({
                title: core.invite
            }, "user-invite-dialog");
            userInviteDialog.startup();
            inviteBtn = new Button({
                label: core["invite"]
            }, 'user-invite-btn');
            inviteBtn.startup();
            inviteBtn.on("click", function (event) {
                inviteEmailInput.set("value", "");
                userInviteDialog.set("title", core["invite"]).show();
            })

            var inviteEmailInput = new ValidationTextBox({required: true, pattern: "^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$"
            }, "invitation_email");
            inviteEmailInput.startup();

            var inviteSendBtn = new Button({
                label: core.send
            }, 'user-invite-send-btn');
            inviteSendBtn.startup();
            inviteSendBtn.on("click", function (event) {
                if( userInviteForm.validate() ) {
                    xhr("/admin/user/invite", {
                        method: "POST",
                        handleAs: "json",
                        headers: {'Content-Type': 'application/json'},
                        data: JSON.stringify({"email": inviteEmailInput.get("value")})}).then(function () {
                        userInviteDialog.hide();
                    }, lib.xhrError);
                } else {
                    lib.textError(core.invalid_form)
                }
            });
            var userInviteForm = new Form({}, '[name="invitation"]');
            userInviteForm.startup();
        }

        var filterInput = new TextBox({placeHolder: core.filter}, "user-filter-input");
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
            renderRow: function (object) {
                var rowElement = this.inherited(arguments);
                if( typeof object.deleted_at !== "undefined" && object.deleted_at !== null ) {
                    rowElement.className += ' deleted';
                }
                return rowElement;
            },
            selectionMode: "none"
        }, 'user-grid');
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
                    var r;
                    action = "view";
                    html.set(viewUsername, user.username);
                    usernameInput.set("value", user.username);
                    emailInput.set("value", user.email);
                    if( typeof enabledCheckBox !== "undefined" ) {
                        enabledCheckBox.set("checked", user.enabled === true);
                        lockedCheckBox.set("checked", user.locked === true);
                        for( g in userGroupsCheckBoxes ) {
                            if( user.groups.indexOf(parseInt(g)) !== -1 ) {
                                userGroupsCheckBoxes[g].set("checked", true);
                            } else {
                                userGroupsCheckBoxes[g].set("checked", false);
                            }
                        }
                        for( r in userRolesCheckBoxes ) {
                            if( user.roles.indexOf(r) !== -1 ) {
                                userRolesCheckBoxes[r].set("checked", true);
                            } else {
                                userRolesCheckBoxes[r].set("checked", false);
                            }
                        }
                    }
                    personSelector.set("value",(user.person !== null) ? user.person.id : null);
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
            query(".dgrid-row .remove-cb", "user-grid").forEach(function (node) {
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

        on(dom.byId('user-grid-filter-form'), 'submit', function (event) {
            event.preventDefault();
            grid.set('collection', store.filter({
                // Pass a RegExp to Memory's filter method
                // Note: this code does not go out of its way to escape
                // characters that have special meaning in RegExps
                match: new RegExp(filterInput.get("value").replace(/\W/, ''), 'i')
            }));
        });

        lib.pageReady();
    }
    return {
        run: run
    }
});