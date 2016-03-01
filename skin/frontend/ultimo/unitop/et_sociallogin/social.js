/**
 * NOTICE OF LICENSE
 *
 * You may not give, sell, distribute, sub-license, rent, lease or lend
 * any portion of the Software or Documentation to anyone.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @category   ET
 * @package    ET_SocialLogin
 * @copyright  Copyright (c) 2015 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://shop.etwebsolutions.com/etws-license-commercial-v1/   ETWS Commercial License (ECL1)
 */


$etSocialjQuery(document).ready(function () {

    $etSocialjQuery('div.social-auth a.soclogin').bind('click', function (event) {

        var url = $etSocialjQuery(this).attr("href");
        var windowName = $etSocialjQuery(this).attr("title");

        var features, w = 820, h = 620;

        var top = (screen.height - h) / 2, left = (screen.width - w) / 2;

        if (top < 0) top = 0;

        if (left < 0) left = 0;

        features = 'top=' + top + ',left=' + left;

        features += ',height=' + h + ',width=' + w + ',resizable=yes';


        var win = window.open(url,win,features);
        win.focus();
        event.preventDefault();
        return false;
    });
});

