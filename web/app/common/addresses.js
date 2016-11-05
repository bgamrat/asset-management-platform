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
        prototypeContent = dataPrototype.replace(/__address__/g, stateProvinceInput.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
    }

    function createDijits() {
        var base = getDivId() + '_' + stateProvinceInput.length + '_';
        dijit = new Select({
            store: typeStore,
            required: true
        }, base + "type");
        typeSelect.push(dijit);
        dijit.startup();
        dijit = new ValidationTextBox({
            trim: true,
            required: false,
            placeholder: core.street
        }, base + "street1");
        street1Input.push(dijit);
        dijit.startup();
        dijit = new ValidationTextBox({
            trim: true,
            required: false
        }, base + "street2");
        street2Input.push(dijit);
        dijit.startup();
        dijit = new ValidationTextBox({
            trim: true,
            required: true,
            placeholder: core.city
        }, base + "city");
        cityInput.push(dijit);
        dijit.startup();
        dijit = new ValidationTextBox({
            trim: true,
            required: true,
            uppercase: true,
            maxLength: 2,
            placeholder: core.state_province
        }, base + "state_province");
        stateProvinceInput.push(dijit);
        dijit.startup();
        dijit = new ValidationTextBox({
            trim: true,
            pattern: "[0-9A-Z-]{2,12}",
            required: false,
            uppercase: true,
            placeholder: core.postal_code
        }, base + "postal_code");
        postalCodeInput.push(dijit);
        dijit.startup();
        dijit = new Select({
            store: countryStore,
            required: true
        }, base + "country");
        dijit.set('value', 'US');
        dijit.set('displayedValue', 'United States');
        countrySelect.push(dijit);
        dijit.startup();
        dijit = new SimpleTextarea({
            placeholder: core.comment,
            trim: true,
            required: false
        }, base + "comment");
        commentInput.push(dijit);
        dijit.startup();
    }

    function destroyRow(id, target) {
        typeSelect.pop().destroyRecursive();
        street1Input.pop().destroyRecursive();
        street2Input.pop().destroyRecursive();
        cityInput.pop().destroyRecursive();
        stateProvinceInput.pop().destroyRecursive();
        postalCodeInput.pop().destroyRecursive();
        countrySelect.pop().destroyRecursive();
        commentInput.pop().destroyRecursive();
        domConstruct.destroy(target);
        stateProvinceInput.length--;
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
        prototypeContent = dataPrototype.replace(/__address__/g, stateProvinceInput.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");

        base = prototypeNode.id + "_" + stateProvinceInput.length;
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
                addOneMoreControl.addClass("hidden");
            }
        });

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var id = parseInt(targetParent.id.replace(/\D/g, ''));
            destroyRow(id, targetParent.parentNode);
            if( countrySelect.length <= lib.constant.MAX_ADDRESSES ) {
                addOneMoreControl.removeClass("hidden");
            }
        });
    }

    function getData() {
        var i, returnData = [];
        for( i = 0; i < stateProvinceInput.length; i++ ) {
            returnData.push(
                    {
                        "type": typeSelect[i].get('value'),
                        "street1": street1Input[i].get('value'),
                        "street2": street2Input[i].get('value'),
                        "city": cityInput[i].get('value'),
                        "stateProvince": stateProvinceInput[i].get('value'),
                        "postalCode": postalCodeInput[i].get('value'),
                        "country": countrySelect[i].get('value'),
                        "comment": commentInput[i].get('value')
                    });
        }
        return returnData;
    }


    function setData(addresses) {
        var i, p, obj;

        query(".form-row.address").forEach(function (node, index) {
            if( index !== 0 ) {
                destroyRow(index, node);
            }
        });

        if( typeof addresses === "object" && addresses !== null && addresses.length > 0 ) {

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
                stateProvinceInput[i].set('value', obj.stateProvince);
                postalCodeInput[i].set('value', obj.postalCode);
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