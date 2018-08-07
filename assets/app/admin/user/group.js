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
        var viewGroupname = dom.byId("group_groupname");
        var groupId;

        var groupViewDialog = new Dialog({
            title: core.view,
            style: "height: 500px; width: 600px;"
        }, "group-view-dialog");
        groupViewDialog.startup();
        groupViewDialog.on("cancel", function (event) {
            grid.clearSelection();
        });

        var tabContainer = new TabContainer({
            style: "height: 500px; width: 100%;"
        }, "group-view-tabs");

        var rolesContentPane = new ContentPane({
            title: core.roles},
        "group-view-roles-tab"
                );
        tabContainer.addChild(rolesContentPane);

        var membersContentPane = new ContentPane({
            title: core.members},
        "group-view-members-tab"
                );
        tabContainer.addChild(membersContentPane);

        tabContainer.startup();

        var newBtn;
        if( dom.byId('group-new-btn') !== null ) {
            newBtn = new Button({
                label: core["new"]
            }, 'group-new-btn');
            newBtn.startup();
            newBtn.on("click", function (event) {
                groupId = null;
                nameInput.set("value", "");
                nameInput.set("readOnly", false);
                activeCheckBox.set("checked", true);
                commentInput.set("value","");
                groupViewDialog.set("title", core["new"]).show();
                action = "new";
            });
        }

        var removeBtn;
        if( dom.byId('group-remove-btn') !== null ) {
            removeBtn = new Button({
                label: core.remove
            }, 'group-remove-btn');
            removeBtn.startup();
            removeBtn.on("click", function (event) {
                var markedForDeletion = query(".dgrid-row .remove-cb input:checked", "group-grid");
                if( markedForDeletion.length > 0 ) {
                    lib.confirmAction(core.areyousure, function () {
                        markedForDeletion.forEach(function (node) {
                            var row = grid.row(node);
                            store.remove(row.data.groupname);
                        });
                    });
                }
            });
        }

        var commentInput = new ValidationTextBox({
            required: false,
            trim: true
        }, "group_comment");
        commentInput.startup();

        var nameInput = new ValidationTextBox({
            readOnly: true,
            trim: true
        }, "group_name");
        nameInput.startup();

        var activeCheckBox;
        if( dom.byId("group_active") !== null ) {
            activeCheckBox = new CheckBox({}, "group_enabled");
            activeCheckBox.startup();
        }

        var groupRolesCheckBoxes = {};
        query('[data-type="group-role-cb"] input[type="checkbox"]').forEach(function (node) {
            var label, cb;
            label = query('label[for="' + node.id + '"]')[0].textContent;
            cb = new CheckBox({label: label}, node.id);
            cb.startup();
            groupRolesCheckBoxes[label] = cb;
        });

        var groupForm = new Form({"disabled": dom.byId("group-save-btn") === null}, '[name="group"]');
        groupForm.startup();

        var saveBtn;
        if( dom.byId('group-save-btn') !== null ) {
            saveBtn = new Button({
                label: core.save
            }, 'group-save-btn');
            saveBtn.startup();
            saveBtn.on("click", function (event) {
                var g, groups, r, roles;
                if( groupForm.validate() ) {
                    roles = [];
                    for( r in groupRolesCheckBoxes ) {
                        if( groupRolesCheckBoxes[r].get("checked") === true ) {
                            roles.push(parseInt(groupRolesCheckBoxes[r].get("id").replace(/.*(\d+)$/, "$1")));
                            //roles.push(r);
                        }
                    }
                    var data = {
                        "id": groupId,
                        "name": nameInput.get("value"),
                        "comment": commentInput.get("value"),
                        "active": activeCheckBox.get("checked"),
                        "roles": roles
                    };
                    grid.collection.put(data).then(function (data) {
                        groupViewDialog.hide();
                        store.fetch();
                        grid.refresh();
                    }, lib.xhrError);
                } else {
                    lib.textError(core.invalid_form)
                }

            });
        }

        var filterInput = new TextBox({placeHolder: core.filter}, "group-filter-input");
        filterInput.startup();

        var TrackableRest = declare([Rest, SimpleQuery, Trackable]);
        var store = new TrackableRest({target: '/api/groups', useRangeHeaders: true, idProperty: 'name'});
        var grid = new (declare([OnDemandGrid, Selection, Editor]))({
            collection: store,
            maxRowsPerPage: 25,
            columns: {
                name: {
                    label: core.name
                },
                comment: {
                    label: core.comment
                },
                active: {
                    label: core.active,
                    editor: CheckBox,
                    sortable: false
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
        }, 'group-grid');
        grid.startup();
        grid.collection.track();

        grid.on(".dgrid-row:click", function (event) {
            var checkBoxes = ["enabled", "locked", "remove"];
            var row = grid.row(event);
            var cell = grid.cell(event);
            var field = cell.column.field;
            var groupname = row.data.name;
            if( checkBoxes.indexOf(field) === -1 ) {
                if( typeof grid.selection[0] !== "undefined" ) {
                    grid.clearSelection();
                }
                grid.select(row);
                grid.collection.get(groupname).then(function (group) {
                    var r;
                    action = "view";
                    groupId = group.id;
                    nameInput.set("value", group.name);
                    commentInput.set("value", group.comment);
                    if( typeof activeCheckBox !== "undefined" ) {
                        activeCheckBox.set("checked", group.active === true);
                        for( r in groupRolesCheckBoxes ) {
                            if( group.roles.indexOf(r) !== -1 ) {
                                groupRolesCheckBoxes[r].set("checked", true);
                            } else {
                                groupRolesCheckBoxes[r].set("checked", false);
                            }
                        }
                    }
                    groupViewDialog.show();
                }, lib.xhrError);
            }
        });

        grid.on('.field-enabled:dgrid-datachange, .field-locked:dgrid-datachange', function (event) {
            var row = grid.row(event);
            var cell = grid.cell(event);
            var field = cell.column.field;
            var name = row.data.name;
            var value = event.value;
            switch( field ) {
                case "enabled":
                case "locked":
                    xhr("/api/groups/" + name, {
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
            query(".dgrid-row .remove-cb", "group-grid").forEach(function (node) {
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

        on(dom.byId('group-grid-filter-form'), 'submit', function (event) {
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