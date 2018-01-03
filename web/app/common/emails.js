define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dojo/data/ObjectStore",
    "dojo/store/Memory",
    "dijit/form/ValidationTextBox",
    "dijit/form/Select",
    "dijit/form/Button",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on,
        query, ObjectStore, Memory,
        ValidationTextBox, Select, Button,
        lib, core) {
    "use strict";

    function run() {

        var base, select, data, storeData, d, memoryStore;
        var dataPrototype;
        var prototypeNode, prototypeContent;
        var store;
        var emailId = [], typeSelect = [], emailInput = [], commentInput = [];
        var divIdInUse = null, iteration = '0';
        var addOneMoreControl = null;

        function getDivId() {
            return divIdInUse;
        }

        function setDivId(divId) {
            divIdInUse = divId + '_emails';
        }

        function cloneNewNode() {
            prototypeContent = dataPrototype.replace(/__email__/g, emailInput.length);
            domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
        }

        function createDijits() {
            var dijit;
            var base = prototypeNode.id + "_" + emailInput.length + "_";
            emailId.push(null);
            dijit = new Select({
                store: store,
                placeholder: core.type,
                required: true
            }, base + "type");
            typeSelect.push(dijit);
            dijit.startup();
            dijit = new ValidationTextBox({
                placeholder: core.email,
                required: false,
                pattern: "[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?",
                trim: true
            }, base + "email");
            emailInput.push(dijit);
            dijit.startup();
            dijit = new ValidationTextBox({
                placeholder: core.comment,
                trim: true,
                required: false
            }, base + "comment");
            commentInput.push(dijit);
            dijit.startup();
        }

        function destroyRow(id, target) {
            var i, item;

            for( i = 0; i < emailId.length; i++ ) {
                if( emailId[i] === id ) {
                    id = i;
                    break;
                }
            }
            emailId.splice(id, 1);
            item = typeSelect.splice(id, 1);
            item[0].destroyRecursive();
            item = emailInput.splice(id, 1);
            item[0].destroyRecursive();
            item = commentInput.splice(id, 1);
            item[0].destroyRecursive();
            domConstruct.destroy(target);
        }

        if( arguments.length > 0 ) {
            setDivId(arguments[0]);
        }

        if( arguments.length > 1 ) {
            iteration = arguments[1];
        }

        prototypeNode = dom.byId(getDivId());
        if( prototypeNode === null ) {
            setDivId(arguments[0] + '_' + iteration);
            prototypeNode = dom.byId(getDivId());
        }

        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__email__/g, emailInput.length);
        base = prototypeNode.id + "_" + emailInput.length;
        select = base + "_type";
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
        data = JSON.parse(domAttr.get(select, "data-options"));
        // Convert the data to an array of objects
        storeData = [];

        for( d in data ) {
            storeData.push(data[d]);
        }
        memoryStore = new Memory({
            idProperty: "value",
            data: storeData});
        store = new ObjectStore({objectStore: memoryStore});

        createDijits();

        addOneMoreControl = query('.emails .add-one-more-row');

        addOneMoreControl.on("click", function (event) {
            cloneNewNode();
            createDijits();
            if( emailInput.length >= lib.constant.MAX_PHONE_NUMBERS ) {
                addOneMoreControl.addClass("hidden");
            }
        });

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var id = parseInt(targetParent.id.replace(/\D/g, ''));
            destroyRow(id, targetParent.parentNode);
            if( emailInput.length <= lib.constant.MAX_PHONE_NUMBERS ) {
                addOneMoreControl.removeClass("hidden");
            }
        });


        function getData() {
            var i, returnData = [], email;
            for( i = 0; i < emailInput.length; i++ ) {
                email = emailInput[i].get('value').trim();
                if( email !== "" ) {
                    returnData.push(
                            {
                                "id": emailId[i],
                                "type": parseInt(typeSelect[i].get('value')),
                                "email": email,
                                "comment": commentInput[i].get('value')
                            });
                }
            }
            return returnData;
        }
        function setData(emails) {
            var i, obj;

            query(".form-row.email",dom.byId(getDivId())).forEach(function (node, index) {
                if( index !== 0 ) {
                    destroyRow(index, node);
                }
            });

            if( typeof emails === "object" && emails !== null && emails.length > 0 ) {

                for( i = 0; i < emails.length; i++ ) {
                    if( i !== 0 ) {
                        cloneNewNode();
                        createDijits();
                    }
                    obj = emails[i];
                    emailId[i] = obj.id;
                    typeSelect[i].set('value', obj.type.id);
                    emailInput[i].set('value', obj.email);
                    commentInput[i].set('value', obj.comment);
                }
            } else {
                emailId[0] = null;
                typeSelect[0].set('value', "");
                emailInput[0].set('value', "");
                commentInput[0].set('value', "");
            }
        }
        function destroy(node) {
            var e;
            query(".form-row.email", node).forEach(function (node, index) {
                destroyRow(index, node);
            });
            e = query(".emails", node);
            domConstruct.destroy(e[0]);
        }
        return {
            setData: setData,
            getData: getData,
            destroy: destroy
        }
    }

    return {
        run: run
    }
}
);