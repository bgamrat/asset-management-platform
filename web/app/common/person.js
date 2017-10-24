define([
    "dojo/_base/lang",
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/registry",
    "dijit/layout/AccordionContainer",
    "dijit/layout/ContentPane",
    "dijit/form/ValidationTextBox",
    "dijit/form/Textarea",
    "dijit/form/Select",
    "dijit/form/ComboBox",
    "dojo/data/ObjectStore",
    "dojo/store/Memory",
    'dojo/store/JsonRest',
    "app/common/emails",
    "app/common/phones",
    "app/common/addresses",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/domReady!"
], function (lang, dom, domAttr, domConstruct, on, query, registry,
        AccordionContainer, ContentPane,
        ValidationTextBox, Textarea, Select, ComboBox,
        ObjectStore, Memory, JsonRest,
        xemails, xphones, xaddresses,
        lib, core) {

    function run() {

        "use strict";
        var divIdInUse = "user_person";
        var firstnameInput = [], middlenameInput = [], lastnameInput = [], titleInput = [];
        var typeSelect = [], commentInput = [];
        var dataPrototype;
        var prototypeNode, prototypeContent;
        var store;
        var personId = [];
        var base, d, data;
        var select, storeData, memoryStore;
        var addOneMoreControl;
        var emails = [], phones = [], addresses = [];
        var divId;
        var personStore;
        var a, aContainer, contentPanes = [];

        function setDivId(divId) {
            divIdInUse = divId;
        }

        function getDivId() {
            return divIdInUse;
        }

        function cloneNewNode() {
            var block, cp, dijit;

            prototypeContent = dataPrototype.replace(/__person__/g, personId.length);
            block = domConstruct.place(prototypeContent, prototypeNode, "after");
            block = block.parentNode;
            cp = query(".content-pane", block);
            dijit = new ContentPane({
                title: core.new,
                content: cp[cp.length - 1],
            });
            dijit.on("change", function (evt) {
                var cp = registry.byId(this.id);
                var first, middle, last;
                first = query("input[id$='firstname']",this);
                first = registry.byId(first[0].id);
                middle = query("input[id$='middlename']",this);
                middle = registry.byId(middle[0].id);
                last = query("input[id$='lastname']",this);
                last = registry.byId(last[0].id);
                cp.set("title", first.get("value") + " "
                        + middle.get("value") + " "
                        + last.get("value"));
            });
            contentPanes.push(dijit);
            aContainer.addChild(dijit);
        }

        function createDijits() {
            var dijit, base = getDivId() + '_';
            if( prototypeNode !== null ) {
                base += personId.length + '_';
            }
            personId.push(null);
            dijit = new Select({
                store: store,
                placeholder: core.type,
                required: true,
                "class": "type-select"
            }, base + "type");
            dijit.startup();
            typeSelect.push(dijit);
            dijit = new ValidationTextBox({
                required: false,
                trim: true,
                pattern: "[A-Za-z\.\,\ \'-]{2,64}",
                "class": "name",
                placeholder: core.title
            }, base + "title");
            dijit.startup();
            titleInput.push(dijit);
            dijit = new ValidationTextBox({
                trim: true,
                properCase: true,
                pattern: "[A-Za-z\.\,\ \'-]{2,64}",
                "class": "name",
                placeholder: core.firstname
            }, base + "firstname");
            dijit.startup();
            firstnameInput.push(dijit);
            dijit = new ValidationTextBox({
                trim: true,
                properCase: true,
                pattern: "[A-Za-z\.\,\ \'-]{,64}",
                maxLength: 64,
                required: false,
                "class": "name",
                placeholder: core.middlename
            }, base + "middlename");
            dijit.startup();
            middlenameInput.push(dijit);
            dijit = new ComboBox({
                required: true,
                trim: true,
                pattern: "[A-Za-z\.\,\ \'-]{2,64}",
                "class": "name",
                store: personStore,
                searchAttr: "name",
                placeholder: core.lastname
            }, base + "lastname");
            dijit.startup();
            lastnameInput.push(dijit);
            dijit = new Textarea({
                placeholder: core.comment,
                trim: true,
                required: false
            }, base + "comment");
            dijit.startup();
            commentInput.push(dijit);
        }

        function destroyRow(id, target) {

            var i, l, item, kid;

            l = typeSelect.length;
            for( i = 0; i < l; i++ ) {
                kid = typeSelect[i].id.replace(/\D/g, '');
                if( kid == id ) {
                    id = i;
                    break;
                }
            }
            personId.splice(id, 1);
            item = contentPane.splice(id, 1);
            item[0].destroyRecursive();
            item = typeSelect.splice(id, 1);
            item[0].destroyRecursive();
            item = titleInput.splice(id, 1);
            item[0].destroyRecursive();
            item = firstnameInput.splice(id, 1);
            item[0].destroyRecursive();
            item = middlenameInput.splice(id, 1);
            item[0].destroyRecursive();
            item = lastnameInput.splice(id, 1);
            item[0].destroyRecursive();
            item = commentInput.splice(id, 1);
            item[0].destroyRecursive();
            domConstruct.destroy(target);
        }

        if( arguments.length > 0 ) {
            setDivId(arguments[0]);
        }
        base = getDivId();

        a = query("." + base + ".accordion");
        aContainer = new AccordionContainer({style: "height: 500px; overflow-y: auto;"}, a[0]);
        aContainer.startup();

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

        select = base + "_type";

        if( dom.byId(select) === null ) {
            lib.textError(select + " not found");
            return;
        }

        data = JSON.parse(domAttr.get(select, "data-options"));
        // Convert the data to an array of objects
        storeData = [];
        for( d in data ) {
            storeData.push(data[d]);
        }
        memoryStore = new Memory({
            idProperty: "value",
            data: storeData});
        store = new ObjectStore({objectStore: memoryStore});

        personStore = new JsonRest({
            target: '/api/store/people?last',
            useRangeHeaders: false,
            idProperty: 'id'});

        createDijits();

        addOneMoreControl = query('.contacts .add-one-more-row');
        if( addOneMoreControl.length > 0 ) {
            addOneMoreControl.on("click", function (event) {
                var divId = getDivId();
                var idNumber = personId.length;
                cloneNewNode();
                createDijits();
                phones[idNumber] = xphones.run(divId, idNumber);
                emails[idNumber] = xemails.run(divId, idNumber);
                addresses[idNumber] = xaddresses.run(divId, idNumber);
                if( personId.length >= lib.constant.MAX_CONTACTS ) {
                    addOneMoreControl.addClass("hidden");
                }
            });
        }

        divId = getDivId();
        emails[0] = xemails.run(divId, 0);
        addresses[0] = xaddresses.run(divId, 0);
        phones[0] = xphones.run(divId, 0);

        function getData() {
            var i, returnData = [];
            for( i = 0; i < personId.length; i++ ) {
                if( lastnameInput[i].get('value') !== "" ) {
                    returnData.push({
                        "id": personId[i],
                        "type": parseInt(typeSelect[i].get('value')),
                        "type_text": typeSelect[i].get('displayedValue'),
                        "title": titleInput[i].get('value'),
                        "firstname": firstnameInput[i].get('value'),
                        "middlename": middlenameInput[i].get('value'),
                        "lastname": lastnameInput[i].get('value'),
                        "name": firstnameInput[i].get('value') + " " + middlenameInput[i].get('value') + " " + lastnameInput[i].get('value'),
                        "comment": commentInput[i].get('value'),
                        "emails": emails[i].getData(),
                        "phones": phones[i].getData(),
                        "addresses": addresses[i].getData()
                    });
                }
            }
            return returnData.length > 0 ? returnData : null;
        }
        function setData(person) {
            var i, p, obj, nodes;

            nodes = query(".form-row.person,.form-row.contacts");
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
                        createDijits();
                    }
                    obj = person[i];
                    personId[i] = obj.id;
                    typeSelect[i].set('value', obj.type.id);
                    titleInput[i].set('value', obj.title);
                    firstnameInput[i].set('value', obj.firstname);
                    middlenameInput[i].set('value', obj.middlename);
                    lastnameInput[i].set('value', obj.lastname);
                    contentPane[i].set('title', obj.firstname + " " + obj.middlename + " " + obj.lastname);
                    commentInput[i].set('value', obj.comment);
                    if( typeof obj.phones !== "undefined" ) {
                        phones[i].setData(obj.phones);
                    } else {
                        phones[i].setData(null);
                    }
                    if( typeof obj.emails !== "undefined" ) {
                        emails[i].setData(obj.emails);
                    } else {
                        emails[i].setData(null);
                    }
                    if( typeof obj.addresses !== "undefined" ) {
                        addresses[i].setData(obj.addresses);
                    } else {
                        addresses[i].setData(null);
                    }
                }
            } else {
                personId[0] = null;
                typeSelect[0].set('value', '');
                titleInput[0].set('value', '');
                firstnameInput[0].set('value', '');
                middlenameInput[0].set('value', '');
                lastnameInput[0].set('value', '');
                commentInput[0].set('value', '');
                phones[0].setData(null);
                emails[0].setData(null);
                addresses[0].setData(null);
            }
        }
        return {
            getData: getData,
            setData: setData
        }

    }
    return {
        run: run
    }
}
);