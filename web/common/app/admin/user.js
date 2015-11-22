require([
    "dojo/_base/declare",
    "dojo/dom",
    "dojo/request/xhr",
    "dojo/dom-attr",
    "dojo/json",
    "dijit/registry",
    'dstore/RequestMemory',
    'dgrid/OnDemandGrid',
    "dgrid/Selection",
    "dojo/domReady!"
], function (declare, dom, xhr, domAttr, json, registry, RequestMemory, OnDemandGrid, Selection) {

    var grid = new (declare([OnDemandGrid, Selection]))({
        collection: new RequestMemory({target: '/api/admin/user/list'}),
        columns: {
            username: 'Username',
            email: 'Email',
            enabled: 'Enabled'
        },
        selectionMode: "single"
    }, 'grid');

    grid.startup();

    grid.on("dgrid-select", function (event) {
        console.log('select');
        console.log(event);
        var username = event.rows[0].data.username;
        xhr("/api/admin/user/" + username, {
            handleAs: "json"
        }).then(function (data) {
            console.log(data);
            domAttr.set("user_username", "value", data.username);
            domAttr.set("user_email", "value", data.email);
            domAttr.set("user_enabled", "checked", data.enabled === true);
        });

    });

});