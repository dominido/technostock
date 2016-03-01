var bp = {
    xsmall: 479,
    small: 599,
    medium: 767,
    large: 979,
    xlarge: 1199
};

function layerednavigation_start(){
    $$('.block-layered-nav .form-button').each(function (e){
        e.observe('click', layerednavigation_price_click_callback);
    });

    $$('.block-layered-nav .input-text[name=layerednavigation-price]').each(function (e){

        e.observe('keypress', layerednavigation_price_click_callback('', this));
    });

    $$('.block-layered-nav .layerednavigation_attr_search').each(function (e) {
        e.observe('keypress', function() {
            layerednavigation_attr_search(e);
        });
    });

    $$('a.layerednavigation-less', 'a.layerednavigation-more').each(function (a){
        a.observe('click', layerednavigation_toggle);
    });

    $$('span.layerednavigation-plusminus').each(function (span){
        span.observe('click', layerednavigation_category_show);
    });

    $$('.block-layered-nav.layerednavigation-collapse-enabled dt').each(function (dt){
        dt.observe('click', layerednavigation_filter_show);
    });

    $$('.block-layered-nav dt img').each(function (img){
        img.observe('mouseover', layerednavigation_tooltip_show);
        img.observe('mouseout', layerednavigation_tooltip_hide);
    });

    $$('.layerednavigation-slider-param').each(function (item) {
        var param = item.value.split(',');
        layerednavigation_slider(param[0], param[1], param[2], param[3], param[4], param[5], item);
    });
}

function layerednavigation_price_click_callback(evt, element) {
    if( !element || element.nodeName == undefined ) element = null;
    if( typeof evt == 'object' && !element){
        element = Event.element(evt);
    }
    if (evt && evt.type == 'keypress' && 13 != evt.keyCode)
        return;

    var prefix = 'layerednavigation-price';
    // from slider
    if (typeof(evt) == 'string'){
        prefix = evt;
    }
    else {
        var el = Event.findElement(evt, 'input');
        if (!Object.isElement(el)){
            el = Event.findElement(evt, 'button');
        }
        prefix = el.name;
    }

    var a = prefix + '-from';
    var b = prefix + '-to';
    //get elements from parent container
    var parent = (element)? element.up('ol'): null;
    var from   = (parent)? parent.select('#' + a).first(): $(a);
    var to     = (parent)? parent.select('#' + b).first(): $(b);

    if(!from ||!to)
        return;

    var numFrom = parseFloat(from.value);
    if (isNaN(numFrom)) {
        numFrom = '';
    }
    var numTo   = parseFloat(to.value);
    if (isNaN(numTo)) {
        numTo = '';
    }

    if (numFrom>numTo && numFrom != '' && numTo != '') numTo = [numFrom, numFrom = numTo][0];

    if (numFrom < 0 || numTo < 0) {
        return;
    }

    var urlElement = (parent)? parent.select('#' + prefix +'-url').first(): $(prefix +'-url');
    var url =  urlElement.value.gsub(a, numFrom).gsub(b, numTo);
    layerednavigation_set_location(url);

}

function layerednavigation_price_focus_callback(evt){
    var el = Event.findElement(evt, 'input');
    if (isNaN(parseFloat(el.value))){
        el.value = '';
    }
}

