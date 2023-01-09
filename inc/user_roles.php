<?php
class OPTU_User_Roles {
    function __construct()
    {
        add_filter('user_contactmethods', [$this, 'user_contact_method_classe'] );
        add_action('admin_init', [$this, 'allow_all_author_to_edit_posts'] );
        add_action( 'init', [$this, 'add_custom_roles'], 11 );
        add_filter( 'login_errors', [$this, 'custom_login_error_msg'] );
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

    public function custom_login_error_msg()
    {
        return <<<HTML
        <!-- Caro programmatore/curioso che stai leggendo questo orribile pezzo di codice, ti starai chiedendo come mai sono stato così aggressivo nello scrivere questo -->
        <!-- banner come mai così tante indicazioni ovvie e graficamente brutte e fastidiose. In poche parole, la spiegazione è che alcuni utonti (finalmente ho capito  -->
        <!-- come mai si chiamano così in programmazione) non leggevano gli avvertimenti, detti a voce e scritti sul gruppo, tentando il login 20 VOLTE DI FILA. Sì, 20. -->
        <!-- Lascio a voi i commenti, non voglio scrivere insulti... E poi, ad essere sincero, sono stufo che la gente dia la colpa alla piattaforma per i suoi problemi -->
        <!-- di memoria. Soprattutto, dato che la funzione di reset password è attiva e semplice da usare. E ora, perchè questo monologo di 1,731 bytes?                 -->
        <!-- Boh, me lo chiedo anche io. A questo punto, se stai leggendo, scrivimi ciao a hey [at] matteogheza.it. Buona giornata, e buona programmazione!              -->
        <!-- Disclaimer: questo testo deve essere interpretato in maniera ironica. Non è stato scritto con finalità di diffamazione o di insulto. Ogni riferimento a     -->
        <!-- persone e cose è puramente casuale. L'utilizzo del termine "utonto", non si riferisce agli utenti del giornalino miei colleghi e amici, ma ad una battuta   -->
        <!-- tipica del mondo dell'informatica e della gestione server e sistemi IT. Ringrazio tutti coloro con cui collaboro per la fantastica esperienza, anche se...  -->
        <!-- Per favore, potete rinunciare al vostro ego ed orgoglio personale e usare la funzione di reset password o chiedere aiuto a qualcuno? Grazie.                -->
        <h3><strong>ERRORE</strong>: Username o password inseriti non sono corretti.<br></h3>
        <a style="font-size: 150%;" href="https://opstime.it/wp-login.php?action=lostpassword" title="Reset Password">Reset della password</a><br>
        <strong>ATTENZIONE</strong>: se non ricordi la tua password, <b><u>non tentare tante volte il login</u></b>, o il tuo account verrà <b>BLOCCATO!</b><br>
        Tentare il reset della password, o contattare un amministratore (scrivere un messaggio sul gruppo in caso di dubbio).
HTML;
    }
}
