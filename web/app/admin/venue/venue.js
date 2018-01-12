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
    "app/common/address",
    "app/lib/common",
    "app/lib/grid",
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/venue",
    "dojo/domReady!"
], function (declare, dom, domConstruct, on, xhr, aspect, query,
        registry, Form, TextBox, ValidationTextBox, CheckBox, SimpleTextarea, Button, Dialog, TabContainer, ContentPane,
        Rest, SimpleQuery, Trackable, OnDemandGrid, Selection, Editor, put,
        xcontact, xaddress, lib, libGrid, core, venue) {
    //"use strict";
    function run(id) {
        var action = null;
        var contact, address;

        var venueId;

        var venueViewDialog = new Dialog({
            title: core.view
        }, "venue-view-dialog");
        venueViewDialog.startup();
        venueViewDialog.on("cancel", function (event) {
            grid.clearSelection();
        });

        var tabContainer = new TabContainer({
            style: "height: 525px; width: 100%;"
        }, "venue-view-tabs");

        var addressContentPane = new ContentPane({
            title: core.address},
        "venue-view-address-tab"
                );
        tabContainer.addChild(addressContentPane);

        var contactsContentPane = new ContentPane({
            title: core.contacts},
        "venue-view-contacts-tab"
                );
        tabContainer.addChild(contactsContentPane);

        var historyContentPane = new ContentPane({
            title: core.history},
        "venue-view-history-tab"
                );
        tabContainer.addChild(historyContentPane);
        tabContainer.startup();

        var newBtn = new Button({
            label: core["new"]
        }, 'venue-new-btn');
        newBtn.startup();
        newBtn.on("click", function (event) {
            venueId = null;
            nameInput.set("value", "");
            activeCheckBox.set("checked", true);
            contact.setData(null);
            venueViewDialog.set("title", core["new"]).show();
            action = "new";
        });

        var removeBtn = new Button({
            label: core.remove
        }, 'venue-remove-btn');
        removeBtn.startup();
        removeBtn.on("click", function (event) {
            var markedForDeletion = query(".dgrid-row .remove-cb input:checked", "venue-grid");
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
        }, "venue_name");
        nameInput.startup();

        var activeCheckBox = new CheckBox({}, "venue_active");
        activeCheckBox.startup();

        var commentInput = new SimpleTextarea({
            placeholder: core.comment,
            trim: true,
            required: false
        }, "venue_comment");
        commentInput.startup();

        var directionsInput = new SimpleTextarea({
            placeholder: venue.directions,
            trim: true,
            required: false
        }, "venue_directions");
        directionsInput.startup();

        var parkingInput = new SimpleTextarea({
            placeholder: venue.parking,
            trim: true,
            required: false
        }, "venue_parking");
        parkingInput.startup();

        var venueForm = new Form({}, '[name="venue"]');
        venueForm.startup();

        var saveBtn = new Button({
            label: core.save
        }, 'venue-save-btn');
        saveBtn.startup();
        saveBtn.on("click", function (event) {
            var beforeNameFilter, filter;
            if( venueForm.validate() ) {
                var data = {
                    "id": venueId,
                    "name": nameInput.get("value"),
                    "active": activeCheckBox.get("checked"),
                    "address": address.getData(),
                    "comment": commentInput.get("value"),
                    "directions": directionsInput.get("value"),
                    "parking": parkingInput.get("value"),
                    "contacts": contact.getData()
                };
                if( action === "view" ) {
                    grid.collection.put(data).then(function (data) {
                        venueViewDialog.hide();
                    }, lib.xhrError);
                } else {
                    filter = new store.Filter();
                    beforeNameFilter = filter.gt('name', data.name);
                    store.filter(beforeNameFilter).sort('name').fetchRange({start: 0, end: 1}).then(function (results) {
                        var beforeId;
                        beforeId = (results.length > 0) ? results[0].id : null;
                        grid.collection.add(data, {"beforeId": beforeId}).then(function (data) {
                            venueViewDialog.hide();
                            store.fetch();
                            grid.refresh();
                        }, lib.xhrError);
                    });
                }
            } else {
                lib.textError(core.invalid_form)
            }
        });

        var filterInput = new TextBox({placeHolder: core.filter}, "venue-filter-input");
        filterInput.startup();

        var TrackableRest = declare([Rest, SimpleQuery, Trackable]);
        var store = new TrackableRest({target: '/api/venues', useRangeHeaders: true, idProperty: 'id'});
        var grid = new (declare([OnDemandGrid, Selection, Editor]))({
            collection: store,
            className: "dgrid-autoheight",
            columns: {
                id: {
                    label: core.id
                },
                name: {
                    label: core.venue,
                    renderCell: function (object, value, td) {
                        put(td, "pre.name", object.name);
                        libGrid.renderContacts(object, object, td);
                    }
                },
                address: {
                    label: core.address,
                    renderCell: function (object, value, td) {
                        libGrid.renderAddress(object, value, td);
                    }
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
        }, 'venue-grid');
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
                grid.collection.get(id).then(display(venue), lib.xhrError);
            }
        });
        function display(venue) {
            var r;
            action = "view";
            venueId = venue.id;
            nameInput.set("value", venue.name);
            activeCheckBox.set("checked", venue.active === true);
            directionsInput.set("value", venue.directions);
            parkingInput.set("value", venue.parking);
            commentInput.set("value", venue.comment);
            contact.setData(venue.contacts);
            address.setData(venue.address);
            lib.showHistory(historyContentPane, venue.history);
            venueViewDialog.show();
        }
        grid.on('.field-active:dgrid-datachange, .field-locked:dgrid-datachange', function (event) {
            var row = grid.row(event);
            var cell = grid.cell(event);
            var field = cell.column.field;
            var name = row.data.name;
            var value = event.value;
            switch( field ) {
                case "active":
                case "locked":
                    xhr("/api/venues/" + name, {
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
            query(".dgrid-row .remove-cb", "venue-grid").forEach(function (node) {
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

        on(dom.byId('venue-grid-filter-form'), 'submit', function (event) {
            event.preventDefault();
            grid.set('collection', store.filter({
                // Pass a RegExp to Memory's filter method
                // Note: this code does not go out of its way to escape
                // characters that have special meaning in RegExps
                match: new RegExp(filterInput.get("value").replace(/\W/, ''), 'i')
            }));
        });

        lib.getAddressTypes();
        contact = xcontact.run('venue_contacts');
        address = xaddress.run('venue');

        lib.pageReady();

        if( typeof id !== "undefined" && id !== null ) {
            grid.collection.get(id).then(display, lib.xhrError);
        }
    }
    return {
        run: run
    }
});