function layerednavigation_slider(from, to, max_value, prefix, min_value, step, item) {
    if(item)
        var slider =  item.siblings().first();
    if(!slider || typeof slider == 'undefined'){
        var slider = $(prefix);
    }

    max_value = parseFloat(max_value);
    min_value = parseFloat(min_value);
    from = (from === "") ? min_value : parseFloat(from);
    to = (to === "") ? max_value : parseFloat(to);

    var width = parseFloat(slider.offsetWidth);
    if (!width) {
        // filter collapsed, we will wait
        setTimeout(function() {
            layerednavigation_slider(from, to, max_value, prefix, min_value, step, item);
        }, 200);
        return;
    }

    var ratePP = (max_value - min_value) / 100;

    var fromPixel = ((from - min_value) || (from - to != 0 || from != 0)) ? ((from - min_value) / ratePP) : 0;
    var toPixel = ((to - min_value) || (from - to != 0 || to != 0)) ? ((to - min_value) / ratePP) : 100;

    Control.Slider.prototype.translateToPx = function (value) {
        return value + '%';
    };

    Control.Slider.prototype.translateToValue = function (offset) {
        return ((offset/((this.maximumOffset() - this.minimumOffset())-this.handleLength) *
        (this.range.end-this.range.start)) + this.range.start);
    };

    Control.Slider.prototype.updateFinished = function () {
        if (Math.round(this.values[1]) == 100) {
            this.handles[0].addClassName('active');
        } else {
            if (this.handles[0].hasClassName('active')) {
                this.handles[0].removeClassName('active');
            }
        }
        if (this.initialized && this.options.onChange)
            this.options.onChange(this.values.length>1 ? this.values : this.value, this);
        this.event = null;
    };

    new Control.Slider(slider.select('.handle'), slider, {
        range: $R(0, 100),
        sliderValue: [fromPixel, toPixel],
        restricted: true,

        onChange: function (values, element){
            this.onSlide(values, element);
            var fromValue = layerednavigation_round(min_value + ratePP * values[0], step);
            var toValue   = layerednavigation_round(min_value + ratePP * values[1], step);
            if (fromValue != from || toValue != to) {
                layerednavigation_price_click_callback(prefix, element.track);
            }
        },
        onSlide: function amastyOnSlide(values, element) {

            if (isNaN(values[0]) || isNaN(values[1])) {
                return;
            }

            var fromValue = layerednavigation_round(min_value + ratePP * values[0], step);
            var toValue   = layerednavigation_round(min_value + ratePP * values[1], step);

            layerednavigation_update_slider_bar(prefix, values[0], values[1], element.track);

            var parent      = element.track.up('ol');
            var sliderFrom  = parent.select('#' + prefix + '-from').first();
            var sliderTo    = parent.select('#' + prefix + '-to').first();

            if(sliderFrom && sliderTo){
                sliderFrom.value = fromValue;
                sliderTo.value   = toValue;
            }

            sliderFrom = parent.select('#' + prefix + '-from-slider').first();
            sliderTo   = parent.select('#' + prefix + '-to-slider').first();
            if(sliderFrom && sliderTo){
                sliderFrom.update(fromValue)
                sliderTo.update(toValue)
            }
        }
    });
    layerednavigation_update_slider_bar(prefix, fromPixel, toPixel, slider);
}

function layerednavigation_round(value, step) {

    value /= step;
    value = parseInt(value);
    value *= step;
    value = parseFloat(value.toFixed(4));
    return value;
}

function layerednavigation_update_slider_bar(prefix, fromPixel, toPixel, slider) {
    var bar = slider.select('.layerednavigation-slider-bar').first();
    if(!bar || typeof bar == 'undefined'){
        bar = $(prefix + '-slider-bar');
    }

    if (bar) {
        var barWidth = toPixel - fromPixel;
        if (fromPixel == toPixel) {
            barWidth = 0;
        }
        bar.setStyle({
            width : barWidth + '%',
            left : fromPixel + '%'
        });
    }
}

function layerednavigation_toggle(evt){
    var attr = Event.findElement(evt, 'a').id.substr(23);

    $$('.layerednavigation-attr-' + attr).invoke('toggle');

    $$('#layerednavigation-less-' + attr, '#layerednavigation-more-' + attr).invoke('toggle');

    Event.stop(evt);
    return false;
}

function layerednavigation_category_show(evt){
    var span = Event.findElement(evt, 'span');
    var id = span.id.substr(16);

    $$('.layerednavigation-cat-parentid-' + id).invoke('toggle');

    span.toggleClassName('minus');
    Event.stop(evt);

    return false;
}

function layerednavigation_filter_show(evt){
    var dt = Event.findElement(evt, 'dt');
    var isRwd = typeof enquire == 'object' && typeof ProductMediaManager == 'object' && typeof bp == 'object';
    if (isRwd && bp.medium > jQuery('body').width()) {
        return;
    }
    var ol = dt.next('dd').select('ol').first();
    if(!ol.hasClassName('layerednavigation-flag-clickfirst')){
        ol.addClassName('layerednavigation-flag-clickfirst');
        if(!ol.hasClassName('no-display-current')){
            ol.toggleClassName('no-display');
        }
    }
    else{
        ol.toggleClassName('no-display');
    }
    dt.next('dd').select('ol').first().removeClassName('no-display-current');

    dt.toggleClassName('layerednavigation-collapsed');

    Event.stop(evt);
    evt.stopPropagation();
    return false;
}

