define([
    "dijit/Dialog",
    "dijit/ConfirmDialog",
    "dijit/form/Button",
    "dojo/i18n!app/nls/core",
    "dojo/domReady!"
], function (Dialog, ConfirmDialog, Button, core) {
    "use strict";

    var confirmDialog = new ConfirmDialog({
        title: core.confirm
    });
    confirmDialog.startup();

    var errorDialog = new Dialog({
        title: core.error
    });
    errorDialog.startup();

    function confirmAction(text, callbackFn) {
        confirmDialog.set("execute", callbackFn);
        confirmDialog.set("content", text);
        confirmDialog.show();
    }
    function isEmpty(obj) {
        // Thanks to: http://stackoverflow.com/a/32108184/2182349
        if( typeof obj.keys !== "undefined" ) {
            return obj.keys({}).length !== 0;
        } else {
            for( var prop in obj ) {
                if( obj.hasOwnProperty(prop) )
                    return false;
            }
            return true;
        }
    }
    function textError(msg) {
        errorDialog.set("content", msg);
        errorDialog.show();
    }
    function xhrError(err) {
        var errObj = JSON.parse(err.response.text);
        if( typeof errObj.error !== "undefined" && errObj.error !== null ) {
            if( typeof errObj.error.message !== "undefined" ) {
                errorDialog.set("title", errObj.error.message);
            }
            if (typeof errObj.error.exception !== "undefined") {
                errorDialog.set("content", errObj.error.exception[0].message.replace("\n", "<br>"));
            }
        } else {
            if( typeof errObj.message !== "undefined" ) {
                errorDialog.set("content", errObj.message);
            } else {
                errorDialog.set("content", errObj);
            }
        }
        errorDialog.show();
    }

    function pageReady() {
        document.querySelector(".loading").classList.add("hidden");
        var i, nodes = document.querySelectorAll(".hide-on-load");
        for( i = 0; i < nodes.length; i++ ) {
            nodes[i].classList.remove("hide-on-load");
        }

    }

    function formatDate(value) {
        var date = new Date(), year, month, day;
        date.setTime(value * 1000);
        year = date.getFullYear();
        month = date.getMonth() + 1,
                day = date.getDate();
        return year + '-' + month + '-' + day;
    }

    function showHistory(historyContentPane, historyLog) {
        var i, date, dateText, d, dataText, h, historyHtml;
        date = new Date();
        historyHtml = "<ul>";
        for( i = 0; i < historyLog.length; i++ ) {
            h = historyLog[i];
            if( h.username === null ) {
                h.username = '';
            }
            date.setTime(h.timestamp.timestamp * 1000);
            dateText = (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear() + " " + date.getHours() + ":" + date.getMinutes();
            dataText = [];
            for( d in h.data ) {
                dataText.push(d + ' set to ' + h.data[d]);
            }
            historyHtml += "<li>" + dateText + " " + h.username + " " + h.action + " " + dataText.join(', ') +
                    "</li>";
        }
        historyHtml += "</ul>";
        if( historyHtml.length > 0 ) {
            historyContentPane.set("content", historyHtml);
        } else {
            historyContentPane.set("content", "");
        }
    }

    return {
        confirmAction: confirmAction,
        formatDate: formatDate,
        pageReady: pageReady,
        isEmpty: isEmpty,
        textError: textError,
        xhrError: xhrError,
        showHistory: showHistory,
        constant: {
            MAX_PHONE_NUMBERS: 5,
            MAX_ADDRESSES: 3,
            MAX_CONTACTS: 5,
            MAX_BRANDS: 20
        }
    };
});
//# sourceURL=common.js