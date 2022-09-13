<?php
class OPTU_Edizione_PDF {
    function __construct()
    {
        add_action( 'init', [$this, 'register_post_type'], 0);
        add_action( 'init', [$this, 'add_role_caps'], 11 );
        add_action('wp', [$this, 'redirect_pdf_post_type_to_media'], 0);
        add_action('admin_enqueue_scripts', [$this, 'load_admin_scripts'], 10, 1);
        add_action("add_meta_boxes", [$this, "register_meta_box"]);
        add_action( 'save_post', [$this, 'save_post']);
    }

    function register_post_type() {
        $labels = array(
            'name'                  => _x( 'Edizioni PDF', 'Post Type General Name', 'text_domain' ),
            'singular_name'         => _x( 'Edizione PDF', 'Post Type Singular Name', 'text_domain' ),
            'menu_name'             => __( 'Edizioni PDF', 'text_domain' ),
            'name_admin_bar'        => __( 'Edizioni PDF', 'text_domain' ),
            'archives'              => __( 'Archivio Edizioni PDF', 'text_domain' ),
            'attributes'            => __( 'Attributi Edizione PDF:', 'text_domain' ),
            'parent_item_colon'     => __( 'Elemento Genitore:', 'text_domain' ),
            'all_items'             => __( 'Tutte le Edizioni PDF', 'text_domain' ),
            'add_new_item'          => __( 'Aggiungi Nuova Edizione PDF', 'text_domain' ),
            'add_new'               => __( 'Aggiungi Nuova', 'text_domain' ),
            'new_item'              => __( 'Nuova Edizione PDF', 'text_domain' ),
            'edit_item'             => __( 'Modifica Edizione PDF', 'text_domain' ),
            'update_item'           => __( 'Aggiorna Edizione PDF', 'text_domain' ),
            'view_item'             => __( 'Visualizza Edizione PDF', 'text_domain' ),
            'view_items'            => __( 'Visualizza Edizioni PDF', 'text_domain' ),
            'search_items'          => __( 'Cerca Edizione PDF', 'text_domain' ),
            'not_found'             => __( 'Non Trovata', 'text_domain' ),
            'not_found_in_trash'    => __( 'Non trovata nel Cestino', 'text_domain' ),
            'featured_image'        => __( 'Immagine in Evidenza', 'text_domain' ),
            'set_featured_image'    => __( 'Imposta Immagine in Evidenza', 'text_domain' ),
            'remove_featured_image' => __( 'Rimuovi Immagine in Evidenza', 'text_domain' ),
            'use_featured_image'    => __( 'Usa come Immagine in Evidenza', 'text_domain' ),
            'insert_into_item'      => __( "Inserisci nell'Elemento", 'text_domain' ),
            'uploaded_to_this_item' => __( 'Caricato in Questo Elemento', 'text_domain' ),
            'items_list'            => __( 'Lista Edizioni PDF', 'text_domain' ),
            'items_list_navigation' => __( 'Naviga nella lista Edizioni PDF', 'text_domain' ),
            'filter_items_list'     => __( 'Filtra Lista Edizioni PDF', 'text_domain' ),
        );
        $rewrite = array(
            'slug'                  => 'edizioni-pdf',
            'with_front'            => true,
            'pages'                 => true,
            'feeds'                 => true,
        );
        $capabilities = array(
            'edit_post'             => 'edit_edizione_pdf',
            'read_post'             => 'read_edizioni_pdf',
            'delete_post'           => 'delete_edizione_pdf',
            'edit_posts'            => 'edit_edizioni_pdf',
            'edit_others_posts'     => 'edit_others_edizioni_pdf',
            'publish_posts'         => 'publish_edizioni_pdf',
            'read_private_posts'    => 'read_private_edizioni_pdf',
        );
        $args = array(
            'labels' => $labels,
            'menu_icon' => 'dashicons-book',
            'has_archive' => true,
            'public' => true,
            'hierarchical' => false,
            'supports' => array(
                'title'
            ),
            'rewrite'   => $rewrite,
            'capabilities' => $capabilities,
            'show_in_rest' => true
        );
        register_post_type( 'edizione_pdf', $args );
    }

