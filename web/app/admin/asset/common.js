define([
    "dojo/i18n!app/nls/core",
    "dojo/i18n!app/nls/asset",
    "dojo/domReady!"
], function (core, asset) {
    "use strict";

    function relationshipLists(listContentPane, relationships) {
        var i, listContent, listHtml = "", r;
        for (r in relationships) {
            listHtml += "<h4>" + asset[r] + "</h4>";
            listHtml += "<ul>";
            listContent = relationships[r];
            for( i = 0; i < listContent.length; i++ ) {
                listHtml += "<li>" + listContent[i].name + "</li>";
            }
            listHtml += "</ul>";
        }
        if( listHtml.length > 0 ) {
            listContentPane.set("content", listHtml);
        } else {
            listContentPane.set("content", "");
        }
    }

    return {
        relationshipLists: relationshipLists
    };
});