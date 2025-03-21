<?php
require_once __DIR__ . '/vendor/autoload.php';
use JefyOkta\Blade\BladeView;

$result =(new BladeView('views/','views/cache/'))->render('index', ['name' => 'Jefy Okta']);

echo  $result;


 ?>