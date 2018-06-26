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
    "app/admin/asset/brand_select",
    "app/lib/common",
    "app/lib/grid",
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/asset",
    "dojo/domReady!"
], function (declare, dom, domConstruct, on, xhr, aspect, query,
        registry, Form, TextBox, ValidationTextBox, CheckBox, SimpleTextarea, Button, Dialog, TabContainer, ContentPane,
        Rest, SimpleQuery, Trackable, OnDemandGrid, Selection, Editor, put,
        xcontact, brandSelect, lib, libGrid, core, asset) {
    //"use strict";
    function run() {
        var action = null;
        var contact;

        var vendorId;

        var vendorViewDialog = new Dialog({
            title: core.view
        }, "vendor-view-dialog");
        vendorViewDialog.startup();
        vendorViewDialog.on("cancel", function (event) {
            grid.clearSelection();
        });

        var tabContainer = new TabContainer({
            style: "height: 525px; width: 100%;"
        }, "vendor-view-tabs");

        var contactsContentPane = new ContentPane({
            title: core.contacts},
        "vendor-view-contacts-tab"
                );
        tabContainer.addChild(contactsContentPane);

        var brandsContentPane = new ContentPane({
            title: core.brands},
        "vendor-view-brands-tab"
                );
        tabContainer.addChild(brandsContentPane);

        var serviceContentPane = new ContentPane({
            title: asset.service},
        "vendor-view-service-tab"
                );
        tabContainer.addChild(serviceContentPane);

        var historyContentPane = new ContentPane({
            title: core.history},
        "vendor-view-history-tab"
                );
        tabContainer.addChild(historyContentPane);
        tabContainer.startup();

        var newBtn = new Button({
            label: core["new"]
        }, 'vendor-new-btn');
        newBtn.startup();
        newBtn.on("click", function (event) {
            vendorId = null;
            contact.setData(null);
            nameInput.set("value", "");
            activeCheckBox.set("checked", true);
            vendorViewDialog.set("title", core["new"]).show();
            action = "new";
        });

        var removeBtn = new Button({
            label: core.remove
        }, 'vendor-remove-btn');
        removeBtn.startup();
        removeBtn.on("click", function (event) {
            var markedForDeletion = query(".dgrid-row .remove-cb input:checked", "vendor-grid");
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
        }, "vendor_name");
        nameInput.startup();

        var activeCheckBox = new CheckBox({}, "vendor_active");
        activeCheckBox.startup();

        var commentInput = new SimpleTextarea({
            placeholder: core.comment,
            trim: true,
            required: false
        }, "vendor_comment");
        commentInput.startup();

        var rmaRequiredCheckBox = new CheckBox({}, "vendor_rma_required");
        rmaRequiredCheckBox.startup();

        var serviceInstructionsInput = new SimpleTextarea({
            placeholder: asset.service_instructions,
            trim: true,
            required: false,
            "class": "service-instructions"
        }, "vendor_service_instructions");
        serviceInstructionsInput.startup();

        var vendorForm = new Form({}, '[name="vendor"]');
        vendorForm.startup();

        var saveBtn = new Button({
            label: core.save
        }, 'vendor-save-btn');
        saveBtn.startup();
        saveBtn.on("click", function (event) {
            if( vendorForm.validate() ) {
                var i, brandData = brandSelect.getData(), brandIds = [];
                for( i = 0; i < brandData.length; i++ ) {
                    brandIds.push(brandData[i].id);
                }
                var data = {
                    "id": vendorId,
                    "name": nameInput.get("value"),
                    "contacts": contact.getData(),
                    "brand_data": brandData,
                    "brands": brandIds,
                    "active": activeCheckBox.get("checked"),
                    "comment": commentInput.get("value"),
                    "rma_required": rmaRequiredCheckBox.get("checked"),
                    "service_instructions": serviceInstructionsInput.get("value")
                };
                grid.collection.put(data).then(function (data) {
                    vendorViewDialog.hide();
                    store.fetch();
                    grid.refresh();
                }, lib.xhrError);
            } else {
                lib.textError(core.invalid_form)
            }
        });

        var filterInput = new TextBox({placeHolder: core.filter}, "vendor-filter-input");
        filterInput.startup();

        var TrackableRest = declare([Rest, SimpleQuery, Trackable]);
        var store = new TrackableRest({target: '/api/vendors', useRangeHeaders: true, idProperty: 'id'});
        var grid = new (declare([OnDemandGrid, Selection, Editor]))({
            collection: store,
            className: "dgrid-autoheight",
            columns: {
                id: {
                    label: core.id
                },
                name: {
                    label: core.name
                },
                brand_data: {
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
        }, 'vendor-grid');
        grid.startup();
        grid.collection.track();

        function load(vendor) {
            var r;
            action = "view";
            vendorId = vendor.id;
            nameInput.set("value", vendor.name);
            brandSelect.setData(vendor.brandData);
            activeCheckBox.set("checked", vendor.active === true);
            contact.setData(vendor.contacts);
            commentInput.set("value", vendor.comment);
            rmaRequiredCheckBox.set("checked", vendor.rma_required === true);
            serviceInstructionsInput.set("value", vendor.service_instructions);
            vendorViewDialog.show();
        }

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
                grid.collection.get(id).then(load, lib.xhrError);
            }
        });

        grid.on('.field-active:dgrid-datachange, .field-locked:dgrid-datachange', function (event) {
            var row = grid.row(event);
            var cell = grid.cell(event);
            var field = cell.column.field;
            var name = row.data.name;
            var value = event.value;
            switch( field ) {
                case "active":
                case "locked":
                    xhr("/api/vendors/" + name, {
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
            query(".dgrid-row .remove-cb", "vendor-grid").forEach(function (node) {
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

        on(dom.byId('vendor-grid-filter-form'), 'submit', function (event) {
            event.preventDefault();
            grid.set('collection', store.filter({
                // Pass a RegExp to Memory's filter method
                // Note: this code does not go out of its way to escape
                // characters that have special meaning in RegExps
                match: new RegExp(filterInput.get("value").replace(/\W/, ''), 'i')
            }));
        });

        contact = xcontact.run('vendor_contacts');
        brandSelect.run('vendor');
        if( typeof loadVendorId !== "undefined" && loadVendorId !== null ) {
            grid.collection.get(loadVendorId).then(load, lib.xhrError);
        }
        lib.pageReady();
    }
    return {
        run: run
    }
});