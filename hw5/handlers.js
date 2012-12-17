var dir = 1;
var clr = 'blue';
var xMax = window.innerWidth-350;
var xMin = 0;
var xEnd = xMax;
var xStart = xMin;

function shiftText() {  
    document.getElementById("xEnd").value = xMax;
    
    window.setInterval("move(1)",1);
}

function clrToggle() {	
    if(clr=='blue') 
        clr = 'red';
    else
        clr = 'blue';
	
    return clr;
}

function setPos(x,y) {
    xObj = document.getElementById('xStart');
    eObj = document.getElementById('xEnd');
    xStart = parseInt(x);
    xEnd = parseInt(y);
    
    // Handle starting point
    if(isNaN(xStart)) {
        xObj.value = '0';
        xStart = 0;
    } else if (xStart > xMax) {
        xObj.value = xMax;
        xStart = xMax;
    } else if (xStart < 0) {
        xObj.value = 0;
        xStart = 0;	
    }
	
    // Handle ending point
    if(isNaN(xEnd)) {
        eObj.value = xMax;
        xEnd = xMax;
    } else if (xEnd > xMax) {
        eObj.value = xMax;
        xEnd = xMax;
    } else if (xEnd < 0) {
        eObj.value = 0;
        xEnd = 0;	
    }

}

function move(cnt) {
    var obj = document.getElementById('panel')
    var xPos = parseInt(obj.style.left);
   
    if(!xPos) xPos = 0;
   
    if(dir == 1) {
        if(xPos + cnt > xEnd) {
            dir = -1;            
        }
    } else {
        if(xPos - cnt < xStart) {
            dir = 1;

        }
    }
   
    if(xPos % 5 == 0) obj.style.color = clrToggle();

    obj.style.left = xPos + (cnt*dir) + 'px';
}

