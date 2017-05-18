require('data-source', ['ajax', 'domchange'], function(ajax, onDomChange) {
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
    var template = element.innerHTML,
        url      = element.getAttribute('data-source');
    element.innerHTML = '';
    element.removeAttribute('data-source');
    ajax(url)
      .then(function( data ) {
        element.innerHTML = render(template,data);
      })
  }
  onDomChange(process,10);
  process();
});
