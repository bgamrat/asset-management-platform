define(["dojo/store/util/QueryResults", "dojo/_base/declare", "dojo/_base/lang", "dojo/store/util/SimpleQueryEngine", "dojo/_base/array"],
        function (QueryResults, declare, lang, SimpleQueryEngine) {

            //    Declare the initial store
            return declare(null, {
                data: [],
                index: {},
                idProperty: "id",
                queryEngine: SimpleQueryEngine,
                constructor: function (options) {
                    lang.mixin(this, options || {});
                    this.setData(this.data || []);
                },
                get: function (id) {
                    return this.index[id];
                },
                getIdentity: function (object) {
                    if( typeof object !== "undefined" ) {
                        return object[this.idProperty];
                    } else {
                        return null;
                    }
                },
                getChildren: function (object) {
                    return object.children;
                },
                put: function (object, options) {
                    var data = this.data,
                            idProperty = this.idProperty;
                    var b, i, l, id = options && options.id
                            || object[this.idProperty];
                    var before, beforeId, beforeObject, parentObject;

                    l = data.length;
                    if( typeof options !== "undefined" ) {

                        if( options.before ) {
                            before = options.before.position;
                            beforeId = options.before.id;

                            // Advance the position of all the objects in the store (supports server side persistence)
                            this.data = data.map(function (obj, i) {
                                if( obj.position >= before ) {
                                    obj.position++;
                                }
                                return obj;
                            });

                            // Set the position of the object being placed to the position of the object it is being placed before
                            object.position = options.before.position;

                            // Get the before object
                            beforeObject = this.index[beforeId];

                            // If the parent of the object is being changed
                            if( beforeObject.parent !== options.oldParent.id ) {

                                // Update the object's parent
                                object.parent = beforeObject.parent;

                                // Remove the object from the children of the prior parent
                                i = this.index[options.oldParent.id].children.map(function (e) {
                                    return e.id;
                                }).indexOf(id);
                                if( i !== -1 ) {
                                    this.index[options.oldParent.id].children.splice(i, 1);
                                }

                                // Add the object to the parent of the before object
                                beforeObject.parent.children.push(object);
                            }

                        }

                        // If the parent id is changing, update the object
                        if( object.parent !== options.parent.id ) {
                            object.parent = options.parent.id;
                        } else {
                            // The parent id isn't changing, update the order of the children under the parent
                            i = this.index[object.parent].children.map(function (e) {
                                return e.id;
                            }).indexOf(object.id);
                            b = this.index[object.parent].children.map(function (e) {
                                return e.id;
                            }).indexOf(beforeId);
                            this.index[object.parent].children.splice(i,1);
                            this.index[object.parent].children.splice(b,0,object);
                        }

                    }
                    
                    // Update the object under the data array
                    i = this.data.map(function (e) {
                        return e.id;
                    }).indexOf(id);
                    if( i !== -1 ) {
                        object = lang.mixin(this.data[i], object);
                        this.data[i] = object;
                    } else {
                        object.children = [];
                        this.data.push(object);
                    }

                    // Update the object under the index object
                    this.index[id] = object;

                    // This ensures the parent has the object as a child
                    if( typeof object.parent !== "undefined" && object.parent !== null ) {
                        if( typeof this.index[object.parent] === "undefined" ) {
                            parentObject = {id: object.parent, children: [object]};
                            this.index[object.parent] = parentObject;
                        } else {
                            if( typeof this.index[object.parent].children === "undefined" ) {
                                this.index[object.parent].children = [object];
                            } else {
                                i = this.index[object.parent].children.map(function (e) {
                                    return e.id;
                                }).indexOf(object.id);
                                if( i === -1 ) {
                                    this.index[object.parent].children.push(object);
                                } else {
                                    // Update the object under the parent
                                    this.index[object.parent].children[i] = object;
                                }
                            }
                        }
                        parentObject = this.index[object.parent];
                        i = this.data.map(function (e) {
                            return e.id;
                        }).indexOf(object.parent);
                        this.data[i] = parentObject;
                    }
                    return id;
                },
                add: function (object, options) {
                    var id = options && options.id
                            || object[this.idProperty];
                    if( this.index[id] ) {
                        throw new Error("Object already exists");
                    }
                    return this.put(object, options);
                },
                remove: function (id) {
                    var parentId, i, j, k, l = this.data.length, children;
                    delete this.index[id];
                    for( i = 0; i < l; i++ ) {
                        if( this.data[i][this.idProperty] == id ) {
                            parentId = this.data[i].parent;
                            this.data.splice(i, 1);
                            children = this.index[parentId].children;
                            k = children.length;
                            for( j = 0; j < k; j++ ) {
                                if( children[j] === id ) {
                                    children.splice(j, 1);
                                }
                            }
                            return;
                        }
                    }
                },
                query: function (query, options) {
                    return QueryResults(
                            (this.queryEngine(query, options))(this.data)
                            );
                },
                setData: function (data) {
                    this.data = data;
                    //    index our data
                    this.index = {};
                    for( var i = 0, l = data.length; i < l; i++ ) {
                        var object = data[i];
                        this.index[object[this.idProperty]] = object;
                    }
                }
            });
        });