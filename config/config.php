<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['module']['assetic'] = array
(
	'module' => "Assetic",
    'name' => "Assetic",
    'description' => "Asset Manager For Ionize CMS.<br/><b>Version : </b>1.0<br/><b>Author : </b>İskender TOTOĞLU<br/><b>Company : </b>ALTI VE BIR IT.<br/><b>Website : </b>http://www.altivebir.com.tr",
    'author' => "İskender TOTOĞLU - ALTI ve BİR IT",
	'version' => "1.0",

	'uri' => 'assetic',
    'has_admin'=> FALSE,
    'has_frontend'=> FALSE,

	'resources' => array(),
);

return $config['module']['assetic'];
