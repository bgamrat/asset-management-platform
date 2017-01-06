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

    function run() {

      
        lib.pageReady();
    }
    return {
        run: run
    }
});