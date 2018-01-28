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
    "dijit/form/DateTextBox",
    "dijit/form/ValidationTextBox",
    "dijit/form/CheckBox",
    "dijit/form/SimpleTextarea",
    "dijit/form/FilteringSelect",
    'dojo/store/JsonRest',
    "dijit/form/Button",
    "dijit/Dialog",
    "dijit/layout/TabContainer",
    "dijit/layout/ContentPane",
    'dstore/Rest',
    'dstore/SimpleQuery',
    'dstore/Trackable',
    'dgrid/OnDemandGrid',
    "dgrid/extensions/ColumnHider",
    "dgrid/Selection",
    'dgrid/Editor',
    'put-selector/put',
    "app/common/contact",
    "app/admin/schedule/contracts",
    "app/admin/schedule/trailers",
    "app/admin/schedule/category_quantities",
    "app/admin/schedule/time_spans",
    "app/admin/schedule/event_items",
    "app/admin/schedule/transfers",
    "app/lib/common",
    "app/lib/grid",
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/schedule",
    "dojo/domReady!"
], function (declare, dom, domConstruct, on, xhr, aspect, query,
        registry, Form, TextBox, DateTextBox, ValidationTextBox, CheckBox, SimpleTextarea, FilteringSelect, JsonRest,
        Button, Dialog, TabContainer, ContentPane,
        Rest, SimpleQuery, Trackable, OnDemandGrid, ColumnHider, Selection, Editor, put,
        xcontact, contracts, trailers, categoryQuantities, timeSpans, eventItems, transfers,
        lib, libGrid, core, schedule) {
    //"use strict";
    function run() {
        var action = null, d, contact;

        var eventId;

        var eventViewDialog = new Dialog({
            title: core.view,
            style: "height:700px;width:1000px"
        }, "event-view-dialog");
        eventViewDialog.startup();
        eventViewDialog.on("cancel", function (event) {
            grid.clearSelection();
        });

        var tabContainer = new TabContainer({
            style: "height: 525px; width: 900px;"
        }, "event-view-tabs");

        var detailsContentPane = new ContentPane({
            title: core.details},
        "event-view-details-tab"
                );
        tabContainer.addChild(detailsContentPane);

        var equipmentContentPane = new ContentPane({
            title: core.contract + " " + core.equipment},
        "event-view-contract-equipment-tab"
                );
        tabContainer.addChild(equipmentContentPane);

        var rentalEquipmentContentPane = new ContentPane({
            title: schedule.rental + " " + core.equipment},
        "event-view-rental-equipment-tab"
                );
        tabContainer.addChild(rentalEquipmentContentPane);

        var transfersContentPane = new ContentPane({
            title: core.transfers },
        "event-view-transfers-tab"
                );
        tabContainer.addChild(transfersContentPane);

        var contactsContentPane = new ContentPane({
            title: core.contacts},
        "event-view-contacts-tab"
                );
        tabContainer.addChild(contactsContentPane);

        var venueContentPane = new ContentPane({
            title: core.venue},
        "event-view-venue-tab"
                );
        tabContainer.addChild(venueContentPane);

        var timesContentPane = new ContentPane({
            title: schedule.schedule},
        "event-view-times-tab"
                );
        tabContainer.addChild(timesContentPane);

        var staffContentPane = new ContentPane({
            title: schedule.staff},
        "event-view-staff-tab"
                );
        tabContainer.addChild(staffContentPane);

        var historyContentPane = new ContentPane({
            title: core.history},
        "event-view-history-tab"
                );
        tabContainer.addChild(historyContentPane);
        tabContainer.startup();

        var newBtn = new Button({
            label: core["new"]
        }, 'event-new-btn');
        newBtn.startup();
        newBtn.on("click", function (event) {
            eventId = null;
            nameInput.set("value", "");
            startInput.set('value', null);
            endInput.set('value', null);
            tentativeCheckBox.set("checked", false);
            billableCheckBox.set("checked", true);
            canceledCheckBox.set("checked", false);
            clientFilteringSelect.set('displayedValue', "");
            descriptionInput.set("value", "");
            contact.setData(null);
            contracts.setData(null);
            trailers.setData(null);
            categoryQuantities.setData(null);
            timeSpans.setData(null);
            eventItems.setData(null);
            document.getElementById("full-equipment-link").classList.add("hidden");
            eventViewDialog.set("title", core["new"]).show();
            action = "new";
        });

        var removeBtn = new Button({
            label: core.remove
        }, 'event-remove-btn');
        removeBtn.startup();
        removeBtn.on("click", function (event) {
            var markedForDeletion = query(".dgrid-row .remove-cb input:checked", "event-grid");
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
        }, "event_name");
        nameInput.startup();

        d = document.getElementById("event_start").value;
        var startInput = new DateTextBox({
            placeholder: core.start,
            trim: true,
            required: false,
            name: "event[start]",
            value: (d === "") ? null : d
        }, "event_start");
        startInput.startup();

        d = document.getElementById("event_end").value;
        var endInput = new DateTextBox({
            placeholder: core.end,
            trim: true,
            required: false,
            name: "event[end]",
            value: (d === "") ? null : d
        }, "event_end");
        endInput.startup();

        var tentativeCheckBox = new CheckBox({}, "event_tentative");
        tentativeCheckBox.startup();
        var billableCheckBox = new CheckBox({}, "event_billable");
        billableCheckBox.startup();
        var canceledCheckBox = new CheckBox({}, "event_canceled");
        canceledCheckBox.startup();

        var clientStore = new JsonRest({
            target: '/api/store/clients',
            useRangeHeaders: false,
            idProperty: 'id'});
        var clientFilteringSelect = new FilteringSelect({
            store: clientStore,
            labelAttr: "name",
            searchAttr: "name",
            pageSize: 25,
            placeholder: core.client
        }, "event_client");
        clientFilteringSelect.startup();

        var venueStore = new JsonRest({
            target: '/api/store/venues',
            useRangeHeaders: false,
            idProperty: 'id'});
        var venueFilteringSelect = new FilteringSelect({
            store: venueStore,
            labelAttr: "name",
            searchAttr: "name",
            pageSize: 25,
            placeholder: core.venue
        }, "event_venue");
        venueFilteringSelect.startup();
        venueFilteringSelect.on("change", function (evt) {
            var id = parseInt(this.id.replace(/\D/g, ''));
            var item = this.get('item');
            document.getElementById('venue-equipment-link').href = '/admin/venue/' + item.id + '/equipment';
            document.getElementById('venue-address').textContent = item.address.address;
            document.getElementById('venue-comment').textContent = item.comment;
            document.getElementById('venue-directions').textContent = item.directions;
            document.getElementById('venue-parking').textContent = item.parking;
        });

        var descriptionInput = new SimpleTextarea({
            placeholder: core.description,
            trim: true,
            required: false
        }, "event_description");
        descriptionInput.startup();

        var eventForm = new Form({}, '[name="event"]');
        eventForm.startup();

        var saveBtn = new Button({
            label: core.save
        }, 'event-save-btn');
        saveBtn.startup();
        saveBtn.on("click", function (event) {
            var st, en;
            if( eventForm.validate() ) {
                st = startInput.get('value');
                en = endInput.get('value');
                var data = {
                    "id": eventId,
                    "name": nameInput.get("value"),
                    "start": st === null ? "" : st,
                    "end": en === null ? "" : en,
                    "tentative": tentativeCheckBox.get("checked"),
                    "billable": billableCheckBox.get("checked"),
                    "canceled": canceledCheckBox.get("checked"),
                    "contacts": contact.getData(),
                    "contracts": contracts.getData(),
                    "client": parseInt(clientFilteringSelect.get("value")),
                    "venue": parseInt(venueFilteringSelect.get("value")),
                    "client_text": clientFilteringSelect.get("displayedValue"),
                    "venue_text": venueFilteringSelect.get("displayedValue"),
                    "time_spans": timeSpans.getData(),
                    "items": eventItems.getData(),
                    "trailers": trailers.getData(),
                    "category_quantities": categoryQuantities.getData(),
                    "trailer_text": [contracts.getTrailerText(), trailers.getText()].join(', '),
                    "description": descriptionInput.get("value")
                };
                grid.collection.put(data).then(function (data) {
                    eventViewDialog.hide();
                    store.fetch();
                    grid.refresh();
                }, lib.xhrError);
            } else {
                lib.textError(core.invalid_form)
            }
        });

        var filterInput = new TextBox({placeHolder: core.filter}, "event-filter-input");
        filterInput.startup();

        var TrackableRest = declare([Rest, SimpleQuery, Trackable]);
        var store = new TrackableRest({target: '/api/events', useRangeHeaders: true, idProperty: 'id'});
        var grid = new (declare([OnDemandGrid, Selection, Editor, ColumnHider]))({
            collection: store,
            className: "dgrid-autoheight",
            columns: {
                id: {
                    label: core.id
                },
                name: {
                    label: core.name
                },
                client_text: {
                    label: core.client
                },
                venue_text: {
                    label: core.venue
                },
                trailer_text: {
                    label: core.trailers
                },
                dates: {
                    label: core.dates,
                    formatter: function (data, object) {
                        var html = "", datesList = "", st = "", en = "", ts = new Date();
                        if( object.start !== null ) {
                            st = object.start;
                            if( st instanceof Date ) {
                                st = lib.formatDate(st, false);
                            }
                        }
                        if( object.end !== null ) {
                            en = object.end;
                            if( en instanceof Date ) {
                                en = lib.formatDate(en, false);
                            }
                        }
                        datesList = st + "-<br>" + en;
                        html = '<span class="date-span">' + datesList + '</span><br>';
                        return html;
                    }
                },
                tentative: {
                    label: schedule.tentative,
                    editor: CheckBox,
                    editOn: "click",
                    sortable: false,
                    renderCell: libGrid.renderGridCheckbox
                },
                billable: {
                    label: schedule.billable,
                    editor: CheckBox,
                    editOn: "click",
                    sortable: false,
                    renderCell: libGrid.renderGridCheckbox
                },
                canceled: {
                    label: schedule.canceled,
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
        }, 'event-grid');
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
                grid.collection.get(id).then(function (event) {
                    var r, equipmentLink, timestamp = new Date();
                    action = "view";
                    eventId = event.id;
                    nameInput.set("value", event.name);
                    if( event.start !== null ) {
                        timestamp.setTime(event.start.timestamp * 1000);
                        startInput.set('value', timestamp);
                    } else {
                        startInput.set('value', null);
                    }
                    if( event.end !== null ) {
                        timestamp.setTime(event.end.timestamp * 1000);
                        endInput.set('value', timestamp);
                    } else {
                        endInput.set('value', null);
                    }
                    tentativeCheckBox.set("checked", event.tentative === true);
                    billableCheckBox.set("checked", event.billable === true);
                    canceledCheckBox.set("checked", event.canceled === true);
                    clientFilteringSelect.set('displayedValue', (event.client !== null) ? event.client.name : null);
                    venueFilteringSelect.set('displayedValue', (event.venue !== null) ? event.venue.name : null);
                    descriptionInput.set("value", event.description);
                    equipmentLink = document.getElementById("full-equipment-link");
                    equipmentLink.href = equipmentLink.href.replace(/(__ID__|\d+)/, event.id);
                    equipmentLink.classList.remove("hidden");
                    trailers.setData(event.trailers);
                    categoryQuantities.setData(event.categoryQuantities);
                    contracts.setData(event.contracts);
                    timeSpans.setData(event.timeSpans);
                    eventItems.setData(event.items);
                    transfers.setData(event.id);
                    eventViewDialog.show();
                    contact.setData(event.contacts);
                }, lib.xhrError);
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
                    xhr("/api/events/" + name, {
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
            query(".dgrid-row .remove-cb", "event-grid").forEach(function (node) {
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

        on(dom.byId('event-grid-filter-form'), 'submit', function (event) {
            event.preventDefault();
            grid.set('collection', store.filter({
                // Pass a RegExp to Memory's filter method
                // Note: this code does not go out of its way to escape
                // characters that have special meaning in RegExps
                match: new RegExp(filterInput.get("value").replace(/\W/, ''), 'i')
            }));
        });

        contact = xcontact.run('event_contacts');
        contracts.run('event_contracts');
        trailers.run('event_trailers');
        categoryQuantities.run();
        timeSpans.run('event_time_spans');
        eventItems.run('event_items');
        transfers.run();
        lib.pageReady();
    }
    return {
        run: run
    }
});
