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
    var countryStore, typeStore;
    var viewType = [], viewEmail = [], viewComment = [];
    var viewComment = [];
    var divIdInUse = null;
    var addOneMoreControl = null;

    function setDivId(divId) {
        divIdInUse = divId + '_emails';
    }

    function getDivId() {
        return divIdInUse;
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__email__/g, viewEmail.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createViewSpans() {
        var base = getDivId() + '_' + viewEmail.length + '_';
        viewType.push(dom.byId(base + "type"));
        viewEmail.push(dom.byId(base + "email"));
        viewComment.push(dom.byId(base + "comment"));
    }

    function destroyRow(id, target) {
        viewType.pop().destroyRecursive();
        viewEmail.pop().destroyRecursive();
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
        prototypeContent = dataPrototype.replace(/__email__/g, viewEmail.length);
        domConstruct.place(prototypeContent, prototypeNode, "after");

        createViewSpans();
    }

    function setData(emails) {
        var i, p, obj;

        query(".emails .view-inline", prototypeNode.parentNode).forEach(function (node, index) {
            if( index !== 0 ) {
                destroyRow(index, node);
            }
        });

        if( typeof emails === "object" && emails !== null && emails.length > 0 ) {

            for( i = 0; i < emails.length; i++ ) {
                if( i !== 0 ) {
                    cloneNewNode();
                    createViewSpans();
                }
                obj = emails[i];
                html.set(viewType[i], obj.typetext);
                html.set(viewEmail[i], obj.email);
                html.set(viewComment[i], obj.comment);
            }
        } else {
            html.set(viewType[0], '');
            html.set(viewEmail[0], "");
            html.set(viewComment[0], "");
        }
    }

    return {
        run: run,
        setData: setData
    }
}
);