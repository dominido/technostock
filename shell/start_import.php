<?php
set_time_limit(0);
ini_set('memory_limit','512M');
require_once 'abstract.php';

class Mage_Shell_Start_Import extends Mage_Shell_Abstract
{


    public function run()
    {
        $import = Mage::getModel('autoimport/import')->importStart();
    }

}

$shell = new Mage_Shell_Start_Import();
$shell->run();