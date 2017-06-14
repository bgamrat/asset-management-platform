define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dojo/data/ObjectStore",
    "dojo/store/Memory",
    "dijit/form/ValidationTextBox",
    "dijit/form/SimpleTextarea",
    "dijit/form/Select",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on,
        query, ObjectStore, Memory,
        ValidationTextBox, SimpleTextarea, Select,
        lib, core) {
    "use strict";

    function run() {

        var base, select, data, d;
        var countryStore, countryStoreData, countryMemoryStore;
        var typeStore, typeStoreData, typeMemoryStore;

        var countryStore, typeStore;
        var addressId = null;
        var defaultType;
        var typeSelect;
        var street1Input, street2Input, cityInput;
        var stateProvinceInput, postalCodeInput, countrySelect;
        var commentInput;
        var divIdInUse = null;

        function setDivId(divId) {
            divIdInUse = divId + '_address';
        }

        function getDivId() {
            return divIdInUse;
        }

        if( arguments.length > 0 ) {
            setDivId(arguments[0]);
            defaultType = arguments[0];
        }

        if (arguments.length > 1) {
            defaultType = arguments[1];
        }

        base = getDivId() + "_";
        select = base + "type";

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

        select = base + "country";
        data = JSON.parse(domAttr.get(select, "data-options"));
        // Convert the data to an array of objects
        countryStoreData = [];
        countryStoreData.push({value: "", label: core.country.toLowerCase()});
        for( d in data ) {
            countryStoreData.push(data[d]);
        }
        countryMemoryStore = new Memory({
            idProperty: "value",
            data: countryStoreData});
        countryStore = new ObjectStore({objectStore: countryMemoryStore});

        typeSelect = new Select({
            store: typeStore,
            required: true
        }, base + "type");
        typeSelect.startup();
        typeSelect.set("displayedValue", defaultType);
        street1Input = new ValidationTextBox({
            trim: true,
            required: false,
            placeholder: core.street
        }, base + "street1");
        street1Input.startup();
        street2Input = new ValidationTextBox({
            trim: true,
            required: false
        }, base + "street2");
        street2Input.startup();
        cityInput = new ValidationTextBox({
            trim: true,
            required: true,
            placeholder: core.city
        }, base + "city");
        cityInput.startup();
        stateProvinceInput = new ValidationTextBox({
            trim: true,
            required: true,
            uppercase: true,
            maxLength: 2,
            pattern: "(A[BKLRZ]|BC|C[AOT]|D[CE]|FL|GA|HI|I[ADLN]|K[SY]|LA|M[ABDEINOST]|N[BCDEHJLMSTUVY]|O[HKNR]|P[AE]|QC|RI|S[CDK]|T[NX]|UT|V[AT]|W[AIVY|YT])",
            placeholder: core.state_province
        }, base + "state_province");
        stateProvinceInput.startup();
        postalCodeInput = new ValidationTextBox({
            trim: true,
            pattern: "[0-9A-Z-]{2,12}",
            required: false,
            uppercase: true,
            placeholder: core.postal_code
        }, base + "postal_code");
        postalCodeInput.startup();
        countrySelect = new Select({
            store: countryStore,
            required: true
        }, base + "country");
        countrySelect.set('value', 'US');
        countrySelect.set('displayedValue', 'United States');
        countrySelect.startup();
        commentInput = new SimpleTextarea({
            placeholder: core.comment,
            trim: true,
            required: false
        }, base + "comment");
        commentInput.startup();

        function getData() {
            var i, returnData = null;
            if( cityInput.get("value") !== "" ) {
                returnData =
                        {
                            "id": addressId[i],
                            "type": parseInt(typeSelect[i].get('value')),
                            "street1": street1Input[i].get('value'),
                            "street2": street2Input[i].get('value'),
                            "city": cityInput[i].get('value'),
                            // Address the duality of names between the forms and objects with two properties
                            "state_province": stateProvinceInput[i].get('value'),
                            "stateProvince": stateProvinceInput[i].get('value'),
                            "postal_code": postalCodeInput[i].get('value'),
                            "postalCode": postalCodeInput[i].get('value'),
                            "country": countrySelect[i].get('value'),
                            "comment": commentInput[i].get('value')
                        };
            }
            return returnData;
        }
        function setData(address) {
            var obj;
            if( typeof address === "object" && address !== null ) {
                obj = address;
                addressId = obj.id;
                typeSelect.set('value', obj.type.id);
                street1Input.set('value', obj.street1);
                street2Input.set('value', obj.street2);
                cityInput.set('value', obj.city);
                stateProvinceInput.set('value', obj.stateProvince);
                postalCodeInput.set('value', obj.postalCode);
                countrySelect.set('value', obj.country);
                commentInput.set('value', obj.comment);

            } else {
                addressId = null;
                typeSelect.set('value', '');
                street1Input.set('value', "");
                street2Input.set('value', "");
                cityInput.set('value', "");
                stateProvinceInput.set('value', "");
                postalCodeInput.set('value', "");
                countrySelect.set('value', "US");
                commentInput.set('value', "");
            }
        }
        return {
            setData: setData,
            getData: getData
        }
    }

    return {
        run: run
    }
}
);