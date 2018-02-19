define([
    "dojo/_base/declare",
    "dojo/dom",
    "dojo/dom-construct",
    "dojo/dom-attr",
    "dojo/html",
    "dojo/on",
    "dojo/request/xhr",
    "dojo/aspect",
    "dojo/query",
    "dijit/registry",
    "dijit/form/Form",
    "dijit/form/TextBox",
    "dijit/form/ValidationTextBox",
    "dijit/form/RadioButton",
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
], function (declare, dom, domConstruct, domAttr, html, on, xhr, aspect, query,
        registry, Form, TextBox, ValidationTextBox, RadioButton, CheckBox, Button,
        Dialog, TabContainer, ContentPane,
        JsonRest, Rest, SimpleQuery, Trackable, OnDemandGrid, Selection, Editor, put,
        lib, libGrid, core) {
    function run() {

        var roleViewDialog = new Dialog({
            title: core.view
        }, "role-view-dialog");
        roleViewDialog.startup();
        roleViewDialog.on("cancel", function (event) {
            grid.clearSelection();
        });

        var tabContainer = new TabContainer({
            style: "height: 500px; width: 100%;"
        }, "role-view-tabs");

        var rolesContentPane = new ContentPane({
            title: core.roles},
        "role-view-roles-tab"
                );
        tabContainer.addChild(rolesContentPane);
        tabContainer.startup();

        var nameInput = new ValidationTextBox({
            placeholder: core.name,
            trim: true,
            pattern: "[a-zA-Z0-9x\.\,\ \+\(\)-]{2,24}",
            required: true
        }, "role_name");
        nameInput.startup();

        var commentInput = new ValidationTextBox({
            placeholder: core.comment,
            trim: true,
            required: false
        }, "role_comment");
        commentInput.startup();

        var activeCheckBox = new CheckBox({}, "role_active");
        activeCheckBox.startup();

        var roleId;

        var roleRolesCheckBoxes = {};
        query('[data-type="role-role-cb"] input[type="checkbox"]').forEach(function (node) {
            var label, cb;
            label = domAttr.get(query('label[for="' + node.id + '"]')[0], "data-text");
            cb = new CheckBox({label: label}, node.id);
            cb.startup();
            roleRolesCheckBoxes[label] = cb;
        });

        var roleForm = new Form({"disabled": dom.byId("role-save-btn") === null}, '[name="role"]');
        roleForm.startup();

        var newBtn;
        if( dom.byId('role-new-btn') !== null ) {
            newBtn = new Button({
                label: core["new"]
            }, 'role-new-btn');
            newBtn.startup();
            newBtn.on("click", function (event) {
                var r;
                roleId = null;
                nameInput.set("value", "");
                commentInput.set("value", "");
                activeCheckBox.set("checked", true);
                for( r in roleRolesCheckBoxes ) {
                    roleRolesCheckBoxes[r].set("checked", false);
                }
                roleViewDialog.set("title", core["new"]).show();
                action = "new";
            });
        }

        var removeBtn;
        if( dom.byId('role-remove-btn') !== null ) {
            removeBtn = new Button({
                label: core.remove
            }, 'role-remove-btn');
            removeBtn.startup();
            removeBtn.on("click", function (event) {
                var markedForDeletion = query(".dgrid-row .remove-cb input:checked", "role-grid");
                if( markedForDeletion.length > 0 ) {
                    lib.confirmAction(core.areyousure, function () {
                        markedForDeletion.forEach(function (node) {
                            var row = grid.row(node);
                            store.remove(row.data.id);
                        });
                    });
                }
            });
        }

        var saveBtn;
        if( dom.byId('role-save-btn') !== null ) {
            saveBtn = new Button({
                label: core.save
            }, 'role-save-btn');
            saveBtn.startup();
            saveBtn.on("click", function (event) {
                var r, roles;
                if( roleForm.validate() ) {
                    roles = [];
                    for( r in roleRolesCheckBoxes ) {
                        if( roleRolesCheckBoxes[r].get("checked") === true ) {
                            roles.push(parseInt(roleRolesCheckBoxes[r].get("id").replace(/.*(\d+)$/, "$1")));
                        }
                    }
                    var data = {
                        "id": roleId,
                        "name": nameInput.get("value"),
                        "active": activeCheckBox.get("checked"),
                        "roles": roles
                    };
                    grid.collection.put(data).then(function (data) {
                        roleViewDialog.hide();
                        store.fetch();
                        grid.refresh();
                    }, lib.xhrError);
                } else {
                    lib.textError(core.invalid_form)
                }

            });
        }


        var filterInput = new TextBox({placeHolder: core.filter}, "role-filter-input");
        filterInput.startup();

        var TrackableRest = declare([Rest, SimpleQuery, Trackable]);
        var store = new TrackableRest({target: '/api/roles', roleangeHeaders: true, idProperty: 'id'});
        var grid = new (declare([OnDemandGrid, Selection, Editor]))({
            collection: store,
            className: "dgrid-autoheight",
            columns: {
                id: {
                    label: core.id
                },
                "default": {
                    label: core["default"],
                    editor: RadioButton,
                    editOn: "click",
                    sortable: false,
                    //renderCell: libGrid.renderGridRadioButton
                },
                name: {
                    label: core.role
                },
                comment: {
                    label: core.comment
                },
                active: {
                    label: core.active,
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
        }, 'role-grid');
        grid.startup();
        grid.collection.track();

        grid.on(".dgrid-row:click", function (event) {
            var checkBoxes = ["enabled", "locked", "remove"];
            var row = grid.row(event);
            var cell = grid.cell(event);
            var field = cell.column.field;
            var id = row.data.id;
            if( checkBoxes.indexOf(field) === -1 ) {
                if( typeof grid.selection[0] !== "undefined" ) {
                    grid.clearSelection();
                }
                grid.select(row);
                grid.collection.get(id).then(function (role) {
                    var r, g;
                    action = "view";
                    roleId = role.id;
                    nameInput.set("value", role.name);
                    commentInput.set("value", role.comment);
                    activeCheckBox.set("checked", role.active === true);
                    for( r in roleRolesCheckBoxes ) {
                        if( role.roles.indexOf(r) !== -1 ) {
                            roleRolesCheckBoxes[r].set("checked", true);
                        } else {
                            roleRolesCheckBoxes[r].set("checked", false);
                        }
                    }
                    roleViewDialog.show();
                }, lib.xhrError);
            }
        });

        grid.on('.field-enabled:dgrid-datachange, .field-locked:dgrid-datachange', function (event) {
            var row = grid.row(event);
            var cell = grid.cell(event);
            var field = cell.column.field;
            var id = row.data.id;
            var value = event.value;
            switch( field ) {
                case "enabled":
                case "locked":
                    xhr("/api/roles/" + id, {
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
            query(".dgrid-row .remove-cb", "role-grid").forEach(function (node) {
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

        on(dom.byId('role-grid-filter-form'), 'submit', function (event) {
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