<?php

namespace Kanboard\Plugin\Dosi;

use DateTime;
use Kanboard\Core\Translator;
use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Security\Role;

class Plugin extends Base
{
    public function initialize()
    {
        $this->applicationAccessMap->add('IndicateursController', '*', Role::APP_USER);

        $this->route->enable();
        $this->route->addRoute('/indicateurs/dosi', 'IndicateursController', 'index', 'Dosi');
        $this->route->addRoute('/indicateurs/dosi/projets', 'IndicateursController', 'projets', 'Dosi');

        $this->hook->on('template:layout:js', array('template' => 'plugins/Dosi/js/jquery.dataTables.min.js'));
        $this->hook->on('template:layout:js', array('template' => 'plugins/Dosi/js/dataTables.bootstrap.min.js'));
        $this->hook->on('template:layout:js', array('template' => 'plugins/Dosi/js/dosi.js'));
    }

    public function onStartup()
    {


        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__.'/Locale');
    }

    public function getClasses()
    {
        return array();
    }

    public function getPluginName()
    {
        return 'Dosi';
    }

    public function getPluginDescription()
    {
        return t('Dosi');
    }

    public function getPluginAuthor()
    {
        return 'Jade Tavernier';
    }

    public function getPluginVersion()
    {
        return '1.0';
    }

    public function getPluginHomepage()
    {
        return '';
    }
}
