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


$etSocialjQuery(function () {
    changeSocialIcons();
    changeShareIcons();

    $etSocialjQuery('#social_login_icons_size, #social_login_icons_shape, #social_login_icons_color').change(function () {
        changeSocialIcons();
    });

    $etSocialjQuery('#social_login_sharing_size, #social_login_sharing_shape, #social_login_sharing_color').change(function () {
        changeShareIcons();
    });
});

function changeSocialIcons() {
    var iconsSize = $etSocialjQuery('#social_login_icons_size').val();
    var iconsShape = $etSocialjQuery('#social_login_icons_shape').val();
    var iconsColor = $etSocialjQuery('#social_login_icons_color').val();

    if (iconsSize == '24') {
        iconsSizeClass = 'socicons-small';
    } else {
        iconsSizeClass = 'socicons-large';
    }

    $etSocialjQuery('#row_social_login_icons_icons_view .admin-social-icons-view span').removeClass();
    $etSocialjQuery('#row_social_login_icons_icons_view .admin-social-icons-view span').addClass(iconsSizeClass);
    $etSocialjQuery('#row_social_login_icons_icons_view .admin-social-icons-view span').addClass(iconsShape + '-' + iconsColor);
}

function changeShareIcons() {
    var iconsSize = $etSocialjQuery('#social_login_sharing_size').val();
    var iconsShape = $etSocialjQuery('#social_login_sharing_shape').val();
    var iconsColor = $etSocialjQuery('#social_login_sharing_color').val();

    if (iconsSize == '24') {
        iconsSizeClass = 'socicons-small';
    } else {
        iconsSizeClass = 'socicons-large';
    }

    $etSocialjQuery('#row_social_login_sharing_icons_view .admin-social-icons-view span').removeClass();
    $etSocialjQuery('#row_social_login_sharing_icons_view .admin-social-icons-view span').addClass(iconsSizeClass);
    $etSocialjQuery('#row_social_login_sharing_icons_view .admin-social-icons-view span').addClass(iconsShape + '-' + iconsColor);
}