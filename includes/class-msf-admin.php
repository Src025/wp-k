<?php
/**
 * Admin class for Multi-Step Forms Manager
 */

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
            $form_data = json_decode(stripslashes($_POST['form_data']), true);
            update_option("msf_{$type}_form_data", $form_data);
            echo '<div class="notice notice-success"><p>Form saved successfully!</p></div>';
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
            <script>
                var msfInitialData = <?php echo wp_json_encode($form_data); ?>;
            </script>
        </div>
        <?php
    }

    private function get_default_form_data($type) {
        if ($type === 'personal') {
            return array(
                'steps' => array(
                    array(
                        'title' => 'Account Type',
                        'fields' => array(
                            array(
                                'type' => 'radio',
                                'name' => 'account_type',
                                'label' => 'Select Account Type',
                                'options' => array(
                                    array(
                                        'value' => 'savings',
                                        'label' => 'Savings Account',
                                        'description' => 'Regular personal savings account.',
                                        'icon' => 'building-columns',
                                        'badge' => 'SWIFT Compatible'
                                    ),
                                    array(
                                        'value' => 'custody',
                                        'label' => 'Custody Account',
                                        'description' => 'Asset custody & safekeeping account.',
                                        'icon' => 'shield-halved',
                                        'badge' => 'ETF Compatible'
                                    ),
                                    array(
                                        'value' => 'numbered',
                                        'label' => 'Numbered Account',
                                        'description' => 'Anonymous, coded account (£50,000 fee).',
                                        'icon' => 'lock',
                                        'badge' => ''
                                    ),
                                    array(
                                        'value' => 'crypto',
                                        'label' => 'Cryptocurrency Account',
                                        'description' => 'Digital asset banking account.',
                                        'icon' => 'bitcoin',
                                        'badge' => 'ETF Compatible'
                                    ),
                                )
                            )
                        )
                    ),
                    array(
                        'title' => 'Personal Details',
                        'fields' => array(
                            array('type' => 'text', 'name' => 'first_name', 'label' => 'First Name'),
                            array('type' => 'text', 'name' => 'last_name', 'label' => 'Last Name'),
                            array('type' => 'date', 'name' => 'dob', 'label' => 'Date of Birth'),
                            array('type' => 'text', 'name' => 'nationality', 'label' => 'Nationality'),
                            array('type' => 'text', 'name' => 'passport', 'label' => 'Passport/ID'),
                            array('type' => 'text', 'name' => 'occupation', 'label' => 'Occupation')
                        )
                    ),
                    // Add more steps as needed
                )
            );
        } else {
            return array(
                'steps' => array(
                    array(
                        'title' => 'Business Type',
                        'fields' => array(
                            array('type' => 'radio', 'name' => 'business_type', 'label' => 'Select Business Type', 'options' => array('Sole Proprietorship', 'Partnership', 'Corporation', 'LLC'))
                        )
                    ),
                    array(
                        'title' => 'Business Details',
                        'fields' => array(
                            array('type' => 'text', 'name' => 'business_name', 'label' => 'Business Name'),
                            array('type' => 'text', 'name' => 'registration_number', 'label' => 'Registration Number'),
                            array('type' => 'date', 'name' => 'incorporation_date', 'label' => 'Incorporation Date'),
                            array('type' => 'text', 'name' => 'industry', 'label' => 'Industry'),
                            array('type' => 'textarea', 'name' => 'business_description', 'label' => 'Business Description')
                        )
                    ),
                    // Add more steps as needed
                )
            );
        }
    }
}