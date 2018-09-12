define([
    "put-selector/put",
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

    function renderGridRadioButton(object, value, td) {
        var cl = td.classList.length, checked;;
        var column = td.classList[cl-1].replace("field-","");
        checked = ( value === true || value === "on") ? "[checked]" : "";
        put(td,"input[type=radio][name=rb-"+column+"][data-id="+object.id+"]"+checked);
    }
    ;

    function renderAddress(object, value, td) {
        var a, i, l, segments, content = [], address_lines, address_segments;
        var address;
        if( object === null ) {
            return;
        }
        if( typeof value[0] === "undefined" ) {
            value = [value];
        }
        if( typeof value === "object" && value !== null && value.length !== 0 ) {
            address_lines = ["street1", "street2"];
            address_segments = ["city", "state_province", "postal_code", "country"];
            for( a in value ) {
                address = value[a];
                if( typeof address["type"] === "undefined" ) {
                    continue;
                } else {
                    if( isNaN(address["type"]) ) {
                        content.push(address["type"]["type"]);
                    } else {
                        content.push(lib.addressTypes[address["type"]]);
                    }
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
    function renderContacts(object, value, td) {
        var i, l;
        if( typeof object.contacts !== "undefined" && object.contacts !== null ) {
            l = object.contacts.length;
            for( i = 0; i < l; i++ ) {
                this.renderPerson(object.contacts[i], object.contacts[i], td);
                this.renderPhone(object.contacts[i].phones, object.contacts[i].phones, td);
                this.renderEmail(object.contacts[i].emails, object.contacts[i].emails, td);
                this.renderAddress(object.contacts[i].addresses, object.contacts[i].addresses, td);
                put(td, "hr");
            }
        }
    }
    ;
    function renderEmail(object, value, td) {
        var e;
        var email, ul = null, li, t, link, c;
        if( object === null ) {
            return;
        }
        if( typeof value === "object" && value !== null && value.length !== 0 ) {
            ul = document.createElement("ul");
            for( e in value ) {
                li = document.createElement("li");
                email = value[e];
                if( isNaN(email["type"]) ) {
                    t = document.createTextNode(email["type"]["type"] + " ");
                } else {
                    t = document.createTextNode(lib.emailTypes[email["type"]] + " ");
                }
                if( email["email"] !== null && email["email"] !== "" ) {
                    link = document.createElement("a");
                    link.href = "mailto:" + email["email"];
                    link.textContent = email["email"];
                    li.appendChild(t);
                    li.appendChild(link);
                    if( typeof email["comment"] !== "undefined" && email["comment"] !== null && email["comment"] !== "" ) {
                        c = document.createTextNode(email["comment"]);
                        li.appendChild(c);
                    }
                }
                ul.appendChild(li);
            }
        }
        if( ul !== null ) {
            put(td, "div", ul);
        }

    }
    ;
    function renderPerson(object, value, td) {
        var type_text;
        if( object === null ) {
            return;
        }
        if( typeof object.type_text === "undefined" ) {
            type_text = object.type.type;
        } else {
            type_text = object.type_text;
        }
        if (typeof object.name === "undefined") {
            object.name = object.firstname + " " + object.lastname;
        }
        put(td, "em", object.name + " (" + type_text + ")");
    }

    function renderPhone(object, value, td) {
        var i, l, p, content = [], phone_lines;
        var phone, row;
        if( object === null ) {
            return;
        }
        if( typeof value === "object" && value !== null && value.length !== 0 ) {
            phone_lines = ["phone", "comment"];
            l = phone_lines.length;
            for( p in value ) {
                phone = value[p];
                if( isNaN(phone["type"]) ) {
                    row = phone["type"]["type"];
                } else {
                    row = lib.phoneTypes[phone["type"]];
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
        renderContacts: renderContacts,
        renderEmail: renderEmail,
        renderGridCheckbox: renderGridCheckbox,
        renderGridRadioButton: renderGridRadioButton,
        renderPerson: renderPerson,
        renderPhone: renderPhone
    };
});
//# sourceURL=grid.js