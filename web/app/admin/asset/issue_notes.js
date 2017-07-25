define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/form/SimpleTextarea",
    "dijit/form/TextBox",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on,
        query,
        SimpleTextarea, TextBox,
        lib, core) {
    //"use strict";

    var dataPrototype, prototypeNode, prototypeContent;
    var noteId = [], updatedInput = [], noteInput = [];
    var divIdInUse = 'issue_notes';
    var addOneMoreControl = null;

    function getDivId() {
        return divIdInUse;
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__note__/g, noteInput.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "first");
        noteId.push(null);
    }

    function createDijits(showDate) {
        var dijit;
        var base = getDivId() + '_' + noteInput.length + '_';
        dijit = new SimpleTextarea({
            placeholder: core.note,
            trim: true,
            required: true
        }, base + "note");
        noteInput.push(dijit);
        dijit.startup();
        dijit = new TextBox({
            disabled: true,
            "class": showDate ? "" : "hidden"
        }, base + "updated");
        updatedInput.push(dijit);
        dijit.startup();

    }

    function destroyRow(id, target) {
        var i, l, item, kid;

        l = noteInput.length;
        for( i = 0; i < l; i++ ) {
            kid = noteInput[i].id.replace(/\D/g, '');
            if( kid == id ) {
                id = i;
                break;
            }
        }

        noteId.splice(id, 1);

        item = noteInput.splice(id, 1);
        item[0].destroyRecursive();
        item = updatedInput.splice(id, 1);
        item[0].destroyRecursive();
        domConstruct.destroy(target);
    }

    function run() {

        prototypeNode = dom.byId(getDivId());
        dataPrototype = domAttr.get(prototypeNode, "data-prototype");
        prototypeContent = dataPrototype.replace(/__note__/g, noteInput.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "first");

        createDijits(false);

        addOneMoreControl = query('.notes .add-one-more-row');

        addOneMoreControl.on("click", function (event) {
            cloneNewNode();
            createDijits(false);
        });

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var id = parseInt(targetParent.id.replace(/\D/g, ''));
            destroyRow(id, target.closest(".form-row.issue-note"));
        });
    }

    function getData() {
        var i, l = noteId.length, returnData = [];
        for( i = 0; i < l; i++ ) {
            if( noteInput[i].get('value') !== "" ) {
                returnData.push(
                        {
                            "note": noteInput[i].get('value')
                        });
            }
        }
        return returnData.length > 0 ? returnData : null;
    }

    function setData(notes) {
        var i, l, timestamp, obj;

        query(".form-row.issue-note", prototypeNode.parentNode).forEach(function (node, index) {
            destroyRow(0, node);
        });

        timestamp = new Date();
        if( typeof notes === "object" && notes !== null ) {
            l = notes.length;
            for( i = 0; i < l; i++ ) {
                cloneNewNode();
                createDijits(true);
                obj = notes[i];
                noteId[i] = obj.id;
                noteInput[i].set('value', obj.note);
                timestamp.setTime(obj.updated.timestamp * 1000);
                updatedInput[i].set('value', timestamp.toLocaleString());
            }
        }
    }

    return {
        run: run,
        getData: getData,
        setData: setData
    }
});