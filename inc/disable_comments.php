<?php

/**
 * Stripped down version of https://wordpress.org/plugins/disable-comments/
 * I removed everything related to plugin analytics and replaced the options
 * with hard-coded functions, since we only need to disable comments completely.
*/

class OPTU_Disable_Comments
{
	private $modified_types = [];

	function __construct()
	{
		add_action( 'plugins_loaded', [ $this, 'init_filters'] );
	}

	/**
	 * Get an array of disabled post type.
	 */
	public function get_disabled_post_types()
	{
		$typeargs = array('public' => true);

		$types = get_post_types($typeargs, 'objects');
		foreach (array_keys($types) as $type) {
			if (!in_array($type, $this->modified_types) && !post_type_supports($type, 'comments')) {   // the type doesn't support comments anyway.
				unset($types[$type]);
			}
		}
		return array_keys($types);
	}

	public function init_filters()
	{
		// These need to happen now.
		add_action('widgets_init', array($this, 'disable_rc_widget'));
		add_filter('wp_headers', array($this, 'filter_wp_headers'));
		add_action('template_redirect', array($this, 'filter_query'), 9);   // before redirect_canonical.

		// Admin bar filtering has to happen here since WP 3.6.
		add_action('template_redirect', array($this, 'filter_admin_bar'));
		add_action('admin_init', array($this, 'filter_admin_bar'));

		// Disable Comments REST API Endpoint
		add_filter('rest_endpoints', array($this, 'filter_rest_endpoints'));

		// remove create comment via xmlrpc
		add_filter('xmlrpc_methods', array($this, 'disable_xmlrc_comments'));
		// rest API Comment Block
		add_filter('rest_pre_insert_comment', array($this, 'disable_rest_API_comments'), 10, 2);

		add_action('wp_loaded', array($this, 'init_wploaded_filters'));
		// Disable "Latest comments" block in Gutenberg.
		add_action('enqueue_block_editor_assets', array($this, 'filter_gutenberg_blocks'));
	}

	public function init_wploaded_filters()
	{
		$disabled_post_types = $this->get_disabled_post_types();
		if (!empty($disabled_post_types)) {
			foreach ($disabled_post_types as $type) {
				// we need to know what native support was for later.
				if (post_type_supports($type, 'comments')) {
					$this->modified_types[] = $type;
					remove_post_type_support($type, 'comments');
					remove_post_type_support($type, 'trackbacks');
				}
			}
			add_filter('comments_array', array($this, 'filter_existing_comments'), 20, 2);
			add_filter('comments_open', array($this, 'filter_comment_status'), 20, 2);
			add_filter('pings_open', array($this, 'filter_comment_status'), 20, 2);
			add_filter('get_comments_number', array($this, 'filter_comments_number'), 20, 2);
		}

		// Filters for the admin only.
		if (is_admin()) {
			add_action('admin_menu', array($this, 'filter_admin_menu'), 9999);  // do this as late as possible.
			add_action('admin_print_styles-index.php', array($this, 'admin_css'));
			add_action('admin_print_styles-profile.php', array($this, 'admin_css'));
			add_action('wp_dashboard_setup', array($this, 'filter_dashboard'));
			add_filter('pre_option_default_pingback_flag', '__return_zero');
		}
		// Filters for front end only.
		else {
			add_action('template_redirect', array($this, 'check_comment_template'));

			add_filter('feed_links_show_comments_feed', '__return_false');
		}
	}

	/**
	 * Replace the theme's comment template with a blank one.
	 * To prevent this, define DISABLE_COMMENTS_REMOVE_COMMENTS_TEMPLATE
	 * and set it to True
	 */
	public function check_comment_template()
	{
		if (is_singular()) {
			add_filter('comments_template', array($this, 'dummy_comments_template'), 20);
			// Remove comment-reply script for themes that include it indiscriminately.
			wp_deregister_script('comment-reply');
			// feed_links_extra inserts a comments RSS link.
			remove_action('wp_head', 'feed_links_extra', 3);
		}
	}

	public function dummy_comments_template()
	{
		return dirname(__FILE__) . '/views/comments.php';
	}

	/**
	 * Remove the X-Pingback HTTP header
	 */
	public function filter_wp_headers($headers)
	{
		unset($headers['X-Pingback']);
		return $headers;
	}

	/**
	 * remove method wp.newComment
	 */
	public function disable_xmlrc_comments($methods)
	{
		unset($methods['wp.newComment']);
		return $methods;
	}

	public function disable_rest_API_comments($prepared_comment, $request)
	{
		return;
	}

	/**
	 * Issue a 403 for all comment feed requests.
	 */
	public function filter_query()
	{
		if (is_comment_feed()) {
			wp_die(__('Comments are closed.', 'disable-comments'), '', array('response' => 403));
		}
	}

	/**
	 * Remove comment links from the admin bar.
	 */
	public function filter_admin_bar()
	{
		if (is_admin_bar_showing()) {
			// Remove comments links from admin bar.
			remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
		}
	}

	/**
	 * Remove the comments endpoint for the REST API
	 */
	public function filter_rest_endpoints($endpoints)
	{
		unset($endpoints['comments']);
		return $endpoints;
	}

	/**
	 * Determines if scripts should be enqueued
	 */
	public function filter_gutenberg_blocks($hook)
	{
		wp_enqueue_script('disable-comments-gutenberg', plugin_dir_url(__FILE__) . 'disable-comments.js', array(), false, true);
	}

	public function filter_admin_menu()
	{
		global $pagenow;

		if ($pagenow == 'comment.php' || $pagenow == 'edit-comments.php') {
			wp_die(__('Comments are closed.', 'disable-comments'), '', array('response' => 403));
		}

		remove_menu_page('edit-comments.php');

		if ($pagenow == 'options-discussion.php') {
			wp_die(__('Comments are closed.', 'disable-comments'), '', array('response' => 403));
		}

		remove_submenu_page('options-general.php', 'options-discussion.php');
	}

	public function filter_dashboard()
	{
		remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
	}

	public function admin_css()
	{
		echo '<style>
			#dashboard_right_now .comment-count,
			#dashboard_right_now .comment-mod-count,
			#latest-comments,
			#welcome-panel .welcome-comments,
			.user-comment-shortcuts-wrap {
				display: none !important;
			}
		</style>';
	}

	public function filter_existing_comments($comments, $post_id)
	{
		return array();
	}

	public function filter_comment_status($open, $post_id)
	{
		return false;
	}

	public function filter_comments_number($count, $post_id)
	{
		return 0;
	}

	public function disable_rc_widget()
	{
		unregister_widget('WP_Widget_Recent_Comments');
		/**
		 * The widget has added a style action when it was constructed - which will
		 * still fire even if we now unregister the widget... so filter that out
		 */
		add_filter('show_recent_comments_widget_style', '__return_false');
	}
}
