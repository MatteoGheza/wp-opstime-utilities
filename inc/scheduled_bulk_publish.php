<?php
class OPTU_Scheduled_Bulk_Publish {
    function __construct()
    {
        add_action( 'init', [$this, 'custom_post_status'], 0);
        add_action('admin_footer-edit.php', [$this, 'custom_status_add_in_quick_edit'] );
        add_action('admin_footer-post.php', [$this, 'custom_status_add_in_post_page'] );
        add_action('admin_footer-post-new.php', [$this, 'custom_status_add_in_post_page'] );

        add_action( 'edizione_publish_post_with_ready_status', [$this, 'cron_handler'] );
        add_action( 'init', [$this, 'register_cron'] );
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

    function register_cron() {
        if (!wp_next_scheduled('edizione_publish_post_with_ready_status')) {
            wp_schedule_event(date_create("tomorrow", wp_timezone())->getTimestamp(), 'daily', 'edizione_publish_post_with_ready_status');
        }
    }

    function cron_handler() {
        $terms = get_terms( array(
            'taxonomy' => 'edizione',
            'hide_empty' => false
        ) );
        $term_ids = [];
        foreach($terms as $term) {
            $publishDate = get_term_meta( $term->term_id, 'edizione_publication_date', true );
            $currentDate = date_create("today", wp_timezone())->format("Y-m-d");
            if($publishDate === $currentDate) $term_ids[] = $term->term_id;
        }

        $query = new WP_Query( array(
            'post_status' => 'ready',
            'tax_query' => array(
                array(
                    'taxonomy' => 'edizione',
                    'terms'    => $term_ids,
                )
            )
        ));
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                wp_update_post( array(
                    'ID' => $query->post->ID,
                    'post_status' => 'publish'
                ));
            }
        }
        wp_reset_postdata();
    }
}
