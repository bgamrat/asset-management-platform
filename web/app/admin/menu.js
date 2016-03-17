define([
    "dojo/_base/declare",
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/request/xhr",
    "dojo/json",
    "dojo/aspect",
    "dojo/query",
    "dijit/registry",
    "dijit/MenuBar",
    "dijit/MenuBarItem",
    "dijit/PopupMenuBarItem",
    "dijit/MenuItem",
    "dijit/DropDownMenu",
    "dijit/form/Button",
    "dijit/Dialog",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (declare, dom, domAttr, domConstruct, on, xhr, json, aspect, query,
        registry, MenuBar, MenuBarItem, PopupMenuBarItem, MenuItem, DropDownMenu, Dialog,
        lib, libGrid, core) {

    function run() {
        var menuBar = new MenuBar({}, "admin-top-menu");

        function createMenuItem(widget,parent) {
            var children, node, item, i, nextLabel = "";
            console.log(widget);
            console.log(parent);
            children = query(parent).children();
            for( i = 0; i < children.length; i++ ) {
                node = children[i];
                console.log(node);
                switch( node.tagName ) {
                    case "A":
                        item = new MenuBarItem({label: node.textContent.trim()}, node);
                        widget.addChild(item);
                        break;
                    case "UL":
                        item = new DropDownMenu();
                        widget.addChild(new PopupMenuBarItem({label: nextLabel, popup:item}));
                        widget = item;    
                    case "LI":
                        createMenuItem(widget,node);     
                        break;
                    case "SPAN":
                        nextLabel = node.textContent.trim();
                        break;
                }
            }
        }

        var menuElements = query("#admin-top-menu ul");
        if( menuElements.length > 0 ) {
            createMenuItem(menuBar,menuElements[0]);
            domConstruct.destroy(menuElements[0]);
        }

        menuBar.startup();
    }
    return {
        run: run
    };
});