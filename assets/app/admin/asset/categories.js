define([
    "dojo/request",
    "dojo/_base/array",
    "dojo/aspect",
    "dojo/store/Memory",
    "dojo/store/Observable",
    "dijit/Tree",
    "dijit/tree/ObjectStoreModel",
    "dojo/domReady!"
], function (request, arrayUtil, aspect, Memory, Observable, Tree, ObjectStoreModel
        ) {

    function run() {

        var categoryStore = new Memory({
            data: [],
            idProperty: 'id',
            getChildren: function (object) {
                return this.query({parent: object.id});
            }
        });
        aspect.around(categoryStore, "put", function (originalPut) {
            return function (obj, options) {
                if( options && options.parent ) {
                    obj.parent_id = obj.parent = options.parent.id;
                }
                return originalPut.call(categoryStore, obj, options);
            }
        });
        var deferred = request.get("/api/categories", {
            handleAs: "json"
        });

        deferred.then(function (res) {
            var observableStore, model;

            arrayUtil.forEach(res, function (category) {
                categoryStore.put(category);
            });
            
            observableStore = new Observable(categoryStore);
            model = new ObjectStoreModel({
                store: observableStore,
                query: {name: 'top'}
            });

            (new Tree({
                model: model,
                showRoot: false,
                persist: true
            })).placeAt("category-tree").startup();

        }, function (err) {
            // This shouldn't occur, but it's defined just in case
            alert("An error occurred: " + err);
        });
    }
    return {
        run: run
    }
});