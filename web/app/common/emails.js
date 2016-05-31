define([
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dojo/data/ObjectStore",
    "dojo/store/Memory",
    "dijit/registry",
    "dijit/form/TextBox",
    "dijit/form/ValidationTextBox",
    "dijit/form/Select",
    "dijit/form/Button",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (declare, lang, dom, domAttr, domConstruct, on,
        query, ObjectStore, Memory,
        registry, TextBox, ValidationTextBox, Select, Button,
        lib, core) {
    "use strict";

    var emailId = 0;
    var dataPrototype;
    var prototypeNode, prototypeContent;
    var store;
    var typeSelect = [], emailInput = [], commentInput = [];
    var divIdInUse = null;
    var addOneMoreControl = null;

    function getDivId() {
        return divIdInUse;
    }

    function setDivId(divId) {
        divIdInUse = divId + '_emails';
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__email__/g, emailId);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits() {
        var base = prototypeNode.id + "_" + emailId + "_";
        typeSelect[emailId] = new Select({
            store: store,
            placeholder: core.type,
            required: true
        }, base + "type");
        typeSelect[emailId].startup();
        emailInput[emailId] = new ValidationTextBox({
            placeholder: core.email,
            required: false,
            pattern: "^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$",
            trim: true
        }, base + "email");
        emailInput[emailId].startup();
        commentInput[emailId] = new ValidationTextBox({
            placeholder: core.comment,
            trim: true,
            required: false
        }, base + "comment");
        commentInput[emailId].startup();
        emailId++;
    }
    
    function destroyRow(id, target) {
        typeSelect[id].destroyRecursive();
        typeSelect.splice(id, 1);
        emailInput[id].destroyRecursive();
        emailInput.splice(id, 1);
        commentInput[id].destroyRecursive();
        commentInput.splice(id, 1);
        domConstruct.destroy(target);
    }

    function run() {

        var base, select, data, storeData, d, memoryStore;
        if( arguments.length > 0 ) {
            setDivId(arguments[0]);
        }

        prototypeNode = dom.byId(getDivId());
        if (prototypeNode === null) {
            setDivId(arguments[0]+'_0');
            prototypeNode = dom.byId(getDivId());
        }
        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__email__/g, emailId);
        base = prototypeNode.id + "_" + emailId;
        select = base + "_type";
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
        data = JSON.parse(domAttr.get(select, "data-options"));
        // Convert the data to an array of objects
        storeData = [];
        storeData.push({value:"",label:core.type.toLowerCase()});
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
            if (typeSelect.length >= lib.constant.MAX_PHONE_NUMBERS) {
                addOneMoreControl.classList.add("hidden");
            }
        });

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var id = parseInt(target.id.replace(/\D/g, ''));
            destroyRow(id, targetParent);
            if (typeSelect.length <= lib.constant.MAX_PHONE_NUMBERS) {
                addOneMoreControl.classList.remove("hidden");
            }
        });
    }

    function getData() {
        var i, returnData = [];
        for( i = 0; i < emailId; i++ ) {
            returnData.push(
                    {
                        "type": typeSelect[i].get('value'),
                        "email": emailInput[i].get('value'),
                        "comment": commentInput[i].get('value')
                    });
        }
        return returnData;
    }

    function setData(emails) {
        var i, p, obj;

        query(".form-row.email", prototypeNode.parentNode).forEach(function (node, index) {
            if( index !== 0 ) {
                destroyRow(index, node);
            }
        });

        if( typeof emails === "object" && emails !== null && emails.length > 0 ) {

            i = 0;
            emailId = 1;
            for( p in emails ) {
                if( i !== 0 ) {
                    cloneNewNode();
                    createDijits();
                }
                obj = emails[p];
                typeSelect[i].set('value', obj.type);
                emailInput[i].set('value', obj.email);
                commentInput[i].set('value', obj.comment);
                i++;
            }
        } else {
            typeSelect[0].set('value', "");
            emailInput[0].set('value', "");
            commentInput[0].set('value', "");
        }
    }
    
    return {
        run: run,
        getData: getData,
        setData: setData
    }
}
);