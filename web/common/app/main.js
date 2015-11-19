require([
		"dojo/dom",
		"dijit/registry",
		"dijit/Menu",
		"dijit/MenuItem",
		"dojo/domReady!"
	], function(dom, registry, Menu, MenuItem){
		// a menu item selection handler
		var onItemSelect = function(event){
			dom.byId("lastSelected").innerHTML = this.get("label");
		};
		// create the Menu container
		var menu = new Menu({}, "mainMenu");

		// create and add child item widgets
		// for each of "edit", "view", "task"
		menu.addChild(new MenuItem({
			id: "edit",
			label: "Edit",
			onClick: onItemSelect
		}));

		menu.addChild(new MenuItem({
			id: "view",
			label: "View",
			onClick: onItemSelect
		}));

		menu.addChild(new MenuItem({
			id: "task",
			label: "Task",
			onClick: onItemSelect
		}));

		menu.startup();
	});