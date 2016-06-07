define([
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/html",
    "dojo/on",
    "dojo/request/xhr",
    "dojo/json",
    "dojo/query",
    "dijit/form/TextBox",
    "dijit/Dialog",
    'dstore/Rest',
    'dstore/SimpleQuery',
    'dstore/Trackable',
    'dgrid/OnDemandGrid',
    "dgrid/Selection",
    //"app/admin-view-only/person",
    "app/lib/common",
    "app/lib/grid",
    "dojo/i18n!app/nls/core",
    "dojo/domReady!"
], function (declare, lang, dom, domAttr, domConstruct, html,
        on, xhr, json, query,
        TextBox, Dialog,
        Rest, SimpleQuery, Trackable, OnDemandGrid, Selection,
        //person,
        lib, libGrid, core) {
    function run() {

        var userViewDialog = new Dialog({
            title: core.view
        }, "user-view-dialog");
        userViewDialog.startup();
        userViewDialog.on("cancel", function (event) {
            grid.clearSelection();
        });
        
        var viewUsername = dom.byId("view-username");
        var viewEmail = dom.byId("view-email");
        var viewEnabled = dom.byId("view-enabled");
        var viewLocked = dom.byId("view-locked");

        var filterInput = new TextBox({placeHolder: core.filter}, "user-filter-input");
        filterInput.startup();

        var TrackableRest = declare([Rest, SimpleQuery, Trackable]);
        var store = new TrackableRest({target: '/api/users', useRangeHeaders: true, idProperty: 'username'});
        var grid = new (declare([OnDemandGrid, Selection]))({
            collection: store,
            className: "dgrid-autoheight",
            columns: {
                username: {
                    label: core.username
                },
                email: {
                    label: core.email
                },
                enabled: {
                    label: core.enabled,
                    sortable: false,
                    renderCell: libGrid.renderGridCheckbox
                },
                locked: {
                    label: core.locked,
                    sortable: false,
                    renderCell: libGrid.renderGridCheckbox
                },
            },
            selectionMode: "none"
        }, 'user-grid');
        grid.startup();
        grid.collection.track();

        grid.on(".dgrid-row:click", function (event) {
            var checkBoxes = ["enabled", "locked"];
            var row = grid.row(event);
            var cell = grid.cell(event);
            var field = cell.column.field;
            var username = row.data.username;
            if( checkBoxes.indexOf(field) === -1 ) {
                if( typeof grid.selection[0] !== "undefined" ) {
                    grid.clearSelection();
                }
                grid.select(row);
                grid.collection.get(username).then(function (user) {
                    html.set( viewUsername, user.username);
                    html.set( viewEmail,user.email);
                    html.set(viewEnabled,user.enabled ? core.yes : core.no );
                    html.set(viewEnabled,user.locked ? core.yes : core.no );
                    //person.setData(user.person);
                    userViewDialog.show();
                }, lib.xhrError);
            }
        });

        on(dom.byId('user-grid-filter-form'), 'submit', function (event) {
            event.preventDefault();
            grid.set('collection', store.filter({
                // Pass a RegExp to Memory's filter method
                // Note: this code does not go out of its way to escape
                // characters that have special meaning in RegExps
                match: new RegExp(filterInput.get("value").replace(/\W/, ''), 'i')
            }));
        });

        //person.run('user');
        lib.pageReady();
    }
    return {
        run: run
    }
});