define([
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/html",
    "dojo/on",
    "app/admin/view-only/emails",
    "app/admin/view-only/phoneNumbers",
    "app/admin/view-only/addresses",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/domReady!"
], function (declare, lang, dom, domAttr, domConstruct, html, on,
        emails, phoneNumbers, addresses,
        lib, core) {

    "use strict";
    var divIdInUse = "user_person";
    var viewFullName;
    var viewType, viewComment;
    var dataPrototype;
    var prototypeNode, prototypeContent;
    var personId = 0;

    function setDivId(divId) {
        divIdInUse = divId + '_person';
    }

    function getDivId() {
        return divIdInUse;
    }

    function createViewSpans() {
        var base = getDivId() + '_';
        if( prototypeNode !== null ) {
            base += personId + '_';
        }
        viewType = dom.byId(base + "type");
        viewFullName = dom.byId(base + "fullname");
        viewComment = dom.byId(base + "comment");
    }

    function run() {
        if( arguments.length > 0 ) {
            setDivId(arguments[0]);
        }

        prototypeNode = dom.byId(getDivId());
        if( prototypeNode !== null ) {
            dataPrototype = domAttr.get(prototypeNode, "data-prototype");
            prototypeContent = dataPrototype.replace(/__person__/g, personId);
            domConstruct.place(prototypeContent, prototypeNode, "after");
        }

        createViewSpans();
        
        phoneNumbers.run(getDivId());
        emails.run(getDivId());
        addresses.run(getDivId());
    }

    function setData(person) {
        if( typeof person === "object" ) {
            if( person === null ) {
                person = {};
            }
            person = lang.mixin({firstname: '', middleinitial: '', lastname: ''}, person);
            
            html.set(viewType, person.typetext);
            html.set(viewFullName, person.fullname);
            html.set(viewComment, person.comment);

            if( typeof person.phone_numbers !== "undefined" ) {
                phoneNumbers.setData(person.phone_numbers);
            } else {
                phoneNumbers.setData(null);
            }
            if( typeof person.emails !== "undefined" ) {
                emails.setData(person.emails);
            } else {
                emails.setData(null);
            }
            if( typeof person.addresses !== "undefined" ) {
                addresses.setData(person.addresses);
            } else {
                addresses.setData(null);
            }
        } else {
            html.set(viewType, "");
            html.set(viewFullName, "");
            html.set(viewComment, "");
            phoneNumbers.setData(null);
            emails.setData(null);
            addresses.setData(null);
        }
    }
    return {
        run: run,
        setData: setData
    }
}
);