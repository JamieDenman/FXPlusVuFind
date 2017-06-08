<?php

namespace fxplusCustom\RecordDriver;
use Zend\ServiceManager\ServiceManager;

class Factory extends \VuFind\RecordDriver\Factory
{
   /**
     * Factory for SolrMarc record driver.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return SolrMarc
     */
    public static function getSolrMarc(ServiceManager $sm)
    {
        $driver = new SolrMarc(
            $sm->getServiceLocator()->get('VuFind\Config')->get('config'),
            null,
            $sm->getServiceLocator()->get('VuFind\Config')->get('searches')
        );
        $driver->attachILS(
            $sm->getServiceLocator()->get('VuFind\ILSConnection'),
            $sm->getServiceLocator()->get('VuFind\ILSHoldLogic'),
            $sm->getServiceLocator()->get('VuFind\ILSTitleHoldLogic')
        );
        $driver->attachSearchService($sm->getServiceLocator()->get('VuFind\Search'));
        return $driver;
    }

    /**
     * Factory for Summon record driver.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return Summon
     */
    public static function getSummon(ServiceManager $sm)
    {
        $summon = $sm->getServiceLocator()->get('VuFind\Config')->get('Summon');
        $driver = new Summon(
            $sm->getServiceLocator()->get('VuFind\Config')->get('config'),
            $summon, $summon
        );
        $driver->setDateConverter(
            $sm->getServiceLocator()->get('VuFind\DateConverter')
        );
        return $driver;
    }

}