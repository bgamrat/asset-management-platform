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
    "app/common/person",
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
        person, brands, lib, libGrid, core) {
    //"use strict";
    function run() {
        var action = null;
        var brandName = null;
        var manufacturerName = null;
        var manufacturerId = null;

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
        tabContainer.startup();

        var newBtn = new Button({
            label: core["new"]
        }, 'manufacturer-new-btn');
        newBtn.startup();
        newBtn.on("click", function (event) {
            nameInput.set("value", "");
            activeCheckBox.set("checked", true);
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
            var beforeId, beforeIdFilter, filter;
            if( manufacturerForm.validate() ) {
                var data = {
                    "id": manufacturerId,
                    "name": nameInput.get("value"),
                    "active": activeCheckBox.get("checked"),
                    "comment": commentInput.get("value"),
                    "contacts": person.getData(),
                    "brands": brands.getData()
                };
                if( action === "view" ) {
                    grid.collection.put(data).then(function (data) {
                        manufacturerViewDialog.hide();
                    }, lib.xhrError);
                } else {
                    filter = new store.Filter();
                    beforeIdFilter = filter.gt('name', data.name);
                    store.filter(beforeIdFilter).sort('name').fetchRange({start: 0, end: 1}).then(function (results) {
                        beforeId = (results.length > 0) ? results[0].name : null;
                        grid.collection.add(data, {"beforeId": beforeId}).then(function (data) {
                            manufacturerViewDialog.hide();
                        }, lib.xhrError);
                    });
                }
            } else {
                lib.textError(core.invalid_form)
            }
        });

        var manufacturerBrandForm = new Form({}, '[name="brands"]');
        manufacturerBrandForm.startup();

        var filterInput = new TextBox({placeHolder: core.filter}, "manufacturer-filter-input");
        filterInput.startup();

        var TrackableRest = declare([Rest, SimpleQuery, Trackable]);
        var store = new TrackableRest({target: '/api/manufacturers', useRangeHeaders: true, idProperty: 'name'});
        var grid = new (declare([OnDemandGrid, Selection, Editor]))({
            collection: store,
            className: "dgrid-autoheight",
            columns: {
                id: {
                    label: core.id
                },
                name: {
                    label: core.manufacturer,
                    renderCell: function (object, value, td) {
                        var i, j, k, content, contactText = [object.name], addresses, address_lines, phoneNumber;
                        address_lines = ['street1', 'street2', 'city', 'stateProvince', 'postalCode', 'country'];
                        if( typeof object.contacts !== "undefined" ) {
                            for( i = 0; i < object.contacts.length; i++ ) {
                                contact = object.contacts[i];
                                contactText.push("");
                                contactText.push("\t" + contact.fullName);
                                if( typeof contact.phoneNumbers !== "undefined" ) {
                                    for( j = 0; j < contact.phoneNumbers.length; j++ ) {
                                        phoneNumber = contact.phoneNumbers[j];
                                        contactText.push("\t" + phoneNumber.phoneNumber);
                                    }
                                }
                                if( typeof object.contacts[i].addresses !== "undefined" ) {
                                    addresses = contact.addresses;
                                    for( j = 0; j < addresses; j++ ) {
                                        contactText.push("");
                                        address = contact.addresses[j];
                                        for( k = 0; k < address_lines.length; k++ ) {
                                            if( address[address_lines[k]] !== "" ) {
                                                contactText.push("\t" + address[address_lines[k]]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if( contactText.length > 0 ) {
                            content = contactText.join("\n");
                            put(td, "pre", content);
                        }
                    }
                },
                brands: {
                    label: core.brands,
                    formatter: function (data, object) {
                        var b, nameList = [], html = "";
                        for( b in data ) {
                            nameList.push(data[b].name);
                        }
                        if( nameList.length > 0 ) {
                            for( n = 0; n < nameList.length; n++ ) {
                                html += '<a class="brand link" href="/admin/asset/manufacturer/' + object.name + '/brand/' + nameList[n] + '">' + nameList[n] + '</a><br>';
                            }
                        }
                        return html;
                    }
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
        }, 'manufacturer-grid');
        grid.startup();
        grid.collection.track();

        grid.on(".dgrid-row:click", function (event) {
            var checkBoxes = ["active", "remove"];
            var row = grid.row(event);
            var cell = grid.cell(event);
            var field = cell.column.field;
            var name = row.data.name;
            if( field !== 'brands' ) {
                if( checkBoxes.indexOf(field) === -1 ) {
                    if( typeof grid.selection[0] !== "undefined" ) {
                        grid.clearSelection();
                    }
                    grid.select(row);
                    grid.collection.get(name).then(function (manufacturer) {
                        manufacturerId = manufacturer.id;
                        action = "view";
                        nameInput.set("value", manufacturer.name);
                        activeCheckBox.set("checked", manufacturer.active === true);
                        person.setData(manufacturer.contacts);
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

        person.run('manufacturer');
        brands.run('manufacturer');

        lib.pageReady();
    }
    return {
        run: run
    }
});