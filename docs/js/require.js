String.prototype.format = function(data) {
  var output   = this,
      flatData = {};
  (function flatten( obj, prefix ) {
    prefix = prefix || '';
    Object.keys(obj).forEach(function( key ) {
      var compositeKey = prefix + key;
      switch(typeof obj[key]) {
        case 'string':
        case 'number':
          flatData[compositeKey] = obj[key];
          break;
        case 'object':
          flatData[compositeKey] = obj[key];
          flatten( obj[key], compositeKey + '.' );
          break;
      }
    });
  })(data);
  Object.keys(flatData).forEach(function(key) {
    while(output.indexOf('{'+key+'}')>=0) output = output.replace('{'+key+'}',flatData[key]);
  });
  return output;
};
window.require=window.define= (function() {
  var a=function(o) {
        return Object.keys(o).map(function(k){return o[k]});
      },
      q=[],
      m={},
      k=[],
      l=function(n){if(k.indexOf(n)>=0)return;k.push(n);var x=new XMLHttpRequest();x.onreadystatechange=function(){if(x.readyState==XMLHttpRequest.DONE&&x.status==200)eval(x.responseText)};x.open('GET',r.uri+n+'.js',!0);x.send();},p=function(){var r=!0,ar=[],e=q.shift();if(!e)return;if(e.s&&m[e.s]){q.length&&setTimeout(p,5);return;}e.o.map(function(d){if(!r)return;if(m[d]){ar.push(m[d])}else{r=!1;q.push(e);l(d)}});if(r){e.s?m[e.s]=e.f.apply(null,ar):e.f.apply(null,ar)}q.length&&setTimeout(p,5)};function r(){var e={s:null,o:[],f:function(){}};a(arguments).map(function(arg){e[(typeof arg).substr(0,1)]=arg});q.push(e);p()}r.uri='/js/';r.amd=!0;return r
})();
