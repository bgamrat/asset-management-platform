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
    "app/lib/common",
    "app/lib/grid",
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/asset",
    "dojo/domReady!"
], function (declare, lang, dom, domAttr, domClass, domConstruct, on,
        xhr, aspect, query, ObjectStore, Memory,
        registry, Form, TextBox, ValidationTextBox, CheckBox, RadioButton, Select, FilteringSelect, SimpleTextarea, Button,
        Dialog, TabContainer, ContentPane,
        JsonRest,
        Rest, SimpleQuery, Trackable, OnDemandGrid, Selection, Editor, put,
        barcodes, lib, libGrid, core, asset) {
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

        var barcodesContentPane = new ContentPane({
            title: asset.barcodes},
        "asset-view-barcodes-tab"
                );
        tabContainer.addChild(barcodesContentPane);

        var historyContentPane = new ContentPane({
            title: asset.history},
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
            statusSelect.set("value", "");
            locationSelect.set("value", "");
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

        var locationTypeRadioButton = new RadioButton({}, "name='asset[location_type]'");
        locationTypeRadioButton.startup();
        on(dom.byId('asset_location_type'), "click", function (event) {
            var target = event.target;
            if( target.tagName === 'LABEL' ) {
                target = dom.byId(domAttr.get(target, "for"));
            }
            var dataUrl = domAttr.get(target, "data-url");
            if( dataUrl !== null ) {
                locationStore.target = dataUrl;
            }
        });

        var locationStore = new JsonRest({
            useRangeHeaders: false,
            idProperty: 'id'});
        var locationFilteringSelect = new FilteringSelect({
            store: locationStore,
            labelAttr: "name",
            searchAttr: "name",
            pageSize: 25
        }, "asset_location");
        locationFilteringSelect.startup();

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
            required: true
        }, select);
        statusSelect.startup();

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
            var beforeId, beforeIdFilter, filter;
            grid.clearSelection();
            if( assetForm.validate() ) {
                var data = {
                    "id": assetId,
                    "model_text": modelFilteringSelect.get("displayedValue"),
                    "status_text": statusSelect.get("displayedValue"),
                    "status": parseInt(statusSelect.get("value")),
                    "model": parseInt(modelFilteringSelect.get("value")),
                    "location": parseInt(locationFilteringSelect.get("value")),
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
                    action = "view";
                    assetId = asset.id;
                    modelFilteringSelect.set('displayedValue', asset.model_text);
                    statusSelect.set("displayedValue", asset.status_text);
                    locationTypeRadioButton.set('value', asset.location_type);
                    locationFilteringSelect.set('displayedValue', asset.location_text);
                    serialNumberInput.set('value', asset.serial_number);
                    commentInput.set('value', asset.comment);
                    if( typeof asset.barcodes !== "undefined" ) {
                        barcodes.setData(asset.barcodes);
                    } else {
                        barcodes.setData(null);
                    }
                    activeCheckBox.set('checked', asset.active);
                    lib.showHistory(historyContentPane, asset.history);
                    if( typeof asset.barcodes[0] !== "undefined" && typeof asset.barcodes[0].barcode !== "undefined" ) {
                        titleBarcode = asset.barcodes[0].barcode;
                    } else {
                        titleBarcode = asset.no_barcode;
                    }
                    assetViewDialog.set('title', core.view + " " + titleBarcode);
                    assetViewDialog.show();
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
        lib.pageReady();
        barcodes.run('asset');
    }
    return {
        run: run
    }
});
