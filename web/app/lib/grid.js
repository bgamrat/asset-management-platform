define([
    'put-selector/put',
    "app/lib/common",
    "dojo/domReady!"
], function (put, lib) {
    "use strict";

    function renderGridCheckbox(object, value, td) {
        if( value === true ) {
            put(td, "span.dijit.dijitReset.dijitInline.dijitCheckBox.dijitCheckBoxChecked.dijitChecked", "");
        } else {
            put(td, "span.dijit.dijitReset.dijitInline.dijitCheckBox", "");
        }
    }
    ;

    function renderAddress(object, value, td) {
        var a, i, l, segments, content = [], address_lines, address_segments;
        var address;
            if( typeof value === "object" && value.length !== 0 ) {
                address_lines = ['street1', 'street2'];
                address_segments = ['city', 'stateProvince', 'postalCode', 'country'];
                for( a in value ) {
                    address = value[a];
                    if( isNaN(address['type']) ) {
                        content.push(address['type']['type']);
                    } else {
                        content.push(lib.addressTypes[address['type']]);
                    }
                    l = address_lines.length;
                    for( i = 0; i < l; i++ ) {
                        if( address[address_lines[i]] !== null && address[address_lines[i]] !== "" ) {
                            content.push(address[address_lines[i]]);
                        }
                    }
                    segments = [];
                    l = address_segments.length;
                    for( i = 0; i < l; i++ ) {
                        if( address[address_segments[i]] !== null && address[address_segments[i]] !== "" ) {
                            segments.push(address[address_segments[i]]);
                        }
                    }
                    content.push(segments.join(" "));
                }
            }
        
        if( content.length > 0 ) {
            content = content.join("\n");
            put(td, "pre", content);
        }

    }
    ;
    function renderEmail(object, value, td) {
        var i, l, e, content = [], email_lines;
        var email, row;
        if( typeof value === "object" && value.length !== 0 ) {
            email_lines = ['email', 'comment'];
            l = email_lines.length;
            for( e in value ) {
                email = value[e];
                if( isNaN(email['type']) ) {
                    row = email['type']['type'];
                } else {
                    row = lib.emailTypes[email['type']];
                }
                row += " ";
                for( i = 0; i < l; i++ ) {
                    if( email[email_lines[i]] !== null && email[email_lines[i]] !== "" ) {
                        row += email[email_lines[i]] + " ";
                    }
                }
                content.push(row);
                content.push("\n");
            }
        }
        if( content.length > 0 ) {
            content = content.join("\n");
            put(td, "pre", content);
        }

    }
    ;
    function renderPerson(object, value, td) {
        put(td, "span", value + " (" + object.type_text + ")");
    }

    function renderPhone(object, value, td) {
        var i, l, p, content = [], phone_lines;
        var phone, row;
        if( typeof value === "object" && value.length !== 0 ) {
            phone_lines = ['phoneNumber', 'comment'];
            l = phone_lines.length;
            for( p in value ) {
                phone = value[p];
                if( isNaN(phone['type']) ) {
                    row = phone['type']['type'];
                } else {
                    row = lib.phoneTypes[phone['type']];
                }
                row += " ";
                for( i = 0; i < l; i++ ) {
                    if( phone[phone_lines[i]] !== "" && phone[phone_lines[i]] !== null ) {
                        row += phone[phone_lines[i]] + " ";
                    }
                }
                content.push(row);
                content.push("\n");
            }
        }
        if( content.length > 0 ) {
            content = content.join("\n");
            put(td, "pre", content);
        }

    }
    ;


    return {
        renderAddress: renderAddress,
        renderEmail: renderEmail,
        renderGridCheckbox: renderGridCheckbox,
        renderPerson: renderPerson,
        renderPhone: renderPhone
    };
});
//# sourceURL=grid.js