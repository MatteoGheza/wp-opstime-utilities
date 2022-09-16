<?php
class OPTU_Static_Site_Cron {
    function __construct()
    {
        add_action('run_static_site_archive_generation_cron', [$this, 'run_static_site_archive_generation_cron'] );
        add_action('wp', [$this, 'custom_cron_job_export_static_site'] );
    }

    // Scheduled Action Hook
    function run_static_site_archive_generation_cron()
    {
        do_action("simply_static_site_export_cron");
    }

    // Schedule Cron Job Event
    function custom_cron_job_export_static_site()
    {
        if (!wp_next_scheduled('run_static_site_archive_generation_cron')) {
            wp_schedule_event(current_time('timestamp'), 'twicedaily', 'run_static_site_archive_generation_cron');
        }
    }
}
