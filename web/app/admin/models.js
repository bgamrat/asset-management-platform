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
    "dijit/form/Button",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (declare, lang, dom, domAttr, domConstruct, on,
        query,
        registry, TextBox, ValidationTextBox, CheckBox, Button,
        lib, core) {
    "use strict";

    var modelId = 0;
    var dataPrototype, prototypeNode, prototypeContent;
    var nameInput = [], commentInput = [], activeCheckBox = [];
    var divIdInUse = null;
    var addOneMoreControl = null;

    function getDivId() {
        return divIdInUse;
    }

    function setDivId(divId) {
        divIdInUse = divId + '_models';
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__model__/g, modelId);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits() {
        var base = getDivId() + '_' + modelId + '_';
        nameInput[modelId] = new ValidationTextBox({
            placeholder: core.name,
            trim: true,
            pattern: "^[a-zA-Z0-9x\.\,\ \+\(\)-]{2,24}$",
            required: true
        }, base + "name");
        nameInput[modelId].startup();
        commentInput[modelId] = new ValidationTextBox({
            placeholder: core.comment,
            trim: true,
            required: false
        }, base + "comment");
        commentInput[modelId].startup();
        activeCheckBox[modelId] = new CheckBox({}, base + "active");
        activeCheckBox[modelId].startup();
        modelId++;
    }

    function destroyRow(id, target) {
        nameInput.pop().destroyRecursive();
        commentInput.pop().destroyRecursive();
        activeCheckBox.pop().destroyRecursive();
        domConstruct.destroy(target);
        modelId--;
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
        prototypeContent = dataPrototype.replace(/__model__/g, modelId);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");

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
        for( i = 0; i < modelId; i++ ) {
            returnData.push(
                    {
                        "name": nameInput[i].get('value'),
                        "comment": commentInput[i].get('value'),
                        "active": activeCheckBox[i].get('value')
                    });
        }
        return returnData;
    }

    function setData(models) {
        var i, p, obj;

        query(".form-row.model", prototypeNode.parentNode).forEach(function (node, index) {
            if( index !== 0 ) {
                destroyRow(index, node);
            }
        });

        if( typeof models === "object" && models !== null && models.length > 0 ) {

            modelId = 1;
            for( i = 0; i < models.length; i++ ) {
                if( i !== 0 ) {
                    cloneNewNode();
                    createDijits();
                }
                obj = models[i];
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