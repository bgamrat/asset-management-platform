define([
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/html",
    "dojo/on",
    "dojo/query",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/domReady!"
], function (declare, lang, dom, domAttr, domConstruct, html, on, query,
        lib, core) {

    "use strict";

    var addressId = 0;
    var dataPrototype, prototypeNode, prototypeContent;
    var viewType = [];
    var viewStreet1 = [], viewStreet2 = [], viewCity = [];
    var viewStateProvince = [], viewPostalCode = [], viewCountry = [];
    var viewComment = [];
    var divIdInUse = null;

    function setDivId(divId) {
        divIdInUse = divId + '_addresses';
    }

    function getDivId() {
        return divIdInUse;
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__address__/g, addressId);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createViewSpans() {
        var base = getDivId() + '_' + addressId + '_';
        viewType[addressId] = dom.byId(base + "type");
        viewStreet1[addressId] = dom.byId(base + "street1");
        viewStreet2[addressId] = dom.byId(base + "street2");
        viewCity[addressId] = dom.byId(base + "city");
        viewStateProvince[addressId] = dom.byId(base + "state_province");
        viewPostalCode[addressId] = dom.byId(base + "postal_code");
        viewCountry[addressId] = dom.byId(base + "country");
        viewComment[addressId] = dom.byId(base + "comment");
        addressId++;
    }

    function destroyRow(id, target) {
        viewType[id].destroyRecursive();
        viewType.splice(id, 1);
        viewStreet1[id].destroyRecursive();
        viewStreet1.splice(id, 1);
        viewStreet2[id].destroyRecursive();
        viewStreet2.splice(id, 1);
        viewCity[id].destroyRecursive();
        viewCity.splice(id, 1);
        viewStateProvince[id].destroyRecursive();
        viewStateProvince.splice(id, 1);
        viewPostalCode[id].destroyRecursive();
        viewPostalCode.splice(id, 1);
        viewCountry[id].destroyRecursive();
        viewCountry.splice(id, 1);
        viewComment[id].destroyRecursive();
        viewComment.splice(id, 1);
        domConstruct.destroy(target);
        addressId--;
    }

    function run() {

        if( arguments.length > 0 ) {
            setDivId(arguments[0]);
        }

        prototypeNode = dom.byId(getDivId());
        if( prototypeNode === null ) {
            setDivId(arguments[0] + '_0');
            prototypeNode = dom.byId(getDivId());
        }

        if( prototypeNode === null ) {
            lib.textError(getDivId() + " not found");
            return;
        }

        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__address__/g, addressId);
        domConstruct.place(prototypeContent, prototypeNode, "after");

        createViewSpans();

    }

    function setData(addresses) {
        var i, p, obj;

        query(".addresses .view-block", prototypeNode.parentNode).forEach(function (node, index) {
            if( index !== 0 ) {
                destroyRow(index, node);
            }
        });

        if( typeof addresses === "object" && addresses !== null && addresses.length > 0 ) {

            addressId = 1;
            for( i = 0; i < addresses.length; i++ ) {
                if( i !== 0 ) {
                    cloneNewNode();
                    createViewSpans();
                }
                obj = addresses[i];
                html.set(viewType[i], obj.typetext);
                html.set(viewStreet1[i], obj.street1);
                html.set(viewStreet2[i], obj.street2);
                html.set(viewCity[i], obj.city);
                html.set(viewStateProvince[i], obj.state_province);
                html.set(viewPostalCode[i], obj.postal_code);
                html.set(viewCountry[i], obj.country);
                html.set(viewComment[i], obj.comment);
            }
        } else {
            html.set(viewType[0], '');
            html.set(viewStreet1[0], "");
            html.set(viewStreet2[0], "");
            html.set(viewCity[0], "");
            html.set(viewStateProvince[0], "");
            html.set(viewPostalCode[0], "");
            html.set(viewCountry[0], "");
            html.set(viewComment[0], "");
        }
    }

    return {
        run: run,
        setData: setData
    }
}
);