define([
    "dojo/_base/declare",
    "dojo/_base/lang",
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/on",
    "dojo/query",
    "dojo/data/ObjectStore",
    "dojo/store/Memory",
    "dijit/registry",
    "dijit/form/RadioButton",
    "dijit/form/FilteringSelect",
    'dojo/store/JsonRest',
    "dojo/domReady!"
], function (declare, lang, dom, domAttr, on,
        query, ObjectStore, Memory,
        RadioButton, FilteringSelect,
        JsonRest) {

    //"use strict";
    var divIdInUse = 'location';

    function getDivId() {
        return divIdInUse;
    }

    function setDivId(divId) {
        divIdInUse = divId + '_location';
    }

    function run(id) {

        if( arguments.length > 0 ) {
            setDivId(arguments[0]);
        }

        var locationTypeRadioButton = [];
        var locationTypeLabels = {};
        query('[name="transfer[' + id + '][ctype]"]').forEach(function (node) {
            var dijit = new RadioButton({"value": node.value, "name": node.name}, node);
            dijit.set("data-url", domAttr.get(node, "data-url"));
            dijit.set("data-location-type-id", node.value);
            dijit.startup();
            locationTypeRadioButton.push(dijit);
        });
        query('label[for^="' + id + '_location_ctype_"]').forEach(function (node) {
            locationTypeLabels[domAttr.get(node, "for").replace(/\D/g, '')] = node.textContent;
        });

        on(dom.byId(id + '_location_ctype'), "click", function (event) {
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
                locationFilteringSelect.set("readOnly", true);
            }
        });

        var textLocationMemoryStore = new Memory({
            idProperty: "id",
            data: []});
        var textLocationStore = new ObjectStore({objectStore: textLocationMemoryStore});

        var locationStore = new JsonRest({
            useRangeHeaders: false,
            idProperty: 'id'});
        var locationFilteringSelect = new FilteringSelect({
            store: null,
            labelAttr: "name",
            searchAttr: "name",
            pageSize: 25,
            readOnly: true,
            "class": 'location-filtering-select'
        }, id + "_location_entity");
        locationFilteringSelect.startup();

        // This should probably be a widget, but this is working for now
        return this;
    }
    function getLocationType() {
        var i, locationTypeSet = false;
        for( i = 0; i < locationTypeRadioButton.length; i++ ) {
            if( locationTypeRadioButton[i].get("checked") === true ) {
                locationTypeSet = true;
                break;
            }
        }
        return locationTypeSet ? locationTypeRadioButton[i].get("value") : null;
    }

    function setLocationType(locationType) {
        var i;
        for( i = 0; i < locationTypeRadioButton.length; i++ ) {
            if( parseInt(locationTypeRadioButton[i].get("data-location-type-id")) === locationType ) {
                locationTypeRadioButton[i].set("checked", true);
                break;
            }
        }
    }

    function getData() {
        return{
            locationId: parseInt(dom.byId(getDivId() + "_id").value),
            locationData: {
                "id": isNaN(locationId) ? null : locationId,
                "type": parseInt(getLocationType()),
                "entity": parseInt(locationFilteringSelect.get("value"))
            }
        };
    }

    function setData(obj, location_text) {
        if( typeof obj !== "undefined" && obj !== null ) {
            dom.byId(getDivId() + "_id").value = obj.id;
            setLocationType(obj.type.id);
            if( obj.type.url !== null ) {
                locationStore.target = obj.type.url;
                locationFilteringSelect.set("store", locationStore);
                locationFilteringSelect.set("readOnly", false);
                locationFilteringSelect.set('displayedValue', location_text);
            } else {
                textLocationMemoryStore.data = [{name: locationTypeLabels[obj.type.id], id: 0}];
                locationFilteringSelect.set("store", textLocationStore);
                locationFilteringSelect.set('displayedValue', location_text);
                locationFilteringSelect.set("readOnly", true);
            }
        }
    }
    return {
        run: run,
        getData: getData,
        setData: setData
    }
});
