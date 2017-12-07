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
    "app/admin/asset/custom_attributes",
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
        barcodes, customAttributes, xlocation, assetCommon, lib, libGrid, core, asset) {
    //"use strict";
    function run() {

        var assetId = null;

        var action = null;

        var assetViewDialog = new Dialog({
            title: core.view,
            style: "width:800px"
        }, "asset-view-dialog");
        assetViewDialog.startup();
        assetViewDialog.on("cancel", function (event) {
            grid.clearSelection();
        });

        var tabContainer = new TabContainer({
            style: "height: 400px; width: 100%;"
        }, "asset-view-tabs");

        var attributesContentPane = new ContentPane({
            title: core.attributes},
        "asset-view-attributes-tab"
                );
        tabContainer.addChild(attributesContentPane);

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
            var id;
            modelFilteringSelect.set("value", "");
            statusSelect.reset();
            location.setData(null);
            barcodes.setData(null);
            customAttributes.setData(null);
            serialNumberInput.set("value", "");
            costInput.set("value", null);
            valueInput.set("value", null);
            commentInput.set("value", "");
            activeCheckBox.set("checked", true);
            ownerFilteringSelect.set('value', null);
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
            target: '/api/store/models?ca',
            useRangeHeaders: false,
            idProperty: 'id'});
        var modelFilteringSelect = new FilteringSelect({
            store: modelStore,
            labelAttr: "name",
            searchAttr: "name",
            pageSize: 25,
            required: true
        }, "asset_model");
        modelFilteringSelect.startup();
        modelFilteringSelect.on("change", function (evt) {
            var item;
            if( action === "new" ) {
                item = this.get('item');
                if( item !== null ) {
                    customAttributes.setData(item.customAttributes);
                }
            }
        });

        var ownerStore = new JsonRest({
            target: '/api/store/vendors',
            useRangeHeaders: false,
            idProperty: 'id'});
        var ownerFilteringSelect = new FilteringSelect({
            store: ownerStore,
            labelAttr: "name",
            searchAttr: "name",
            "required": false,
            pageSize: 25
        }, "asset_owner");
        ownerFilteringSelect.startup();

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

        var location = xlocation.run("", "asset");

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
            var beforeModelTextFilter, filter, data, purchased;
            grid.clearSelection();
            if( assetForm.validate() ) {
                purchased = purchasedInput.get("value");
                data = {
                    "id": assetId,
                    "model_text": modelFilteringSelect.get("displayedValue"),
                    "status_text": statusSelect.get("displayedValue"),
                    "status": parseInt(statusSelect.get("value")),
                    "purchased": purchased === null ? "" : purchased,
                    "cost": parseFloat(costInput.get("value")),
                    "value": parseFloat(valueInput.get("value")),
                    "owner": parseInt(ownerFilteringSelect.get("value")),
                    "model": parseInt(modelFilteringSelect.get("value")),
                    "location": location.getData(),
                    "location_text": location.getText().replace(/<br( \/)?>/, "\n"),
                    "barcode": barcodes.getActive(),
                    "barcodes": barcodes.getData(),
                    "serial_number": serialNumberInput.get("value"),
                    "active": activeCheckBox.get("checked"),
                    "custom_attributes": customAttributes.getData(),
                    "comment": commentInput.get("value")
                };
                grid.collection.put(data).then(function (data) {
                    assetViewDialog.hide();
                    store.fetch();
                    grid.refresh();
                }, lib.xhrError);
            } else {
                lib.textError(core.invalid_form);
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
                    label: asset.model
                },
                serial_number: {
                    label: asset.serial_number
                },
                status_text: {
                    label: core.status
                },
                location_text: {
                    label: asset.location,
                    formatter: function (item) {
                        return "<pre>" + item + "</pre>";
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
                    var titleBarcode, timestamp;
                    if( typeof asset.barcodes[0] !== "undefined" && typeof asset.barcodes[0].barcode !== "undefined" ) {
                        titleBarcode = asset.barcodes[0].barcode;
                    } else {
                        titleBarcode = asset.model_text;
                    }
                    assetViewDialog.set('title', core.view + " " + titleBarcode);
                    assetViewDialog.show();
                    action = "view";
                    assetId = asset.id;
                    modelFilteringSelect.set('displayedValue', asset.model.brandModelName);
                    statusSelect.set("displayedValue", asset.status.name);
                    timestamp = new Date();
                    timestamp.setTime(asset.purchased.timestamp * 1000);
                    purchasedInput.set('value', timestamp);
                    costInput.set("value", asset.cost);
                    valueInput.set("value", asset.value);
                    if( asset.owner !== null ) {
                        ownerFilteringSelect.set("displayedValue", asset.owner.name);
                    } else {
                        ownerFilteringSelect.set("value", null);
                    }
                    location.setData(asset.location, asset.locationText);
                    serialNumberInput.set('value', asset.serialNumber);
                    customAttributes.setData(asset.customAttributes);
                    commentInput.set('value', asset.comment);
                    if( typeof asset.barcodes !== "undefined" ) {
                        barcodes.setData(asset.barcodes);
                    } else {
                        barcodes.setData(null);
                    }
                    activeCheckBox.set('checked', asset.active);
                    assetCommon.relationshipLists(modelRelationshipsContentPane,
                            {"requires": asset.model.requires,
                                "required_by": asset.model.requiredBy,
                                "extends": asset.model["extends"],
                                "extended_by": asset.model.extendedBy}, asset.model.satisfies);
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

        barcodes.run('asset');
        customAttributes.run('asset');
        lib.pageReady();
    }
    return {
        run: run
    };
});
