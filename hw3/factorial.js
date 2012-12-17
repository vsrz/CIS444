
function factorial(n) {
    var result = 1;
    
    for (var x=1;x<=n;x++) {
        result = result * x;
        
    }
    return result;
}

var n = prompt('Enter a number')
document.write('\
        <table border="1" class="tblFactorial">\
            <caption>\
                ville017 HW3 - Factorial\
            </caption>\
            <thead>\
                <tr><th>Number</th><th>Value</th></tr>\
            </thead>\
            </tbody>\
')
for (var x=1;x<=n;x++) {
    document.write('<tr')
    if(x%2>0) {
        document.write(' class="altrow"')
    }
    document.write('><td>' + x + '</td><td>' + factorial(x) + '</td></tr>') 
}

document.write('</tbody></table>')

