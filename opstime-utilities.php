<?php
/*
Plugin Name: OPsTimeUtilities
Description: Plugin con configurazioni e funzioni utilizzate dall'OPs Time
Author: Redazione OPsTime
Version: 1.0.0
Requires PHP: 5.3
*/

define("PLUGIN_PATH", plugin_dir_path( __FILE__ ));
define("INCLUDE_PATH", PLUGIN_PATH . "//inc//");

require( INCLUDE_PATH . "disable_functions.php" );
require( INCLUDE_PATH . "cron_jobs.php" );
require( INCLUDE_PATH . "user_roles.php" );
require( INCLUDE_PATH . "edizione_pdf.php" );

class OPsTimeUtilities {
    private $disable_functions;
    private $cron_jobs;
    private $user_roles;
    private $edizione_pdf;

    function __construct()
    {
        $this->disable_functions = new OPTU_Disable_Functions();
        $this->cron_jobs = new OPTU_Cron_Jobs();
        $this->user_roles = new OPTU_User_Roles();
        $this->edizione_pdf = new OPTU_Edizione_PDF();
    }
}

$OPTU = new OPsTimeUtilities();
