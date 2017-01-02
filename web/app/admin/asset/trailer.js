define([
    "dojo/request",
    "dojo/_base/array",
    "dojo/aspect",
    "dojo/store/Memory",
    "dojo/store/Observable",
    "dijit/Tree",
    "dijit/tree/ObjectStoreModel",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-dom",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (request, arrayUtil, aspect, Memory, Observable, Tree, ObjectStoreModel,
        lib, core) {

    function run(trailerId) {

        var trailerContentsStore = new Memory({
            data: [],
            idProperty: 'id',
            getChildren: function (object) {
                return this.query({parent: object.id});
            }
        });
        aspect.around(trailerContentsStore, "put", function (originalPut) {
            return function (obj, options) {
                if( options && options.parent ) {
                    obj.parent_id = obj.parent = options.parent.id;
                }
                return originalPut.call(trailerContentsStore, obj, options);
            }
        });
        var deferred = request.get("/api/store/trailercontents/"+trailerId, {
            handleAs: "json"
        });

        deferred.then(function (res) {
            var observableStore, model;

            arrayUtil.forEach(res, function (item) {
                trailerContentsStore.put(item);
            });
            
            observableStore = new Observable(trailerContentsStore);
            model = new ObjectStoreModel({
                store: observableStore,
                query: {name: 'top'}
            });

            (new Tree({
                model: model,
                showRoot: false,
                persist: true,
            })).placeAt("trailer-tree").startup();

        }, function (err) {
            // This shouldn't occur, but it's defined just in case
            alert("An error occurred: " + err);
        });
        lib.pageReady();
    }
    return {
        run: run
    }
});