document.observe('dom:loaded', function(evt) {
    chosenInit();
});

function chosenInit() {
    var config = {
        'select'    : {disable_search_threshold:10}
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
}
 jQuery(document).ready(function() {
    jQuery('body').bind('focusin focus', function(e){
      e.preventDefault();
    })
});