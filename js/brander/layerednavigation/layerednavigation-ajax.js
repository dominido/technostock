var layerednavigation_working  = false;
var layerednavigation_blocks   = {};

function layerednavigation_ajax_fallback_mode() {
    var myNav = navigator.userAgent.toLowerCase();
    var isIE = (myNav.indexOf('msie') != -1) ? parseInt(myNav.split('msie')[1]) : false;
    return isIE == 7 || isIE == 8;
}

function layerednavigation_ajax_init(){
    if (layerednavigation_ajax_fallback_mode()) {
        return;
    }

    $$('div.block-layered-nav a', layerednavigation_toolbar_selector + ' a').
        each(function(e){
            var p = e.up();
            if (p.hasClassName('layerednavigation-cat') || p.hasClassName('layerednavigation-clearer')){
                return;
            }

            e.onclick = function(){
                if (this.hasClassName('checked')) {
                    this.removeClassName('checked');
                } else {
                    this.addClassName('checked');
                }

                var s = this.href;
                if (s.indexOf('#') > 0){
                    s = s.substring(0, s.indexOf('#'))
                }
                layerednavigation_ajax_push_state(s);
                layerednavigation_ajax_request(s);
                return false;
            };
        });

    $$('div.block-layered-nav select.layerednavigation-ajax-select', layerednavigation_toolbar_selector + ' select').
        each(function(e){
            e.onchange = 'return false';
            Event.observe(e, 'change', function(e){
                layerednavigation_ajax_push_state(this.value);
                layerednavigation_ajax_request(this.value);
                Event.stop(e);
            });
        });

    jQuery('.item .product-name').dotdotdot();
    //price slider for mobile fix
    var handle = jQuery( ".slider-wrap .handle" );
    handle.draggable({
        axis: 'x',
        containment: jQuery('.slider-wrap'),
        revert: true,
        stop: function (){
            var l = ( 100 * parseFloat(jQuery(this).css("left")) / parseFloat(jQuery(this).parent().css("width")) )+ "%" ;
            var t = ( 100 * parseFloat(jQuery(this).css("top")) / parseFloat(jQuery(this).parent().css("height")) )+ "%" ;
            jQuery(this).css("left" , l);
            jQuery(this).css("top" , t);
        }
    });
}

function layerednavigation_get_created_container()
{
    var elements = document.getElementsByClassName('layerednavigation-page-container');
    return (elements.length > 0) ? elements[0] : null;
}

function layerednavigation_get_container()
{
    var createdElement = layerednavigation_get_created_container();
    if (!createdElement) {
        var container_element = null;

        var elements = $$('div.category-products');
        if (elements.length == 0) {
            container_element = layerednavigation_get_empty_container();
        } else {
            container_element = elements[0];
        }

        if (!container_element) {
            console.debug('Please add the <div class="layerednavigation-page-container"> to the list template as per installtion guide. Enable template hints to find the right file if needed.');
        }

        container_element.wrap('div', { 'class': 'layerednavigation-page-container', 'id' : 'layerednavigation-page-container' });

        createdElement = layerednavigation_get_created_container();

        $(createdElement).insert({ bottom : '<div style="display:none" class="layerednavigation-overlay"><div></div></div>'});
    }
    return createdElement;
}

function layerednavigation_get_empty_container()
{
    var notes = document.getElementsByClassName('note-msg');
    if (notes.length == 1) {
        return notes[0];
    }
}

function layerednavigation_ajax_push_state(url) {
    if (typeof window.history.pushState === 'function') {
        window.history.pushState({url: url}, '', url);
    }
}

