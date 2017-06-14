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
    "dijit/form/ValidationTextBox",
    "dijit/form/CheckBox",
    "dijit/form/SimpleTextarea",
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
    "app/admin/client/contracts",
    "app/lib/common",
    "app/lib/grid",
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/client",
    "dojo/domReady!"
], function (declare, dom, domConstruct, on, xhr, aspect, query,
        registry, Form, TextBox, ValidationTextBox, CheckBox, SimpleTextarea, Button, Dialog, TabContainer, ContentPane,
        Rest, SimpleQuery, Trackable, OnDemandGrid, Selection, Editor, put,
        xperson, contracts, lib, libGrid, core, client) {
    //"use strict";
    function run() {
        var action = null;
        var person;

        var clientId;

        var clientViewDialog = new Dialog({
            title: core.view
        }, "client-view-dialog");
        clientViewDialog.startup();
        clientViewDialog.on("cancel", function (event) {
            grid.clearSelection();
        });

        var tabContainer = new TabContainer({
            style: "height: 525px; width: 100%;"
        }, "client-view-tabs");

        var contactsContentPane = new ContentPane({
            title: core.contacts},
        "client-view-contacts-tab"
                );
        tabContainer.addChild(contactsContentPane);

        var contractsContentPane = new ContentPane({
            title: client.contracts},
        "client-view-contracts-tab"
                );
        tabContainer.addChild(contractsContentPane);

        var historyContentPane = new ContentPane({
            title: core.history},
        "client-view-history-tab"
                );
        tabContainer.addChild(historyContentPane);
        tabContainer.startup();

        var newBtn = new Button({
            label: core["new"]
        }, 'client-new-btn');
        newBtn.startup();
        newBtn.on("click", function (event) {
            clientId = null;
            nameInput.set("value", "");
            activeCheckBox.set("checked", true);
            person.setData(null);
            contracts.setData(null);
            clientViewDialog.set("title", core["new"]).show();
            action = "new";
        });

        var removeBtn = new Button({
            label: core.remove
        }, 'client-remove-btn');
        removeBtn.startup();
        removeBtn.on("click", function (event) {
            var markedForDeletion = query(".dgrid-row .remove-cb input:checked", "client-grid");
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
        }, "client_name");
        nameInput.startup();

        var activeCheckBox = new CheckBox({}, "client_active");
        activeCheckBox.startup();

        var commentInput = new SimpleTextarea({
            placeholder: core.comment,
            trim: true,
            required: false
        }, "client_comment");
        commentInput.startup();

        var clientForm = new Form({}, '[name="client"]');
        clientForm.startup();

        var saveBtn = new Button({
            label: core.save
        }, 'client-save-btn');
        saveBtn.startup();
        saveBtn.on("click", function (event) {
            var beforeNameFilter, filter;
            if( clientForm.validate() ) {
                var data = {
                    "id": clientId,
                    "name": nameInput.get("value"),
                    "active": activeCheckBox.get("checked"),
                    "comment": commentInput.get("value"),
                    "contacts": person.getData(),
                    "contracts": contracts.getData()
                };
                if( action === "view" ) {
                    grid.collection.put(data).then(function (data) {
                        clientViewDialog.hide();
                    }, lib.xhrError);
                } else {
                    filter = new store.Filter();
                    beforeNameFilter = filter.gt('name', data.name);
                    store.filter(beforeNameFilter).sort('name').fetchRange({start: 0, end: 1}).then(function (results) {
                        var beforeId;
                        beforeId = (results.length > 0) ? results[0].id : null;
                        grid.collection.add(data, {"beforeId": beforeId}).then(function (data) {
                            clientViewDialog.hide();
                            store.fetch();
                            grid.refresh();
                        }, lib.xhrError);
                    });
                }
            } else {
                lib.textError(core.invalid_form)
            }
        });

        var filterInput = new TextBox({placeHolder: core.filter}, "client-filter-input");
        filterInput.startup();

        var TrackableRest = declare([Rest, SimpleQuery, Trackable]);
        var store = new TrackableRest({target: '/api/clients', useRangeHeaders: true, idProperty: 'id'});
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
                contracts: {
                    label: client.contracts,
		    formatter: function (data, object) {
                        var c, n, nameList = [], html = "", datesList = [], st = "", en = "";
                        if( data !== "" ) {
                            for( c in data ) {
                                nameList.push(data[c].name);
				if (data[c].start !== null) {
				    st = data[c].start;
				}
				if (data[c].end !== null) {
				    en = data[c].end;
                                }
				datesList.push(en + "-" + st);
                            }
                            if( nameList.length > 0 ) {
                                for( n = 0; n < nameList.length; n++ ) {
                                    html += '<a class="contract link" href="/admin/client/' + object.name + '/contract/' + nameList[n] + '">' + nameList[n] + '</a><span class="date-span">' + datesList[n] + '</span><br>';
                                }
                            }
                        }
                        return html;
                    }
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
        }, 'client-grid');
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
                grid.collection.get(id).then(function (client) {
                    var r;
                    action = "view";
                    clientId = client.id;
                    nameInput.set("value", client.name);
                    activeCheckBox.set("checked", client.active === true);
                    commentInput.set("value", client.comment);
                    person.setData(client.contacts);
                    contracts.setData(client.contracts);
                    clientViewDialog.show();
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
                    xhr("/api/clients/" + name, {
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
            query(".dgrid-row .remove-cb", "client-grid").forEach(function (node) {
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

        on(dom.byId('client-grid-filter-form'), 'submit', function (event) {
            event.preventDefault();
            grid.set('collection', store.filter({
                // Pass a RegExp to Memory's filter method
                // Note: this code does not go out of its way to escape
                // characters that have special meaning in RegExps
                match: new RegExp(filterInput.get("value").replace(/\W/, ''), 'i')
            }));
        });

        person = xperson.run('client_contacts');
        contracts.run('client');

        lib.pageReady();
    }
    return {
        run: run
    }
});
