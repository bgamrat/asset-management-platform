define([
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-class",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/request/xhr",
    "dojo/aspect",
    "dojo/query",
    "dojo/data/ObjectStore",
    "dojo/store/Memory",
    "dijit/registry",
    "dijit/form/Form",
    "dijit/form/CurrencyTextBox",
    "dijit/form/DateTextBox",
    "dijit/form/TextBox",
    "dijit/form/ValidationTextBox",
    "dijit/form/CheckBox",
    "dijit/form/RadioButton",
    "dijit/form/Select",
    "dijit/form/FilteringSelect",
    "dijit/form/SimpleTextarea",
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
    "app/admin/asset/transfer_items",
    "app/admin/common/bill_to",
    "app/admin/asset/location",
    "app/admin/asset/common",
    "app/lib/common",
    "app/lib/grid",
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/asset",
    "dojo/domReady!"
], function (declare, lang, dom, domAttr, domClass, domConstruct, on,
        xhr, aspect, query, ObjectStore, Memory,
        registry, Form, CurrencyTextBox, DateTextBox, TextBox, ValidationTextBox, CheckBox, RadioButton, Select, FilteringSelect, SimpleTextarea, Button,
        Dialog, TabContainer, ContentPane,
        JsonRest,
        Rest, SimpleQuery, Trackable, OnDemandGrid, Selection, Editor, put,
        transferItems, billTo, xlocation, assetCommon, lib, libGrid, core, asset) {
    //"use strict";
    function run() {

        var transferId = null;

        var action = null;

        var transferViewDialog = new Dialog({
            title: core.view,
            style: "width:900px"
        }, "transfer-view-dialog");
        transferViewDialog.startup();
        transferViewDialog.on("cancel", function (event) {
            grid.clearSelection();
        });

        var tabContainer = new TabContainer({
            style: "height: 200px; width: 100%;"
        }, "transfer-view-tabs");

        var itemsContentPane = new ContentPane({
            title: core.items},
        "transfer-view-items-tab"
                );
        tabContainer.addChild(itemsContentPane);

        var billToContentPane = new ContentPane({
            title: core.bill_to},
        "transfer-view-bill-to-tab"
                );
        tabContainer.addChild(billToContentPane);

        var historyContentPane = new ContentPane({
            title: core.history},
        "transfer-view-history-tab"
                );
        tabContainer.addChild(historyContentPane);
        tabContainer.startup();

        var newBtn = new Button({
            label: core["new"]
        }, 'transfer-new-btn');
        newBtn.startup();
        newBtn.on("click", function (event) {
            createdInput.set("value", null);
            updatedInput.set("value", null);
            statusSelect.set("value", "");
            transferItems.setData(null);
            fromFilteringSelect.set("value", "");
            sourceLocation.setData(null);
            toFilteringSelect.set("value", "");
            destinationLocation.setData(null);
            trackingNumberInput.set("value", "");
            costInput.set("value", null);
            instructionsInput.set("value", "");
            updatedInput.set("value", null);
            createdInput.set("value", null);
            transferViewDialog.set("title", core["new"]).show();
            action = "new";
        });

        var removeBtn = new Button({
            label: core.remove
        }, 'transfer-remove-btn');
        removeBtn.startup();
        removeBtn.on("click", function (event) {
            var markedForDeletion = query(".dgrid-row .remove-cb input:checked", "transfer-grid");
            if( markedForDeletion.length > 0 ) {
                lib.confirmAction(core.areyousure, function () {
                    markedForDeletion.forEach(function (node) {
                        var row = grid.row(node);
                        store.remove(row.data.id);
                    });
                });
            }
        });

        var createdInput = new TextBox({
            value: dom.byId("transfer_created").value,
            disabled: true
        }, "transfer_created");
        createdInput.startup();
        var updatedInput = new ValidationTextBox({
            value: dom.byId("transfer_updated").value,
            disabled: true
        }, "transfer_updated");
        updatedInput.startup();

        var transferStatusSelect = dom.byId('transfer_status');
        data = JSON.parse(domAttr.get(transferStatusSelect, "data-options"));
        // Convert the data to an array of objects
        storeData = [];
        for( d in data ) {
            storeData.push(data[d]);
        }
        memoryStore = new Memory({
            idProperty: "value",
            data: storeData});
        var statusStore = new ObjectStore({objectStore: memoryStore});
        var defaultStatusId = domAttr.get("transfer_status", "data-selected");
        var statusSelect = new Select({
            store: statusStore,
            placeholder: asset.status,
            required: true,
            "class": "status-select"
        }, "transfer_status");
        statusSelect.startup();
        statusSelect.set("value", defaultStatusId);

        var peopleStore = new JsonRest({
            target: '/api/store/people',
            useRangeHeaders: false,
            idProperty: 'id'});
        var fromFilteringSelect = new FilteringSelect({
            store: peopleStore,
            labelAttr: "name",
            searchAttr: "name",
            placeholder: core.person,
            pageSize: 25
        }, "transfer_from");
        fromFilteringSelect.startup();
        var sourceLocation = xlocation.run("source", "transfer");

        var toFilteringSelect = new FilteringSelect({
            store: peopleStore,
            labelAttr: "name",
            searchAttr: "name",
            placeholder: core.person,
            pageSize: 25
        }, "transfer_to");
        toFilteringSelect.startup();
        var destinationLocation = xlocation.run("destination", "transfer");

        carrierSelect = dom.byId('transfer_carrier');
        var data = JSON.parse(domAttr.get(carrierSelect, "data-options"));
        // Convert the data to an array of objects
        var storeData = [], d;
        for( d in data ) {
            storeData.push(data[d]);
        }
        var memoryStore = new Memory({
            idProperty: "value",
            data: storeData});
        var carrierStore = new ObjectStore({objectStore: memoryStore});
        var carrierSelect = new Select({
            store: carrierStore,
            placeholder: asset.carrier,
            required: true,
            "class": "carrier-select"
        }, "transfer_carrier");
        carrierSelect.startup();
        carrierSelect.on("change", function () {
            var carrierId = this.get('value');
            if( !isNaN(carrierId) ) {
                carrierServiceStore.target = carrierServiceStore.target.replace(/\d*$/, carrierId);
            }
        });

        var carrierServiceStore = new JsonRest({
            target: '/api/store/carrierservices?carrier=',
            useRangeHeaders: false,
            idProperty: 'id'});
        var carrierServiceSelect = new FilteringSelect({
            store: carrierServiceStore,
            labelAttr: "name",
            searchAttr: "name",
            placeholder: core.service,
            required: false,
            pageSize: 25
        }, "transfer_carrier_service");
        carrierServiceSelect.startup();

        var trackingNumberInput = new ValidationTextBox({
            trim: true,
            placeholder: asset.tracking_number,
            pattern: "[A-Za-z0-9\.\,\ \'-]{2,64}"
        }, "transfer_tracking_number");
        trackingNumberInput.startup();

        var costInput = new CurrencyTextBox({
            placeholder: core.cost,
            trim: true,
            required: false
        }, "transfer_cost");
        costInput.startup();

        var instructionsInput = new SimpleTextarea({
            placeholder: core.instructions,
            trim: true,
            required: false
        }, "transfer_instructions");
        instructionsInput.startup();

        var transferForm = new Form({}, '[name="transfer"]');
        transferForm.startup();

        var saveBtn = new Button({
            label: core.save
        }, 'transfer-save-btn');
        saveBtn.startup();
        saveBtn.on("click", function (event) {
            var beforeTransferTextFilter, filter, data, locationId, locationData, purchased;
            grid.clearSelection();
            if( transferForm.validate() ) {
                data = {
                    "id": transferId,
                    "status_text": statusSelect.get("displayedValue"),
                    "status": parseInt(statusSelect.get("value")),
                    "cost": parseFloat(costInput.get("value")),
                    "carrier": carrierSelect.get("value"),
                    "carrier_text": carrierSelect.get("displayedValue"),
                    "carrier_service": carrierServiceSelect.get("value"),
                    "tracking_number": trackingNumberInput.get("value"),
                    "instructions": instructionsInput.get("value"),
                    "items": transferItems.getData(),
                    "bill_to": billTo.getData(),
                    "from": fromFilteringSelect.get("value"),
                    "source_location": sourceLocation.getData(),
                    "source_location_text": sourceLocation.getText(),
                    "to": toFilteringSelect.get("value"),
                    "destination_location": destinationLocation.getData(),
                    "destination_location_text": destinationLocation.getText()
                };
                if( action === "view" ) {
                    grid.collection.put(data).then(function (data) {
                        transferViewDialog.hide();
                    }, lib.xhrError);
                } else {
                    filter = new store.Filter();
                    beforeTransferTextFilter = filter.gt('id', data.id);
                    store.filter(beforeTransferTextFilter).sort('id').fetchRange({start: 0, end: 1}).then(function (results) {
                        var beforeId;
                        beforeId = (results.length > 0) ? results[0].id : null;
                        grid.collection.add(data, {"beforeId": beforeId}).then(function (data) {
                            transferViewDialog.hide();
                            store.fetch();
                            grid.refresh();
                        }, lib.xhrError);
                    });
                }
            } else {
                lib.textError(core.invalid_form)
            }
        });

        var filterInput = new TextBox({placeHolder: core.filter}, "transfer-filter-input");
        filterInput.startup();

        var TrackableRest = declare([Rest, SimpleQuery, Trackable]);
        var store = new TrackableRest({target: '/api/transfers', useRangeHeaders: true, idProperty: 'id'});
        var grid = new (declare([OnDemandGrid, Selection, Editor]))({
            collection: store,
            className: "dgrid-autoheight",
            sort: "id",
            columns: {
                id: {
                    label: core.id
                },
                status_text: {
                    label: core.status
                },
                source_location_text: {
                    label: core.from,
                    formatter: function(item) {
                        return item.replace(/\n/g,"<br>");
                    }
                },
                destination_location_text: {
                    label: core.to,
                    formatter: function(item) {
                        return item.replace(/\n/g,"<br>");
                    }
                },
                carrier_text: {
                    label: core.carrier
                },
                tracking_number: {
                    label: core.tracking_number,
                    formatter: function (data, object) {
                        var html;
                        if (object.tracking_url !== null && object.tracking_url !== "") {
                            if( data !== null && data !== "" ) {
                                html = '<a href="' + object.tracking_url.replace("__ID__",data) + '" target="_blank">' + data + '</a>';
                            }
                        } else {
                            html = (typeof data !== "undefined" && data !== null) ? data : "";
                        }
                        return html;
                    }
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
        }, 'transfer-grid');
        grid.startup();
        grid.collection.track();

        grid.on(".dgrid-row:click", function (event) {
            var checkBoxes = ["remove"];
            var row = grid.row(event);
            var cell = grid.cell(event);
            var field = cell.column.field;
            var id = row.data.id;
            var timestamp;
            if( checkBoxes.indexOf(field) === -1 ) {
                if( typeof grid.selection[0] !== "undefined" ) {
                    grid.clearSelection();
                }
                grid.select(row);
                grid.collection.get(id).then(function (transfer) {
                    transferViewDialog.set('title', core.view + " " + transfer.id);
                    transferViewDialog.show();
                    action = "view";
                    transferId = transfer.id;
                    statusSelect.set("value", transfer.status.id);
                    costInput.set("value", transfer.cost);
                    transferItems.setData(transfer.items);
                    if( typeof transfer.carrier !== "undefined" && transfer.carrier !== null && typeof transfer.carrier.id !== "undefined" ) {
                        carrierServiceStore.target = carrierServiceStore.target.replace(/\d*$/, transfer.carrier.id);
                        carrierServiceSelect.set('displayedValue', transfer.carrierService.name);
                        carrierSelect.set("value", transfer.carrier.id);
                        carrierSelect.set("displayedValue", transfer.carrier_name);
                    } else {
                        carrierSelect.reset();
                    }
                    trackingNumberInput.set("value", transfer.trackingNumber);
                    instructionsInput.set("value", transfer.instructions);
                    billTo.setData(transfer.bill_to);
                    fromFilteringSelect.set("value", null);
                    if( transfer.from !== null && typeof transfer.from.fullName !== "undefined" ) {
                        fromFilteringSelect.set("displayedValue", transfer.from.fullName);
                    }
                    sourceLocation.setData(transfer.sourceLocation, transfer.sourceLocationText);
                    toFilteringSelect.set("value", null);
                    if( transfer.to !== null && typeof transfer.to.fullName !== "undefined" ) {
                        toFilteringSelect.set("displayedValue", transfer.to.fullName);
                    }
                    destinationLocation.setData(transfer.destinationLocation, transfer.destinationLocationText);
                    timestamp = new Date();
                    timestamp.setTime(transfer.updated.timestamp * 1000);
                    updatedInput.set('value', timestamp.toLocaleString());
                    timestamp.setTime(transfer.updated.timestamp * 1000);
                    createdInput.set("value", timestamp.toLocaleString());
                    lib.showHistory(historyContentPane, transfer["history"]);
                }, lib.xhrError);
            }
        });

        grid.on('.field-remove:dgrid-datachange', function (event) {
            var row = grid.row(event);
            var cell = grid.cell(event);
            var field = cell.column.field;
            var id = row.data.id;
            var value = event.value;
            switch( field ) {
                case "active":
                    xhr("/api/transfers/" + id, {
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
            query(".dgrid-row .remove-cb", "transfer-grid").forEach(function (node) {
                registry.findWidgets(node)[0].set("checked", state);
            });
        });

        aspect.before(grid, "removeRow", function (rowElement) {
            // Destroy the checkbox widgets
            var e, elements = [grid.cell(rowElement, "remove").element];
            var widget;
            for( e in elements ) {
                widget = (e.contents || e).widget;
                if( widget ) {
                    widget.destroyRecursive();
                }
            }
        });

        on(dom.byId('transfer-grid-filter-form'), 'submit', function (event) {
            event.preventDefault();
            grid.set('collection', store.filter({
                // Pass a RegExp to Memory's filter method
                // Note: this code does not go out of its way to escape
                // characters that have special meaning in RegExps
                match: new RegExp(filterInput.get("value").replace(/\W/, ''), 'i')
            }));
        });

        transferItems.run();
        billTo.run('transfer');
        lib.pageReady();
    }
    return {
        run: run
    }
});
