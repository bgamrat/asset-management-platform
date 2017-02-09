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

    function getDivId() {
        return divIdInUse;
    }

    function setDivId(divId) {
        divIdInUse = divId;
    }

    function cloneNewNode() {
        var prototypeContent = dataPrototype.replace(/__contract__/g, contractFilteringSelect.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");
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
        dijit.on("change", function () {
            var selectedContract = this;

            contractTrailersStore.get(selectedContract.value).then(function (data) {
                var templateContract;
                var i, l, reqd = [], avail = [];
                var equipmentLink = dom.byId("contract-equipment-link-" + selectedContract.id);
                l = data.required.length;
                for( i = 0; i < l; i++ ) {
                    reqd.push(data.required[i].name);
                }
                l = data.available.length;
                for( i = 0; i < l; i++ ) {
                    avail.push(data.available[i].name);
                }
                // Backticks won't work with the old Chrome browser
                templateContract = '<dt data-contract-id="' + data.id + '" class="term">' + selectedContract.displayedValue + '</dt>' +
                        '<dd>';
                if( reqd.length > 0 ) {
                    templateContract += '<span class="label">&nbsp;' + core.requires + '</span>' + reqd.join() + '<br>';
                }
                if( avail.length > 0 ) {
                    '<span class="label">&nbsp;' + core.available + '</span>' + avail.join();
                }
                templateContract += '</dd>';

                domConstruct.place(templateContract, dom.byId("trailers-required-by-contracts"), "last");
                // TODO: Fix so it puts the URL in
                equipmentLink.href = '/admin/contract/'+ data.id + '/equipment';
            });
        });
        contractFilteringSelect.push(dijit);
    }

    function destroyRow(id, target) {
        var item;
        if( id !== null ) {
            item = contractFilteringSelect.splice(id, 1);
            item[0].destroyRecursive();
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
        prototypeContent = dataPrototype.replace(/__contract__/g, contractFilteringSelect.length);
        domConstruct.place(prototypeContent, prototypeNode.parentNode, "last");

        createDijit();

        addOneMoreControl = query('.contracts .add-one-more-row');

        addOneMoreControl.on("click", function (event) {
            var dataType = domAttr.get(event.target, "data-type");
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

    function getData(relationship) {
        var i, returnData = [];
        for( i = 0; i < contractsFilteringSelect.length; i++ ) {
            returnData.push(
                    parseInt(contractsFilteringSelect[i].get("value")));
        }
        return returnData;
    }

    function setData(relationship, models) {
        var i;

        query(".form-row.contract", prototypeNode.parentNode).forEach(function (node, index) {
            destroyRow(null, node);
        });

        if( typeof models === "object" && models !== null && models.length > 0 ) {
            for( i = 0; i < models.length; i++ ) {
                cloneNewNode();
                createDijit();
                contractFilteringSelect[i].set("value", contracts[i].id);
                contractFilteringSelect[i].set("displayedValue", contracts[i].name);
            }
        }
    }

    return {
        run: run,
        getData: getData,
        setData: setData
    }
}
);
//# sourceURL=contract.js