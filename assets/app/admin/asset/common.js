define([
    "dojo/i18n!app/nls/asset",
    "dojo/domReady!"
], function (asset) {
    "use strict";

    function relationshipLists(listContentPane, relationships, satisfies) {
        var i, listContent, listHtml = "", r, s;
        listHtml += '<div class="justify left">';
        if( Object.keys(satisfies).length > 0 ) {
            listHtml += '<div class="content">';
            listHtml += "<h4>" + asset.satisfies + "</h4>";
            listHtml += "<ul>";
            for( s in satisfies ) {
                listHtml += "<li>" + satisfies[s].full_name + "</li>";
            }
            listHtml += "</ul></div>";
        }

        if( Object.keys(relationships).length > 0 ) {
            for( r in relationships ) {
                listContent = relationships[r];
                if( listContent.length > 0 ) {
                    listHtml += '<div class="content">';
                    listHtml += "<h4>" + asset[r] + "</h4>";
                    listHtml += "<ul>";
                    for( i = 0; i < listContent.length; i++ ) {
                        listHtml += "<li>" + listContent[i].name + "</li>";
                    }
                    listHtml += "</ul></div>";
                }
            }
        }
        listHtml += "</div>";
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