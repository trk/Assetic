<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['default_controller'] = "assetic";
$route[''] = $route['default_controller'] . '/index';
// $route['(.*)'] = $route['default_controller'] . "/index/$1";
// To be able to add customs controllers
// 1. Comment the previous line : $route['(.*)'] = $route['default_controller'] . "/index/$1";
// 2. Uncomment these lines
$route['404_override'] = $route['default_controller'];
$route['(.*)'] = "/$1";