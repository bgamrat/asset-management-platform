define([
    "dojo/_base/declare",
    "dojo/dom",
    "dojo/dom-attr",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/query",
    "dijit/registry",
    "dijit/MenuBar",
    "dijit/MenuBarItem",
    "dijit/PopupMenuItem",
    "dijit/PopupMenuBarItem",
    "dijit/MenuItem",
    "dijit/DropDownMenu",
    "dijit/form/Button",
    "dijit/Dialog",
    "app/lib/common",
    "dojo/i18n!app/nls/core",
    "dojo/NodeList-traverse",
    "dojo/domReady!"
], function (declare, dom, domAttr, domConstruct, on, query, registry,
        MenuBar, MenuBarItem, PopupMenuItem, PopupMenuBarItem, MenuItem, DropDownMenu, Dialog,
        lib, libGrid, core) {
    //"use strict";
    function run() {
        var menuBar = new MenuBar({}, "admin-top-menu");

        function createMenuItem(widget, parent, depth) {
            var children, node, item, i, label, nextNode;
            var popup, popupMenuObj, labelObj, link;
            children = query(parent).children();
            for( i = 0; i < children.length; i++ ) {
                node = children[i];
                nextNode = (typeof children[i + 1] !== "undefined") ? children[i + 1] : null;
                switch( node.tagName ) {
                    case "SPAN":
                    case "A":
                        label = node.textContent.trim();
                        if( typeof node.href !== "undefined") {
                            link = node.href;
                        } else {
                            link = null;
                        }
                        if( nextNode !== null && nextNode.tagName === "UL" ) {
                            popup = new DropDownMenu();
                            popupMenuObj = {label: label, popup: popup};
                            if( depth <= 1 ) {
                                item = new PopupMenuBarItem(popupMenuObj);
                            } else {
                                item = new PopupMenuItem(popupMenuObj);
                            }
                            createMenuItem(popup, nextNode, depth + 1);
                        } else {
                            labelObj = {label: label};
                            if( depth <= 1 ) {
                                item = new MenuBarItem(labelObj);
                            } else {
                                item = new MenuItem(labelObj);
                            }
                        }
                        if( link !== null ) {
                            item.on("click", function () {
                                location.href = link
                            });
                        }
                        widget.addChild(item);
                        break;
                    case "LI":
                        createMenuItem(widget, node, depth + 1);
                        break;
                }
            }
        }

        var menuElements = query("#admin-top-menu ul");
        if( menuElements.length > 0 ) {
            createMenuItem(menuBar, menuElements[0], 0);
            domConstruct.destroy(menuElements[0]);
        }
        menuBar.startup();
    }
    return {
        run: run
    };
});
//# sourceURL=menu.js