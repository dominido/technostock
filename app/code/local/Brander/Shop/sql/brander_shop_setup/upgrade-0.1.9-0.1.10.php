<?php
/**
* Magento
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@magento.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade Magento to newer
* versions in the future. If you wish to customize Magento for your
* needs please refer to http://www.magento.com for more information.
*
* @category    Mage
* @package     Mage_Directory
* @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

/**
* Fill table directory/country
*/

$dataUA = array(
    array('UA', 'Автономна Республіка Крим', 'Автономна Республіка Крим'),
    array('UA', 'Вінницька область', 'Вінницька область'),
    array('UA', 'Волинська область', 'Волинська область'),
    array('UA', 'Дніпропетровська область', 'Дніпропетровська область'),
    array('UA', 'Донецька область', 'Донецька область'),
    array('UA', 'Житомирська область', 'Житомирська область'),
    array('UA', 'Закарпатська область', 'Закарпатська область'),
    array('UA', 'Запорізька область', 'Запорізька область'),
    array('UA', 'Івано-Франківська область', 'Івано-Франківська область'),
    array('UA', 'Київська область', 'Київська область'),
    array('UA', 'Київ Місто', 'Місто Київ'),
    array('UA', 'Кіровоградська область', 'Кіровоградська область'),
    array('UA', 'Луганська область', 'Луганська область'),
    array('UA', 'Львівська область', 'Львівська область'),
    array('UA', 'Миколаївська область', 'Миколаївська область'),
    array('UA', 'Одеська область', 'Одеська область'),
    array('UA', 'Полтавська область', 'Полтавська область'),
    array('UA', 'Рівненська область', 'Рівненська область'),
    array('UA', 'Севастополь Місто', 'Місто Севастополь'),
    array('UA', 'Сумська область', 'Сумська область'),
    array('UA', 'Тернопільська область', 'Тернопільська область'),
    array('UA', 'Харківська область', 'Харківська область'),
    array('UA', 'Херсонська область', 'Херсонська область'),
    array('UA', 'Хмельницька область', 'Хмельницька область'),
    array('UA', 'Черкаська область', 'Черкаська область'),
    array('UA', 'Чернівецька область', 'Чернівецька область'),
    array('UA', 'Чернігівська область', 'Чернігівська область')
);

$dataEN = array(
    array('UA', 'Autonomous Republic of Crimea', 'Autonomous Republic of Crimea'),
    array('UA', 'Vinnytsya Region', 'Vinnytsya Region'),
    array('UA', 'Volyn region','Volyn region'),
    array('UA', 'Dnipropetrovs’k Region', 'Dnipropetrovs’k Region'),
    array('UA', 'Donets’k Region', 'Donets’k Region'),
    array('UA', 'Zhytomyr Region', 'Zhytomyr Region'),
    array('UA', 'Zakarpattya Region', 'Zakarpattya Region'),
    array('UA', 'Zaporizhzhya Region', 'Zaporizhzhya Region'),
    array('UA', 'Ivano-Frankivs’k Region', 'Ivano-Frankivs’k Region'),
    array('UA', 'Kyiv Region', 'Kyiv Region'),
    array('UA', 'The City of Kyiv', 'The City of Kyiv'),
    array('UA', 'Kirovograd Region', 'Kirovograd Region'),
    array('UA', 'Luhans’k Region', 'Luhans’k Region'),
    array('UA', 'Lviv Region', 'Lviv Region'),
    array('UA', 'Mykolayiv Region', 'Mykolayiv Region'),
    array('UA', 'Odesa Region', 'Odesa Region'),
    array('UA', 'Poltava Region', 'Poltava Region'),
    array('UA', 'Rivne Region', 'Rivne Region'),
    array('UA', 'The City of Sevastopol', 'The City of Sevastopol'),
    array('UA', 'Sumy Region', 'Sumy Region'),
    array('UA', 'Ternopil Region', 'Ternopil Region'),
    array('UA', 'Kharkiv Region', 'Kharkiv Region'),
    array('UA', 'Kherson Region', 'Kherson Region'),
    array('UA', 'Khmelnytsky Region', 'Khmelnytsky Region'),
    array('UA', 'Cherkasy Region', 'Cherkasy Region'),
    array('UA', 'Chernivtsi Region', 'Chernivtsi Region'),
    array('UA', 'Chernigiv Region', 'Chernigiv Region'),
);

$dataRU = array(
    array('UA', 'АР Крым', 'Автономная Республика Крым'),
    array('UA', 'Винницкая область', 'Винницкая область'),
    array('UA', 'Волынская область', 'Волынская область'),
    array('UA', 'Днепропетровская область', 'Днепропетровская область'),
    array('UA', 'Донецкая область', 'Донецкая область'),
    array('UA', 'Житомирская область', 'Житомирская область'),
    array('UA', 'Закарпатская область', 'Закарпатская область'),
    array('UA', 'Запорожская область', 'Запорожская область'),
    array('UA', 'Ивано-Франковская область', 'Ивано-Франковская область'),
    array('UA', 'Киевская область', 'Киевская область'),
    array('UA', 'город Киев', 'город Киев'),
    array('UA', 'Кировоградская область', 'Кировоградская область'),
    array('UA', 'Луганская область', 'Луганская область'),
    array('UA', 'Львовская область', 'Львовская область'),
    array('UA', 'Николаевская область', 'Николаевская область'),
    array('UA', 'Одесская область', 'Одесская область'),
    array('UA', 'Полтавская область', 'Полтавская область'),
    array('UA', 'Ровенская область', 'Ровенская область'),
    array('UA', 'город Севастополь', 'город Севастополь'),
    array('UA', 'Сумская область', 'Сумская область'),
    array('UA', 'Тернопольская область', 'Тернопольская область'),
    array('UA', 'Харьковская область', 'Харьковская область'),
    array('UA', 'Херсонская область', 'Херсонская область'),
    array('UA', 'Хмельницкая область', 'Хмельницкая область'),
    array('UA', 'Черкасская область', 'Черкасская область'),
    array('UA', 'Черновицкая область', 'Черновицкая область'),
    array('UA', 'Черниговская область', 'Черниговская область')
);

foreach ($dataEN as $key => $row) {
    $bind = array(
        'country_id'    => $row[0],
        'code'          => $row[1],
        'default_name'  => $row[2],
    );
    $installer->getConnection()->insert($installer->getTable('directory/country_region'), $bind);
    $regionId = $installer->getConnection()->lastInsertId($installer->getTable('directory/country_region'));

    $bind = array(
        'locale'    => 'en_US',
        'region_id' => $regionId,
        'name'      => $row[2]
    );
    $installer->getConnection()->insert($installer->getTable('directory/country_region_name'), $bind);

    $bind = array(
        'locale'    => 'uk_UA',
        'region_id' => $regionId,
        'name'      => $dataUA[$key][2]
    );
    $installer->getConnection()->insert($installer->getTable('directory/country_region_name'), $bind);

    $bind = array(
        'locale'    => 'ru_RU',
        'region_id' => $regionId,
        'name'      => $dataRU[$key][2]
    );
    $installer->getConnection()->insert($installer->getTable('directory/country_region_name'), $bind);
}
