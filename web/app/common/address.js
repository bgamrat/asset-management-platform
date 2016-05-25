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

    var addressId = 0;
    var dataPrototype;
    var prototypeNode, prototypeContent;
    var base, select, countryStore, typeStore;
    var typeSelect = [];
    var street1Input = [], street2Input = [], cityInput = [];
    var stateProvinceInput = [], postalCodeInput = [], countrySelect = [];
    var divIdInUse = null;
    var addOneMoreControl = null;

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
            required: true,
            value: 'US'
        }, base + "country");
        countrySelect[addressId].startup();
        addressId++;
    }

    function run() {

        var base, select, data, d;
        var countryStoreData, countryMemoryStore;
        var typeStoreData, typeMemoryStore;
        if( arguments.length > 0 ) {
            setDivId(arguments[0]);
        }

        prototypeNode = dom.byId(getDivId());
        if (prototypeNode === null) {
            setDivId(arguments[0]+'_0');
            prototypeNode = dom.byId(getDivId());
        }
        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__address__/g, addressId);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
        
        base = prototypeNode.id + "_" + addressId;
        select = base + "_type";
        data = JSON.parse(domAttr.get(select, "data-options"));
        // Convert the data to an array of objects
        typeStoreData = [];
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
        for( d in data ) {
            countryStoreData.push(data[d]);
        }
        countryMemoryStore = new Memory({
            idProperty: "value",
            data: countryStoreData});
        countryStore = new ObjectStore({objectStore: countryMemoryStore});

        query('[id$="address"]').forEach(function (node, index) {
            if( index !== 0 ) {
                cloneNewNode();
            }
            createDijits();
        });

        addOneMoreControl = query('.addresses .add-one-more-row');

        addOneMoreControl.on("click", function (event) {
            var target = event.target.parentNode;
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
        for( i in countrySelect ) {
            returnData.push(
                    {
                        "street1": stateProvinceInput[i].get('value'),
                        "street2": stateProvinceInput[i].get('value'),
                        "city": stateProvinceInput[i].get('value'),
                        "state_province": stateProvinceInput[i].get('value'),
                        "postal_code": postalCodeInput[i].get('value'),
                        "country": countrySelect[i].get('value')
                    });
        }
        return returnData;
    }

    function getDivId() {
        var q;
        if( divIdInUse !== null ) {
            q = divIdInUse;
        } else {
            q = query('[id$="addresses"]');
            if( q.length > 0 ) {
                q = q[0];
            }
        }
        return q;
    }

    function setDivId(divId) {
        divIdInUse = divId + '_address';
    }

    function setData(address) {
        var i, p, obj;

        query(".form-row.address", prototypeNode.parentNode).forEach(function (node, index) {
            if( index !== 0 ) {
                destroyRow(index, node);
            }
        });

        if( typeof address === "object" && address !== null && address.length > 0 ) {

            i = 0;
            addressId = 1;
            for( p in address ) {
                if( i !== 0 ) {
                    cloneNewNode();
                    createDijits();
                }
                obj = address[p];
                street1Input[0].set('value', obj.street1);
                street2Input[0].set('value', obj.street2);
                cityInput[0].set('value', obj.city);
                stateProvinceInput[0].set('value', obj.state_province);
                postalCodeInput[0].set('value', obj.postal_code);
                countrySelect[0].set('value', obj.country);
                i++;
            }
        } else {
            street1Input[0].set('value', "");
            street2Input[0].set('value', "");
            cityInput[0].set('value', "");
            stateProvinceInput[0].set('value', "");
            postalCodeInput[0].set('value', "");
            countrySelect[0].set('value', "");
        }
    }

    function destroyRow(id, target) {
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
        domConstruct.destroy(target);
    }

    return {
        getData: getData,
        run: run,
        setData: setData
    }
}
);