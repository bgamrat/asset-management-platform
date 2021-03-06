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
    "dojo/date/locale",
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
    "app/admin/asset/issue_items",
    "app/admin/asset/issue_notes",
    "app/admin/common/bill_to",
    "app/lib/common",
    "app/lib/grid",
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/asset",
    "dojo/domReady!"
], function (declare, lang, dom, domAttr, domClass, domConstruct, on,
        xhr, aspect, query, ObjectStore, Memory, locale,
        registry, Form, CurrencyTextBox, DateTextBox, TextBox, ValidationTextBox, CheckBox, RadioButton, Select, FilteringSelect, SimpleTextarea, Button,
        Dialog, TabContainer, ContentPane,
        JsonRest,
        Rest, SimpleQuery, Trackable, OnDemandGrid, Selection, Editor, put,
        issueItems, issueNotes, billTo, lib, libGrid, core, asset) {
    //"use strict";
    function run() {

        var issueId = null;

        var action = null;

        var issueViewDialog = new Dialog({
            title: core.view,
            style: "width:800px"
        }, "issue-view-dialog");
        issueViewDialog.startup();
        issueViewDialog.on("cancel", function (event) {
            grid.clearSelection();
        });

        var tabContainer = new TabContainer({
            style: "height: 300px; width: 100%;"
        }, "issue-view-tabs");

        var detailsContentPane = new ContentPane({
            title: asset.details},
        "issue-view-details-tab"
                );
        tabContainer.addChild(detailsContentPane);
        tabContainer.startup();

        var itemsContentPane = new ContentPane({
            title: core.items},
        "issue-view-items-tab"
                );
        tabContainer.addChild(itemsContentPane);

        var expensesContentPane = new ContentPane({
            title: core.expenses},
        "issue-view-expenses-tab"
                );
        tabContainer.addChild(expensesContentPane);
        tabContainer.startup();

        var historyContentPane = new ContentPane({
            title: core.history},
        "issue-view-history-tab"
                );
        tabContainer.addChild(historyContentPane);
        tabContainer.startup();

        var newBtn = new Button({
            label: core["new"]
        }, 'issue-new-btn');
        newBtn.startup();
        newBtn.on("click", function (event) {
            priorityInput.reset();
            typeSelect.set("value", defaultTypeId);
            statusSelect.set("value", defaultStatusId);
            trailerSelect.set("value", "");
            assignedToFilteringSelect.set("value", "");
            summaryInput.set("value", "");
            detailsInput.set("value", "");
            issueNotes.setData(null);
            issueItems.setData(null);
            costInput.set("value", null);
            billableCheckBox.set("checked", false);
            replacedCheckBox.set("checked", false);
            issueViewDialog.set("title", core["new"]).show();
            updatedInput.set("value", null);
            createdInput.set("value", null);
            action = "new";
        });

        var removeBtn = new Button({
            label: core.remove
        }, 'issue-remove-btn');
        removeBtn.startup();
        removeBtn.on("click", function (event) {
            var markedForDeletion = query(".dgrid-row .remove-cb input:checked", "issue-grid");
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
            value: dom.byId("issue_created_at").value,
            disabled: true
        }, "issue_created_at");
        createdInput.startup();
        var updatedInput = new ValidationTextBox({
            value: dom.byId("issue_updated_at").value,
            disabled: true
        }, "issue_updated_at");
        updatedInput.startup();

        var priorityInput = new ValidationTextBox({
            trim: true,
            pattern: "[0-9]+",
            required: true,
            placeholder: asset.priority
        }, "issue_priority");
        priorityInput.startup();

        var issueSelect = dom.byId('issue_trailer');
        var d, data = JSON.parse(domAttr.get(issueSelect, "data-options"));
        // Convert the data to an array of objects
        var storeData = [];
        for( d in data ) {
            storeData.push(data[d]);
        }
        var memoryStore = new Memory({
            idProperty: "label",
            data: storeData});
        var trailerStore = new ObjectStore({objectStore: memoryStore});
        var trailerSelect = new Select({
            store: trailerStore,
            placeholder: asset.trailer,
            required: true,
            "class": "asset-trailer",
            "searchAttr": "label"
        }, "issue_trailer");
        trailerSelect.startup();

        issueSelect = dom.byId('issue_type');
        var d, data = JSON.parse(domAttr.get(issueSelect, "data-options"));
        // Convert the data to an array of objects
        storeData = [];
        for( d in data ) {
            storeData.push(data[d]);
        }
        memoryStore = new Memory({
            idProperty: "value",
            data: storeData});
        var typeStore = new ObjectStore({objectStore: memoryStore});
        var defaultTypeId = domAttr.get("issue_type", "data-selected");
        var typeSelect = new Select({
            store: typeStore,
            placeholder: asset.type,
            required: true,
            "class": "status-type"
        }, "issue_type");
        typeSelect.startup();

        issueSelect = dom.byId('issue_status');
        data = JSON.parse(domAttr.get(issueSelect, "data-options"));
        // Convert the data to an array of objects
        storeData = [];
        for( d in data ) {
            storeData.push(data[d]);
        }
        memoryStore = new Memory({
            idProperty: "value",
            data: storeData});
        var statusStore = new ObjectStore({objectStore: memoryStore});
        var defaultStatusId = domAttr.get("issue_status", "data-selected");
        var statusSelect = new Select({
            store: statusStore,
            placeholder: asset.status,
            required: true,
            "class": "status-select"
        }, "issue_status");
        statusSelect.startup();

        var replacedCheckBox = new CheckBox({}, "issue_replaced");
        replacedCheckBox.startup();

        var billableCheckBox = new CheckBox({}, "issue_billable");
        billableCheckBox.startup();

        var select = "issue_status";

        if( dom.byId(select) === null ) {
            lib.textError(select + " not found");
            return;
        }

        var peopleStore = new JsonRest({
            target: '/api/store/people',
            useRangeHeaders: false,
            idProperty: 'id'});
        var assignedToFilteringSelect = new FilteringSelect({
            store: peopleStore,
            labelAttr: "name",
            searchAttr: "name",
            placeholder: asset.assigned_to,
            pageSize: 25,
            required: true
        }, "issue_assigned_to");
        assignedToFilteringSelect.startup();

        var costInput = new CurrencyTextBox({
            placeholder: core.cost,
            trim: true,
            required: false
        }, "issue_cost");
        costInput.startup();

        var summaryInput = new ValidationTextBox({
            placeholder: asset.summary,
            trim: true,
            "class": "wide",
            required: false
        }, "issue_summary");
        summaryInput.startup();

        var detailsInput = new SimpleTextarea({
            placeholder: core.details,
            trim: true,
            required: false
        }, "issue_details");
        detailsInput.startup();

        var issueForm = new Form({}, '[name="issue"]');
        issueForm.startup();

        var saveBtn = new Button({
            label: core.save
        }, 'issue-save-btn');
        saveBtn.startup();
        saveBtn.on("click", function (event) {
            var beforeModelTextFilter, filter, data, locationId, locationData, purchased;
            grid.clearSelection();
            if( issueForm.validate() ) {
                data = {
                    "id": issueId,
                    "priority": parseInt(priorityInput.get("value")),
                    "assigned_to": assignedToFilteringSelect.get("value"),
                    "type": parseInt(typeSelect.get("value")),
                    "status": parseInt(statusSelect.get("value")),
                    "assigned_to_text": assignedToFilteringSelect.get("displayedValue"),
                    "billable": billableCheckBox.get("checked"),
                    "type_text": typeSelect.get("displayedValue"),
                    "status_text": statusSelect.get("displayedValue"),
                    "purchased": purchased === null ? "" : purchased,
                    "cost": parseFloat(costInput.get("value")),
                    "trailer": trailerSelect.get("displayedValue"),
                    "trailer_text": trailerSelect.get("displayedValue"),
                    "items": issueItems.getData(),
                    "notes": issueNotes.getData(),
                    "bill_tos": billTo.getData(),
                    "summary": summaryInput.get("value"),
                    "details": detailsInput.get("value"),
                    "replaced": replacedCheckBox.get("checked")
                };
                grid.collection.put(data).then(function (data) {
                    issueViewDialog.hide();
                    store.fetch();
                    grid.refresh();
                }, lib.xhrError);
            } else {
                lib.textError(core.invalid_form)
            }
        });

        var filterInput = new TextBox({placeHolder: core.filter}, "issue-filter-input");
        filterInput.startup();

        var TrackableRest = declare([Rest, SimpleQuery, Trackable]);
        var store = new TrackableRest({target: '/api/issues', useRangeHeaders: true, idProperty: 'id'});
        var grid = new (declare([OnDemandGrid, Selection, Editor]))({
            collection: store,
            sort: "priority",
            maxRowsPerPage: 25,
            columns: {
                id: {
                    label: asset.issue + " " + core.id
                },
                trailer_text: {
                    label: asset.trailer
                },
                /*
                 barcode: {
                 label: asset.barcode
                 },
                 */

                priority: {
                    label: asset.priority
                },
                type_text: {
                    label: asset.type
                },
                status_text: {
                    label: asset.status
                },
                summary: {
                    label: asset.summary
                },
                assigned_to_text: {
                    label: asset.assigned_to
                },
                billable: {
                    label: core.billable,
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
        }, 'issue-grid');
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
                grid.collection.get(id).then(function (issue) {
                    var t, f;
                    issueViewDialog.show();
                    action = "view";
                    issueId = issue.id;
                    priorityInput.set("value", issue.priority);
                    typeSelect.set("value", issue.type.id);
                    statusSelect.set("value", issue.status.id);
                    trailerSelect.set("value", issue.trailer.name);
                    if( issue.assignedTo !== null ) {
                        assignedToFilteringSelect.set("displayedValue", issue.assignedTo.fullName);
                    } else {
                        assignedToFilteringSelect.set("value", null);
                    }
                    summaryInput.set("value", issue.summary);
                    detailsInput.set("value", issue.details);
                    issueItems.setData(issue.items);
                    issueNotes.setData(issue.notes);
                    f = null;
                    if( typeof issue.updatedAt !== "undefined" ) {
                        t = new Date(issue.updatedAt.timestamp * 1000);
                        f = locale.format(t);
                    }
                    updatedInput.set("value", f);
                    f = null;
                    if( typeof issue.createdAt !== "undefined" ) {
                        t = new Date(issue.createdAt.timestamp * 1000);
                        f = locale.format(t);
                    }
                    createdInput.set("value", f);
                    billableCheckBox.set("checked", issue.billable === true);
                    replacedCheckBox.set("checked", issue.replaced === true);
                    costInput.set("value", issue.cost);
                    billTo.setData(issue.billTos);
                    lib.showHistory(historyContentPane, issue.history);
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
                    xhr("/api/issues/" + name, {
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
            query(".dgrid-row .remove-cb", "issue-grid").forEach(function (node) {
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

        on(dom.byId('issue-grid-filter-form'), 'submit', function (event) {
            event.preventDefault();
            grid.set('collection', store.filter({
                // Pass a RegExp to Memory's filter method
                // Note: this code does not go out of its way to escape
                // characters that have special meaning in RegExps
                match: new RegExp(filterInput.get("value").replace(/\W/, ''), 'i')
            }));
        });

        issueItems.run();
        issueNotes.run();
        billTo.run('issue');
        lib.pageReady();
    }
    return {
        run: run
    }
});
