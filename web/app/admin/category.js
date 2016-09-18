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
    var nameInput = [], commentInput = [], activeCheckBox = [];
    var divIdInUse = null;
    var addOneMoreControl = null;
    var divId = "categories_categories";

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__category__/g, nameInput.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits() {
        var dijit;
        var base = divId + '_' + nameInput.length + '_';
        dijit = new ValidationTextBox({
            placeholder: core.name,
            trim: true,
            pattern: "^[a-zA-Z0-9x\.\,\ \+\(\)-]{2,24}$",
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
        nameInput.pop().destroyRecursive();
        commentInput.pop().destroyRecursive();
        activeCheckBox.pop().destroyRecursive();
        domConstruct.destroy(target);
    }

    function run() {
        var base;

        prototypeNode = dom.byId(divId);

        if( prototypeNode === null ) {
            lib.textError(divId + " not found");
            return;
        }

        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__category__/g, nameInput.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
        base = prototypeNode.id + "_0_";

        createDijits();

        addOneMoreControl = query('.categories .add-one-more-row');

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
        
        var saveBtn = new Button({
            label: core.save,
            type: "submit"
        }, 'categories-save-btn');
        saveBtn.startup();
        //saveBtn.on("click", function (event) {});
        lib.pageReady();
    }

    function getData() {
        var i, returnData = [];
        for( i = 0; i < nameInput.length; i++ ) {
            returnData.push(
                    {
                        "name": nameInput[i].get('value'),
                        "comment": commentInput[i].get('value'),
                        "active": activeCheckBox[i].get('value')
                    });
        }
        return returnData;
    }

    function setData(categorys) {
        var i, obj;

        query(".form-row.category", prototypeNode.parentNode).forEach(function (node, index) {
            if( index !== 0 ) {
                destroyRow(index, node);
            }
        });

        if( typeof categorys === "object" && categorys !== null && categorys.length > 0 ) {

            for( i = 0; i < categorys.length; i++ ) {
                if( i !== 0 ) {
                    cloneNewNode();
                    createDijits();
                }
                obj = categorys[i];
                nameInput[i].set('value', obj.name);
                commentInput[i].set('value', obj.comment);
                activeCheckBox[i].set('value', obj.active);
            }
        } else {
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