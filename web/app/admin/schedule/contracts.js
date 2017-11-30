define([
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/form/FilteringSelect",
    'dojo/store/JsonRest',
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/domReady!"
], function (dom, domAttr, domConstruct, on,
        query,
        FilteringSelect,
        JsonRest, lib, core) {
    "use strict";

    var dataPrototype, prototypeNode, prototypeContent;
    var divIdInUse, contractStore, contractFilteringSelect = [], contractTrailersStore;
    var trailerText = {};

    function getDivId() {
        return divIdInUse;
    }

    function setDivId(divId) {
        divIdInUse = divId;
    }

    function cloneNewNode() {
        prototypeContent = dataPrototype.replace(/__contract__/g, contractFilteringSelect.length);
        domConstruct.place(prototypeContent, prototypeNode, "after");
    }

    function createDijit() {
        var base = prototypeNode.id + "_" + contractFilteringSelect.length;
        var dijit = new FilteringSelect({
            store: contractStore,
            labelAttr: "name",
            searchAttr: "name",
            pageSize: 25
        }, base);
        dijit.startup();
        contractFilteringSelect.push(dijit);
        dijit.on("change", function (evt) {
            var id = parseInt(this.id.replace(/\D/g, ''));
            var item = this.get('item');
            var templateContract;
            var i, l, reqd = [], avail = [], t, hidden;
            var equipmentLink = dom.byId("contract-equipment-link-event_contracts_" + id);
            l = item.requiresTrailers.length;
            trailerText[id] = [];
            for( i = 0; i < l; i++ ) {
                t = item.requiresTrailers[i];
                reqd.push('<a target="_blank" href="/admin/trailer/'+t.trailer.name+'/equipment-by-category">'+t.trailer.name+'</a>');
                trailerText[id].push(t.trailer.name);
            }
            l = item.availableTrailers.length;
            for( i = 0; i < l; i++ ) {
                t = item.availableTrailers[i];
                avail.push('<a target="_blank" href="/admin/trailer/'+t.trailer.name+'/equipment-by-category">'+t.trailer.name+'</a>');
                trailerText[id].push(t.trailer.name + "?")
            }

            hidden = (reqd.length === 0 && avail.length === 0) ? 'class="hidden"' : "";

            // Backticks won't work with the old Chrome browser
            templateContract = '<li class="contract-equipment-list" id="contract-equipment-list-' + id + '" data-contract-id="' + item.id + '" ' + hidden + '><span>' + item.name + '</span>';
            if( reqd.length > 0 ) {
                templateContract += '<span class="label">&nbsp;' + core.requires + '</span>&nbsp;' + reqd.join() + '<br>';
            }
            if( avail.length > 0 ) {
                templateContract += '<span class="label">&nbsp;' + core.available + '</span>&nbsp;' + avail.join();
            }
            templateContract += '</li>';

            if( dom.byId("contract-equipment-list-" + id) === null ) {
                domConstruct.place(templateContract, dom.byId("trailers-required-by-contracts"), "last");
            } else {
                domConstruct.place(templateContract, dom.byId("contract-equipment-list-" + id), "replace");
            }

            equipmentLink.href = '/admin/contract/' + item.id + '/equipment';
        });
    }

    function destroyRow(id, target) {
        var item;
        if( id !== null ) {
            item = contractFilteringSelect.splice(id, 1);
            item[0].destroyRecursive();
            delete trailerText[id];
            domConstruct.destroy("contract-equipment-list-" + id);
        } else {
            contractFilteringSelect.pop().destroyRecursive();
        }
        domConstruct.destroy(target);
    }

    function run() {
        var addOneMoreControl = null;

        contractStore = new JsonRest({
            target: '/api/store/contracts',
            useRangeHeaders: false,
            idProperty: 'id'});

        contractTrailersStore = new JsonRest({
            target: '/api/store/contracttrailers',
            useRangeHeaders: false,
            idProperty: 'id'});

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

        cloneNewNode();
        createDijit();

        addOneMoreControl = query('.contracts .add-one-more-row');

        addOneMoreControl.on("click", function (event) {
            cloneNewNode();
            createDijit();
        });

        on(prototypeNode.parentNode, ".remove-form-row:click", function (event) {
            var target = event.target;
            var targetParent = target.parentNode;
            var idPieces = targetParent.id.split('-');
            destroyRow(idPieces[1], targetParent.parentNode);
        });

    }

    function getData() {
        var i, returnData = [];
        for( i = 0; i < contractFilteringSelect.length; i++ ) {
            returnData.push(
                    parseInt(contractFilteringSelect[i].get("value")));
        }
        return returnData;
    }


    function getTrailerText() {
        var t, returnData = [];
        for( t in trailerText ) {
            returnData.push(
                    trailerText[t].join(', '));
        }
        return returnData.join(', ');
    }


    function setData(contracts) {
        var i;

        query(".form-row.contract", prototypeNode.parentNode).forEach(function (node, index) {
            destroyRow(null, node);
        });
        query(".contract-equipment-list").forEach(function (node, index) {
            domConstruct.destroy(node);
        });
        
        if( typeof contracts === "object" && contracts !== null && contracts.length > 0 ) {
            for( i = 0; i < contracts.length; i++ ) {
                cloneNewNode();
                createDijit();
                contractFilteringSelect[i].set("displayedValue", contracts[i].name);
            }
        } else {
            cloneNewNode();
            createDijit();
        }
    }

    return {
        run: run,
        getData: getData,
        getTrailerText: getTrailerText,
        setData: setData
    }
}
);
//# sourceURL=contract.js