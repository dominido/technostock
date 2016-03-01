
//original is /www/unitop/web/skin/frontend/ultimo/default/js/app.js

function layeredToggle() {


    var breakpointMedium = 768;
    var isResponsive = $j('body').hasClass('responsive');


    jQuery.fn.toggleSingle = function (options) {

        // passing destruct: true allows
        var settings = $j.extend({
            destruct: false
        }, options);

        return this.each(function () {
            if (!settings.destruct) {
                $j(this).on('click', function () {
                    $j(this)
                        .toggleClass('active')
                        .next()
                        .toggleClass('');
                });
                // Hide the content
                $this = $j(this);
                if (!$this.hasClass('active')) {
                    $this.next().addClass('');
                }
                //$j(this).next().addClass('no-display');
            } else {
                // Remove event handler so that the toggle link can no longer be used
                $j(this).off('click');
                // Remove all classes that were added by this plugin
                $j(this)
                    .removeClass('active')
                    .next()
                    .removeClass('');
            }

        });
    }

// ==============================================
// UI Pattern - Toggle Content (tabs and accordions in one setup)
// ==============================================

    $j('.toggle-content').each(function () {
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

// ==============================================
// Layered Navigation Block
// ==============================================

// On product list pages, we want to show the layered nav/category menu immediately above the product list
    if (isResponsive) {
        if ($j('.block-layered-nav').length && $j('.category-products').length) {
            enquire.register('screen and (max-width: ' + (breakpointMedium - 1) + 'px)', {
                match: function () {
                    $j('.block-layered-nav').insertBefore($j('.category-products'))
                },
                unmatch: function () {
                    // Move layered nav back to left column
                    $j('.block-layered-nav').insertAfter($j('#layered-nav-marker'))
                }
            });
        }
    }

// ==============================================
// Blocks collapsing (on smaller viewports)
// ==============================================

    if (isResponsive) {
        enquire.register('(max-width: ' + (breakpointMedium - 1) + 'px)', {
            setup: function () {
                this.toggleElements = $j(
                    '.sidebar .block:not(.block-layered-nav) .block-title, ' +
                    '.block-layered-nav .block-subtitle--filter, ' +
                        //'.block-layered-nav .block-title, ' + //Currently this element is hidden in mobile view
                    '.mobile-collapsible .block-title'
                );
            },
            match: function () {
                this.toggleElements.toggleSingle();
            },
            unmatch: function () {
                this.toggleElements.toggleSingle({destruct: true});
            }
        });
    }

// ==============================================
// Blocks collapsing on all viewports
// ==============================================

//Exclude elements with ".mobile-collapsible" for backward compatibility
    $j('.collapsible:not(.mobile-collapsible) .block-title').toggleSingle();

}