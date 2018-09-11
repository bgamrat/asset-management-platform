define([
    "dojo/_base/declare",
    "dojo/dom",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/request/xhr",
    "dojo/aspect",
    "dojo/query",
    "dijit/registry",
    "dijit/form/Form",
    "dijit/form/TextBox",
    "dijit/form/ValidationTextBox",
    "dijit/form/CurrencyTextBox",
    "dijit/form/CheckBox",
    "dijit/form/SimpleTextarea",
    "dijit/form/Button",
    "dijit/Dialog",
    "dstore/Rest",
    "dstore/SimpleQuery",
    "dstore/Trackable",
    "dgrid/OnDemandGrid",
    "dgrid/Selection",
    "dgrid/Editor",
    "put-selector/put",
    "app/admin/asset/models",
    "app/admin/asset/satisfies",
    "app/lib/common",
    "app/lib/grid",
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/asset",
    "dojo/domReady!"
], function (declare, dom, domConstruct, on, xhr, aspect, query,
        registry, Form, TextBox, ValidationTextBox, CurrencyTextBox, CheckBox, SimpleTextarea, Button,
        Dialog,
        Rest, SimpleQuery, Trackable, OnDemandGrid, Selection, Editor, put,
        models,satisfies, lib, libGrid, core, asset) {
    // "use strict";
    function run() {
        var action = null;
        var contact;

        var setId;

        var setViewDialog = new Dialog({
            title: core.view,
            style: "width: 800px; height: 800px;"
        }, "set-view-dialog");
        setViewDialog.startup();
        setViewDialog.on("cancel", function (event) {
            grid.clearSelection();
        });

        var newBtn = new Button({
            label: core["new"]
        }, "set-new-btn");
        newBtn.startup();
        newBtn.on("click", function (event) {
            setId = null;
            nameInput.set("value", "");
            valueInput.set("value", null);
            inUseCheckBox.set("checked", true);
            setViewDialog.set("title", core["new"]).show();
            action = "new";
        });

        var removeBtn = new Button({
            label: core.remove
        }, "set-remove-btn");
        removeBtn.startup();
        removeBtn.on("click", function (event) {
            var markedForDeletion = query(".dgrid-row .remove-cb input:checked", "set-grid");
            if( markedForDeletion.length > 0 ) {
                lib.confirmAction(core.areyousure, function () {
                    markedForDeletion.forEach(function (node) {
                        var row = grid.row(node);
                        store.remove(row.data.id);
                    });
                });
            }
        });

        var nameInput = new ValidationTextBox({
            trim: true,
            pattern: "[A-Za-z0-9\.\,\ \'-]{2,64}"
        }, "set_name");
        nameInput.startup();

        var valueInput = new CurrencyTextBox({
            placeholder: core.value,
            trim: true,
            required: false
        }, "set_value");
        valueInput.startup();

        var commentInput = new SimpleTextarea({
            placeholder: core.comment,
            trim: true,
            required: false
        }, "set_comment");
        commentInput.startup();

        var inUseCheckBox = new CheckBox({}, "set_in_use");
        inUseCheckBox.startup();

        var setForm = new Form({}, '[name="set"]');
        setForm.startup();

        var saveBtn = new Button({
            label: core.save
        }, "set-save-btn");
        saveBtn.startup();
        saveBtn.on("click", function (event) {
            if( setForm.validate() ) {
                var data = {
                    "id": setId,
                    "name": nameInput.get("value"),
                    "comment": commentInput.get("value"),
                    "value": valueInput.get("value"),
                    "in_use": inUseCheckBox.get("checked"),
                    "satisfies": satisfies.getData(),
                    "models": models.getData(),
                };
                grid.collection.put(data).then(function (data) {
                    setViewDialog.hide();
                    store.fetch();
                    grid.refresh();
                }, lib.xhrError);
            } else {
                lib.textError(core.invalid_form);
            }
        });

        var filterInput = new TextBox({placeHolder: core.filter}, "set-filter-input");
        filterInput.startup();

        var TrackableRest = declare([Rest, SimpleQuery, Trackable]);
        var store = new TrackableRest({target: "/api/sets", useRangeHeaders: true, idProperty: "id"});
        var grid = new (declare([OnDemandGrid, Selection, Editor]))({
            collection: store,
            maxRowsPerPage: 25,
            columns: {
                id: {
                    label: core.id
                },
                name: {
                    label: asset.set,
                    renderCell: function (object, value, td) {
                        put(td, "pre.name", object.name);
                        libGrid.renderContacts(object, object, td);
                    },
                    className: "xset"
                },
                comment: {
                    label: core.comment
                },
                value: {
                    label: core.value
                },
                in_use: {
                    label: core.in_use,
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
            renderRow: function (object) {
                var rowElement = this.inherited(arguments);
                if( typeof object.deleted_at !== "undefined" && object.deleted_at !== null ) {
                    rowElement.className += " deleted";
                }
                return rowElement;
            },
            selectionMode: "none"
        }, "set-grid");
        grid.startup();
        grid.collection.track();

        grid.on(".dgrid-row:click", function (event) {
            var checkBoxes = ["active", "remove"];
            var row = grid.row(event);
            var cell = grid.cell(event);
            var field = cell.column.field;
            var id = row.data.id;
            if( checkBoxes.indexOf(field) === -1 ) {
                if( typeof grid.selection[0] !== "undefined" ) {
                    grid.clearSelection();
                }
                grid.select(row);
                grid.collection.get(id).then(function (set) {
                    action = "view";
                    setId = set.id;
                    satisfies.setData(set.satisfies);
                    models.setData(set.models);
                    setViewDialog.show();
                    nameInput.set("value", set.name);
                    commentInput.set("value", set.comment);
                    valueInput.set("value", set.value);
                    inUseCheckBox.set("checked", set.active === true);
                }, lib.xhrError);
            }
        });

        grid.on(".field-active:dgrid-datachange, .field-locked:dgrid-datachange", function (event) {
            var row = grid.row(event);
            var cell = grid.cell(event);
            var field = cell.column.field;
            var name = row.data.name;
            var value = event.value;
            switch( field ) {
                case "active":
                case "locked":
                    xhr("/api/sets/" + name, {
                        method: "PATCH",
                        handleAs: "json",
                        headers: {"Content-Type": "application/json"},
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
            query(".dgrid-row .remove-cb", "set-grid").forEach(function (node) {
                registry.findWidgets(node)[0].set("checked", state);
            });
        });

        aspect.before(grid, "removeRow", function (rowElement) {
            // Destroy the checkbox widgets
            var e, elements = [grid.cell(rowElement, "remove").element, grid.cell(rowElement, "active"), grid.cell(rowElement, "locked")];
            var widget;
            for( e in elements ) {
                widget = (e.contents || e).widget;
                if( widget ) {
                    widget.destroyRecursive();
                }
            }
        });

        on(dom.byId("set-grid-filter-form"), "submit", function (event) {
            event.preventDefault();
            grid.set("collection", store.filter({
                // Pass a RegExp to Memory's filter method
                // Note: this code does not go out of its way to escape
                // characters that have special meaning in RegExps
                match: new RegExp(filterInput.get("value").replace(/\W/, ""), "i")
            }));
        });

        satisfies.run('set');
        models.run('set');
        lib.pageReady();
    }
    return {
        run: run
    };
});