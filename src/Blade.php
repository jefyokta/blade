<?php
namespace JefyOkta\Blade;

use Oktaax\Types\OktaaxConfig;
use Oktaax\Types\AppConfig;

trait Blade {

    public function __construct() {
        $this->config = new OktaaxConfig(
            new BladeView("views/","views/cache/"),
            'log',
            false,
            null,
            null,
            new AppConfig(null, false, 300, 'Oktaax'),
            'public/'

         
        );
    }
    

}; ?>