function layerednavigation_ajax_request(url){
    /*
     * Clean hash param to avoid scrolling page down
     */
    if (typeof amscroll_object != 'undefined') {
        amscroll_object.setHashParam('page', null);
        amscroll_object.setHashParam('top', null);

        amscroll_params.url = url;
        amscroll_object.setUrl(url);
    }

    var block = layerednavigation_get_container();

    if (block && layerednavigation_scroll_to_products) {
        block.scrollTo();
    }

    layerednavigation_working = true;

    $$('div.layerednavigation-overlay').each(function(e){
        e.show();
    });

    var request = new Ajax.Request(url,{
            method: 'get',
            parameters:{'is_ajax':1},
            onSuccess: function(response){
                try {
                    var data = response.responseText;
                    if(!data.isJSON()){
                        throw new EventException('Cannot convert response data to JSON');
                    }

                    data = data.evalJSON();
                    if (!data.page || !data.blocks){
                        throw new EventException('Invalid data structure in response');
                    }
                    layerednavigation_ajax_update(data);

                } catch (e) {
                    console.log(e.message);
                    setLocation(url);
                }

                layerednavigation_working = false;
                layerednavigation_skip_hash_change = false;

                doGridItemsEqualHeight(jQuery);
                setPageThemeActions(jQuery);
                //layeredToggle();
            },
            onFailure: function(){
                layerednavigation_working = false;
                setLocation(url);
            }
        }
    );
}

function layerednavigation_get_first_descendant(element) {

    var targetElement = element.firstChild;
    if(typeof element.firstDescendant != "undefined") {
        targetElement = element.firstDescendant();
    }
    return targetElement;
}

function layerednavigation_ajax_update(data){

    //update category (we need all category as some filters changes description)
    var tmp = document.createElement('div');
    tmp.innerHTML = data.page;

    var title = data.title;
    if (title) {
        $$('title')[0].update(title);
    }

    var block = layerednavigation_get_container();
    if (block) {
        var targetElement = layerednavigation_get_first_descendant(tmp);
        /* move top filter before container */
        var layerednavigationFiltersTop = block.select('.layerednavigation-filters-top').first();
        if(layerednavigationFiltersTop){
            var parent = block.parentNode;
            parent.insertBefore(layerednavigationFiltersTop, block);
        }
        var colLeft = block.select('.col-left').first();
        if(colLeft){
            var parent = block.parentNode;
            parent.insertBefore(colLeft, block);
        }

        /*
         * If returned element is not HTML tag
         */
        if (targetElement == null) {
            tmp.innerHTML = '<p class="note-msg">' + data.page + '</p>';
            targetElement = layerednavigation_get_first_descendant(tmp);
        }
        block.parentNode.replaceChild(targetElement, block);
        if (typeof AmConfigurableData != 'undefined') {
            try{
                targetElement.innerHTML.evalScripts();
            }
            catch(ex){
                console.debug(ex);
            }
        }
    }

    var blocks = data.blocks;
    for (var id in blocks){
        var html   = blocks[id];
        if (html){
            tmp.innerHTML = html;
        }

        block = $$('div.'+id)[0];
        if (html){

            if (!block){
                if(!colLeft && id == 'layerednavigation-filters-left'){
                    var blockleft = $$('.category-products')[0];
                    if (blockleft) {
                        var targetElement = layerednavigation_get_first_descendant(tmp);
                        blockleft.parentNode.insertBefore(targetElement, blockleft);
                    }
                } else {
                    block = layerednavigation_blocks[id]; // the block WAS in the structure a few requests ago
                    layerednavigation_blocks[id] = null;
                }
            }
            if (block){
                var targetElement = layerednavigation_get_first_descendant(tmp);

                block.parentNode.replaceChild(targetElement, block);
            }
        }
        else { // no filters returned, need to remove
            if (block){
                var empty = document.createTextNode('');
                layerednavigation_blocks[id] = empty; // remember the block in the DOM structure
                block.parentNode.replaceChild(empty, block);
            }
        }
    }

    if (typeof layerednavigation_jquery_init !== 'undefined') {
        layerednavigation_jquery_init();
    }
    layerednavigation_start();
    layerednavigation_ajax_init();
    layerednavigation_move_top_filter();

    try {
        var categoryProducts = $$('.category-products, .catblocks').first();
        var colLeft = layerednavigation_get_container().parentNode.select('.col-left').first();
        if(colLeft && categoryProducts){
            var parent = categoryProducts.parentNode;
            parent.insertBefore(colLeft, categoryProducts);
        }
        layerednavigation_external();
    } catch (e) {
        console.debug(e);
    }

    $$('div.block-layered-nav select.layerednavigation-ajax-select', layerednavigation_toolbar_selector + ' select')
        .each(function(element) {
            new Chosen(element,{disable_search_threshold:10});
        });
}

