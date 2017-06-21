define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/form/ValidationTextBox",
    "dijit/form/CheckBox",
    "dijit/form/CurrencyTextBox",
    "dijit/form/DateTextBox",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on,
        query,
        ValidationTextBox, CheckBox, CurrencyTextBox, DateTextBox,
        lib, core) {
    //"use strict";

    var dataPrototype, prototypeNode, prototypeContent;
    var contractId = [], nameInput = [], commentInput = [], activeCheckBox = [];
    var startInput = [], endInput = [], valueInput = [];
    var divIdInUse = null;
    var addOneMoreControl = null;

    function getDivId() {
        return divIdInUse;
    }

    function setDivId(divId) {
        divIdInUse = divId + '_contracts';
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__contract__/g, nameInput.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
        contractId.push(null);
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
        dijit = new CheckBox({'checked': true}, base + "active");
        activeCheckBox.push(dijit);
        dijit = new DateTextBox({
            placeholder: core.start,
            trim: true,
            required: false
        }, base + "start");
        dijit.startup();
        startInput.push(dijit);
        dijit = new DateTextBox({
            placeholder: core.end,
            trim: true,
            required: false
        }, base + "end");
        dijit.startup();
        endInput.push(dijit);
        dijit.startup();
        dijit = new CurrencyTextBox({
            placeholder: core.value,
            trim: true,
            required: false
        }, base + "value");
        dijit.startup();
        valueInput.push(dijit);
    }

    function destroyRow(id, target) {
        var item;
        contractId.splice(id, 1);
        item = nameInput.splice(id, 1);
        item[0].destroyRecursive();
        item = commentInput.splice(id, 1);
        item[0].destroyRecursive();
        item = activeCheckBox.splice(id, 1);
        item[0].destroyRecursive();
        item = startInput.splice(id, 1);
        item[0].destroyRecursive();
        item = endInput.splice(id, 1);
        item[0].destroyRecursive();
        item = valueInput.splice(id, 1);
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
        prototypeContent = dataPrototype.replace(/__contract__/g, nameInput.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");

        createDijits();

        addOneMoreControl = query('.contracts .add-one-more-row');

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
            if( nameInput.length <= lib.constant.MAX_CONTRACTS ) {
                addOneMoreControl.removeClass("hidden");
            }
        });
    }

    function getData() {
        var i, returnData = [], st, en;
        for( i = 0; i < nameInput.length; i++ ) {
            if( nameInput[i].get('value') !== "" ) {
                st = startInput[i].get('value');
                en = endInput[i].get('value');
                returnData.push(
                        {
                            "id": contractId[i],
                            "name": nameInput[i].get('value'),
                            "comment": commentInput[i].get('value'),
                            "active": activeCheckBox[i].get("checked"),
                            "start": st === null ? "" : st,
                            "end": en === null ? "" : en,
                            "value": valueInput[i].get('value')
                        });
            }
        }
        return returnData.length > 0 ? returnData : null;
    }

    function setData(contracts) {
        var i, p, obj;

        query(".form-row.contract", prototypeNode.parentNode).forEach(function (node, index) {
            if( index !== 0 ) {
                destroyRow(index, node);
            }
        });

        if( typeof contracts === "object" && contracts !== null && contracts.length > 0 ) {

            for( i = 0; i < contracts.length; i++ ) {
                if( i !== 0 ) {
                    cloneNewNode();
                    createDijits();
                }
                obj = contracts[i];
                contractId[i] = obj.id;
                nameInput[i].set('value', obj.name);
                commentInput[i].set('value', obj.comment);
                activeCheckBox[i].set("checked", obj.active === true);
                startInput[i].set('value', obj.start);
                endInput[i].set('value', obj.end);
                valueInput[i].set('value', obj.value);
            }
        } else {
            contractId[0] = null;
            nameInput[0].set('value', "");
            commentInput[0].set('value', "");
            activeCheckBox[0].set('checked', true);
            startInput[0].set('value', "");
            endInput[0].set('value', "");
            valueInput[0].set('value', "");
        }
    }

    return {
        run: run,
        getData: getData,
        setData: setData
    }
});