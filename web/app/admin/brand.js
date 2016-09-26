define([
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-class",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/request/xhr",
    "dojo/dom-form",
    "dojo/aspect",
    "dojo/query",
    "dojo/data/ObjectStore",
    "dojo/store/Memory",
    "dijit/registry",
    "dijit/form/Form",
    "dijit/form/TextBox",
    "dijit/form/ValidationTextBox",
    "dijit/form/CheckBox",
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
    "app/admin/barcodes",
    "app/admin/model_relationships",
    "app/lib/common",
    "app/lib/grid",
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/asset",
    "dojo/domReady!"
], function (declare, lang, dom, domAttr, domClass, domConstruct, on,
        xhr, domForm, aspect, query, ObjectStore, Memory,
        registry, Form, TextBox, ValidationTextBox, CheckBox, Select, FilteringSelect, SimpleTextarea, Button,
        Dialog, TabContainer, ContentPane,
        JsonRest,
        Rest, SimpleQuery, Trackable, OnDemandGrid, Selection, Editor, put,
        barcodes, modelRelationships, lib, libGrid, core, asset) {
    //"use strict";
    function run() {

        var apiUrl = location.href.replace(/admin\/asset\/manufacturer\/([^\/]+)\/brand\/(.*)/, 'api/manufacturers/$1/brands/$2/models');

        var modelId = null;

        var action = null;

        var modelViewDialog = new Dialog({
            title: core.view
        }, "model-view-dialog");
        modelViewDialog.startup();
        modelViewDialog.on("cancel", function (event) {
            grid.clearSelection();
        });

        var tabContainer = new TabContainer({
            style: "height: 300px; width: 100%;"
        }, "model-view-tabs");

        var requiresContentPane = new ContentPane({
            title: asset.requires},
        "model-view-requires-tab"
                );
        tabContainer.addChild(requiresContentPane);
        var supportsContentPane = new ContentPane({
            title: asset.supports},
        "model-view-supports-tab"
                );
        tabContainer.addChild(supportsContentPane);
        var extendsContentPane = new ContentPane({
            title: asset.extends},
        "model-view-extends-tab"
                );
        tabContainer.addChild(extendsContentPane);
        var historyContentPane = new ContentPane({
            title: asset.history},
        "model-view-history-tab"
                );
        tabContainer.addChild(historyContentPane);

        tabContainer.startup();

        var newBtn = new Button({
            label: core["new"]
        }, 'model-new-btn');
        newBtn.startup();
        newBtn.on("click", function (event) {
            categorySelect.set("value", "");
            nameInput.set("value", "");
            activeCheckBox.set("checked", true);
            action = "new";
            modelId = null;
            modelViewDialog.set("title", core["new"]).show();
        });

        var removeBtn = new Button({
            label: core.remove
        }, 'model-remove-btn');
        removeBtn.startup();
        removeBtn.on("click", function (event) {
            var markedForDeletion = query(".dgrid-row .remove-cb input:checked", "model-grid");
            if( markedForDeletion.length > 0 ) {
                lib.confirmAction(core.areyousure, function () {
                    markedForDeletion.forEach(function (node) {
                        var row = grid.row(node);
                        store.remove(row.data.id);
                    });
                });
            }
        });

        var select = "model_category";

        if( dom.byId(select) === null ) {
            lib.textError(select + " not found");
            return;
        }

        var data = JSON.parse(domAttr.get(select, "data-options"));
        // Convert the data to an array of objects
        var storeData = [], d;
        for( d in data ) {
            storeData.push(data[d]);
        }
        var memoryStore = new Memory({
            idProperty: "value",
            data: storeData});
        var store = new ObjectStore({objectStore: memoryStore});

        var categorySelect = new Select({
            store: store,
            placeholder: asset.category,
            required: true
        }, "model_category");
        categorySelect.startup();

        var nameInput = new ValidationTextBox({
            placeholder: core.name,
            trim: true,
            pattern: "[a-zA-Z0-9x\.\,\ \+\(\)-]{2,24}",
            required: true,
        }, "model_name");
        nameInput.startup();

        var activeCheckBox = new CheckBox({}, "model_active");
        activeCheckBox.startup();

        var commentInput = new SimpleTextarea({
            placeholder: core.comment,
            trim: true,
            required: false
        }, "model_comment");
        commentInput.startup();

        var modelStore = new JsonRest({
            target: '/api/model/select',
            useRangeHeaders: false,
            idProperty: 'id'});

        var modelForm = new Form({}, '[name="model"]');
        modelForm.startup();

        var saveBtn = new Button({
            label: core.save
        }, 'model-save-btn');
        saveBtn.startup();
        saveBtn.on("click", function (event) {
            var beforeNameFilter, objParm, filter;
            grid.clearSelection();
            if( modelForm.validate() ) {
                var data = {
                    "id": modelId,
                    "category_text": categorySelect.get("displayedValue"),
                    "category": parseInt(categorySelect.get("value")),
                    "name": nameInput.get("value"),
                    "active": activeCheckBox.get("checked"),
                    "comment": commentInput.get("value"),
                };
                if( action === "view" ) {
                    grid.collection.put(data).then(function (data) {
                        modelViewDialog.hide();
                    }, lib.xhrError);
                } else {
                    filter = new store.Filter();
                    beforeNameFilter = filter.gt('name', data.name);
                    store.filter(beforeNameFilter).sort('name').fetchRange({start: 0, end: 1}).then(function (results) {
                        var beforeId;
                        beforeId = (results.length > 0) ? results[0].name : null;
                        if( beforeId !== null ) {
                            objParm = {"beforeId": beforeId};
                        } else {
                            objParm = null;
                        }
                        grid.collection.add(data, objParm).then(function (data) {
                            modelViewDialog.hide();
                        }, lib.xhrError);
                    });
                }
            } else {
                lib.textError(core.invalid_form)
            }
        });

        var filterInput = new TextBox({placeHolder: core.filter}, "model-filter-input");
        filterInput.startup();

        var TrackableRest = declare([Rest, SimpleQuery, Trackable]);
        var store = new TrackableRest({target: apiUrl, useRangeHeaders: true, idProperty: 'name'});
        var grid = new (declare([OnDemandGrid, Selection, Editor]))({
            collection: store,
            className: "dgrid-autoheight",
            columns: {
                id: {
                    label: core.id
                },
                category_text: {
                    label: asset.category
                },
                name: {
                    label: asset.model,
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
        }, 'model-grid');
        grid.startup();
        grid.collection.track();

        grid.on(".dgrid-row:click", function (event) {
            var checkBoxes = ["active", "remove"];
            var row = grid.row(event);
            var cell = grid.cell(event);
            var field = cell.column.field;
            var name = row.data.name;
            if( checkBoxes.indexOf(field) === -1 ) {
                if( typeof grid.selection[0] !== "undefined" ) {
                    grid.clearSelection();
                }
                grid.select(row);
                grid.collection.get(name).then(function (model) {
                    var i, history, historyHtml, date, dateText, dataText, d;
                    action = "view";
                    modelId = model.id;
                    categorySelect.set('displayedValue', model.category_text);
                    nameInput.set('value', model.name);
                    commentInput.set('value', model.comment);
                    activeCheckBox.set('checked', model.active);
                    lib.showHistory(historyContentPane, model.history);
                    modelViewDialog.set('title', core.view + " " + model.name);
                    modelViewDialog.show();
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
                    xhr(apiUrl + '/' + name, {
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
            query(".dgrid-row .remove-cb", "model-grid").forEach(function (node) {
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

        on(dom.byId('model-grid-filter-form'), 'submit', function (event) {
            event.preventDefault();
            grid.set('collection', store.filter({
                // Pass a RegExp to Memory's filter method
                // Note: this code does not go out of its way to escape
                // characters that have special meaning in RegExps
                match: new RegExp(filterInput.get("value").replace(/\W/, ''), 'i')
            }));
        });
        modelRelationships.run();
        lib.pageReady();
    }
    return {
        run: run
    }
});
