define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/form/ValidationTextBox",
    "dijit/form/CheckBox",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on,
        query,
        ValidationTextBox, CheckBox,
        lib, core) {
    //"use strict";

    var dataPrototype, prototypeNode, prototypeContent;
    var brandId = [], nameInput = [], commentInput = [], activeCheckBox = [];
    var divIdInUse = null;
    var addOneMoreControl = null;

    function getDivId() {
        return divIdInUse;
    }

    function setDivId(divId) {
        divIdInUse = divId + '_brands';
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__brand__/g, nameInput.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
        brandId.push(null);
    }

    function createDijits() {
        var dijit;
        var base = getDivId() + '_' + nameInput.length + '_';
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
        var i, item;

        for( i = 0; i < brandId.length; i++ ) {
            if( brandId[i] === id ) {
                id = i;
                break;
            }
        }
        brandId.splice(id, 1);
        item = nameInput.splice(id, 1);
        item[0].destroyRecursive();
        item = commentInput.splice(id, 1);
        item[0].destroyRecursive();
        item = activeCheckBox.splice(id, 1);
        item[0].destroyRecursive();
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
        prototypeContent = dataPrototype.replace(/__brand__/g, nameInput.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");

        createDijits();

        addOneMoreControl = query('.brands .add-one-more-row');

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
            if (nameInput[i].get('value') !== "") {
                returnData.push(
                        {
                            "id": brandId[i],
                            "name": nameInput[i].get('value'),
                            "comment": commentInput[i].get('value'),
                            "active": activeCheckBox[i].get('value')
                        });
            }
        }
        return returnData.length > 0 ? returnData : null;
    }

    function setData(brands) {
        var i, p, obj;

        query(".form-row.brand", prototypeNode.parentNode).forEach(function (node, index) {
            if( index !== 0 ) {
                destroyRow(index, node);
            }
        });

        if( typeof brands === "object" && brands !== null && brands.length > 0 ) {

            for( i = 0; i < brands.length; i++ ) {
                if( i !== 0 ) {
                    cloneNewNode();
                    createDijits();
                }
                obj = brands[i];
                brandId[i] = obj.id;
                nameInput[i].set('value', obj.name);
                commentInput[i].set('value', obj.comment);
                activeCheckBox[i].set('value', obj.active);
            }
        } else {
            brandId[0] = null;
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