document.observe("dom:loaded", function(event) {
    layerednavigation_ajax_init();

    if (typeof window.history.replaceState === "function") {
        window.history.replaceState({url: document.URL}, document.title);

        setTimeout(function() {
            /*
              Timeout is a workaround for iPhone
              Reproduce scenario is following:
              1. Open category
              2. Use pagination
              3. Click on product
              4. Press "Back"
              Result: Ajax loads the same content right after regular page load
             */
            window.onpopstate = function(e){
                if(e.state){
                    layerednavigation_ajax_request(e.state.url);
                }
            };
        }, 0)
    }

});

var layerednavigation_toolbar_selector = 'div.toolbar';
var layerednavigation_scroll_to_products = false;

function layerednavigation_external(){
    //add here all external scripts for page reloading
    // like igImgPreviewInit(); 
    if (typeof amscroll_object != 'undefined') {
        amscroll_object.init(amscroll_params);
        amscroll_object.bindClick();
    }

    if (typeof layerednavigation_demo != 'undefined') {
        layerednavigation_demo();
    }
    if (typeof AmAjaxObj != 'undefined') {
        AmAjaxShoppCartLoad('button.btn-cart');
    }

    //amfinder
    var amfinderScript = document.getElementById('amfinder_script');
    if (amfinderScript) {
        eval(amfinderScript.innerHTML);
    }

    if (typeof ProductMediaManager != 'undefined') {
        layerednavigation_external_rwd();
    }

    if (typeof amlabel_init == 'function') {
        amlabel_init();
    }

    /**
     * Third-party themes
     */

    if (typeof jQuery != 'undefined' && typeof calculateMenuItemsInRow == 'function') {
        layerednavigation_external_megatron();
    }

    //Ultimo fortis themes
    if (typeof doGridItemsEqualHeight != 'undefined') {
        setTimeout('doGridItemsEqualHeight(jQuery)', 100);
        setTimeout('doGridItemsEqualHeight(jQuery)', 300);
        setTimeout('doGridItemsEqualHeight(jQuery)', 800);
        setTimeout('doGridItemsEqualHeight(jQuery)', 2000);
    }

    // venedor/default
    if (typeof products_grid_resize == 'function') {
        products_grid_resize();
    }

    if (typeof jQuery != 'undefined' && typeof jQuery.resize == 'function') {
        jQuery.resize();
    }

    // Magento 1.9.1 Configurable swatches
    if (typeof ConfigurableSwatchesList != 'undefined') {
        ConfigurableSwatchesList.init();
    }
}

