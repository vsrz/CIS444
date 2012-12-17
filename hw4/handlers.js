

function checkInventory(object) {                
    if(isNaN(object.value)) {
        alert('Quantity is invalid!');                    
        object.value = '';                    
        object.focus();
    } else if(object.value > 99 || object.value < 0) {
        alert('Quantity must be between 0 and 99.');
        object.value = '';
        object.focus();
    } else {
        if(object.value == '') object.value = 0;    
    }
                
    if(object.value != '') {
        calcTotal();
    }
                
}
                
function calcTotal() {
    var total = 0;
    var val;
    val = parseInt(document.getElementById("tApple").value);
    if(isNaN(val)) val = 0;
    total += val * 0.59;
    
    val = parseInt(document.getElementById("tOrange").value);
    if(isNaN(val)) val = 0;
    total += val * 0.49;
    
    val = parseInt(document.getElementById("tBanana").value);
    if(isNaN(val)) val = 0;
    total += val * 0.39;                
                
    document.getElementById('tSubtotal').value = parseFloat(total.toFixed(2));
    return parseFloat(total);
                
}
function alertTotal() { 
    var rate = 0.05;                
    var total = calcTotal();
                
    total *= (rate+1);
    alert('Thank you. Total amount is $' + total.toFixed(2));
}