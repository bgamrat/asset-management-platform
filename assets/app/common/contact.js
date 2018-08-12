define([
    "dojo/_base/lang",
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/registry",
    "dojo/request/xhr",
    "dijit/form/FilteringSelect",
    "dojo/store/JsonRest",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (lang, dom, domAttr, domConstruct, on, query, registry, xhr,
        FilteringSelect, JsonRest,
        lib, core) {

    function run() {

        "use strict";
        var divIdInUse = "contact";
        var nameSelect = [];
        var dataPrototype;
        var prototypeNode, prototypeContent;
        var personId = [];
        var base;
        var addOneMoreControl;
        var contactStore;

        function setDivId(divId) {
            divIdInUse = divId;
        }

        function getDivId() {
            return divIdInUse;
        }

        function cloneNewNode() {
            prototypeContent = dataPrototype.replace(/__person__/g, personId.length);
            domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
        }

        function createDijit() {
            var dijit, base = getDivId();
            var idNumber = personId.length;
            if( prototypeNode !== null ) {
                base += "_"+idNumber;
            }
            personId.push(null);
            dijit = new FilteringSelect({
                required: true,
                "class": "name",
                store: contactStore,
                searchAttr: "name",
                placeholder: core.lastname
            }, base);
            dijit.startup();
            dijit.on("change", loadContact);
            nameSelect.push(dijit);
        }

        function setContactValues(obj, i) {
            var template, phones = "", emails = "", addresses = "", j, k, l, m, base, d;
            var addressProps;
            personId[i] = obj.id;
            nameSelect[i].set("displayedValue", obj.firstname + " " + obj.lastname);
            if (obj.emails.length !== 0) {
                l = obj.emails.length;
                for (j = 0; j < l; j++) {
                    emails += obj.emails[i].email.replace(/ (.*@.*)$|<br>/g,' <a href="mailto:$1">$1</a><br>');
                }
            }
            if (obj.phones.length !== 0) {
                l = obj.phones.length;
                for (j = 0; j < l; j++) {
                    phones += obj.phones[i].phone + "<br>";
                }
            }
            l = obj.addresses.length;
            addressProps = ["street1","street2","city","state_province","country"];
            m = addressProps.length;
            for (j = 0; j < l; j++) {
                addresses += obj.addresses[j].type.type + "<br>";
                for (k = 0; k < m; k++) {
                    if (typeof obj.addresses[j][addressProps[k]]!== "undefined") {
                        addresses += obj.addresses[j][addressProps[k]] + "<br>";
                    }
                }
            }
            template =
`
<div class="view-contact-details justify">
<span class="phones">
${phones}
</span>
<span class="emails">
${emails}
</span>
<span class="addresses">
${addresses}
</span>
</div>
`;
            base = query("#"+getDivId() + "_" + i).closest(".form-row.contact");
            d = query(".view-contact-details",base[0]);
            domConstruct.destroy(d[0]);
            domConstruct.place(domConstruct.toDom(template),base[0],"last");
        }

        function loadContact(evt) {
            var item = this.get("item");
            var id, idx;
            if( item === null || evt === null ) {
                return;
            }
            id = item.id;
            idx = this.id.replace(/\D/g, "");
            xhr.get("/api/people/" + id, {
                handleAs: "json"
            }).then(function (data) {
                var i, l, kid, cp;
                l = nameSelect.length;
                for( i = 0; i < l; i++ ) {
                    kid = nameSelect[i].id.replace(/\D/g, "");
                    if( kid == idx ) {
                        setContactValues(data, i, false);
                        break;
                    }
                }
            });
        }

        function destroyRow(id, target) {
            var i, l, item, kid, d;
            l = nameSelect.length;
            for( i = 0; i < l; i++ ) {
                kid = nameSelect[i].id.replace(/\D/g, "");
                if( kid == id ) {
                    id = i;
                    break;
                }
            }
            personId.splice(id, 1);
            item = nameSelect.splice(id, 1);
            item[0].destroyRecursive();
            d = query(".contact-details",target);
            domConstruct.destroy(d[0]);
            domConstruct.destroy(target);
        }

        if( arguments.length > 0 ) {
            setDivId(arguments[0]);
        }

        base = getDivId();

        prototypeNode = dom.byId(getDivId());
        if( prototypeNode !== null ) {
            dataPrototype = domAttr.get(prototypeNode, "data-prototype");
            if( dataPrototype !== null ) {
                cloneNewNode();
                base += "_0";
            } else {
                prototypeNode = null;
            }
        }

        contactStore = new JsonRest({
            target: "/api/store/people?",
            useRangeHeaders: false,
            idProperty: "id"});

        createDijit();

        addOneMoreControl = query(".contacts .add-one-more-row");
        if( addOneMoreControl.length > 0 ) {
            addOneMoreControl.on("click", function (event) {
                cloneNewNode();
                createDijit();
                if( personId.length >= lib.constant.MAX_CONTACTS ) {
                    addOneMoreControl.addClass("hidden");
                }
            });
        }

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var id = parseInt(targetParent.id.replace(/\D/g, ""));
            destroyRow(id, targetParent.parentNode);
            if( nameSelect.length <= lib.constant.MAX_PHONE_NUMBERS ) {
                addOneMoreControl.removeClass("hidden");
            }
        });

        function getData() {
            var i, returnData = [];
            for( i = 0; i < personId.length; i++ ) {
                if( nameSelect[i].get("value") !== "" ) {
                    returnData.push( personId[i] );
                }
            }
            return returnData.length > 0 ? returnData : null;
        }
        function setData(contact) {
            var i, p, obj, nodes, d;

            nodes = query(".form-row.contact");
            nodes.forEach(function (node, index) {
                if( index !== 0 ) {
                    destroyRow(index, node);
                }
            });

            if( typeof contact === "object" && contact !== null ) {
                if( !contact.hasOwnProperty("length") ) {
                    contact = [contact];
                }
                for( i = 0; i < contact.length; i++ ) {
                    if( i !== 0 ) {
                        cloneNewNode();
                        createDijit();
                    }
                    obj = contact[i];
                    nameSelect[i].set("displayedValue",obj.firstname + " " + obj.lastname);
                }
            } else {
                personId[0] = null;
                nameSelect[0].set("value", "");
                base = query("#"+getDivId() + "_0").closest(".form-row.contact");
                d = query(".view-contact-details",base[0]);
                domConstruct.destroy(d[0]);
            }
        }
        return {
            getData: getData,
            setData: setData
        };

    }
    return {
        run: run
    };
}
);