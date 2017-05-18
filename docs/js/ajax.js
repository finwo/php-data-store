(function(exports) {

  var factories = [
    function () {return new XMLHttpRequest()},
    function () {return new ActiveXObject("Msxml2.XMLHTTP")},
    function () {return new ActiveXObject("Msxml3.XMLHTTP")},
    function () {return new ActiveXObject("Microsoft.XMLHTTP")}
  ];

  function httpObject() {
    var xmlhttp = false;
    factories.forEach(function(factory) {
      try {
        xmlhttp = xmlhttp || factory();
      } catch(e) {
        return;
      }
    });
    return xmlhttp;
  }

  function serializeObject(obj,prefix) {
    var str = [], p;
    for(p in obj) {
      if (obj.hasOwnProperty(p)) {
        var k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
        str.push((v !== null && typeof v === "object") ?
          serializeObject(v, k) :
        encodeURIComponent(k) + "=" + encodeURIComponent(v));
      }
    }
    return str.join("&");
  }

  function SimplePromise()
  {
    var self  = this,
        queue = [],
        doneFunction = null,
        failFunction = function(e){throw e;},
        started      = false,
        running      = false;
    this.then = function(callback) {
      queue.push(callback);
      if(started&&!running) self.run();
      return self;
    };
    this.fail = function(callback) {
      failFunction = callback;
      if(started&&!running) self.run();
      return self;
    };
    this.done = function(callback) {
      doneFunction = callback;
      if(started&&!running) self.run();
      return self;
    };
    this.start = function(callback) {
      queue.push(callback);
      self.run();
      return self;
    };
    this.run = function(data, done) {
      started = true;
      running = true;
      var returnValue;
      if(this!=self) {
        done = this;
      }
      while(queue.length) {
        var func = queue.shift();
        if (!func) {
          running = false;
          if (typeof done === 'function')         return done(data);
          if (typeof doneFunction === 'function') return doneFunction(data);
          return data;
        }
        try {
          returnValue = null;
          returnValue = func.call(null, data, self.run.bind(done), failFunction);
        } catch(e) {
          running = false;
          if (typeof failFunction === 'function') return failFunction(e, data);
          throw e;
        }
        if(!returnValue) {
          return;
        }
      }
      running = false;
      if (typeof done === 'function')         return done(data);
      if (typeof doneFunction === 'function') return doneFunction(data);
      return data;
    };
  }

  function ajax( uri, options ) {
    options = options || {};

    var method  = (options.method || 'GET').toUpperCase(),
        data    = options.data || {},
        promise = new SimplePromise();

    var req = httpObject();
    if (!req) return;

    // Insert data?
    if(Object.keys(data).length) {
      var serializedData = serializeObject(data);
      switch(method) {
        case 'GET':
          uri += ((uri.indexOf('?')===false) ? '?' : '&') + serializedData;
          data = {};
          break;
        case 'POST':
          req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
          break;
      }
    }

    // Let's start
    req.open(method, uri, true);

    return promise.start(function(d, resolve, reject) {
      req.onreadystatechange = function() {
        if(req.readyState!=4) return;
        if(req.status<200||req.status>=300) {
          reject('Invalid response');
        }
        var receivedData = req.responseText;
        try {
          receivedData = JSON.parse(receivedData);
        } catch(e) {
          // Nothing to worry about
        }
        resolve(receivedData);
      };
      req.send(data);
    });
  }

  // Export our freshly created plugin
  exports.ajax = ajax;
  if (typeof define === 'function' && define.amd) {
    define('ajax', function() {
      return ajax;
    })
  }

  // Attach to window as well
  if (typeof window !== 'undefined') {
    window.ajax = ajax;
  }

})(typeof exports === 'object' && exports || this);
