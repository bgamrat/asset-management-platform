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
    "app/admin/asset/barcodes",
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
        barcodes, assetCommon, lib, libGrid, core, asset) {
    //"use strict";
    function run() {

        var assetId = null;

        var action = null;

        var assetViewDialog = new Dialog({
            title: core.view,
            style: "width:500px"
        }, "asset-view-dialog");
        assetViewDialog.startup();
        assetViewDialog.on("cancel", function (event) {
            grid.clearSelection();
        });

        var tabContainer = new TabContainer({
            style: "height: 300px; width: 100%;"
        }, "asset-view-tabs");


        var locationContentPane = new ContentPane({
            title: asset.location},
        "asset-view-location-tab"
                );
        tabContainer.addChild(locationContentPane);

        var expensesContentPane = new ContentPane({
            title: core.expenses},
        "asset-view-expenses-tab"
                );
        tabContainer.addChild(expensesContentPane);
        tabContainer.startup();

        var barcodesContentPane = new ContentPane({
            title: asset.barcodes},
        "asset-view-barcodes-tab"
                );
        tabContainer.addChild(barcodesContentPane);

        var modelRelationshipsContentPane = new ContentPane({
            title: asset.model_relationships},
        "asset-view-model-relationships-tab"
                );
        tabContainer.addChild(modelRelationshipsContentPane);

        var historyContentPane = new ContentPane({
            title: core.history},
        "asset-view-history-tab"
                );
        tabContainer.addChild(historyContentPane);
        tabContainer.startup();

        var newBtn = new Button({
            label: core["new"]
        }, 'asset-new-btn');
        newBtn.startup();
        newBtn.on("click", function (event) {
            modelFilteringSelect.set("value", "");
            statusSelect.reset();
            locationFilteringSelect.reset();
           // locationTypeRadioButton.reset();
            barcodes.setData(null);
            serialNumberInput.set("value", "");
            commentInput.set("value", "");
            activeCheckBox.set("checked", true);
            assetViewDialog.set("title", core["new"]).show();
            action = "new";
        });

        var removeBtn = new Button({
            label: core.remove
        }, 'asset-remove-btn');
        removeBtn.startup();
        removeBtn.on("click", function (event) {
            var markedForDeletion = query(".dgrid-row .remove-cb input:checked", "asset-grid");
            if( markedForDeletion.length > 0 ) {
                lib.confirmAction(core.areyousure, function () {
                    markedForDeletion.forEach(function (node) {
                        var row = grid.row(node);
                        store.remove(row.data.id);
                    });
                });
            }
        });

        var modelStore = new JsonRest({
            target: '/api/store/models',
            useRangeHeaders: false,
            idProperty: 'id'});
        var modelFilteringSelect = new FilteringSelect({
            store: modelStore,
            labelAttr: "name",
            searchAttr: "name",
            pageSize: 25
        }, "asset_model");
        modelFilteringSelect.startup();

        var serialNumberInput = new ValidationTextBox({
            trim: true,
            placeholder: asset.serial_number,
            pattern: "[A-Za-z0-9\.\,\ \'-]{2,64}"
        }, "asset_serial_number");
        serialNumberInput.startup();

        var activeCheckBox = new CheckBox({}, "asset_active");
        activeCheckBox.startup();

        var select = "asset_status";

        if( dom.byId(select) === null ) {
            lib.textError(select + " not found");
            return;
        }

        on(dom.byId('asset_location_ctype'), "click", function (event) {
            var target = event.target, targetId;
            if( target.tagName === 'LABEL' ) {
                target = dom.byId(domAttr.get(target, "for"));
            }
            var dataUrl = domAttr.get(target, "data-url");
            if( dataUrl !== null && dataUrl !== "" ) {
                locationFilteringSelect.set("readOnly", false);
                locationStore.target = dataUrl;
                locationFilteringSelect.set("store", locationStore);
            } else {
                targetId = target.id.replace(/\D/g, '');
                textLocationMemoryStore.data = [{name: locationTypeLabels[targetId], id: 0}];
                locationFilteringSelect.set("store", textLocationStore);
                locationFilteringSelect.set("displayedValue", locationTypeLabels[targetId]);
                locationFilteringSelect.set("readOnly", true);
            }
        });

        var textLocationMemoryStore = new Memory({
            idProperty: "id",
            data: []});
        var textLocationStore = new ObjectStore({objectStore: textLocationMemoryStore});

        var locationStore = new JsonRest({
            useRangeHeaders: false,
            idProperty: 'id'});
        var locationFilteringSelect = new FilteringSelect({
            store: null,
            labelAttr: "name",
            searchAttr: "name",
            pageSize: 25,
            readOnly: true,
            "class": 'location-filtering-select'
        }, "asset_location_entity");
        locationFilteringSelect.startup();

        var locationTypeLabels = {};
        query('label[for^="asset_location_ctype_"]').forEach(function (node) {
            locationTypeLabels[domAttr.get(node, "for").replace(/\D/g, '')] = node.textContent;
        });

        var locationTypeRadioButton = [];

        query('[name="asset[location][ctype]"]').forEach(function (node) {
            var id;
            var dijit = new RadioButton({"value": node.value, "name": node.name}, node);
            if( node.checked ) {
                dijit.set("checked", true);
                id = node.id.replace(/\D/g, '');
                textLocationMemoryStore.data = [{"name": locationTypeLabels[id], id: 0}];
                locationFilteringSelect.set("store", textLocationMemoryStore);
                locationFilteringSelect.set("displayedValue", locationTypeLabels[id]);
                locationFilteringSelect.set("value", id);
                locationFilteringSelect.set("readOnly", true);
            }
            dijit.set("data-url", domAttr.get(node, "data-url"));
            dijit.set("data-location-type-id", node.value);
            dijit.startup();
            locationTypeRadioButton.push(dijit);
        });

        var data = JSON.parse(domAttr.get(select, "data-options"));
        // Convert the data to an array of objects
        var statusStoreData = [], d;
        for( d in data ) {
            statusStoreData.push(data[d]);
        }
        var statusMemoryStore = new Memory({
            idProperty: "value",
            data: statusStoreData});
        var store = new ObjectStore({objectStore: statusMemoryStore});

        var statusSelect = new Select({
            store: store,
            placeholder: asset.status,
            required: true,
            value: domAttr.get(select, 'data-selected')
        }, select);
        statusSelect.startup();

        var purchasedInput = new DateTextBox({
            placeholder: core.purchased,
            trim: true,
            required: false
        }, "asset_purchased");
        purchasedInput.startup();

        var costInput = new CurrencyTextBox({
            placeholder: core.cost,
            trim: true,
            required: false
        }, "asset_cost");
        costInput.startup();

        var valueInput = new CurrencyTextBox({
            placeholder: core.value,
            trim: true,
            required: false
        }, "asset_value");
        valueInput.startup();

        var commentInput = new SimpleTextarea({
            placeholder: core.comment,
            trim: true,
            required: false
        }, "asset_comment");
        commentInput.startup();

        var assetForm = new Form({}, '[name="asset"]');
        assetForm.startup();

        var saveBtn = new Button({
            label: core.save
        }, 'asset-save-btn');
        saveBtn.startup();
        saveBtn.on("click", function (event) {
            var beforeModelTextFilter, filter, data, locationId, locationData, purchased;
            grid.clearSelection();
            if( assetForm.validate() ) {
                locationId = parseInt(dom.byId("asset_location_id").value);
                locationData = {
                    "id": isNaN(locationId) ? null : locationId,
                    "type": parseInt(getLocationType()),
                    "entity": parseInt(locationFilteringSelect.get("value"))
                };
                purchased = purchasedInput.get("value");
                data = {
                    "id": assetId,
                    "model_text": modelFilteringSelect.get("displayedValue"),
                    "status_text": statusSelect.get("displayedValue"),
                    "status": parseInt(statusSelect.get("value")),
                    "purchased": purchased === null ? "" : purchased,
                    "cost": parseFloat(costInput.get("value")),
                    "value": parseFloat(valueInput.get("value")),
                    "model": parseInt(modelFilteringSelect.get("value")),
                    "location": locationData,
                    "location_text": locationFilteringSelect.get("displayedValue"),
                    "barcode": barcodes.getActive(),
                    "barcodes": barcodes.getData(),
                    "serial_number": serialNumberInput.get("value"),
                    "active": activeCheckBox.get("checked"),
                    "comment": commentInput.get("value"),
                };
                if( action === "view" ) {
                    grid.collection.put(data).then(function (data) {
                        assetViewDialog.hide();
                    }, lib.xhrError);
                } else {
                    filter = new store.Filter();
                    beforeModelTextFilter = filter.gt('model_text', data.model_text);
                    store.filter(beforeModelTextFilter).sort('model_text').fetchRange({start: 0, end: 1}).then(function (results) {
                        var beforeId;
                        beforeId = (results.length > 0) ? results[0].id : null;
                        grid.collection.add(data, {"beforeId": beforeId}).then(function (data) {
                            assetViewDialog.hide();
                            store.fetch();
                            grid.refresh();
                        }, lib.xhrError);
                    });
                }
            } else {
                lib.textError(core.invalid_form)
            }
        });

        var filterInput = new TextBox({placeHolder: core.filter}, "asset-filter-input");
        filterInput.startup();

        var TrackableRest = declare([Rest, SimpleQuery, Trackable]);
        var store = new TrackableRest({target: '/api/assets', useRangeHeaders: true, idProperty: 'id'});
        var grid = new (declare([OnDemandGrid, Selection, Editor]))({
            collection: store,
            className: "dgrid-autoheight",
            sort: "model_text",
            columns: {
                id: {
                    label: core.id
                },
                barcode: {
                    label: asset.barcode
                },
                model_text: {
                    label: asset.model,
                },
                serial_number: {
                    label: asset.serial_number,
                },
                status_text: {
                    label: core.status
                },
                location_text: {
                    label: asset.location
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
        }, 'asset-grid');
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
                grid.collection.get(id).then(function (asset) {
                    var titleBarcode;
                    if( typeof asset.barcodes[0] !== "undefined" && typeof asset.barcodes[0].barcode !== "undefined" ) {
                        titleBarcode = asset.barcodes[0].barcode;
                    } else {
                        titleBarcode = asset.model_text;
                    }
                    assetViewDialog.set('title', core.view + " " + titleBarcode);
                    assetViewDialog.show();
                    action = "view";
                    assetId = asset.id;
                    modelFilteringSelect.set('displayedValue', asset.model_text);
                    statusSelect.set("displayedValue", asset.status_text);
                    purchasedInput.set("value", asset.purchased);
                    costInput.set("value", asset.cost);
                    valueInput.get("value", asset.value);
                    dom.byId("asset_location_id").value = asset.location.id;
                    setLocationType(asset.location.type.id);
                    if( asset.location.type.url !== null ) {
                        locationStore.target = asset.location.type.url;
                        locationFilteringSelect.set("store", locationStore);
                        locationFilteringSelect.set("readOnly", false);
                        locationFilteringSelect.set('displayedValue', asset.location_text);
                    } else {
                        textLocationMemoryStore.data = [{name: locationTypeLabels[asset.location.type.id], id: 0}];
                        locationFilteringSelect.set("store", textLocationStore);
                        locationFilteringSelect.set('displayedValue', asset.location_text);
                        locationFilteringSelect.set("readOnly", true);
                    }
                    serialNumberInput.set('value', asset.serial_number);
                    commentInput.set('value', asset.comment);
                    if( typeof asset.barcodes !== "undefined" ) {
                        barcodes.setData(asset.barcodes);
                    } else {
                        barcodes.setData(null);
                    }
                    activeCheckBox.set('checked', asset.active);
                    assetCommon.relationshipLists(modelRelationshipsContentPane, asset.model_relationships);
                    lib.showHistory(historyContentPane, asset.history);
                }, lib.xhrError);
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
                    xhr("/api/assets/" + name, {
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
            query(".dgrid-row .remove-cb", "asset-grid").forEach(function (node) {
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

        on(dom.byId('asset-grid-filter-form'), 'submit', function (event) {
            event.preventDefault();
            grid.set('collection', store.filter({
                // Pass a RegExp to Memory's filter method
                // Note: this code does not go out of its way to escape
                // characters that have special meaning in RegExps
                match: new RegExp(filterInput.get("value").replace(/\W/, ''), 'i')
            }));
        });

        function getLocationType() {
            var i, locationTypeSet = false;
            for( i = 0; i < locationTypeRadioButton.length; i++ ) {
                if( locationTypeRadioButton[i].get("checked") === true ) {
                    locationTypeSet = true;
                    break;
                }
            }
            return locationTypeSet ? locationTypeRadioButton[i].get("value") : null;
        }

        function setLocationType(locationType) {
            var i;
            for( i = 0; i < locationTypeRadioButton.length; i++ ) {
                if( parseInt(locationTypeRadioButton[i].get("data-location-type-id")) === locationType ) {
                    locationTypeRadioButton[i].set("checked", true);
                    break;
                }
            }
        }

        lib.pageReady();
        barcodes.run('asset');
    }
    return {
        run: run
    }
});
