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
    "dstore/Rest",
    "dstore/SimpleQuery",
    "dstore/Trackable",
    "dgrid/OnDemandGrid",
    "dgrid/Selection",
    "dgrid/Editor",
    "put-selector/put",
    "app/common/contact",
    "app/admin/asset/carrier_service",
    "app/lib/common",
    "app/lib/grid",
    "dojo/i18n!app/nls/core",
    "dojo/domReady!"
], function (declare, dom, domConstruct, on, xhr, aspect, query,
        registry, Form, TextBox, ValidationTextBox, CheckBox, SimpleTextarea, Button, Dialog, TabContainer, ContentPane,
        Rest, SimpleQuery, Trackable, OnDemandGrid, Selection, Editor, put,
        xcontact, carrierService, lib, libGrid, core) {
    // "use strict";
    function run() {
        var action = null;
        var contact;

        var carrierId;

        var carrierViewDialog = new Dialog({
            title: core.view,
            style: "width: 800px; height: 800px;"
        }, "carrier-view-dialog");
        carrierViewDialog.startup();
        carrierViewDialog.on("cancel", function (event) {
            grid.clearSelection();
        });

        var tabContainer = new TabContainer({
            style: "width: 780px; height: 600px"
        }, "carrier-view-tabs");

        var contactsContentPane = new ContentPane({
            title: core.contacts},
        "carrier-view-contacts-tab"
                );
        tabContainer.addChild(contactsContentPane);

        var accountContentPane = new ContentPane({
            title: core.account},
        "carrier-view-account-tab"
                );
        tabContainer.addChild(accountContentPane);

        var servicesContentPane = new ContentPane({
            title: core.services},
        "carrier-view-services-tab"
                );
        tabContainer.addChild(servicesContentPane);

        var historyContentPane = new ContentPane({
            title: core.history},
        "carrier-view-history-tab"
                );
        tabContainer.addChild(historyContentPane);
        tabContainer.startup();

        var newBtn = new Button({
            label: core["new"]
        }, "carrier-new-btn");
        newBtn.startup();
        newBtn.on("click", function (event) {
            carrierId = null;
            nameInput.set("value", "");
            contact.setData(null);
            activeCheckBox.set("checked", true);
            carrierViewDialog.set("title", core["new"]).show();
            action = "new";
        });

        var removeBtn = new Button({
            label: core.remove
        }, "carrier-remove-btn");
        removeBtn.startup();
        removeBtn.on("click", function (event) {
            var markedForDeletion = query(".dgrid-row .remove-cb input:checked", "carrier-grid");
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
            pattern: "[A-Za-z\.\,\ \'-]{2,64}"
        }, "carrier_name");
        nameInput.startup();

        var activeCheckBox = new CheckBox({}, "carrier_active");
        activeCheckBox.startup();

        var commentInput = new SimpleTextarea({
            placeholder: core.comment,
            trim: true,
            required: false
        }, "carrier_comment");
        commentInput.startup();

        var accountInformationInput = new SimpleTextarea({
            placeholder: core.account_information,
            trim: true,
            required: false,
            "class": "account-information"
        }, "carrier_account_information");
        accountInformationInput.startup();

        var trackingUrlInput = new ValidationTextBox({
            trim: true,
            placeholder: "https://www.example.com/tracking/__ID__",
            pattern: "https?://[A-Za-z0-9:/\., '_%#@&+?-]{2,64}"
        }, "carrier_tracking_url");
        trackingUrlInput.startup();

        var carrierForm = new Form({}, '[name="carrier"]');
        carrierForm.startup();

        var saveBtn = new Button({
            label: core.save
        }, "carrier-save-btn");
        saveBtn.startup();
        saveBtn.on("click", function (event) {
            if( carrierForm.validate() ) {
                var data = {
                    "id": carrierId,
                    "name": nameInput.get("value"),
                    "contacts": contact.getData(),
                    "services": carrierService.getData(),
                    "active": activeCheckBox.get("checked"),
                    "comment": commentInput.get("value"),
                    "account_information": accountInformationInput.get("value"),
                    "tracking_url": trackingUrlInput.get("value")
                };
                grid.collection.put(data).then(function (data) {
                    carrierViewDialog.hide();
                    store.fetch();
                    grid.refresh();
                }, lib.xhrError);
            } else {
                lib.textError(core.invalid_form);
            }
        });

        var filterInput = new TextBox({placeHolder: core.filter}, "carrier-filter-input");
        filterInput.startup();

        var TrackableRest = declare([Rest, SimpleQuery, Trackable]);
        var store = new TrackableRest({target: "/api/carriers", useRangeHeaders: true, idProperty: "id"});
        var grid = new (declare([OnDemandGrid, Selection, Editor]))({
            collection: store,
            maxRowsPerPage: 25,
            columns: {
                id: {
                    label: core.id
                },
                name: {
                    label: core.carrier,
                    renderCell: function (object, value, td) {
                        put(td, "pre.name", object.name);
                        libGrid.renderContacts(object, object, td);
                    }
                },
                accountInformation: {
                    label: core.account_information,
                    sortable: false,
                    renderCell: function (object, value, td) {
                        put(td, "pre", object.account_information);
                    }
                },
                comment: {
                    label: core.comment
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
                    rowElement.className += " deleted";
                }
                return rowElement;
            },
            selectionMode: "none"
        }, "carrier-grid");
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
                grid.collection.get(id).then(function (carrier) {
                    action = "view";
                    carrierId = carrier.id;
                    carrierViewDialog.show();
                    nameInput.set("value", carrier.name);
                    activeCheckBox.set("checked", carrier.active === true);
                    contact.setData(carrier.contacts);
                    carrierService.setData(carrier.services);
                    commentInput.set("value", carrier.comment);
                    accountInformationInput.set("value", carrier.account_information);
                    trackingUrlInput.set("value", carrier.tracking_url);

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
                    xhr("/api/carriers/" + name, {
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
            query(".dgrid-row .remove-cb", "carrier-grid").forEach(function (node) {
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

        on(dom.byId("carrier-grid-filter-form"), "submit", function (event) {
            event.preventDefault();
            grid.set("collection", store.filter({
                // Pass a RegExp to Memory's filter method
                // Note: this code does not go out of its way to escape
                // characters that have special meaning in RegExps
                match: new RegExp(filterInput.get("value").replace(/\W/, ""), "i")
            }));
        });

        contact = xcontact.run("carrier_contacts");
        carrierService.run();

        lib.pageReady();
    }
    return {
        run: run
    };
});