function layerednavigation_tooltip_show(evt){
    var img = Event.findElement(evt, 'img');
    var txt = img.alt;

    var tooltip = $(img.id + '-tooltip');
    if (!tooltip) {
        tooltip           = document.createElement('div');
        tooltip.className = 'layerednavigation-tooltip';
        tooltip.id        = img.id + '-tooltip';
        tooltip.innerHTML = img.alt;

        document.body.appendChild(tooltip);
    }

    var offset = Element.cumulativeOffset(img);
    tooltip.style.top  = offset[1] + 'px';
    tooltip.style.left = (offset[0] + 30) + 'px';
    tooltip.show();
}

function layerednavigation_tooltip_hide(evt){
    var img = Event.findElement(evt, 'img');
    var tooltip = $(img.id + '-tooltip');
    if (tooltip) {
        tooltip.remove();
    }
}

function layerednavigation_set_location(url){
    if (typeof layerednavigation_working != 'undefined' && !layerednavigation_ajax_fallback_mode()) {
        layerednavigation_ajax_push_state(url);
        return layerednavigation_ajax_request(url);
    }
    else {
        return setLocation(url);
    }
}

function layerednavigation_attr_highlight(li, str)
{
    /*
     * Remove previous highlight
     */
    layerednavigation_attr_unhighlight(li);

    var ch = li.childElements();
    if (ch.length >  0) {
        ch = ch[0];
        if (ch.tagName == 'A') {
            var img = '';
            if (li.readAttribute('img')) {
                img = li.readAttribute('img');
            }
            ch.innerHTML = img + li.readAttribute('data-text').replace(new RegExp(str,'gi'), '<span class="layerednavigation-hightlighted">' + str + '</span>');
        }
    }
}

function layerednavigation_attr_unhighlight(li)
{
    var ch = li.childElements();
    if (ch.length > 0) {
        ch = ch[0];
        if (ch.tagName == 'A') {
            var img = '';
            if (li.readAttribute('img')) {
                img = li.readAttribute('img');
            }
            ch.innerHTML = img + li.readAttribute('data-text');
        }
    }
}


function layerednavigation_attr_search(searchBox){
    var str = searchBox.value.toLowerCase();
    var all = searchBox.up(1).childElements();

    all.each(function(li) {
        if (li.hasAttribute('data-text')) {
            var val = li.getAttribute('data-text').toLowerCase();
            if (!val || val == 'search' || val.indexOf(str) > -1) {
                if (str != '' && val.indexOf(str) > -1) {
                    layerednavigation_attr_highlight(li, str);
                } else {
                    layerednavigation_attr_unhighlight(li);
                }
                li.show();
            }
            else {
                layerednavigation_attr_unhighlight(li);
                li.hide();
            }
        }
    });
}

function layerednavigation_set_one_heihgt() {
    var minHeight = {};
    $$('.layerednavigation-item-top dd').each(function (item) {
        minHeight["pos-" + item.positionedOffset()[1]] = 0;
        item.setAttribute('filter-pos',"pos-" + item.positionedOffset()[1] );
    });

    $$('.layerednavigation-item-top dd').each(function (item) {
        current = item.getHeight();
        if (current > minHeight["pos-" + item.positionedOffset()[1]]) minHeight["pos-" + item.positionedOffset()[1]] = current;
    });
    $$('.layerednavigation-item-top dd').each(function (item) {
        item.setStyle({
            minHeight: minHeight[ item.getAttribute('filter-pos') ] + 'px'
        });
    });
    /*fix problem - vertical scroll takes 7px from width*/
    $$('.layerednavigation-item-top dd ol').each(function (item) {
        if (item.clientHeight != item.scrollHeight) {
            item.setStyle({
                overflowX: 'hidden',
                paddingRight: '7px'
            });
        }
    })
}


