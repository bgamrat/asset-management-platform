define([
    "dojo/_base/lang",
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/registry",
    "dojo/request/xhr",
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
], function (lang, dom, domAttr, domConstruct, on, query, registry, xhr,
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
        var aContainer, contentPane = [];

        function setDivId(divId) {
            divIdInUse = divId;
        }

        function getDivId() {
            return divIdInUse;
        }

        function cloneNewNode() {
            var block, cp, dijit;

            prototypeContent = dataPrototype.replace(/__person__/g, personId.length);
            block = domConstruct.toDom(prototypeContent);
            cp = query(".content-pane", block);
            dijit = new ContentPane({
                title: core.new,
                content: cp[0]
            });
            dijit.on("change", function (evt) {
                var cp = registry.byId(this.id);
                var first, middle, middleName, last;
                first = query("input[id$='firstname']", this);
                first = registry.byId(first[0].id);
                middle = query("input[id$='middlename']", this);
                middle = registry.byId(middle[0].id);
                middleName = middle.get("value");
                if (middleName === "") {
                    middleName = "";
                }
                last = query("input[id$='lastname']", this);
                last = registry.byId(last[0].id);
                cp.set("title", first.get("value") + " "
                        + middleName + " "
                        + last.get("value"));
            });
            contentPane.push(dijit);
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
            dijit.on("change", loadPerson);
            lastnameInput.push(dijit);
            dijit = new Textarea({
                placeholder: core.comment,
                trim: true,
                required: false
            }, base + "comment");
            dijit.startup();
            commentInput.push(dijit);
        }

        function setPersonValues(obj, i) {

            personId[i] = obj.id;
            typeSelect[i].set('value', obj.type.id);
            titleInput[i].set('value', obj.title);
            firstnameInput[i].set('value', obj.firstname);
            middlenameInput[i].set('value', obj.middlename);
            lastnameInput[i].set('value', obj.lastname);

            contentPane[i].set('title', obj.firstname + " " + ((obj.middlename === null) ? "" : obj.middlename) + " " + obj.lastname);
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

        function loadPerson(evt) {
            var item = this.get("item");
            var id, idx;
            if (item === null) {
                return;
            }
            id = item.id;
            idx = this.id.replace(/\D/g,'');
            xhr.get('/api/people/' + id, {
                handleAs: "json"
            }).then(function (data) {
                var i, l, kid, cp;
                l = lastnameInput.length;
                for( i = 0; i < l; i++ ) {
                    kid = lastnameInput[i].id.replace(/\D/g, '');
                    if( kid == idx ) {
                        setPersonValues(data,i);
                        break;
                    }
                }
            });
        }

        function destroyRow(id, target) {

            var i, l, item, kid, cp;

            l = typeSelect.length;
            for( i = 0; i < l; i++ ) {
                kid = typeSelect[i].id.replace(/\D/g, '');
                if( kid == id ) {
                    id = i;
                    break;
                }
            }
            personId.splice(id, 1);
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
            cp = contentPane.splice(id, 1);
            emails[id].destroy(target);
            phones[id].destroy(target);
            addresses[id].destroy(target);
            aContainer.removeChild(cp[0]);
            cp[0].destroyDescendants(false);
            cp[0].destroyRendering(false);
            cp[0].destroyRecursive();
            domConstruct.destroy(target);
        }

        if( arguments.length > 0 ) {
            setDivId(arguments[0]);
        }
        base = getDivId();

        aContainer = new AccordionContainer({style: "overflow-y: auto;"}, domConstruct.place("<div>",dom.byId(base),"first"));
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

        on(aContainer, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var id = parseInt(targetParent.id.replace(/\D/g, ''));
            while( !targetParent.classList.contains("content-pane") ) {
                targetParent = targetParent.parentNode;
            }
            destroyRow(id, targetParent);
        });

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
                    setPersonValues(obj,i);
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
        };

    }
    return {
        run: run
    };
}
);