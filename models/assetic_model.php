<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Assetic model
 *
 * The model that handles actions
 *
 * @author	@author	İskender TOTOĞLU | Altı ve Bir Bilişim Teknolojileri | http://www.altivebir.com.tr
 */
class Assetic_model extends Base_model
{
    protected $LibraryPath;

	/**
	 * Model Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

        $this->LibraryPath  = MODPATH . 'Assetic' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR;

        // M-A-L-PATH -> Module Assetic Library Path
        define('MODALPATH', $this->LibraryPath);

        if( ! class_exists('Universalclassloader') )
        {
            $this->load->library('Universalclassloader');
            $this->Universalclassloader->registerNamespace('Assetic', MODALPATH);
            $this->Universalclassloader->registerNamespace('Symfony', MODALPATH);
            $this->Universalclassloader->registerNamespace('Compressor', MODALPATH);
            $this->Universalclassloader->register();
        }
	}
}

