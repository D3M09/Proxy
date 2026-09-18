var vm = require('vm');
var fs = require('fs');
var aesCode = fs.readFileSync('C:/xampp/htdocs/infinity/aes.js', 'utf8');
var ctx = {};
vm.createContext(ctx);
vm.runInContext(aesCode, ctx);
var slowAES = ctx.slowAES;

function toNumbers(d){var e=[];d.replace(/(..)/g,function(d){e.push(parseInt(d,16))});return e}
function toHex(d){var f='';for(var g=0;g<d.length;g++)f+=(16>d[g]?'0':'')+d[g].toString(16);return f.toLowerCase()}

var a=toNumbers('f655ba9d09a112d4968c63579db590b4');
var b=toNumbers('98344c2eee86c3994890592585b49f80');
var c=toNumbers('10a73bde9f2b07b0ea3a643ac0a0d636');
console.log('__test=' + toHex(slowAES.decrypt(c, 2, a, b)));
