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
    "app/admin/asset/location",
    "app/admin/asset/trailer_relationships",
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
        xlocation, trailerRelationships, assetCommon, lib, libGrid, core, asset) {
    //"use strict";
    function run() {

        var trailerId = null;

        var action = null;

        var trailerViewDialog = new Dialog({
            title: core.view,
            style: "width:500px"
        }, "trailer-view-dialog");
        trailerViewDialog.startup();
        trailerViewDialog.on("cancel", function (event) {
            grid.clearSelection();
        });

        var tabContainer = new TabContainer({
            style: "height: 300px; width: 100%;"
        }, "trailer-view-tabs");


        var locationContentPane = new ContentPane({
            title: asset.location},
        "trailer-view-location-tab"
                );
        tabContainer.addChild(locationContentPane);

        var expensesContentPane = new ContentPane({
            title: core.expenses},
        "trailer-view-expenses-tab"
                );
        tabContainer.addChild(expensesContentPane);
        tabContainer.startup();

        var requiresContentPane = new ContentPane({
            title: asset.requires},
        "trailer-view-requires-tab"
                );
        tabContainer.addChild(requiresContentPane);

        var extendsContentPane = new ContentPane({
            title: asset["extends"]},
        "trailer-view-extends-tab"
                );
        tabContainer.addChild(extendsContentPane);

        var historyContentPane = new ContentPane({
            title: core.history},
        "trailer-view-history-tab"
                );
        tabContainer.addChild(historyContentPane);
        tabContainer.startup();

        var newBtn = new Button({
            label: core["new"]
        }, 'trailer-new-btn');
        newBtn.startup();
        newBtn.on("click", function (event) {
            modelFilteringSelect.set("value", "");
            statusSelect.set("value", "");
            location.setData(null);
            serialNumberInput.set("value", "");
            descriptionInput.set("value", "");
            activeCheckBox.set("checked", true);
            trailerViewDialog.set("title", core["new"]).show();
            action = "new";
        });

        var removeBtn = new Button({
            label: core.remove
        }, 'trailer-remove-btn');
        removeBtn.startup();
        removeBtn.on("click", function (event) {
            var markedForDeletion = query(".dgrid-row .remove-cb input:checked", "trailer-grid");
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
        }, "trailer_model");
        modelFilteringSelect.startup();

        var nameInput = new ValidationTextBox({
            placeholder: core.name,
            trim: true,
            pattern: "[a-zA-Z0-9x\.\,\ \+\(\)\'\"-]{2,24}",
            required: true
        }, "trailer_name");
        nameInput.startup();

        var serialNumberInput = new ValidationTextBox({
            trim: true,
            placeholder: asset.serial_number,
            pattern: "[A-Za-z0-9\.\,\ \'-]{2,64}"
        }, "trailer_serial_number");
        serialNumberInput.startup();

        var activeCheckBox = new CheckBox({}, "trailer_active");
        activeCheckBox.startup();

        var select = "trailer_status";

        if( dom.byId(select) === null ) {
            lib.textError(select + " not found");
            return;
        }

        var location = xlocation.run("", "trailer");

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
            placeholder: trailer.status,
            required: true
        }, select);
        statusSelect.startup();

        var purchasedInput = new DateTextBox({
            placeholder: core.purchased,
            trim: true,
            required: false
        }, "trailer_purchased");
        purchasedInput.startup();

        var costInput = new CurrencyTextBox({
            placeholder: core.cost,
            trim: true,
            required: false
        }, "trailer_cost");
        costInput.startup();

        var descriptionInput = new SimpleTextarea({
            placeholder: core.description,
            trim: true,
            required: false
        }, "trailer_description");
        descriptionInput.startup();

        var trailerForm = new Form({}, '[name="trailer"]');
        trailerForm.startup();

        var saveBtn = new Button({
            label: core.save
        }, 'trailer-save-btn');
        saveBtn.startup();
        saveBtn.on("click", function (event) {
            var data, purchased;
            grid.clearSelection();
            if( trailerForm.validate() ) {
                purchased = purchasedInput.get("value");
                data = {
                    "id": trailerId,
                    "model_text": modelFilteringSelect.get("displayedValue"),
                    "status_text": statusSelect.get("displayedValue"),
                    "status": parseInt(statusSelect.get("value")),
                    "purchased": purchased === null ? "" : lib.formatDate(purchased,false),
                    "cost": parseFloat(costInput.get("value")),
                    "model": parseInt(modelFilteringSelect.get("value")),
                    "name": nameInput.get("value"),
                    "location": location.getData(),
                    "location_text": location.getText().replace(/<br( \/)?>/, "\n"),
                    "serial_number": serialNumberInput.get("value"),
                    "active": activeCheckBox.get("checked"),
                    "extends": trailerRelationships.getData("extends"),
                    "requires": trailerRelationships.getData("requires"),
                    "extended_by": trailerRelationships.getData("extended_by"),
                    "required_by": trailerRelationships.getData("required_by"),
                    "description": descriptionInput.get("value")
                };
                grid.collection.put(data).then(function (data) {
                    trailerViewDialog.hide();
                    store.fetch();
                    grid.refresh();
                }, lib.xhrError);
            } else {
                lib.textError(core.invalid_form)
            }
        });

        var filterInput = new TextBox({placeHolder: core.filter}, "trailer-filter-input");
        filterInput.startup();

        var TrackableRest = declare([Rest, SimpleQuery, Trackable]);
        var store = new TrackableRest({target: '/api/trailers', useRangeHeaders: true, idProperty: 'id'});
        var grid = new (declare([OnDemandGrid, Selection, Editor]))({
            collection: store,
            className: "dgrid-autoheight",
            sort: "model_text",
            maxRowsPerPage: 25,
            columns: {
                id: {
                    label: core.id
                },
                name: {
                    label: core.name
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
                        return (item === null) ? "" : "<pre>" + item + "</pre>";
                    }
                },
                description: {
                    label: core.description
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
        }, 'trailer-grid');
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
                grid.collection.get(id).then(function (trailer) {
                    trailerViewDialog.set('title', core.view + " " + trailer.name);
                    trailerViewDialog.show();
                    action = "view";
                    trailerId = trailer.id;
                    nameInput.set("value", trailer.name);
                    modelFilteringSelect.set('displayedValue', trailer.model.name);
                    statusSelect.set("displayedValue", trailer.status.name);
                    purchasedInput.set("value", trailer.purchased);
                    costInput.set("value", trailer.cost);
                    location.setData(trailer.location, trailer.locationText);
                    serialNumberInput.set('value', trailer.serialNumber);
                    descriptionInput.set('value', trailer.description);
                    activeCheckBox.set('checked', trailer.active);
                    trailerRelationships.setData("extends", trailer['extends']);
                    trailerRelationships.setData("requires", trailer['requires']);
                    trailerRelationships.setData("extended_by", trailer.extendedBy);
                    trailerRelationships.setData("required_by", trailer.requiredBy);
                    lib.showHistory(historyContentPane, trailer["history"]);
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
                    xhr("/api/trailers/" + name, {
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
            query(".dgrid-row .remove-cb", "trailer-grid").forEach(function (node) {
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

        on(dom.byId('trailer-grid-filter-form'), 'submit', function (event) {
            event.preventDefault();
            grid.set('collection', store.filter({
                // Pass a RegExp to Memory's filter method
                // Note: this code does not go out of its way to escape
                // characters that have special meaning in RegExps
                match: new RegExp(filterInput.get("value").replace(/\W/, ''), 'i')
            }));
        });

        trailerRelationships.run();
        lib.pageReady();
    }
    return {
        run: run
    }
});
