define([
    "dojo/dom",
    "dojo/store/Memory",
    "dijit/tree/ObjectStoreModel",
    "dijit/Tree",
    "dojo/domReady!"
], function (dom,
        Memory, ObjectStoreModel, Tree) {
//"use strict";
    function run() {
        var store = new Memory({data: menuTreeStoreData
            ,
            getChildren: function (object) {
                return this.query({parent: object.id});
            }});
        var model = new ObjectStoreModel({
            store: store,
            query: {id: 'admin'}
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