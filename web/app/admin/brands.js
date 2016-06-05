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

    var brandId = 0;
    var dataPrototype, prototypeNode, prototypeContent;
    var nameInput = [], commentInput = [], activeCheckBox = [];
    var divIdInUse = null;
    var addOneMoreControl = null;

    function getDivId() {
        var q;
        if( divIdInUse !== null ) {
            q = divIdInUse;
        } else {
            q = query('[id$="brands"]');
            if( q.length > 0 ) {
                q = q[0];
            }
        }
        return q;
    }

    function setDivId(divId) {
        divIdInUse = divId + '_brands';
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__brand__/g, brandId);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits() {
        var base = getDivId() + '_' + brandId + '_';
        nameInput[brandId] = new ValidationTextBox({
            placeholder: core.name,
            trim: true,
            pattern: "^[a-zA-Z0-9x\.\,\ \+\(\)-]{2,24}$",
            required: true
        }, base + "name");
        nameInput[brandId].startup();
        commentInput[brandId] = new ValidationTextBox({
            placeholder: core.comment,
            trim: true,
            required: false
        }, base + "comment");
        commentInput[brandId].startup();
        activeCheckBox[brandId] = new CheckBox({}, base + "active");
        activeCheckBox[brandId].startup();
        brandId++;
    }

    function destroyRow(id, target) {
        nameInput[id].destroyRecursive();
        nameInput.splice(id, 1);
        commentInput[id].destroyRecursive();
        commentInput.splice(id, 1);
        activeCheckBox[id].destroyRecursive();
        activeCheckBox.splice(id, 1);
        domConstruct.destroy(target);
        brandId--;
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
        prototypeContent = dataPrototype.replace(/__brand__/g, brandId);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");

        createDijits();

        addOneMoreControl = query('.brands .add-one-more-row');

        addOneMoreControl.on("click", function (event) {
            cloneNewNode();
            createDijits();
            if( nameInput.length >= lib.constant.MAX_BRANDS ) {
                addOneMoreControl.classList.add("hidden");
            }
        });

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var id = parseInt(target.id.replace(/\D/g, ''));
            destroyRow(id, targetParent);
            if( nameInput.length <= lib.constant.MAX_BRANDS ) {
                addOneMoreControl.classList.remove("hidden");
            }
        });
    }

    function getData() {
        var i, returnData = [];
        for( i = 0; i < brandId; i++ ) {
            returnData.push(
                    {
                        "name": nameInput[i].get('value'),
                        "comment": commentInput[i].get('value'),
                        "active": activeCheckBox[i].get('value')
                    });
        }
        return returnData;
    }

    function setData(brands) {
        var i, p, obj;

        query(".form-row.brand-number", prototypeNode.parentNode).forEach(function (node, index) {
            if( index !== 0 ) {
                destroyRow(index, node);
            }
        });

        if( typeof brands === "object" && brands !== null && brands.length > 0 ) {

            brandId = 1;
            for( i = 0; i < brands.length; i++ ) {
                if( i !== 0 ) {
                    cloneNewNode();
                    createDijits();
                }
                obj = brands[i];
                nameInput[i].set('value', obj.name);
                commentInput[i].set('value', obj.comment);
                activeCheckBox[i].set('value', obj.active);
            }
        } else {
            nameInput[0].set('value', "");
            commentInput[0].set('value', "");
            activeCheckBox[i].set('value', "");
        }
    }

    return {
        run: run,
        getData: getData,
        setData: setData
    }
});