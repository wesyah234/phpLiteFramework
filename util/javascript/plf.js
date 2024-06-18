/*
* ====================================================================
*
* License:      GNU General Public License
*
* Copyright (c) 2005 Centare Group Ltd.  All rights reserved.
*
* This file is part of PHP Lite Framework
*
* PHP Lite Framework is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2.1
* of the License, or (at your option) any later version.
*
* PHP Lite Framework is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* Please refer to the file license.txt in the root directory of this
* distribution for the GNU General Public License or see
* http://www.gnu.org/licenses/lgpl.html
*
* You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* ====================================================================
*
*/

// plf.js Java script include file. 
// Included in the framework automatically 

// http://jsfiddle.net/a_incarnati/kqo10jLb/4/
function formatDate(date) {
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var ampm = hours >= 12 ? 'pm' : 'am';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0' + minutes : minutes;
    var strTime = hours + ':' + minutes + ' ' + ampm;
    return date.getMonth() + 1 + "/" + date.getDate() + "/" + date.getFullYear() + " " + strTime;
}

// this is used by addLogoutWarningPopup in frameworkFunctions to pop a message on the top of the page and  push content down
// it also flashes a bit to catch the eye.
function jquerypoptop(msg) {
    var el = document.createElement("div");
    el.setAttribute('class', 'alert alert-danger alert-dismissable');
    el.setAttribute('id', 'logoutwarning');
    el.style.padding = "30px 60px 30px 60px";
    el.innerHTML = '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><p>' + msg + '</p>';
    $('body').prepend(el);
    $('#logoutwarning').fadeToggle(1200).fadeToggle(600).fadeToggle(1200).fadeToggle(600).fadeToggle(1200).fadeToggle(600);
}

/**
 * This is used by addHeartbeat() to do an ajaxy heartbeat ping.  URL it will hit is the root framework url, defined in every project with the
 * constant: FRAMEWORKURL. This will return a small amount of bytes.  Only use this for apps where you absolutely don't want a timeout
 * when user is inactive.
 * @param url
 */
function jqueryheartbeat(url) {
    $.ajax({
        type : 'GET',
        url : url,
        cache:false,
        dataType : 'html',
    });
}


// thanks to: 
// http://stackoverflow.com/a/2044793/2799545
function selectElementContents(el) {
    var body = document.body, range, sel;
    if (document.createRange && window.getSelection) {
        range = document.createRange();
        sel = window.getSelection();
        sel.removeAllRanges();
        try {
            range.selectNodeContents(el);
            sel.addRange(range);
        } catch (e) {
            range.selectNode(el);
            sel.addRange(range);
        }
    } else if (body.createTextRange) {
        range = body.createTextRange();
        range.moveToElementText(el);
        range.select();
    }
}


// new version using jquery... old version used prototype/scriptaculous
function toggle_visibility_sa(id) {
    var theControl = jQuery('#' + id);
    if (theControl.is(':visible')) {
        theControl.fadeOut("slow");
    }
    else {
        theControl.fadeIn("slow");
    }
}

// here's a vesion that just sets the display to none, no javascript library needed
function toggle_visibility_sa_nolib(id) {
    var e = document.getElementById(id);
    if (e.style.display == 'none')
        e.style.display = 'block';
    else
        e.style.display = 'none';
}



/**
 *
 * Function called from makeAjaxSelect() from frameworkFunctions.php to update a div tag from
 * an Ajax select drop down list. This version uses jquery... we switched to jquery type syntax
 * because prototype's Ajax.Updater was not able to find <tr id="rowid"> style elements in IE.
 *
 **/
function ajaxSelect(url, div, controlName, paramName) {
    var pars;
    if (paramName != '') {
        var selectedValue = controlName.value;
        pars = paramName + '=' + selectedValue;
    }
    if (document.getElementById(div) != null) {
        /*jQuery("#"+div).load(url, pars);*/
        jQuery.get(url, pars,
            function (data) {
                jQuery("#" + div).html(data, pars);
            }
        );
    }
    else {
        jQuery.get(url, pars);
    }
}

/**
 * This method is called when user clicks out of an editable cell in a table built with MyTable
 * @param url
 * @param div
 * @param primaryKeyFieldname
 * @param primaryKeyValue
 * @param editedFieldName
 * @param controlName
 */
function ajaxTableEdit(url, div, primaryKeyFieldname, primaryKeyValue, editedFieldName, controlName) {
    var pars;
    pars = 'cell=' + controlName.id + '&textContent=' + encodeURIComponent(controlName.textContent.trim())  + '&primaryKeyFieldname=' + primaryKeyFieldname + '&primaryKeyValue=' + primaryKeyValue+ '&' + primaryKeyFieldname + '=' + primaryKeyValue+ '&' + editedFieldName + '=' + encodeURIComponent(controlName.innerText.trim())+ '&ajaxeditXYZ123=1';
    if (document.getElementById(div) != null) {
        jQuery.post(url,
            pars,
            function (data) {
                jQuery("#" + div).html(data);
            }
        );
    }
    else {
        jQuery.get(url);
    }
}
/**
 * This method is called when user clicks on an editable checkbox in a table built with MyTable. Very much like the above ajaxTableEdit but it does a check on the "checked" property of the control to see if it should pass down a 1 or a 0.  Note, this will not adhere to the CHECKBOX_CHECKED property from PHP land.
 * @param url
 * @param div
 * @param primaryKeyFieldname
 * @param primaryKeyValue
 * @param editedFieldName
 * @param controlName
 */
