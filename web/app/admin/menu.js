require([
    "dijit/MenuBar",
    "dijit/PopupMenuBarItem",
    "dijit/MenuItem",
    "dijit/DropDownMenu",
    "dojo/domReady!"
], function(MenuBar, PopupMenuBarItem, MenuItem, DropDownMenu){
    var menuBar = new MenuBar({});

    var subMenu = new DropDownMenu({});
    pSubMenu.addChild(new MenuItem({
        label: "File item #1"
    }));
    pSubMenu.addChild(new MenuItem({
        label: "File item #2"
    }));
    pMenuBar.addChild(new PopupMenuBarItem({
        label: "File",
        popup: pSubMenu
    }));

    var pSubMenu2 = new DropDownMenu({});
    pSubMenu2.addChild(new MenuItem({
        label: "Cut",
        iconClass: "dijitEditorIcon dijitEditorIconCut"
    }));
    pSubMenu2.addChild(new MenuItem({
        label: "Copy",
        iconClass: "dijitEditorIcon dijitEditorIconCopy"
    }));
    pSubMenu2.addChild(new MenuItem({
        label: "Paste",
        iconClass: "dijitEditorIcon dijitEditorIconPaste"
    }));
    pMenuBar.addChild(new PopupMenuBarItem({
        label: "Edit",
        popup: pSubMenu2
    }));

    pMenuBar.placeAt("wrapper");
    pMenuBar.startup();
});
