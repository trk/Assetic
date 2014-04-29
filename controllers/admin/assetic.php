<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ASSETIC MODULE ADMIN CONTROLLER
 *
 * @author  : İskender TOTOĞLU | Altı ve Bir Bilişim Teknolojileri | http://www.altivebir.com.tr
 */
class Assetic extends Module_Admin
{
    /**
     * Controller URL
     *
     * @var string (with admin url)
     */
    protected $controller_url;

    /**
     * Controller View Folder
     *
     * @var string
     */
    protected $controller_folder = 'admin/assetic/';

	/**
	 * Constructor
	 *
	 * @access  public
	 * @return  void
	 */
	public function construct()
	{
        // Load Needed Models
        $this->load->model(
            array(
                'assetic_model'
            ), '', TRUE);

        // Set Controller URL
        $this->controller_url = admin_url() . 'module/assetic/assetic/';
	}

    // ------------------------------------------------------------------------

	/**
	 * Admin panel
	 * Called from the modules list.
	 *
	 * @access  public
	 * @return  parsed view
	 *
	 */
	public function index()
	{
        $this->output($this->controller_folder . 'index');
	}
}