function ajaxTableEditCheckbox(url, div, primaryKeyFieldname, primaryKeyValue, editedFieldName, controlName) {
    if (controlName.checked) {
        valueToPassDown=1;
    }
    else {
        valueToPassDown=0;
    }
    var pars;
    pars = 'cell=' + controlName.id + '&textContent=' + valueToPassDown + '&primaryKeyFieldname=' + primaryKeyFieldname + '&primaryKeyValue=' + primaryKeyValue+ '&' + primaryKeyFieldname + '=' + primaryKeyValue+ '&' + editedFieldName + '=' + valueToPassDown + '&ajaxeditXYZ123=1';
    if (document.getElementById(div) != null) {
        jQuery.post(url,
            pars,
            function (data) {
                jQuery("#" + div).html(data);
            }
        );
    }
    else {
        jQuery.get(url);
    }
}
/**
 * This method is called when user clicks on an editable select in a table built with MyTable. Very much like the above ajaxTableEdit
 * @param url
 * @param div
 * @param primaryKeyFieldname
 * @param primaryKeyValue
 * @param editedFieldName
 * @param controlName
 */
function ajaxTableEditSelect(url, div, primaryKeyFieldname, primaryKeyValue, editedFieldName, controlName) {
    /** get the value from the select control**/
    var selectedValue = controlName.value;
    var pars;
    pars = 'cell=' + controlName.id + '&textContent=' + encodeURIComponent(selectedValue)  + '&primaryKeyFieldname=' + primaryKeyFieldname + '&primaryKeyValue=' + primaryKeyValue+ '&' + primaryKeyFieldname + '=' + primaryKeyValue+ '&' + editedFieldName + '=' + encodeURIComponent(selectedValue)+ '&ajaxeditXYZ123=1';
    if (document.getElementById(div) != null) {
        jQuery.post(url,
            pars,
            function (data) {
                jQuery("#" + div).html(data);
            }
        );
    }
    else {
        jQuery.get(url);
    }
}



/**
 *
 * Function called from makeAjaxCheckbox() from frameworkFunctions.php to update a div tag from
 * an checkbox seletion. Needs jquery library which is included in the php lite framework.
 *
 **/
function ajaxCheckbox(url, div, controlName, paramName) {
    var pars;
    if (paramName != '') {
        if (controlName.checked) {
            pars = paramName + '=1';
        }
        else {
            pars = paramName + '=0';
        }
    }
    if (document.getElementById(div) != null) {

        /*jQuery("#"+div).load(url, pars);*/

        jQuery.get(url, pars,
            function (data) {
                jQuery("#" + div).html(data);
            }
        );

    }
    else {
        jQuery.get(url, pars);
    }
}


/**
 *
 * Function called from makeAjaxText() from frameworkFunctions.php to update a div tag based
 * on what is typed in a textbox. Needs jquery library which is included in the php lite framework.
 * optionally, we can pass in a delay timer (in milliseconds) which controls how long it sits before
 * firing the call to the url, for search boxes where people are typing fast.
 *
 **/
var ajaxTextTimeout = null;

function ajaxText(url, div, controlName, paramName, delayBeforeFiring) {
    if (ajaxTextTimeout) {
        clearTimeout(ajaxTextTimeout);

    }
    ajaxTextTimeout = setTimeout(function () {

        var pars;
        if (paramName != '') {
            pars = paramName + '=' + controlName.value;
        }
        if (document.getElementById(div) != null) {

            /*jQuery("#"+div).load(url, pars);*/

            jQuery.post(url, pars,
                function (data) {
                    jQuery("#" + div).html(data);
                }
            );

        }
        else {
            jQuery.post(url, pars);
        }
    }, delayBeforeFiring);
}

function ajaxTextarea(url, div, controlName, paramName, delayBeforeFiring) {
    if (ajaxTextTimeout) {
        clearTimeout(ajaxTextTimeout);
    }
    ajaxTextTimeout = setTimeout(function () {

        var pars;
        if (paramName != '') {
            pars = paramName + '=' + controlName.value;
        }
        if (document.getElementById(div) != null) {

            /*jQuery("#"+div).load(url, pars);*/

            jQuery.post(url, pars,
                function (data) {
                    jQuery("#" + div).html(data);
                }
            );

        }
        else {
            jQuery.post(url, pars);
        }
    }, delayBeforeFiring);
}
