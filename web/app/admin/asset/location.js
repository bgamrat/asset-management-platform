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
    "dijit/form/RadioButton",
    "dijit/form/FilteringSelect",
    'dojo/store/JsonRest',
    "dojo/aspect",
    "dojo/i18n!app/nls/core",
    "dojo/domReady!"
], function (declare, lang, dom, domAttr, domConstruct, on,
        query, ObjectStore, Memory,
        RadioButton, FilteringSelect,
        JsonRest, aspect,
        core) {


    function run() {
        //"use strict";
        var locationId = null;
        var divIdInUse = 'location';
        var namesAndUrls = [];

        function getDivId() {
            return divIdInUse;
        }

        function setDivId(divId) {
            divIdInUse = divId + '_location';
        }

        //"use strict";
        var formNameInUse = 'transfer';

        function getFormName() {
            return formNameInUse;
        }

        function setFormName(name) {
            formNameInUse = name;
        }

        var locationTypeRadioButton = [];
        var locationTypeLabels = {};
        var locationFilteringSelect;

        if( arguments.length > 0 ) {
            setDivId(arguments[0]);
        }
        if( arguments.length > 1 ) {
            setFormName(arguments[1]);
        }

        var id = getDivId();
        var formName = getFormName();

        query('[name="' + formName + '[' + id + '][ctype]"]').forEach(function (node) {
            var dijit = new RadioButton({"value": node.value, "name": node.name}, node);
            namesAndUrls.push(
                    {
                        "value": parseInt(node.value),
                        "data-url": domAttr.get(node, "data-url"),
                        "data-type": domAttr.get(node, "data-type"),
                        "data-location-type-id": node.value});
            dijit.set("data-url", domAttr.get(node, "data-url"));
            dijit.startup();
            locationTypeRadioButton.push(dijit);
        });
        query('label[for^="' + formName + "_" + id + '_ctype_"]').forEach(function (node) {
            locationTypeLabels[domAttr.get(node, "for").replace(/\D/g, '')] = node.textContent;
        });

        on(dom.byId(formName + "_" + id + '_ctype'), on.selector("label, input", "click"), function (event) {
            var target = event.target, targetId;
            if( target.tagName === 'LABEL' ) {
                target = dom.byId(domAttr.get(target, "for"));
            }
            var dataUrl = domAttr.get(target, "data-url");
            if( dataUrl !== null && dataUrl !== "" ) {
                locationFilteringSelect.set("readOnly", false);
                locationStore.target = dataUrl;
                locationFilteringSelect.set("store", locationStore);
            } else {
                targetId = target.id.replace(/\D/g, '');
                textLocationMemoryStore.data = [{name: locationTypeLabels[targetId], id: 0}];
                locationFilteringSelect.set("store", textLocationStore);
                locationFilteringSelect.set("displayedValue", locationTypeLabels[targetId]);
                // @TODO - uncomment this line when Firefox stops crashing under CentOS
                // locationFilteringSelect.set("readOnly", true);
            }
        });

        var textLocationMemoryStore = new Memory({
            idProperty: "id",
            data: []});
        var textLocationStore = new ObjectStore({objectStore: textLocationMemoryStore});

        var locationStore = new JsonRest({
            useRangeHeaders: false,
            idProperty: 'hash'});
        aspect.after(locationStore, "query", function (deferred) {
            return deferred.then(function (response) {
                var s, locationType, i, l, item;
                if( response !== null ) {
                    locationType = namesAndUrls[getLocationType()]["data-type"];
                    if( response.length > 0 ) {
                        l = response.length;
                        for( i = 0; i < l; i++ ) {
                            item = response[i];
                            if( typeof item.hash === "undefined" ) {
                                response[i].hash = locationType + "/" + item.id;
                            }
                        }
                    }
                }
                return response;
            });
        });

        locationFilteringSelect = new FilteringSelect({
            store: locationStore,
            labelAttr: "label",
            labelType: "html",
            searchAttr: "name",
            placeholder: core.contact,
            required: false,
            pageSize: 25,
            intermediateChanges: true,
            "class": 'location-filtering-select'
        }, formName + "_" + id + "_entity");
        locationFilteringSelect.startup();

        function getLocationType() {
            var i, l;
            l = locationTypeRadioButton.length;
            for( i = 0; i < l; i++ ) {
                if( locationTypeRadioButton[i].get("checked") === true ) {
                    return i;
                }
            }
            return null;
        }

        function setLocationType(locationType) {
            var i, l = locationTypeRadioButton.length;
            for( i = 0; i < l; i++ ) {
                if( parseInt(locationTypeRadioButton[i].value) === locationType ) {
                    locationTypeRadioButton[i].set("checked", true);
                    break;
                }
            }
        }

        return {
            getData: function () {
                var locationType = namesAndUrls[getLocationType()].value;
                var item = locationFilteringSelect.get("item");
                var entityId, personId, addressId, address = null, hash, returnObj;

                if( !isNaN(locationType) ) {
                    locationType = parseInt(locationType);
                } else {
                    locationType = null;
                }
                entityId = personId = addressId = null;
                if( typeof item.hash !== "undefined" && item.hash !== null ) {
                    hash = item.hash;
                    hash = hash.split('/');
                    entityId = parseInt(hash[1]);
                    if( hash.length > 2 ) {
                        personId = parseInt(hash[2]);
                        addressId = parseInt(hash[3]);
                        address = hash.length > 3 ? "on" : null;
                    }
                }
                returnObj = {
                    "id": locationId,
                    "entity": entityId,
                    "person_id": personId,
                    "address_id" : addressId,
                    "address": address,
                    "type": locationType
                };
                if( address !== null ) {
                    returnObj.address = address;
                }
                return returnObj;

            },
            getText: function () {
                var item = locationFilteringSelect.get("item");
                return (typeof item.label !== "undefined") ? item.label : item.name;
            },
            setData: function (obj, location_text) {
                if( typeof obj !== "undefined" && obj !== null ) {
                    dom.byId(getFormName() + "_" + getDivId() + "_id").value = obj.id;
                    locationId = obj.id;
                    setLocationType(obj.type.id);
                    if( obj.type.url !== null ) {
                        locationStore.target = obj.type.url;
                        locationFilteringSelect.set("store", locationStore);
                        locationFilteringSelect.set("readOnly", false);
                        if (obj.address === true && obj.entityData !== null) {
                            obj.entityData.label = location_text;
                            locationFilteringSelect.set('item', obj.entityData);
                        } else {
                            locationFilteringSelect.set('displayedValue', obj.entityData.name);
                        }
                    } else {
                        textLocationMemoryStore.data = [{name: locationTypeLabels[obj.type.id], id: 0}];
                        locationFilteringSelect.set("store", textLocationStore);
                        locationFilteringSelect.set('displayedValue', location_text);
                        locationFilteringSelect.set("readOnly", true);
                    }
                }
            }
        }
    }
    return {
        run: run
    }
});
