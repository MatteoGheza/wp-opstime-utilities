<?php
class OPTU_User_Roles {
    function __construct()
    {
        add_filter('user_contactmethods', [$this, 'user_contact_method_classe'] );
        add_action('admin_init', [$this, 'allow_all_author_to_edit_posts'] );
        add_action( 'init', [$this, 'add_custom_roles'], 11 );
    }

    // Register User Contact Methods
    function user_contact_method_classe($user_contact_method)
    {
        $user_contact_method['classe'] = 'Classe (es. 1BLIC)';
        return $user_contact_method;
    }

    /* Allow authors to edit other author's posts */
    function allow_all_author_to_edit_posts()
    {
        $role = get_role('author');
        $role->add_cap('edit_others_posts');
    }

    function add_custom_roles() {
        $role_caps = [
            "designer" => [
                "edit_qr",
                "read_qr",
                "edit_qrs",
                "edit_others_qrs",
                "publish_qrs",
                "read_private_qrs",
                "edit_edizione_pdf",
                "read_edizioni_pdf",
                "delete_edizione_pdf",
                "edit_edizioni_pdf",
                "edit_others_edizioni_pdf",
                "publish_edizioni_pdf",
                "read_private_edizioni_pdf"
            ],
            "advertizer" => [
                "edit_qr",
                "read_qr",
                "delete_qr",
                "edit_qrs",
                "edit_others_qrs",
                "publish_qrs",
                "read_private_qrs"
            ],
        ];

        foreach ($role_caps as $role_name => $cap_list) {
            $role_cap_list = [];
            foreach ($cap_list as $cap) {
                $role_cap_list[$cap] = true;
            }
            add_role(
                $role_name,
                ucfirst($role_name),
                $role_cap_list
            );
        }
    }
}
