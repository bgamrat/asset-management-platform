define([
    "dojo/_base/declare",
    "dojo/request",
    "dojo/_base/array",
    "dojo/aspect",
    "dojo/dom",
    "dojo/on",
    "dijit/form/Form",
    "dijit/form/TextBox",
    "dijit/form/Select",
    'dojo/store/JsonRest',
    'dstore/Rest',
    'dstore/SimpleQuery',
    'dgrid/OnDemandGrid',
    'dgrid/Editor',
    'put-selector/put',
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/asset",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (declare, request, arrayUtil, aspect, dom, on,
        Form, TextBox, Select,
        JsonRest, Rest, SimpleQuery, OnDemandGrid, Editor, put,
        lib, core, asset) {

    function run(trailerId) {

        var filterInput = new TextBox({placeHolder: core.filter}, "trailer-filter-input");
        filterInput.startup();
        on(dom.byId('trailer-grid-filter-form'), 'submit', function (event) {
            event.preventDefault();
            grid.set('collection', store.filter({
                // Pass a RegExp to Memory's filter method
                // Note: this code does not go out of its way to escape
                // characters that have special meaning in RegExps
                match: new RegExp(filterInput.get("value").replace(/\W/, ''), 'i')
            }));
        });
        var RestStore = declare([Rest, SimpleQuery]);
        var store = new RestStore({target: "/api/store/trailercontents/" + trailerId, useRangeHeaders: true, idProperty: 'id'});
        var grid = new OnDemandGrid({
            collection: store,
            className: "dgrid-autoheight",
            sort: "category",
            columns: {
                id: {
                    label: core.id
                },
                category_text: {
                    label: asset.category
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
                    label: asset.location
                },
                description: {
                    label: core.description
                }
            },
            renderRow: function (object) {
                var rowElement = this.inherited(arguments);
                if( typeof object.deleted_at !== "undefined" && object.deleted_at !== null ) {
                    rowElement.classList.add('deleted');
                }
                if( typeof object.status_text !== "undefined" && object.status_text !== 'Operational' ) {
                    rowElement.classList.add(object.status_text.replace(/ /g,'-').toLowerCase());
                }
                return rowElement;
            },
            selectionMode: "none"
        }, 'trailer-grid');
        grid.startup();
        lib.pageReady();
    }
    return {
        run: run
    }
});