define([
    "dojo/_base/declare",
    "dojo/dom",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/promise/all",
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
    "app/lib/common",
    "app/lib/grid",
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/client",
    "dojo/i18n!app/nls/person",
    "dojo/domReady!"
], function (declare, dom, domConstruct, on,
        all, xhr, aspect, query,
        registry, Form, TextBox, ValidationTextBox, CheckBox, SimpleTextarea, Button,
        Dialog, TabContainer, ContentPane,
        Rest, SimpleQuery, Trackable, OnDemandGrid, Selection, Editor, put,
        person, lib, libGrid, core, client, personWords) {
    //"use strict";
    function run() {
        var action = null;
        var personId = null;
        var urlParams, id;

        var personViewDialog = new Dialog({
            title: core.view,
            style: "width:800px"
        }, "person-view-dialog");
        personViewDialog.startup();
        personViewDialog.on("cancel", function (event) {
            grid.clearSelection();
        });

        var tabContainer = new TabContainer({
            style: "height: 525px; width: 100%;"
        }, "person-view-tabs");

        var baseContentPane = new ContentPane({
            title: core.person},
        "person-view-base-tab"
                );
        tabContainer.addChild(baseContentPane);

        var staffContentPane = new ContentPane({
            title: personWords.staff},
        "person-view-staff-tab"
                );
        tabContainer.addChild(staffContentPane);

        var employmentContentPane = new ContentPane({
            title: personWords.employment},
        "person-view-employment-tab"
                );
        tabContainer.addChild(employmentContentPane);

        var userContentPane = new ContentPane({
            title: core.user},
        "person-view-user-tab"
                );
        tabContainer.addChild(userContentPane);

        var historyContentPane = new ContentPane({
            title: core.history},
        "person-view-history-tab"
                );
        tabContainer.addChild(historyContentPane);
        tabContainer.startup();

        var newBtn = new Button({
            label: core["new"]
        }, 'person-new-btn');
        newBtn.startup();
        newBtn.on("click", function (event) {
            person.setData(null);
            personViewDialog.set("title", core["new"]).show();
            action = "new";
            personId = null;
        });

        var removeBtn = new Button({
            label: core.remove
        }, 'person-remove-btn');
        removeBtn.startup();
        removeBtn.on("click", function (event) {
            var markedForDeletion = query(".dgrid-row .remove-cb input:checked", "person-grid");
            if( markedForDeletion.length > 0 ) {
                lib.confirmAction(core.areyousure, function () {
                    markedForDeletion.forEach(function (node) {
                        var row = grid.row(node);
                        store.remove(row.data.name);
                    });
                });
            }
        });

        var saveBtn = new Button({
            label: core.save
        }, 'person-save-btn');
        saveBtn.startup();
        saveBtn.on("click", function (event) {
            var data;
            grid.clearSelection();
            if( personForm.validate() ) {
                data = person.getData();
                data.id = personId;
                grid.collection.put(data).then(function (data) {
                    personViewDialog.hide();
                    store.fetch();
                    grid.refresh();
                }, lib.xhrError);
            } else {
                lib.textError(core.invalid_form)
            }

        });

        var filterInput = new TextBox({placeHolder: core.filter}, "person-filter-input");
        filterInput.startup();

        var personForm = new Form({}, '[name="person"]');
        personForm.startup();

        var TrackableRest = declare([Rest, SimpleQuery, Trackable]);
        var store = new TrackableRest({target: '/api/people', useRangeHeaders: true, idProperty: 'id'});
        var grid;

        all([lib.getAddressTypes(), lib.getEmailTypes(), lib.getPersonTypes(), lib.getPhoneTypes()]).then(function (results) {
            grid = new (declare([OnDemandGrid, Selection, Editor]))({
                collection: store,
                className: "dgrid-autoheight",
                columns: {
                    id: {
                        label: core.id
                    },
                    name: {
                        label: core.person,
                        renderCell: libGrid.renderPerson
                    },
                    phones: {
                        label: core.phone_number,
                        renderCell: libGrid.renderPhone
                    },
                    emails: {
                        label: core.email,
                        renderCell: libGrid.renderEmail
                    },
                    addresses: {
                        label: core.address,
                        renderCell: libGrid.renderAddress
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
            }, 'person-grid');
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
                    grid.collection.get(id).then(function (pers) {
                        personId = pers.id;
                        action = "view";
                        person.setData(pers);
                        personViewDialog.show();
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
                        xhr("/api/persons/" + name, {
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

            on(dom.byId('person-grid-filter-form'), 'submit', function (event) {
                event.preventDefault();
                grid.set('collection', store.filter({
                    // Pass a RegExp to Memory's filter method
                    // Note: this code does not go out of its way to escape
                    // characters that have special meaning in RegExps
                    match: new RegExp(filterInput.get("value").replace(/\W/, ''), 'i')
                }));
            });

            var cbAll = new CheckBox({}, "cb-all");
            cbAll.startup();
            cbAll.on("click", function (event) {
                var state = this.checked;
                query(".dgrid-row .remove-cb", "person-grid").forEach(function (node) {
                    registry.findWidgets(node)[0].set("checked", state);
                });
            });
            urlParams = new URLSearchParams(window.location.search);
            if( urlParams.has("id") ) {
                id = urlParams.get("id");
                grid.collection.get(id).then(function (pers) {
                    personId = pers.id;
                    action = "view";
                    person.setData(pers);
                    personViewDialog.show();
                }, lib.xhrError);
            }
        });

        person.run();

        lib.pageReady();
    }
    return {
        run: run
    }
});