function layerednavigation_external_rwd() {
    enquire.register('(max-width: ' + bp.medium + 'px)', {
        setup: function () {},
        match: function () {
            $j('.block-subtitle--filter').toggleSingle({destruct: true});
            $j('.block-subtitle--filter').toggleSingle();
        },
        unmatch: function () {
            $j('.block-subtitle--filter').toggleSingle({destruct: true});
        }
    });

    /*align Product Grid Actions */
    if ($j('.products-grid').length) {
        var alignProductGridActions = function () {
            // Loop through each product grid on the page
            $j('.products-grid').each(function(){
                var gridRows = []; // This will store an array per row
                var tempRow = [];
                productGridElements = $j(this).children('li');
                productGridElements.each(function (index) {
                    // The JS ought to be agnostic of the specific CSS breakpoints, so we are dynamically checking to find
                    // each row by grouping all cells (eg, li elements) up until we find an element that is cleared.
                    // We are ignoring the first cell since it will always be cleared.
                    if ($j(this).css('clear') != 'none' && index != 0) {
                        gridRows.push(tempRow); // Add the previous set of rows to the main array
                        tempRow = []; // Reset the array since we're on a new row
                    }
                    tempRow.push(this);

                    // The last row will not contain any cells that clear that row, so we check to see if this is the last cell
                    // in the grid, and if so, we add its row to the array
                    if (productGridElements.length == index + 1) {
                        gridRows.push(tempRow);
                    }
                });

                $j.each(gridRows, function () {
                    var tallestProductInfo = 0;
                    $j.each(this, function () {
                        // Since this function is called every time the page is resized, we need to remove the min-height
                        // and bottom-padding so each cell can return to its natural size before being measured.
                        $j(this).find('.product-info').css({
                            'min-height': '',
                            'padding-bottom': ''
                        });

                        // We are checking the height of .product-info (rather than the entire li), because the images
                        // will not be loaded when this JS is run.
                        var productInfoHeight = $j(this).find('.product-info').height();
                        // Space above .actions element
                        var actionSpacing = 10;
                        // The height of the absolutely positioned .actions element
                        var actionHeight = $j(this).find('.product-info .actions').height();

                        // Add height of two elements. This is necessary since .actions is absolutely positioned and won't
                        // be included in the height of .product-info
                        var totalHeight = productInfoHeight + actionSpacing + actionHeight;
                        if (totalHeight > tallestProductInfo) {
                            tallestProductInfo = totalHeight;
                        }

                        // Set the bottom-padding to accommodate the height of the .actions element. Note: if .actions
                        // elements are of varying heights, they will not be aligned.
                        $j(this).find('.product-info').css('padding-bottom', actionHeight + 'px');
                    });
                    // Set the height of all .product-info elements in a row to the tallest height
                    $j.each(this, function () {
                        $j(this).find('.product-info').css('min-height', tallestProductInfo);
                    });
                });
            });
        }
        alignProductGridActions();
    }


    jQuery('.toggle-content').each(function () {
        var wrapper = jQuery(this);

        var hasTabs = wrapper.hasClass('tabs');
        var startOpen = wrapper.hasClass('open');

        var dl = wrapper.children('dl:first');
        var dts = dl.children('dt');
        var panes = dl.children('dd');
        var groups = new Array(dts, panes);

        //Create a ul for tabs if necessary.
        if (hasTabs) {
            var ul = jQuery('<ul class="toggle-tabs"></ul>');
            dts.each(function () {
                var dt = jQuery(this);
                var li = jQuery('<li></li>');
                li.html(dt.html());
                ul.append(li);
            });
            ul.insertBefore(dl);
            var lis = ul.children();
            groups.push(lis);
        }

        //Add "last" classes.
        var i;
        for (i = 0; i < groups.length; i++) {
            groups[i].filter(':last').addClass('last');
        }

        function toggleClasses(clickedItem, group) {
            var index = group.index(clickedItem);
            var i;
            for (i = 0; i < groups.length; i++) {
                groups[i].removeClass('current');
                groups[i].eq(index).addClass('current');
            }
        }

        //Toggle on tab (dt) click.
        dts.on('click', function (e) {
            //They clicked the current dt to close it. Restore the wrapper to unclicked state.
            if (jQuery(this).hasClass('current') && wrapper.hasClass('accordion-open')) {
                wrapper.removeClass('accordion-open');
            } else {
                //They're clicking something new. Reflect the explicit user interaction.
                wrapper.addClass('accordion-open');
            }
            toggleClasses(jQuery(this), dts);
        });

        //Toggle on tab (li) click.
        if (hasTabs) {
            lis.on('click', function (e) {
                toggleClasses(jQuery(this), lis);
            });
            //Open the first tab.
            lis.eq(0).trigger('click');
        }

        //Open the first accordion if desired.
        if (startOpen) {
            dts.eq(0).trigger('click');
        }
    });
}

function layerednavigation_external_megatron() {
    var windowWidth = window.innerWidth || document.documentElement.clientWidth;
    var animate = jQuery(".notouch .animate");
    var animateDelay = jQuery(".notouch .animate-delay-outer");
    var animateDelayItem = jQuery(".notouch .animate-delay");
    if (windowWidth > 767) {
        animate.bind("inview", function (event, visible) {
            if (visible && !jQuery(this).hasClass("animated")) jQuery(this).addClass("animated")
        });
        animateDelay.bind("inview", function (event, visible) {
            if (visible && !jQuery(this).hasClass("animated")) {
                var j = -1;
                var $this = jQuery(this).find(".animate-delay");
                $this.each(function () {
                    var $this = jQuery(this);
                    j++;
                    setTimeout(function () {
                        $this.addClass("animated")
                    }, 200 * j)
                });
                jQuery(this).addClass("animated")
            }
        })
    } else {
        animate.each(function () {
            jQuery(this).removeClass("animate")
        });
        animateDelayItem.each(function () {
            jQuery(this).removeClass("animate-delay")
        })
    }
}
