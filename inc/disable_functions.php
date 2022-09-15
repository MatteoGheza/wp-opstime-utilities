<?php
class OPTU_Disable_Functions {
    function __construct() {
        add_filter('manage_posts_columns', [$this, 'posts_columns'], 1 );
        add_action('init', [$this, 'disable_tags'], 1 );
        add_action('admin_init', [$this, 'trim_admin_menu'] );
        add_action('init', [$this, 'disable_emojis'] );
        add_action( 'admin_head' , [$this, 'admin_head'] );
        /*
        add_action('wp_dashboard_setup', [$this, 'remove_dashboard_widgets'] );
        add_action( 'wp_before_admin_bar_render', [$this, 'remove_admin_bar_menus'] );
        */
    }

    function posts_columns($columns) {
        do_action( 'qm/debug', $columns );
        unset($columns["tags"]);
        return $columns;
    }

    function disable_tags() {
        register_taxonomy('post_tag', []);
    }

    function trim_admin_menu()
    {
        if (!current_user_can('administrator')) {
            remove_menu_page('tools.php'); // No tools for <= editors
        }
    }

    /* Disable emojis (https://kinsta.com/it/knowledgebase/disabilitare-emoji-in-wordpress/#disable-emojis-code) */
    function disable_emojis()
    {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        add_filter('tiny_mce_plugins', [$this, 'disable_emojis_tinymce']);
        add_filter('wp_resource_hints', [$this, 'disable_emojis_remove_dns_prefetch'], 10, 2);
    }

    /**
     * Remove emoji CDN hostname from DNS prefetching hints.
     *
     * @param array $urls URLs to print for resource hints.
     * @param string $relation_type The relation type the URLs are printed for.
     * @return array Difference betwen the two arrays.
     */
    function disable_emojis_remove_dns_prefetch($urls, $relation_type)
    {
        if ('dns-prefetch' == $relation_type) {
            /** This filter is documented in wp-includes/formatting.php */
            $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/');

            $urls = array_diff($urls, array($emoji_svg_url));
        }

        return $urls;
    }

    /**
     * Filter function used to remove the tinymce emoji plugin.
     *
     * @param array $plugins
     * @return array Difference betwen the two arrays
     */
    function disable_emojis_tinymce($plugins)
    {
        if (is_array($plugins)) {
            return array_diff($plugins, array('wpemoji'));
        } else {
            return array();
        }
    }

    function admin_head() {
        remove_meta_box("xmlsf_section", ["qrcode", "post", "edizione_pdf"], "side");
        remove_meta_box("heateor_ogmt_meta", ["qrcode", "post", "page", "media", "edizione_pdf"], "advanced");

        /*
        remove_meta_box("aioseo-settings", ["qr", "post", "edizione_pdf"], "normal");
        remove_action("post_submitbox_misc_actions", [ aioseo()->admin, 'addPublishScore' ]);

        remove_action("init", [ aioseo()->admin, 'addPostColumnsAjax' ]);
    
        remove_action("admin_bar_menu", [ aioseo()->admin, 'adminBarMenu' ], 9);
        remove_action("admin_menu", [ aioseo()->admin, 'addMenu' ], 9);
        remove_action("admin_menu", [ aioseo()->admin, 'hideScheduledActionsMenu' ], 9);
        remove_action("admin_init", [ aioseo()->admin, 'addPluginScripts' ], 9);
        remove_action("admin_footer", [ aioseo()->admin, 'addAioseoModalPortal' ], 9);
        remove_action("admin_enqueue_scripts", [ aioseo()->admin, 'enqueueAioseoModalPortal' ], 9);
    
        remove_action( 'wp_dashboard_setup', [ aioseo()->dashboard, 'addDashboardWidgets' ], 9 );
        */
    }

    /*
    function remove_dashboard_widgets() {
        global $wp_meta_boxes;
        unset($wp_meta_boxes['dashboard']['normal']['core']['aioseo-overview']);
        unset($wp_meta_boxes['dashboard']['normal']['high']['aioseo-seo-setup']);
    }

    function remove_admin_bar_menus() {
        global $wp_admin_bar;
        ((object) $wp_admin_bar)->remove_node("aioseo");
        ((object) $wp_admin_bar)->remove_node("aioseo-main");
        ((object) $wp_admin_bar)->remove_node("aioseo-notifications");
        ((object) $wp_admin_bar)->remove_node("aioseo-settings");
        ((object) $wp_admin_bar)->remove_node("aioseo-search-appearance");
        ((object) $wp_admin_bar)->remove_node("aioseo-social-networks");
        ((object) $wp_admin_bar)->remove_node("aioseo-sitemaps");
        ((object) $wp_admin_bar)->remove_node("aioseo-link-assistant");
        ((object) $wp_admin_bar)->remove_node("aioseo-redirects");
        ((object) $wp_admin_bar)->remove_node("aioseo-local-seo");
        ((object) $wp_admin_bar)->remove_node("aioseo-seo-analysis");
        ((object) $wp_admin_bar)->remove_node("aioseo-tools");
        ((object) $wp_admin_bar)->remove_node("aioseo-feature-manager");
        ((object) $wp_admin_bar)->remove_node("aioseo-about");
        ((object) $wp_admin_bar)->remove_node("aioseo-pro-upgrade");
    }
    */
}
