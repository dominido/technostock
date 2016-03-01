document.observe('dom:loaded', function(evt) {
    var config = {
      'select'                   : {disable_search_threshold:10}
    }
    var results = [];
    for (var selector in config) {
      var elements = $$(selector);
      for (var i = 0; i < elements.length; i++) {
          if(elements[i].visible()) {
              results.push(new Chosen(elements[i],config[selector]));
          }
      }
    }
    return results;
});