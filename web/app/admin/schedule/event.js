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
    "dgrid/Selection",
    'dgrid/Editor',
    'put-selector/put',
    "app/common/person",
    "app/admin/schedule/contracts",
    "app/admin/schedule/trailers",
    "app/admin/schedule/time_spans",
    "app/lib/common",
    "app/lib/grid",
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/schedule",
    "dojo/domReady!"
], function (declare, dom, domConstruct, on, xhr, aspect, query,
        registry, Form, TextBox, DateTextBox, ValidationTextBox, CheckBox, SimpleTextarea, FilteringSelect, JsonRest,
        Button, Dialog, TabContainer, ContentPane,
        Rest, SimpleQuery, Trackable, OnDemandGrid, Selection, Editor, put,
        xperson, contracts, trailers, timeSpans,
        lib, libGrid, core, schedule) {
    //"use strict";
    function run() {
        var action = null, d, person;

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
            style: "height: 525px; width: 860px;"
        }, "event-view-tabs");

        var detailsContentPane = new ContentPane({
            title: core.details},
        "event-view-details-tab"
                );
        tabContainer.addChild(detailsContentPane);

        var equipmentContentPane = new ContentPane({
            title: core.equipment},
        "event-view-equipment-tab"
                );
        tabContainer.addChild(equipmentContentPane);

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
            title: schedule.times},
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
            person.setData(null);
            contracts.setData(null);
            trailers.setData(null);
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
            var beforeNameFilter, filter, st, en;
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
                    "contacts": person.getData(),
                    "client": parseInt(clientFilteringSelect.get("value")),
                    "venue": parseInt(venueFilteringSelect.get("value")),
                    "client_text": clientFilteringSelect.get("displayedValue"),
                    "venue_text": venueFilteringSelect.get("displayedValue"),
                    "description": descriptionInput.get("value")
                };
                if( action === "view" ) {
                    grid.collection.put(data).then(function (data) {
                        eventViewDialog.hide();
                    }, lib.xhrError);
                } else {
                    filter = new store.Filter();
                    beforeNameFilter = filter.gt('name', data.name);
                    store.filter(beforeNameFilter).sort('name').fetchRange({start: 0, end: 1}).then(function (results) {
                        var beforeId;
                        beforeId = (results.length > 0) ? results[0].id : null;
                        grid.collection.add(data, {"beforeId": beforeId}).then(function (data) {
                            eventViewDialog.hide();
                            store.fetch();
                            grid.refresh();
                        }, lib.xhrError);
                    });
                }
            } else {
                lib.textError(core.invalid_form)
            }
        });

        var filterInput = new TextBox({placeHolder: core.filter}, "event-filter-input");
        filterInput.startup();

        var TrackableRest = declare([Rest, SimpleQuery, Trackable]);
        var store = new TrackableRest({target: '/api/events', useRangeHeaders: true, idProperty: 'id'});
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
                client_text: {
                    label: core.client
                },
                venue_text: {
                    label: core.venue
                },
                dates: {
                    label: core.dates,
                    formatter: function (data, object) {
                        var html = "", datesList = "", st = "", en = "";
                        if( object.start !== null ) {
                            st = object.start;
                        }
                        if( object.end !== null ) {
                            en = object.end;
                        }
                        datesList = en + "-" + st;
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
                    var r;
                    action = "view";
                    eventId = event.id;
                    nameInput.set("value", event.name);
                    startInput.set('value', event.start);
                    endInput.set('value', event.end);
                    tentativeCheckBox.set("checked", event.tentative === true);
                    billableCheckBox.set("checked", event.billable === true);
                    canceledCheckBox.set("checked", event.canceled === true);
                    clientFilteringSelect.set('displayedValue', event.client_text);
                    venueFilteringSelect.set('displayedValue', event.venue_text);
                    descriptionInput.set("value", event.comment);
                    person.setData(event.contacts);
                    eventViewDialog.show();
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

        query('#schedule-grid-control-dialog [type="checkbox"]').forEach(function (node) {
            var dijit;
            dijit = new CheckBox({}, node.id);
            dijit.startup();
        });

        var scheduleGridControlDialog = new Dialog({
            title: core.view,
            style: "height:700px;width:700px"
        }, "schedule-grid-control-dialog");
        scheduleGridControlDialog.startup();

        var showGridControlsBtn = new Button({
            label: core.grid_controls,
            "class": "right"
        }, "show-grid-controls-btn");
        showGridControlsBtn.startup();
        showGridControlsBtn.on("click", function (event) {
            scheduleGridControlDialog.show();
        });

        person = xperson.run('event_contacts');
        contracts.run('event_contracts');
        trailers.run('event_trailers');
        timeSpans.run('event_time_spans');
        lib.pageReady();
    }
    return {
        run: run
    }
});