    function add_role_caps() {
        $role_caps = [
            "contributor" => [
                "read_edizioni_pdf",
                "read_private_edizioni_pdf"
            ],
            "author" => [
                "edit_edizione_pdf",
                "read_edizioni_pdf",
                "delete_edizione_pdf",
                "edit_edizioni_pdf",
                "edit_others_edizioni_pdf",
                "publish_edizioni_pdf",
                "read_private_edizioni_pdf"
            ],
            "editor" => [
                "edit_edizione_pdf",
                "read_edizioni_pdf",
                "delete_edizione_pdf",
                "edit_edizioni_pdf",
                "edit_others_edizioni_pdf",
                "publish_edizioni_pdf",
                "read_private_edizioni_pdf"
            ]
        ];

        foreach ($role_caps as $role_name => $cap_list) {
            $role = get_role($role_name);
            foreach ($cap_list as $cap) {
                $role->add_cap($cap);
            }
        }
    }

    //Intercept the post before it actually renders so we can redirect if it's a PDF
    function redirect_pdf_post_type_to_media() {
	    global $post;
	    if(!is_admin() && isset($post->post_type) && $post->post_type == 'edizione_pdf') {
            $url = urldecode(get_post_meta($post->ID, 'edizione_pdf_media_id', true));
            if(
                (!$url || is_null($url) || empty($url)) ||
                parse_url($url, PHP_URL_HOST) !== parse_url(get_site_url(), PHP_URL_HOST)
            ) {
                $url = get_home_url();
            }
            header( "Location: $url", true );
            exit();
	    }
    }

    /**
     * Render the metabox
     */
    function render_metabox()
    {
        // Variables
        global $post;
        $saved = get_post_meta($post->ID, 'edizione_pdf_media_id', true);
?>
        <style>
        .metabox-form-margin {
            margin-bottom: 0.5em;
        }
        #events_video_upload_btn {
            margin-top: 0.5em;
            display: block;
        }
        </style>
        <fieldset>
                <div>
                    <label class="metabox-form-margin" id="edizione_pdf_upload_notice">
                        Carica il file PDF dell'edizione da aggiungere.
                    </label>
                    <label id="edizione_pdf_media_filename">
                    </label><br>
                    <a class="metabox-form-margin" id="edizione_pdf_media_url" href="#" target="_blank">
                    </a>
                    <input type="hidden" name="edizione_pdf_media" id="edizione_pdf_media" value="<?php echo esc_attr($saved); ?>">
                    <button type="button" class="button metabox-form-margin" id="events_video_upload_btn"
                     data-media-uploader-target="#edizione_pdf_media" data-media-upload-notice="#edizione_pdf_upload_notice" data-media-filename-label="#edizione_pdf_media_filename" data-media-url="#edizione_pdf_media_url">Carica PDF</button>
                </div>
        </fieldset>
<?php
        // Security field
        wp_nonce_field('edizione_pdf_form_metabox_nonce', 'edizione_pdf_form_metabox_process'); 
    }

    function save_post( $post_id ) {
        /* Verify the nonce before proceeding. */
        if ( !isset( $_POST['edizione_pdf_form_metabox_process'] ) || !wp_verify_nonce( $_POST['edizione_pdf_form_metabox_process'], 'edizione_pdf_form_metabox_nonce' ) )
          return $post_id;
      
        /* Get the posted data and sanitize it for use as an HTML class. */
        $new_meta_value = ( isset( $_POST['edizione_pdf_media'] ) ? $_POST['edizione_pdf_media'] : "" );
      
        /* Get the meta key. */
        $meta_key = 'edizione_pdf_media_id';
      
        /* Get the meta value of the custom field key. */
        $meta_value = get_post_meta( $post_id, $meta_key, true );
      
        /* If a new meta value was added and there was no previous value, add it. */
        if ( $new_meta_value && empty($meta_value) )
          add_post_meta( $post_id, $meta_key, $new_meta_value, true );
      
        /* If the new meta value does not match the old value, update it. */
        elseif ( $new_meta_value && $new_meta_value != $meta_value )
          update_post_meta( $post_id, $meta_key, $new_meta_value );
      
        /* If there is no new meta value but an old value exists, delete it. */
        elseif ( empty($new_meta_value) && $meta_value )
          delete_post_meta( $post_id, $meta_key, $meta_value );
    }

    /**
     * Load the media uploader and our js code
     */
    function load_admin_scripts($hook)
    {
        global $typenow;
        if ($typenow == 'edizione_pdf') {
            wp_enqueue_media();
        
            // Registers and enqueues the required javascript.
            wp_register_script('meta-box-select-media', plugins_url('select-media.js', __FILE__), array(
                'jquery'
            ));
            wp_enqueue_script('meta-box-select-media');
        }
    }

    function register_meta_box()
    {
        add_meta_box("edizione_pdf_media", "Carica edizione", [$this, "render_metabox"], "edizione_pdf", "normal");
    }
}