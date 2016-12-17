define([
    "dojo/dom",
    "dojo/store/Memory",
    "dijit/tree/ObjectStoreModel",
    "dojo/store/JsonRest",
    "dijit/Tree",
    "dojo/domReady!"
], function (dom,
        Memory, ObjectStoreModel, JsonRest, Tree) {
//"use strict";
    function run() {

        var store = new JsonRest({
            target: "/api/store/adminmenus/",
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
            },
        });
        // Create the Tree.
        var tree = new Tree({
            id: "admin-menu",
            model: model,
            persist: true,
            onClick: function (item) {
                if( typeof item.uri !== "undefined" && item.uri !== null ) {
                    location.href = item.uri;
                }
            },
            getIconClass: function (item, opened) {
                return (item && item.has_children) ? (opened ? "dijitFolderOpened" : "dijitFolderClosed") : "dijitLeaf"
            }
        });
        tree.placeAt(dom.byId("admin-left-menu"));
        tree.startup();
    }
    return {
        run: run
    };
});
//# sourceURL=menu.js