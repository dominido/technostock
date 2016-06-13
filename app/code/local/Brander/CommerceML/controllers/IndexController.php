<?php

class Brander_CommerceML_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $limit = ini_get('memory_limit');
        ini_set('memory_limit', '1024M');

        // get import model
        $import = Mage::getModel('brandercml/import')
            ->setConfigurableAttributes('size')
            ->setConfigurableInOffers(false)
            ->setSuperAttributePricingOption('size')
            ->setLimit(10)
            ->setConsoleLog(true)
            //->clearCatalog()
            //->clearAttributes()
            //->setSourceFile('ricamare/import.xml', 'catalog')
            //->setSourceFile('ricamare/offers.xml', 'offers')
            ->setSourceFile('zvek/import.xml', 'catalog')
            ->setSourceFile('zvek/offers.xml', 'offers')
            //->setIsDebugMode(true)
        ;

        // import catalog
        $import->setImportType('catalog')
            ->run();

        // import offers
        $import->setImportType('offers')
            ->run();

        /*$import = Mage::getModel('brandercml/import2')
            //->clearCatalog()
            //->clearAttributes()
            ->setImportSourceFile('ricamare/import.xml')
            ->setOffersSourceFile('ricamare/offers.xml')
            ->isDebugMode(true)
            ->setLimit(100)
            ->run()
        ;*/

        ini_set('memory_limit', $limit);
    }
}