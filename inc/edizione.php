<?php
class OPTU_Edizione {
    function __construct()
    {
        add_action( 'init', [$this, 'register_taxonomy'], 0);
        add_action( 'admin_head', [$this, 'admin_css'] );
        add_filter( 'manage_edit-edizione_columns', [$this, 'edizione_columns'], 1 );
        add_action( 'edizione_add_form_fields', [$this, 'add_custom_fields'] );
        add_action( 'edizione_edit_form_fields', [$this, 'edit_term_fields'], 10, 2 );
        add_action( 'admin_enqueue_scripts', [$this, 'load_admin_scripts'], 10, 1 );
        add_action( 'enqueue_block_editor_assets', [$this, 'load_gutenberg_scripts'] );
        add_action( 'created_edizione', [$this, 'save_term_fields'] );
        add_action( 'edited_edizione', [$this, 'save_term_fields'] );
        add_action( 'restrict_manage_posts', [$this, 'filter_post_by_edizione'], 10, 2 );
    }

    function register_taxonomy() {

        $labels = array(
            'name'                       => 'Edizioni',
            'singular_name'              => 'Edizione',
            'menu_name'                  => 'Edizioni',
            'all_items'                  => 'Tutte le Edizioni',
            'new_item_name'              => 'Nome dell\\\'edizione',
            'add_new_item'               => 'Aggiungi Nuova Edizione',
            'edit_item'                  => 'Modifica Edizione',
            'update_item'                => 'Aggiorna Edizione',
            'view_item'                  => 'Visualizza Edizione',
            'separate_items_with_commas' => 'Separa le edizioni con una virgola',
            'add_or_remove_items'        => 'Aggiungi o Rimuovi Edizioni',
            'choose_from_most_used'      => 'Scegli tra le più selezionate',
            'popular_items'              => 'Edizioni più popolari',
            'search_items'               => 'Cerca Edizioni',
            'not_found'                  => 'Non Trovata',
            'no_terms'                   => 'Nessuna Edizione',
            'items_list'                 => 'Lista Edizioni',
            'items_list_navigation'      => 'Naviga nella lista Edizioni',
        );
        $rewrite = array(
            'slug'                       => 'edizioni',
            'with_front'                 => false,
            //'hierarchical'               => false,
        );
        $capabilities = array(
            'manage_terms'               => 'manage_categories',
            'edit_terms'                 => 'edit_edizioni',
            'delete_terms'               => 'delete_edizioni',
            'assign_terms'               => 'assign_edizioni',
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => false,
            'query_var'                  => 'edizione',
            'rewrite'                    => $rewrite,
            //'capabilities'               => $capabilities,
            'show_in_rest'               => true,
            'rest_base'                  => 'edizioni',
        );
        register_taxonomy( 'edizione', array( 'post' ), $args );
    
    }

    public function admin_css()
	{
		echo '<style>
        body.taxonomy-edizione .term-description-wrap,
        body.taxonomy-edizione .term-slug-wrap {
            display:none;
        }
		</style>';
	}

    function edizione_columns($columns) {
        do_action( 'qm/debug', $columns );
        return $columns;
    }

    function add_custom_fields( $taxonomy ) {
    ?>
        <div class="form-field">
            <label for="publication_date">Data di pubblicazione</label>
			<input type="date" name="publication_date" id="publication_date" />
			<p>Gli articoli (segnati come pronti per la pubblicazione) verranno resi visibili da mezzanotte del giorno selezionato.
                Se non viene selezionata una data, sarà richiesta la pubblicazione manuale di ogni articolo.
            </p>
		</div>
    <?php
    }

    function edit_term_fields( $term, $taxonomy ) {
        $publication_date = get_term_meta( $term->term_id, 'edizione_publication_date', true );
    ?>
        <tr class="form-field">
            <th><label for="publication_date">Data di pubblicazione</label></th>
            <td>
                <input name="publication_date" id="publication_date" type="date" value="<?php echo esc_attr( $publication_date ) ?>" />
                <p class="description">Gli articoli (segnati come pronti per la pubblicazione) verranno resi visibili da mezzanotte del giorno selezionato.
                Se non viene selezionata una data, sarà richiesta la pubblicazione manuale di ogni articolo.</p>
            </td>
        </tr>
    <?php
    }

    /**
     * Load the media uploader and our js code
     */
    function load_admin_scripts($hook)
    {
        if (get_current_screen() == 'edit-post_tag') {
            // Registers and enqueues the required javascript.
            wp_register_script('edizione-custom-fields', plugins_url('edizione_custom_fields.js', __FILE__), array(
                'jquery'
            ));
            wp_enqueue_script('edizione-custom-fields');
        }
    }

    function load_gutenberg_scripts() {
        $asset_file = include( plugin_dir_path( __FILE__ ) . 'edizione_taxonomy_selector/build/index.asset.php');
        wp_register_script(
            'edizione-taxonomy-selector',
            plugins_url('edizione_taxonomy_selector/build/index.js', __FILE__),
            $asset_file['dependencies'],
            $asset_file['version']
        );
        wp_enqueue_script('edizione-taxonomy-selector');
    }

    function save_term_fields( $term_id ) {
        update_term_meta(
            $term_id,
            'edizione_publication_date',
            sanitize_text_field( $_POST[ 'publication_date' ] )
        );        
    }
    
    function filter_post_by_edizione( $post_type, $which ) {
        //Based on https://generatewp.com/filtering-posts-by-taxonomies-in-the-dashboard/

        if ( $post_type !== 'post' ) {
            return;
        }

        $taxonomy_slug = "edizione";

		// Retrieve taxonomy terms
		$terms = get_terms( $taxonomy_slug );

		// Display filter HTML
		echo "<select name='{$taxonomy_slug}' id='{$taxonomy_slug}' class='postform'>";
		echo '<option value="">Mostra tutte le Edizioni</option>';
		foreach ( $terms as $term ) {
			printf(
				'<option value="%1$s" %2$s>%3$s (%4$s)</option>',
				$term->slug,
				( ( isset( $_GET[$taxonomy_slug] ) && ( $_GET[$taxonomy_slug] == $term->slug ) ) ? ' selected="selected"' : '' ),
				$term->name,
				$term->count
			);
		}
		echo '</select>';
    }
}
