define([
    "dojo/_base/declare",
    "dojo/dom",
    "dojo/dom-attr",
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
    "dijit/form/TextBox",
    "dijit/form/ValidationTextBox",
    "dijit/form/NumberTextBox",
    "dijit/form/CheckBox",
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
    "app/admin/asset/custom_attributes",
    "app/admin/asset/satisfies",
    "app/admin/asset/model_relationships",
    "app/lib/common",
    "app/lib/grid",
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/asset",
    "dojo/domReady!"
], function (declare, dom, domAttr, domConstruct, on,
        xhr, aspect, query, ObjectStore, Memory,
        registry, Form, CurrencyTextBox, TextBox, ValidationTextBox, NumberTextBox,
        CheckBox, FilteringSelect, SimpleTextarea, Button,
        Dialog, TabContainer, ContentPane,
        JsonRest,
        Rest, SimpleQuery, Trackable, OnDemandGrid, Selection, Editor, put,
        customAttributes, satisfies, modelRelationships, lib, libGrid, core, asset) {
//"use strict";
    function run() {

        var apiUrl = location.href.replace(/admin\/asset\/manufacturer\/([^\/]+)\/brand\/(.*)/, 'api/manufacturers/$1/brands/$2/models');
        var modelId = null;
        var action = null;
        var modelViewDialog = new Dialog({
            title: core.view,
            style: "width:500px"
        }, "model-view-dialog");
        modelViewDialog.startup();
        modelViewDialog.on("cancel", function (event) {
            grid.clearSelection();
        });
        var tabContainer = new TabContainer({
            style: "height: 300px; width: 100%;"
        }, "model-view-tabs");

        var attributesContentPane = new ContentPane({
            title: core.attributes},
        "model-view-attributes-tab"
                );
        tabContainer.addChild(attributesContentPane);

        var satisfiesContentPane = new ContentPane({
            title: core.satisfies},
        "model-view-satisfies-tab"
                );
        tabContainer.addChild(satisfiesContentPane);

        var requiresContentPane = new ContentPane({
            title: asset.requires},
        "model-view-requires-tab"
                );
        tabContainer.addChild(requiresContentPane);
        var extendsContentPane = new ContentPane({
            title: asset["extends"]},
        "model-view-extends-tab"
                );
        tabContainer.addChild(extendsContentPane);
        var valuesContentPane = new ContentPane({
            title: core.values},
        "model-view-values-tab"
                );
        tabContainer.addChild(valuesContentPane);
        var historyContentPane = new ContentPane({
            title: core.history},
        "model-view-history-tab"
                );
        tabContainer.addChild(historyContentPane);
        tabContainer.startup();
        var newBtn = new Button({
            label: core["new"]
        }, 'model-new-btn');
        newBtn.startup();
        newBtn.on("click", function (event) {
            categoryFilteringSelect.set("value", "");
            nameInput.set("value", "");
            containerCheckBox.set("checked", false);
            defaultContractValueInput.set('value', null);
            defaultEventValueInput.set('value', null);
            activeCheckBox.set("checked", true);
            satisfies.setData("required_by", null);
            modelRelationships.setData("extends", null);
            modelRelationships.setData("requires", null);
            modelRelationships.setData("extended_by", null);
            modelRelationships.setData("required_by", null);
            customAttributes.setData(null);
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

        var categoryStore = new JsonRest({
            target: '/api/store/categories',
            useRangeHeaders: false,
            idProperty: 'id'});
        var categoryFilteringSelect = new FilteringSelect({
            store: categoryStore,
            labelAttr: "name",
            searchAttr: "name",
            pageSize: 25,
            required: true
        }, select);
        categoryFilteringSelect.startup();
        var nameInput = new ValidationTextBox({
            placeholder: core.name,
            trim: true,
            pattern: "[a-zA-Z0-9x\.\,\ \+\(\)\'\"-]{2,24}",
            required: true
        }, "model_name");
        nameInput.startup();
        var containerCheckBox = new CheckBox({}, "model_container");
        containerCheckBox.startup();
        var weightInput = new NumberTextBox({
            placeholder: asset.weight,
            trim: true,
            required: false
        }, "model_weight");
        weightInput.startup();
        var carnetValueInput = new CurrencyTextBox({
            placeholder: core.value,
            trim: true,
            required: false
        }, "model_carnet_value");
        carnetValueInput.startup();
        var defaultContractValueInput = new CurrencyTextBox({
            placeholder: core.value,
            trim: true,
            required: false
        }, "model_default_contract_value");
        defaultContractValueInput.startup();
        var defaultEventValueInput = new CurrencyTextBox({
            placeholder: core.value,
            trim: true,
            required: false
        }, "model_default_event_value");
        defaultEventValueInput.startup();
        var activeCheckBox = new CheckBox({}, "model_active");
        activeCheckBox.startup();
        var commentInput = new SimpleTextarea({
            placeholder: core.comment,
            trim: true,
            required: false
        }, "model_comment");
        commentInput.startup();
        var modelForm = new Form({}, '[name="model"]');
        modelForm.startup();
        var saveBtn = new Button({
            label: core.save
        }, 'model-save-btn');
        saveBtn.startup();
        saveBtn.on("click", function (event) {
            var objParm, filter;
            grid.clearSelection();
            if( modelForm.validate() ) {
                var data = {
                    "id": modelId,
                    "category_text": categoryFilteringSelect.get("displayedValue"),
                    "category": parseInt(categoryFilteringSelect.get("value")),
                    "name": nameInput.get("value"),
                    "container": containerCheckBox.get("checked"),
                    "custom_attributes": customAttributes.getData(),
                    "weight": weightInput.get("value"),
                    "carnet_value": parseFloat(carnetValueInput.get("value")),
                    "default_contract_value": parseFloat(defaultContractValueInput.get("value")),
                    "default_event_value": parseFloat(defaultEventValueInput.get("value")),
                    "active": activeCheckBox.get("checked"),
                    "comment": commentInput.get("value"),
                    "satisfies": satisfies.getData(),
                    "extends": modelRelationships.getData("extends"),
                    "requires": modelRelationships.getData("requires"),
                    "extended_by": modelRelationships.getData("extended_by"),
                    "required_by": modelRelationships.getData("required_by")
                };
                if( action === "view" ) {
                    grid.collection.put(data, {"overwrite": true}).then(function (data) {
                        modelViewDialog.hide();
                    }, lib.xhrError);
                } else {
                    grid.collection.add(data, objParm).then(function (data) {
                        modelViewDialog.hide();
                        store.fetch();
                        grid.refresh();
                    }, lib.xhrError);
                }
            } else {
                lib.textError(core.invalid_form)
            }
        });
        var filterInput = new TextBox({placeHolder: core.filter}, "model-filter-input");
        filterInput.startup();
        var TrackableRest = declare([Rest, SimpleQuery, Trackable]);
        var store = new TrackableRest({target: apiUrl, useRangeHeaders: true, idProperty: 'id'});
        var grid = new (declare([OnDemandGrid, Selection, Editor]))({
            collection: store,
            sort: "name",
            maxRowsPerPage: 25,
            columns: {
                id: {
                    label: core.id
                },
                category_text: {
                    label: asset.category
                },
                name: {
                    label: asset.model
                },category_text: {
                    label: asset.category
                },
                container: {
                    label: asset.container,
                    renderCell: libGrid.renderGridCheckbox
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
                    action = "view";
                    modelId = model.id;
                    categoryFilteringSelect.set('displayedValue', model.category.name);
                    nameInput.set('value', model.name);
                    containerCheckBox.set('checked', model.container);
                    weightInput.set("value", model.weight);
                    commentInput.set('value', model.comment);
                    customAttributes.setData(model.customAttributes);
                    activeCheckBox.set('checked', model.active);
                    carnetValueInput.set('value', model.carnetValue);
                    defaultContractValueInput.set('value', model.defaultContractValue);
                    defaultEventValueInput.set('value', model.defaultEventValue);
                    satisfies.setData(model.satisfies);
                    modelRelationships.setData("extends", model['extends']);
                    modelRelationships.setData("requires", model['requires']);
                    modelRelationships.setData("extended_by", model.extendedBy);
                    modelRelationships.setData("required_by", model.requiredBy);
                    lib.showHistory(historyContentPane, model["history"]);
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

        customAttributes.run('model');
        satisfies.run();
        modelRelationships.run();
        lib.pageReady();
    }
    return {
        run: run
    }
});
//# sourceURL=brand.js