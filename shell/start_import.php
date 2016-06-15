<?php
require_once 'abstract.php';

class Mage_Shell_Start_Import extends Mage_Shell_Abstract
{


    public function run()
    {
        //$import = Mage::getModel('autoimport/import')->cronImportStart();
        
       /* Mage::getModel('brandercml/importgpd')
            ->setSourceFileName('tehnostok-import.xml')
            ->setSourceFilePath('var/import/')
            ->setConsoleLog(true)
            ->run();*/
    }

}

$shell = new Mage_Shell_Start_Import();
$shell->run();