define([
    "dojo/_base/lang",
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/registry",
    "dojo/request/xhr",
    "dijit/form/FilteringSelect",
    'dojo/store/JsonRest',
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/domReady!"
], function (lang, dom, domAttr, domConstruct, on, query, registry, xhr,
        FilteringSelect, JsonRest,
        lib, core) {

    function run() {

        "use strict";
        var divIdInUse = "contact";
        var nameSelect = [];
        var dataPrototype;
        var prototypeNode, prototypeContent;
        var personId = [];
        var base;
        var addOneMoreControl;
        var personStore;

        function setDivId(divId) {
            divIdInUse = divId;
        }

        function getDivId() {
            return divIdInUse;
        }

        function cloneNewNode() {
            prototypeContent = dataPrototype.replace(/__person__/g, personId.length);
            domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
        }

        function createDijit() {
            var dijit, base = getDivId();
            var divId = getDivId();
            var idNumber = personId.length;
            if( prototypeNode !== null ) {
                base += '_'+idNumber;
            }
            personId.push(null);
            dijit = new FilteringSelect({
                required: true,
                "class": "name",
                store: personStore,
                searchAttr: "name",
                placeholder: core.lastname
            }, base);
            dijit.startup();
            dijit.on("change", loadPerson);
            nameSelect.push(dijit);
        }

        function setPersonValues(obj, i) {
            personId[i] = obj.id;
            nameSelect[i].set('displayedValue', obj.fullName);
        }

        function loadPerson(evt) {
            var item = this.get("item");
            var id, idx;
            if( item === null || evt === null ) {
                return;
            }
            id = item.id;
            idx = this.id.replace(/\D/g, '');
            xhr.get('/api/people/' + id, {
                handleAs: "json"
            }).then(function (data) {
                var i, l, kid, cp;
                l = nameSelect.length;
                for( i = 0; i < l; i++ ) {
                    kid = nameSelect[i].id.replace(/\D/g, '');
                    if( kid == idx ) {
                        setPersonValues(data, i, false);
                        break;
                    }
                }
            });
        }

        function destroyRow(id, target) {
            var i, l, item, kid;
            l = nameSelect.length;
            for( i = 0; i < l; i++ ) {
                kid = nameSelect[i].id.replace(/\D/g, '');
                if( kid == id ) {
                    id = i;
                    break;
                }
            }
            personId.splice(id, 1);
            item = nameSelect.splice(id, 1);
            item[0].destroyRecursive();
            domConstruct.destroy(target);
        }

        if( arguments.length > 0 ) {
            setDivId(arguments[0]);
        }

        base = getDivId();

        prototypeNode = dom.byId(getDivId());
        if( prototypeNode !== null ) {
            dataPrototype = domAttr.get(prototypeNode, "data-prototype");
            if( dataPrototype !== null ) {
                cloneNewNode();
                base += "_0";
            } else {
                prototypeNode = null;
            }
        }

        personStore = new JsonRest({
            target: '/api/store/people?',
            useRangeHeaders: false,
            idProperty: 'id'});

        createDijit();

        addOneMoreControl = query('.contacts .add-one-more-row');
        if( addOneMoreControl.length > 0 ) {
            addOneMoreControl.on("click", function (event) {
                cloneNewNode();
                createDijit();
                if( personId.length >= lib.constant.MAX_CONTACTS ) {
                    addOneMoreControl.addClass("hidden");
                }
            });
        }

        function getData() {
            var i, returnData = [];
            for( i = 0; i < personId.length; i++ ) {
                if( nameSelect[i].get('value') !== "" ) {
                    returnData.push( personId[i] );
                }
            }
            return returnData.length > 0 ? returnData : null;
        }
        function setData(person) {
            var i, p, obj, nodes;

            nodes = query(".form-row.person,.form-row.contact");
            nodes.forEach(function (node, index) {
                if( index !== 0 ) {
                    destroyRow(index, node);
                }
            });

            if( typeof person === "object" && person !== null ) {
                if( !person.hasOwnProperty('length') ) {
                    person = [person];
                }
                for( i = 0; i < person.length; i++ ) {
                    if( i !== 0 ) {
                        cloneNewNode();
                        createDijit();
                    }
                    obj = person[i];
                    setPersonValues(obj, i);
                }
            } else {
                personId[0] = null;
                nameSelect[0].set('value', '');
            }
        }
        return {
            getData: getData,
            setData: setData
        };

    }
    return {
        run: run
    };
}
);