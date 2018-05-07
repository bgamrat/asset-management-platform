define([
    "dojo/dom",
    "dojo/request/xhr",
    "dojo/store/Memory",
    "dijit/tree/ObjectStoreModel",
    "dijit/Tree",
    "dojo/store/JsonRest",
    "dijit/form/ComboBox",
    "dojo/i18n!app/nls/core",
    "dojo/domReady!"
], function (dom,
        xhr, Memory, ObjectStoreModel, Tree, JsonRest, ComboBox, core) {
//"use strict";
    function run() {

        var searchInput, searchStore;

        xhr.get("/api/menustore/adminmenu/", {
            handleAs: "json"
        }).then(function (res) {
            var i, l, store = [], memory, model;
            l = res.length;
            for( i = 0; i < l; i++ ) {
                store.push(res[i]);
            }
            memory = new Memory({
                data: store,
                getChildren: function (object) {
                    return this.query({parent: object.id});
                }
            });

            // Create the model
            var model = new ObjectStoreModel({
                store: memory,
                query: {id: 'admin'},
                mayHaveChildren: function(object) {
                    return typeof object.has_children !== "undefined" && object.has_children;
                }
            });

            // Create the Tree.
            var tree = new Tree({
                id: "admin-menu",
                model: model,
                persist: true,
                showRoot: false,
                onClick: function (item) {
                    if( typeof item.uri !== "undefined" && item.uri !== null ) {
                        location.href = item.uri;
                    }
                },
                getIconClass: function (item, opened) {
                    return (item && item.has_children) ? (opened ? "dijitFolderOpened" : "dijitFolderClosed") : "dijitLeaf"
                }
            }, "admin-left-menu");
            tree.startup();
        });

        searchStore = new JsonRest({
            target: '/api/store/search',
            useRangeHeaders: false,
            idProperty: 'id'});
        searchInput = new ComboBox({
            trim: true,
            "class": "search",
            store: searchStore,
            searchAttr: "name",
            placeholder: core.search
        }, "search");
        searchInput.startup();
        searchInput.on("change", function (evt) {
            console.log(evt);
            console.log(searchInput.store);
        });

    }
    return {
        run: run
    };
});
//# sourceURL=menu.js