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

    var dataPrototype, prototypeNode, prototypeContent;
    var viewType = [], viewPhoneNumber = [], viewComment = [];
    var divIdInUse = null;

    function setDivId(divId) {
        divIdInUse = divId + '_phone_numbers';
    }

    function getDivId() {
        return divIdInUse;
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__phone_number__/g, viewPhoneNumber.length);
        domConstruct.place(prototypeContent, prototypeNode, "after");
    }

    function createViewSpans() {
        var base = getDivId() + '_' + viewPhoneNumber.length + '_';
        viewType.push(dom.byId(base + "type"));
        viewPhoneNumber.push(dom.byId(base + "phone_number"));
        viewComment.push(dom.byId(base + "comment"));
    }

    function destroyRow(id, target) {
        viewType.pop().destroyRecursive();
        viewPhoneNumber.pop().destroyRecursive();
        viewComment.pop().destroyRecursive();
        domConstruct.destroy(target);
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
        prototypeContent = dataPrototype.replace(/__phone_number__/g, viewPhoneNumber.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");

        createViewSpans();
    }

    function setData(phoneNumbers) {
        var i, p, obj;

        query(".phone_numbers .view-inline", prototypeNode.parentNode).forEach(function (node, index) {
            if( index !== 0 ) {
                destroyRow(index, node);
            }
        });

        if( typeof phoneNumbers === "object" && phoneNumbers !== null && phoneNumbers.length > 0 ) {

            for( i = 0; i < phoneNumbers.length; i++ ) {
                if( i !== 0 ) {
                    cloneNewNode();
                    createViewSpans();
                }
                obj = phoneNumbers[i];
                html.set(viewType[i], obj.typetext);
                html.set(viewPhoneNumber[i], obj.phone_number);
                html.set(viewComment[i], obj.comment);
            }
        } else {
            html.set(viewType[0], '');
            html.set(viewPhoneNumber[0], "");
            html.set(viewComment[0], "");
        }
    }

    return {
        run: run,
        setData: setData
    }
}
);