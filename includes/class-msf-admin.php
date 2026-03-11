<?php
/**
 * Admin class for Multi-Step Forms Manager
 */

require_once MSF_PLUGIN_DIR . 'default-form-templates.php';

class MSF_Admin {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    public function add_admin_menu() {
        add_menu_page(
            'Multi-Step Forms',
            'Multi-Step Forms',
            'manage_options',
            'multi-step-forms',
            array($this, 'admin_page'),
            'dashicons-feedback',
            30
        );

        add_submenu_page(
            'multi-step-forms',
            'Personal Forms',
            'Personal Forms',
            'manage_options',
            'msf-personal',
            array($this, 'personal_forms_page')
        );

        add_submenu_page(
            'multi-step-forms',
            'Business Forms',
            'Business Forms',
            'manage_options',
            'msf-business',
            array($this, 'business_forms_page')
        );
    }

    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'multi-step-forms') !== false) {
            wp_enqueue_style('msf-admin-style', MSF_PLUGIN_URL . 'assets/css/msf-admin.css', array(), MSF_VERSION);
            wp_enqueue_script('msf-admin-script', MSF_PLUGIN_URL . 'assets/js/msf-admin.js', array('jquery', 'jquery-ui-sortable'), MSF_VERSION, true);
            wp_enqueue_media();

            // supply initial form data to the script if we're on one of our subpages
            $type = '';
            if (isset($_GET['page'])) {
                if ($_GET['page'] === 'msf-personal') {
                    $type = 'personal';
                } elseif ($_GET['page'] === 'msf-business') {
                    $type = 'business';
                }
            }
            if ($type) {
                // fetch whatever is stored; if the option isn't set or doesn't
                // contain the expected structure we'll fall back to default
                $data = get_option("msf_{$type}_form_data");
                if (!is_array($data) || !isset($data['steps']) || !is_array($data['steps'])) {
                    $data = $this->get_default_form_data($type);
                }

                wp_localize_script('msf-admin-script', 'msfAdminData', array('initial' => $data));
            }
        }
    }

    public function admin_page() {
        ?>
        <div class="wrap">
            <h1>Multi-Step Forms Manager</h1>
            <p>Welcome to the Multi-Step Forms Manager. Here you can create and manage your forms.</p>

            <div class="msf-dashboard">
                <div class="msf-card">
                    <h3>Personal Account Forms</h3>
                    <p>Manage forms for personal account applications.</p>
                    <a href="<?php echo admin_url('admin.php?page=msf-personal'); ?>" class="button">Manage Personal Forms</a>
                </div>

                <div class="msf-card">
                    <h3>Business Account Forms</h3>
                    <p>Manage forms for business account applications.</p>
                    <a href="<?php echo admin_url('admin.php?page=msf-business'); ?>" class="button">Manage Business Forms</a>
                </div>
            </div>

            <h2>How to Use</h2>
            <ol>
                <li>Create your form steps and fields in the respective sections.</li>
                <li>Use the shortcode <code>[multi_step_form type="personal"]</code> or <code>[multi_step_form type="business"]</code> to display the form on any page.</li>
                <li>Customize the form appearance and behavior as needed.</li>
            </ol>
        </div>
        <?php
    }

    public function personal_forms_page() {
        $this->forms_page('personal');
    }

    public function business_forms_page() {
        $this->forms_page('business');
    }

    private function forms_page($type) {
        $form_data = get_option("msf_{$type}_form_data", $this->get_default_form_data($type));

        if (isset($_POST['save_form']) && check_admin_referer('msf_save_form')) {
            $new_data = json_decode(stripslashes($_POST['form_data']), true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($new_data)) {
                echo '<div class="notice notice-error"><p>Unable to save form: invalid JSON detected.</p></div>';
            } else {
                update_option("msf_{$type}_form_data", $new_data);
                // redirect to refresh page and avoid resubmission; will re-fetch updated option
                $redirect = add_query_arg(array('page' => $_GET['page'], 'updated' => '1'), admin_url('admin.php'));
                wp_redirect($redirect);
                exit;
            }
        }

        if (isset($_GET['updated'])) {
            echo '<div class="notice notice-success"><p>Form saved successfully!</p></div>';
            // reload form_data so builder shows latest
            $form_data = get_option("msf_{$type}_form_data", $this->get_default_form_data($type));
        }

        ?>
        <div class="wrap">
            <h1><?php echo ucfirst($type); ?> Account Form</h1>

            <form method="post" action="" id="msf-admin-form">
                <?php wp_nonce_field('msf_save_form'); ?>
                <div id="msf-form-builder">
                    <button type="button" id="msf-add-step" class="button">Add Step</button>
                    <ul id="msf-steps" class="msf-steps-list"></ul>
                    <textarea name="form_data" id="form_data" style="width: 100%; height: 200px; display: none;"><?php echo esc_textarea(json_encode($form_data, JSON_PRETTY_PRINT)); ?></textarea>
                    <p><em>The JSON will be generated automatically from the builder above.</em></p>
                </div>

                <p><input type="submit" name="save_form" class="button button-primary" value="Save Form"></p>
            </form>
        </div>
        <?php
    }

    private function get_default_form_data($type) {
        return ($type === 'personal') ? get_default_personal_form() : get_default_business_form();
    }
}