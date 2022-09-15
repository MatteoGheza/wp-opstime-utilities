<?php
class OPTU_Others {
    function __construct()
    {
        add_action( 'init', [$this, 'custom_post_status'], 0);
        add_action('admin_footer-edit.php', [$this, 'custom_status_add_in_quick_edit'] );
        add_action('admin_footer-post.php', [$this, 'custom_status_add_in_post_page'] );
        add_action('admin_footer-post-new.php', [$this, 'custom_status_add_in_post_page'] );
    }

    function custom_post_status() {
        $args = array(
            'label'                     => 'Pronto alla Pubblicazione',
            'label_count'               => _n_noop( 'Pronto alla Pubblicazione (%s)',  'Pronti alla Pubblicazione (%s)', 'text_domain' ), 
            'public'                    => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'exclude_from_search'       => true,
        );
        register_post_status( 'ready', $args );
    }

    function custom_status_add_in_quick_edit() {
        echo "<script>
        jQuery(document).ready( function() {
            jQuery( 'select[name=\"_status\"] option:eq(1)' ).after( '<option value=\"ready\">Pronto alla Pubblicazione (programmato, vedere edizione)</option>' );      
        });
        </script>";
    }
    
    function custom_status_add_in_post_page() {
        echo "<script>
        jQuery(document).ready( function() {        
            jQuery( 'select[name=\"post_status\"]' ).after( '<option value=\"ready\">Pronto alla Pubblicazione (programmato, vedere edizione)</option>' );
        });
        </script>";
    }
}
