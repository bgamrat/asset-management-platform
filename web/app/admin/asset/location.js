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
        var currentLabel = '';
        var locationId = null;
        var divIdInUse = 'location';

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
            dijit.set("data-url", domAttr.get(node, "data-url"));
            dijit.set("data-location-type-id", node.value);
            dijit.startup();
            locationTypeRadioButton.push(dijit);
        });
        query('label[for^="' + id + '_ctype_"]').forEach(function (node) {
            locationTypeLabels[domAttr.get(node, "for").replace(/\D/g, '')] = node.textContent;
        });

        on(dom.byId(formName + "_" + id + '_ctype'), "click", function (event) {
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
            idProperty: "hash",
            data: []});
        var textLocationStore = new ObjectStore({objectStore: textLocationMemoryStore});

        var locationStore = new JsonRest({
            useRangeHeaders: false,
            idProperty: 'hash'});
        aspect.after(locationStore, "query", function (deferred) {
            return deferred.then(function (response) {
                console.log(response);
                var s, locationType, hash, i, l, item;
                if( response !== null) {
                    s = locationStore.target.split('/');
                    if (s.length === 4) {
                        locationType = s[3];
                    }
                    if (response.length > 0) {
                        l = response.length;
                        for (i = 0; i < l; i++) {
                            item = response[i];
                            if (typeof item.hash === "undefined") {
                                item.hash = locationType + "/" + item.id;
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

        on(locationFilteringSelect, "change", function (evt) {
            domConstruct.place(currentLabel, this.id.replace("entity", "echo"), "only");
        });

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

        return {
            getData: function () {
                var locationType = getLocationType();
                var hash = locationFilteringSelect.get("value");
                var entityId;

                if( !isNaN(locationType) ) {
                    locationType = parseInt(locationType);
                } else {
                    locationType = null;
                }
                entityId = null;
                if( hash !== null ) {
                    if( typeof hash.length !== "undefined" && hash.length > 2 ) {
                        hash = hash.split('/');
                        entityId = parseInt(hash[hash.length-1]);
                    }
                }
                return{
                    "id": locationId,
                    "type": locationType,
                    "entity": entityId
                }
                ;
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
                        locationFilteringSelect.set('displayedValue', location_text);
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
