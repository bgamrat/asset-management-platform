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
    "dijit/form/SimpleTextarea",
    "dijit/form/Select",
    "dijit/form/Button",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (declare, lang, dom, domAttr, domConstruct, on,
        query, ObjectStore, Memory,
        registry, TextBox, ValidationTextBox, SimpleTextarea, Select, Button,
        lib, core) {
    "use strict";

    var addressId = 0;
    var dataPrototype, prototypeNode, prototypeContent;
    var countryStore, typeStore;
    var typeSelect = [];
    var street1Input = [], street2Input = [], cityInput = [];
    var stateProvinceInput = [], postalCodeInput = [], countrySelect = [];
    var commentInput = [];
    var divIdInUse = null;
    var addOneMoreControl = null;

    function setDivId(divId) {
        divIdInUse = divId + '_addresses';
    }

    function getDivId() {
        return divIdInUse;
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__address__/g, addressId);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits() {
        var base = getDivId() + '_' + addressId + '_';
        typeSelect[addressId] = new Select({
            store: typeStore,
            required: true
        }, base + "type");
        typeSelect[addressId].startup();
        street1Input[addressId] = new ValidationTextBox({
            trim: true,
            required: false,
            placeholder: core.street
        }, base + "street1");
        street1Input[addressId].startup();
        street2Input[addressId] = new ValidationTextBox({
            trim: true,
            required: false
        }, base + "street2");
        street2Input[addressId].startup();
        cityInput[addressId] = new ValidationTextBox({
            trim: true,
            required: true,
            placeholder: core.city
        }, base + "city");
        cityInput[addressId].startup();
        stateProvinceInput[addressId] = new ValidationTextBox({
            trim: true,
            required: true,
            uppercase: true,
            maxLength: 2,
            placeholder: core.state_province
        }, base + "state_province");
        stateProvinceInput[addressId].startup();
        postalCodeInput[addressId] = new ValidationTextBox({
            trim: true,
            pattern: "^[0-9A-Z-]{2,12}$",
            required: false,
            uppercase: true,
            placeholder: core.postal_code
        }, base + "postal_code");
        postalCodeInput[addressId].startup();
        countrySelect[addressId] = new Select({
            store: countryStore,
            required: true
        }, base + "country");
        countrySelect[addressId].set('value', 'US');
        countrySelect[addressId].set('displayedValue', 'United States');
        countrySelect[addressId].startup();
        commentInput[addressId] = new SimpleTextarea({
            placeholder: core.comment,
            trim: true,
            required: false
        }, base + "comment");
        commentInput[addressId].startup();
        addressId++;
    }

    function destroyRow(id, target) {
        typeSelect[id].destroyRecursive();
        typeSelect.splice(id, 1);
        street1Input[id].destroyRecursive();
        street1Input.splice(id, 1);
        street2Input[id].destroyRecursive();
        street2Input.splice(id, 1);
        cityInput[id].destroyRecursive();
        cityInput.splice(id, 1);
        stateProvinceInput[id].destroyRecursive();
        stateProvinceInput.splice(id, 1);
        postalCodeInput[id].destroyRecursive();
        postalCodeInput.splice(id, 1);
        countrySelect[id].destroyRecursive();
        countrySelect.splice(id, 1);
        commentInput[id].destroyRecursive();
        commentInput.splice(id, 1);
        domConstruct.destroy(target);
        addressId--;
    }

    function run() {

        var base, select, data, d;
        var countryStoreData, countryMemoryStore;
        var typeStoreData, typeMemoryStore;

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
        prototypeContent = dataPrototype.replace(/__address__/g, addressId);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");

        base = prototypeNode.id + "_" + addressId;
        select = base + "_type";

        if( dom.byId(select) === null ) {
            lib.textError(select + " not found");
            return;
        }

        data = JSON.parse(domAttr.get(select, "data-options"));
        // Convert the data to an array of objects
        typeStoreData = [];
        typeStoreData.push({value: "", label: core.type.toLowerCase()});
        for( d in data ) {
            typeStoreData.push(data[d]);
        }
        typeMemoryStore = new Memory({
            idProperty: "value",
            data: typeStoreData});
        typeStore = new ObjectStore({objectStore: typeMemoryStore});

        select = base + "_country";
        data = JSON.parse(domAttr.get(select, "data-options"));
        // Convert the data to an array of objects
        countryStoreData = [];
        countryStoreData.push({value: "", label: core.country.toLowerCase()});
        for( d = 0; d < data.length; d++ ) {
            countryStoreData.push(data[d]);
        }
        countryMemoryStore = new Memory({
            idProperty: "value",
            data: countryStoreData});
        countryStore = new ObjectStore({objectStore: countryMemoryStore});

        createDijits();

        addOneMoreControl = query('.addresses .add-one-more-row', getDivId());

        addOneMoreControl.on("click", function (event) {
            cloneNewNode();
            createDijits();
            if( countrySelect.length >= lib.constant.MAX_ADDRESSES ) {
                addOneMoreControl.classList.add("hidden");
            }
        });

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var id = parseInt(target.id.replace(/\D/g, ''));
            destroyRow(id, targetParent);
            if( countrySelect.length <= lib.constant.MAX_ADDRESSES ) {
                addOneMoreControl.classList.remove("hidden");
            }
        });
    }

    function getData() {
        var i, returnData = [];
        for( i = 0; i < addressId; i++ ) {
            returnData.push(
                    {
                        "type": typeSelect[i].get('value'),
                        "street1": street1Input[i].get('value'),
                        "street2": street2Input[i].get('value'),
                        "city": cityInput[i].get('value'),
                        "state_province": stateProvinceInput[i].get('value'),
                        "postal_code": postalCodeInput[i].get('value'),
                        "country": countrySelect[i].get('value'),
                        "comment": commentInput[i].get('value')
                    });
        }
        return returnData;
    }


    function setData(addresses) {
        var i, p, obj;

        query(".form-row.address", prototypeNode.parentNode).forEach(function (node, index) {
            if( index !== 0 ) {
                destroyRow(index, node);
            }
        });

        if( typeof addresses === "object" && addresses !== null && addresses.length > 0 ) {

            addressId = 1;
            for( i = 0; i < addresses.length; i++ ) {
                if( i !== 0 ) {
                    cloneNewNode();
                    createDijits();
                }
                obj = addresses[i];
                typeSelect[i].set('value', obj.type);
                street1Input[i].set('value', obj.street1);
                street2Input[i].set('value', obj.street2);
                cityInput[i].set('value', obj.city);
                stateProvinceInput[i].set('value', obj.state_province);
                postalCodeInput[i].set('value', obj.postal_code);
                countrySelect[i].set('value', obj.country);
                commentInput[i].set('value', obj.comment);
            }
        } else {
            typeSelect[0].set('value', '');
            street1Input[0].set('value', "");
            street2Input[0].set('value', "");
            cityInput[0].set('value', "");
            stateProvinceInput[0].set('value', "");
            postalCodeInput[0].set('value', "");
            countrySelect[0].set('value', "US");
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