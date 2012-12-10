/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function clearContactForm() {
    document.getElementById('name').value = '';
    document.getElementById('email').value = '';
    document.getElementById('message').value = '';
    return(true);
}

// Remove the ghost text when the user clicks a box
function removeGhostText(obj,val) {
    if(obj.value == val || val == null) {
        obj.style.color = "#000000";
        obj.value = "";
    }
    
}

// Replace the ghost text if the user decides not to search
function restoreGhostText(obj,val) {
    if(val == null) {
        obj.value = "Search...";
        obj.style.color = "#cfcfcf";

    }
    else if (obj.value == '') {
        obj.style.color = "#cfcfcf";
        obj.value = val;
    }
}

// Change the css color of an object
function changeColor(obj, val) {
    obj.style.color = val;
}

// Replace the ghost text if the user decides not to search
function restoreFriendText(obj,val) {
    obj.style.color = "#cfcfcf";
    if(val == null)
        obj.value = "Find Friends...";
    else
        obj.value = val;
}

function errorField(obj) {
    obj.style.border = "2px solid red";
    
}

// Basic validation on contact form
function contactValidate() {
    var name = document.getElementById('name');
    var email = document.getElementById('email');
    var msg = document.getElementById('message');
    var err = 0;
    
    // does name exist?
    if(!name.value) {
        errorField(name);
        err++;
    }
    
    // first validate existing value, then look for @ in 
    if(!email.value) {
        errorField(email);
        err++;
    } else {
        var str = email.value;
        var pattern = /\S+@\S+/;
        if(!str.match(pattern)) {
            errorField(email);
            err++;
        }
    }
    
    // did they specify a message?
    if(!msg.value) {
        errorField(msg);
        err++;
    }
    
    // submit the form only if there is not an error
    return(err==0);
    
}

// validate the login page
function loginValidate() {
    var login = document.getElementById('email');
    var password = document.getElementById('password');
    var err = 0;
    
    
    if(!login.value) {
        errorField(login);
        err++;
    }   
    
    if(!password.value) {
        errorField(password);
        err++;
    }
    
    return(err==0);
}

function contactCheckValid(obj) {
    if(obj.value) {
        obj.style.border = "2px solid #cfcfcf";
    }
}