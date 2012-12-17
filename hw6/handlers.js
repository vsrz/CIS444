
/**
 * CIS444 - HW6 - By Jeremy Villegas
 * Fall 2012
 */

function validate() {    
    var teamid = document.getElementById('teamid');
    
    if(isNaN(teamid.value) && teamid.value != "" && teamid.value != "TeamID...") {
        teamid.style.border = "2px solid red";
        teamid.value = "";
        teamid.focus();
        return(false);
    }
    
    return(true);    
    
}

function checkGhostText(obj, txt) {
    if(obj.value == '') {
        obj.value = txt;
        obj.style.color = "#acacac";
        obj.style.border = "1px solid grey";
    }
    if(obj.value == txt) {
        obj.style.color = "#acacac";
    }
}

function clearGhostText(obj) {
    obj.value = '';
    obj.style.color = "black";
}



function fontChange(mult) {
    var table = document.getElementById('teams');
    var rows = table.getElementsByTagName('tr');
    
    if(mult == "") mult = 1;
    for(i = 0; i < rows.length; i++) {
        if(rows[i].style.fontSize == "") {
            rows[i].style.fontSize = "1.0em";
        }
            
        rows[i].style.fontSize = parseFloat(rows[i].style.fontSize) + (mult * 0.2) + "em";
    }
    
}

