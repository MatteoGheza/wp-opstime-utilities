<?php
/*
Plugin Name: OPsTimeUtilities
Description: Plugin con configurazioni e funzioni utilizzate dall'OPs Time
Author: Matteo Gheza <matteo.gheza07@gmail.com>
Version: 1.0.0
Requires PHP: 5.3
*/

if (!defined('ABSPATH')) {
	exit;
}

define("PLUGIN_PATH", plugin_dir_path( __FILE__ ));
define("INCLUDE_PATH", PLUGIN_PATH . "//inc//");

require( INCLUDE_PATH . "disable_functions.php" );
require( INCLUDE_PATH . "disable_comments.php" );
require( INCLUDE_PATH . "static_site_cron.php" );
require( INCLUDE_PATH . "user_roles.php" );
require( INCLUDE_PATH . "edizione_pdf.php" );
require( INCLUDE_PATH . "edizione.php" );
require( INCLUDE_PATH . "scheduled_bulk_publish.php" );

class OPsTimeUtilities {
    private $disable_functions;
    private $disable_comments;
    private $static_site_cron;
    private $user_roles;
    private $edizione_pdf;
    private $edizione;
    private $scheduled_bulk_publish;

    function __construct()
    {
        $this->disable_functions = new OPTU_Disable_Functions();
        $this->disable_comments = new OPTU_Disable_Comments();
        $this->static_site_cron = new OPTU_Static_Site_Cron();
        $this->user_roles = new OPTU_User_Roles();
        $this->edizione_pdf = new OPTU_Edizione_PDF();
        $this->edizione = new OPTU_Edizione();
        $this->scheduled_bulk_publish = new OPTU_Scheduled_Bulk_Publish();
    }
}

$OPTU = new OPsTimeUtilities();