function layerednavigation_move_top_filter(){
    setTimeout(function() {
        layerednavigation_set_one_heihgt();
    }, 200);

    var categoryProducts = $$('.category-products, .catblocks').first();
    var layerednavigationFiltersTop = $$('.layerednavigation-filters-top').first();
    if( layerednavigationFiltersTop ) {
        if( categoryProducts ){
            var parent = categoryProducts.parentNode;
            parent.insertBefore(layerednavigationFiltersTop, categoryProducts);
            layerednavigation_rwd_toggle_content();
        }
        if (typeof enquire != 'undefined') {
            enquire.register('(max-width: ' + bp.medium + 'px)', {
                setup: function () {},
                match: function () {
                    var parentByList = $$('.layerednavigation-filters-left #narrow-by-list').first();
                    if(!parentByList){

                        //narrow-by-list
                    }
                    if(parentByList) {
                        $j('.layerednavigation-container-top, ' +/*hide duplicated static block*/
                        '.block-layered-nav .block-title:eq( 1 ),    ' +
                        '.block-content .currently:eq( 1 ),' +
                        '.block-layered-nav .block-content .block-subtitle:not(.block-subtitle--filter):eq( 1 )').hide();

                        $$('.layerednavigation-item-top #narrow-by-list').each(function (item) {
                            if( 'layerednavigation-filter-position-both' == item.getAttribute('data-position') ){
                                item.hide();
                            }
                            else {
                                item.childElements().each(function (child) {
                                        child.addClassName('layerednavigation-toggle-selector');
                                        child.setStyle({
                                            minHeight: 0
                                        });
                                        parentByList.appendChild(child);
                                    }
                                );
                                item.parentNode.remove();
                            }
                        });

                        layerednavigation_rwd_toggle_content();
                    }
                },
                unmatch: function () {
                    $j('.layerednavigation-container-top, ' +/*show duplicated static block*/
                    '.block-layered-nav .block-title,    ' +
                    '.block-content .currently,' +
                    '.block-layered-nav .block-content .block-subtitle:not(.block-subtitle--filter)').show();

                    var layerednavigationContainerTop = $$('.layerednavigation-container-top').first();
                    $$('.layerednavigation-toggle-selector').each(function(item, ch){
                        if( ch++ % 2 == 0 ){
                            var container = new Element('div', { 'class': 'layerednavigation-item-top block-content am-toggle-content' });
                            var dl        = new Element('dl',  { 'class': 'layerednavigation-narrow-by-list',
                                                                 'id'   : "narrow-by-list"
                                                        });

                            container.appendChild(dl);
                            dl.appendChild(item);
                            layerednavigationContainerTop.appendChild(container);
                        }
                        else{
                            $$('.layerednavigation-item-top #narrow-by-list').last().appendChild(item);
                        }

                    });
                    $$('[data-position="layerednavigation-filter-position-both"]').each(function (item) {
                        item.show();
                    });
                    layerednavigation_set_one_heihgt();

                    $$('.layerednavigation-filters-left #narrow-by-list.no-display').each(function (item) {
                        item.removeClassName('no-display');
                    });
                }
            });
        }
    }
}

function layerednavigation_rwd_toggle_content(){
    var isRwd = typeof enquire == 'object' && typeof ProductMediaManager == 'object' && typeof bp == 'object';
    if (!isRwd) {
        return true;
    }

    $j('.am-toggle-content').each(function () {
        var wrapper = jQuery(this);
        var hasTabs = wrapper.hasClass('tabs');
        var hasAccordion = wrapper.hasClass('accordion');
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
        function toggleClasses(clickedItem, group, groups) {
            var index = group.index(clickedItem);
            var i;
            for (i = 0; i < groups.length; i++) {
                groups[i].removeClass('current');
                groups[i].eq(index).addClass('current');
            }
        }
        //Toggle on tab (dt) click.
        dts.unbind('click');
        dts.on('click', function (e) {
            if($j._data(this, 'events').click.length == 1) {
                var wrapper = jQuery(this).parents('.am-toggle-content, .toggle-content');
                //They clicked the current dt to close it. Restore the wrapper to unclicked state.
                if (jQuery(this).hasClass('current') && wrapper.hasClass('accordion-open')) {
                    wrapper.removeClass('accordion-open');
                } else {
                    //They're clicking something new. Reflect the explicit user interaction.
                    wrapper.addClass('accordion-open');
                }
                var dl = wrapper.children('dl:first');
                var dts = dl.children('dt');
                var panes = dl.children('dd');
                var groups = new Array(dts, panes);
                toggleClasses(jQuery(this), dts, groups);
            }
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


document.observe("dom:loaded", function() {
    layerednavigation_start();
    layerednavigation_move_top_filter();
});
