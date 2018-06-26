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
    "dijit/form/CheckBox",
    "dijit/form/SimpleTextarea",
    "dijit/form/Button",
    "dijit/Dialog",
    "dijit/layout/TabContainer",
    "dijit/layout/ContentPane",
    'dstore/Rest',
    'dstore/SimpleQuery',
    'dstore/Trackable',
    'dgrid/OnDemandGrid',
    "dgrid/Selection",
    'dgrid/Editor',
    'put-selector/put',
    "app/common/contact",
    "app/admin/asset/brands",
    "app/lib/common",
    "app/lib/grid",
    "dojo/i18n!app/nls/core",
    "dojo/domReady!"
], function (declare, dom, domConstruct, on,
        xhr, aspect, query,
        registry, Form, TextBox, ValidationTextBox, CheckBox, SimpleTextarea, Button,
        Dialog, TabContainer, ContentPane,
        Rest, SimpleQuery, Trackable, OnDemandGrid, Selection, Editor, put,
        xcontact, brands, lib, libGrid, core) {
    //"use strict";
    function run() {
        var action = null;
        var manufacturerId = null;
        var contact;

        var manufacturerViewDialog = new Dialog({
            title: core.view,
            style: "width:800px"
        }, "manufacturer-view-dialog");
        manufacturerViewDialog.startup();
        manufacturerViewDialog.on("cancel", function (event) {
            grid.clearSelection();
        });

        var tabContainer = new TabContainer({
            style: "height: 525px; width: 100%;"
        }, "manufacturer-view-tabs");

        var contactsContentPane = new ContentPane({
            title: core.contacts},
        "manufacturer-view-contacts-tab"
                );
        tabContainer.addChild(contactsContentPane);

        var brandsContentPane = new ContentPane({
            title: core.brands},
        "manufacturer-view-brands-tab"
                );
        tabContainer.addChild(brandsContentPane);

        var historyContentPane = new ContentPane({
            title: core.history},
        "manufacturer-view-history-tab"
                );
        tabContainer.addChild(historyContentPane);
        tabContainer.startup();

        var newBtn = new Button({
            label: core["new"]
        }, 'manufacturer-new-btn');
        newBtn.startup();
        newBtn.on("click", function (event) {
            nameInput.set("value", "");
            activeCheckBox.set("checked", true);
            contact.setData(null);
            brands.setData(null);
            manufacturerViewDialog.set("title", core["new"]).show();
            action = "new";
            manufacturerId = null;
        });

        var removeBtn = new Button({
            label: core.remove
        }, 'manufacturer-remove-btn');
        removeBtn.startup();
        removeBtn.on("click", function (event) {
            var markedForDeletion = query(".dgrid-row .remove-cb input:checked", "manufacturer-grid");
            if( markedForDeletion.length > 0 ) {
                lib.confirmAction(core.areyousure, function () {
                    markedForDeletion.forEach(function (node) {
                        var row = grid.row(node);
                        store.remove(row.data.name);
                    });
                });
            }
        });

        var nameInput = new ValidationTextBox({
            trim: true,
            pattern: "[A-Za-z\.\,\ \'-]{2,64}"
        }, "manufacturer_name");
        nameInput.startup();

        var activeCheckBox = new CheckBox({}, "manufacturer_active");
        activeCheckBox.startup();

        var commentInput = new SimpleTextarea({
            placeholder: core.comment,
            trim: true,
            required: false
        }, "manufacturer_comment");
        commentInput.startup();

        var manufacturerForm = new Form({}, '[name="manufacturer"]');
        manufacturerForm.startup();

        var saveBtn = new Button({
            label: core.save
        }, 'manufacturer-save-btn');
        saveBtn.startup();
        saveBtn.on("click", function (event) {
            if( manufacturerForm.validate() ) {
                var data = {
                    "id": manufacturerId,
                    "name": nameInput.get("value"),
                    "active": activeCheckBox.get("checked"),
                    "comment": commentInput.get("value"),
                    "contacts": contact.getData(),
                    "brands": brands.getData()
                };
                grid.collection.put(data).then(function (data) {
                    manufacturerViewDialog.hide();
                    store.fetch();
                    grid.refresh();
                }, lib.xhrError);
            } else {
                lib.textError(core.invalid_form)
            }
        });

        var manufacturerBrandForm = new Form({}, '[name="brands"]');
        manufacturerBrandForm.startup();

        var filterInput = new TextBox({placeHolder: core.filter}, "manufacturer-filter-input");
        filterInput.startup();

        var TrackableRest = declare([Rest, SimpleQuery, Trackable]);
        var store = new TrackableRest({target: '/api/manufacturers', useRangeHeaders: true, idProperty: 'id'});
        var grid = new (declare([OnDemandGrid, Selection, Editor]))({
            collection: store,
            className: "dgrid-autoheight",
            sort: "name",
            columns: {
                id: {
                    label: core.id
                },
                name: {
                    label: core.manufacturer,
                    renderCell: function (object, value, td) {
                        put(td, "pre.name", object.name);
                        libGrid.renderContacts(object, object, td);
                    }
                },
                brands: {
                    label: core.brands,
                    formatter: function (data, object) {
                        var b, nameList = [], html = "";
                        if( data !== "" ) {
                            for( b in data ) {
                                nameList.push(data[b].name);
                            }
                            if( nameList.length > 0 ) {
                                for( n = 0; n < nameList.length; n++ ) {
                                    html += '<a class="brand link" href="/admin/asset/manufacturer/' + object.name + '/brand/' + nameList[n] + '">' + nameList[n] + '</a><br>';
                                }
                            }
                        }
                        return html;
                    }
                },
                active: {
                    label: core.active,
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
                    rowElement.className += ' deleted';
                }
                return rowElement;
            },
            selectionMode: "none"
        }, 'manufacturer-grid');
        grid.startup();
        grid.collection.track();

        grid.on(".dgrid-row:click", function (event) {
            var checkBoxes = ["active", "remove"];
            var row = grid.row(event);
            var cell = grid.cell(event);
            var field = cell.column.field;
            var id = row.data.id;
            if( field !== 'brands' ) {
                if( checkBoxes.indexOf(field) === -1 ) {
                    if( typeof grid.selection[0] !== "undefined" ) {
                        grid.clearSelection();
                    }
                    grid.select(row);
                    grid.collection.get(id).then(function (manufacturer) {
                        manufacturerId = manufacturer.id;
                        action = "view";
                        nameInput.set("value", manufacturer.name);
                        commentInput.set("value", manufacturer.comment);
                        activeCheckBox.set("checked", manufacturer.active === true);
                        contact.setData(manufacturer.contacts);
                        brands.setData(manufacturer.brands);
                        manufacturerViewDialog.show();
                    }, lib.xhrError);
                }
            }
        });

        grid.on('.field-active:dgrid-datachange', function (event) {
            var row = grid.row(event);
            var cell = grid.cell(event);
            var field = cell.column.field;
            var name = row.data.name;
            var value = event.value;
            switch( field ) {
                case "active":
                    xhr("/api/manufacturers/" + name, {
                        method: "PATCH",
                        handleAs: "json",
                        headers: {'Content-Type': 'application/json'},
                        data: JSON.stringify({"field": field,
                            "value": value})
                    }).then(function (data) {
                    }, lib.xhrError);
                    break;
            }
        });

        var cbAll = new CheckBox({}, "cb-all");
        cbAll.startup();
        cbAll.on("click", function (event) {
            var state = this.checked;
            query(".dgrid-row .remove-cb", "manufacturer-grid").forEach(function (node) {
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

        on(dom.byId('manufacturer-grid-filter-form'), 'submit', function (event) {
            event.preventDefault();
            grid.set('collection', store.filter({
                // Pass a RegExp to Memory's filter method
                // Note: this code does not go out of its way to escape
                // characters that have special meaning in RegExps
                match: new RegExp(filterInput.get("value").replace(/\W/, ''), 'i')
            }));
        });

        contact = xcontact.run('manufacturer_contacts');
        brands.run('manufacturer');

        lib.pageReady();
    }
    return {
        run: run
    }
});