require('data-source', ['ajax', 'domchange'], function(ajax, onDomChange) {
  function listen( element, event, handler ) {
    if ( typeof element == 'string' ) {
      handler = event;
      event   = element;
      element = null;
    }
    if ( !element && event == 'change' ) {
      onDomChange(handler,10);
      return;
    }
    var add = element.addEventListener || element.attachEvent;
    add = add.bind(element);
    add(event,handler);
  }
  function render( template, data ) {
    if ( Array.isArray(data) ) {
      return data.map(render.bind(null,template)).join('');
    }
    return template.format(data);
  }
  function process( element ) {
    if(!element) {
      document.querySelectorAll('[data-source]').forEach(process);
      return;
    }
    if(!element.getAttribute) {
      document.querySelectorAll('[data-source]').forEach(process);
      return;
    }
    var template = element.innerHTML,
        url      = element.getAttribute('data-source');
    if ( url.indexOf('{') >= 0 ) {
      return;
    }
    element.innerHTML = '';
    element.removeAttribute('data-source');
    ajax(url)
      .then(function( data ) {
        element.innerHTML = render(template,data);
      })
  }

  listen('change',process);
  listen(document.body,'click',process);
  process();
});
