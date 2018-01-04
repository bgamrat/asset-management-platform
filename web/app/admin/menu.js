define([
    "dojo/dom",
    "dojo/store/Memory",
    "dijit/tree/ObjectStoreModel",
    "dojo/store/JsonRest",
    "dijit/Tree",
    "dijit/form/ComboBox",
    "dojo/i18n!app/nls/core",
    "dojo/domReady!"
], function (dom,
        Memory, ObjectStoreModel, JsonRest, Tree, ComboBox, core) {
//"use strict";
    function run() {

        var searchInput, searchStore;

        var store = new JsonRest({
            target: "/api/menustore/adminmenus/",
            getChildren: function (object) {
                return this.get(object.id).then(function (fullObject) {
                    return fullObject.children;
                });
            }
        });

        var model = new ObjectStoreModel({
            store: store,
            mayHaveChildren: function (object) {
                return object.has_children;
            }, getRoot: function (onItem) {
                this.store.get("admin").then(onItem);
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

        searchStore = new JsonRest({
            target: '/api/store/search',
            useRangeHeaders: false,
            idProperty: 'id'});
        searchInput = new ComboBox({
            trim: true,
            pattern: "[A-Za-z\.\,\ \'-]{2,64}",
            "class": "name",
            store: searchStore,
            searchAttr: "name",
            placeholder: core.search
        }, "search");
        searchInput.startup();
        searchInput.on("change", function(evt){
            console.log(evt);
            console.log(searchInput.store);
        });

    }
    return {
        run: run
    };
});
//# sourceURL=menu.js