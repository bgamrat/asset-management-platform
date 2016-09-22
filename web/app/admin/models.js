define([
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/registry",
    "dijit/form/TextBox",
    "dijit/form/ValidationTextBox",
    "dijit/form/CheckBox",
    "dijit/form/Select",
    "dojo/data/ObjectStore",
    "dojo/store/Memory",
    "dijit/form/Button",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/asset",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (declare, lang, dom, domAttr, domConstruct, on,
        query,
        registry, TextBox, ValidationTextBox, CheckBox, Select, ObjectStore, Memory, Button,
        lib, core, asset) {
    "use strict";

    var dataPrototype, prototypeNode, prototypeContent;
    var categorySelect = [], nameInput = [], commentInput = [], activeCheckBox = [];
    var store;
    var divIdInUse = null;
    var addOneMoreControl = null;

    function getDivId() {
        return divIdInUse;
    }

    function setDivId(divId) {
        divIdInUse = divId + '_models';
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__model__/g, nameInput.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits() {
        var dijit;
        var base = getDivId() + '_' + nameInput.length + '_';
        dijit = new Select({
            store: store,
            placeholder: asset.category,
            required: true
        }, base + "category");
        dijit.startup();
        categorySelect.push(dijit);
        dijit = new ValidationTextBox({
            placeholder: core.name,
            trim: true,
            pattern: "[a-zA-Z0-9x\.\,\ \+\(\)-]{2,24}",
            required: true
        }, base + "name");
        nameInput.push(dijit);
        dijit.startup();
        dijit = new ValidationTextBox({
            placeholder: core.comment,
            trim: true,
            required: false
        }, base + "comment");
        commentInput.push(dijit);
        dijit.startup();
        dijit = new CheckBox({}, base + "active");
        activeCheckBox.push(dijit);
        dijit.startup();
    }

    function destroyRow(id, target) {
        categorySelect.pop().destroyRecursive();
        nameInput.pop().destroyRecursive();
        commentInput.pop().destroyRecursive();
        activeCheckBox.pop().destroyRecursive();
        domConstruct.destroy(target);
    }

    function run() {
        var base, select, d, data, storeData, memoryStore;

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
        prototypeContent = dataPrototype.replace(/__model__/g, nameInput.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
        base = prototypeNode.id + "_0_";

        select = base + "category";

        if( dom.byId(select) === null ) {
            lib.textError(select + " not found");
            return;
        }

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

        addOneMoreControl = query('.models .add-one-more-row');

        addOneMoreControl.on("click", function (event) {
            cloneNewNode();
            createDijits();
            if( nameInput.length >= lib.constant.MAX_BRANDS ) {
                addOneMoreControl.addClass("hidden");
            }
        });

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var id = parseInt(targetParent.id.replace(/\D/g, ''));
            destroyRow(id, targetParent.parentNode);
            if( nameInput.length <= lib.constant.MAX_BRANDS ) {
                addOneMoreControl.removeClass("hidden");
            }
        });
    }

    function getData() {
        var i, returnData = [];
        for( i = 0; i < nameInput.length; i++ ) {
            returnData.push(
                    {
                        "category": categorySelect[i].get('value'),
                        "name": nameInput[i].get('value'),
                        "comment": commentInput[i].get('value'),
                        "active": activeCheckBox[i].get('value')
                    });
        }
        return returnData;
    }

    function setData(models) {
        var i, obj;

        query(".form-row.model", prototypeNode.parentNode).forEach(function (node, index) {
            if( index !== 0 ) {
                destroyRow(index, node);
            }
        });

        if( typeof models === "object" && models !== null && models.length > 0 ) {

            for( i = 0; i < models.length; i++ ) {
                if( i !== 0 ) {
                    cloneNewNode();
                    createDijits();
                }
                obj = models[i];
                categorySelect[i].set('value', obj.category);
                nameInput[i].set('value', obj.name);
                commentInput[i].set('value', obj.comment);
                activeCheckBox[i].set('value', obj.active);
            }
        } else {
            categorySelect[i].set('value', '');
            nameInput[0].set('value', "");
            commentInput[0].set('value', "");
            activeCheckBox[0].set('value', "");
        }
    }

    return {
        run: run,
        getData: getData,
        setData: setData
    }
});