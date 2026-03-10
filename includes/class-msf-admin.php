<?php
/**
 * Admin class for Multi-Step Forms Manager
 */

class MSF_Admin {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_init', array($this, 'register_settings'));
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
            'Settings',
            'Settings',
            'manage_options',
            'msf-settings',
            array($this, 'settings_page')
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
            wp_enqueue_script('msf-admin-script', MSF_PLUGIN_URL . 'assets/js/msf-admin.js', array('jquery'), MSF_VERSION, true);
            wp_enqueue_media(); // For media uploader
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
            $form_data = $this->process_form_submission($_POST);
            update_option("msf_{$type}_form_data", $form_data);
            echo '<div class="notice notice-success"><p>Form saved successfully!</p></div>';
        }

        ?>
        <div class="wrap">
            <h1><?php echo ucfirst($type); ?> Account Form Builder</h1>

            <form method="post" action="" id="msf-form-builder">
                <?php wp_nonce_field('msf_save_form'); ?>

                <div class="msf-form-builder-container">
                    <div class="msf-steps-builder">
                        <h3>Form Steps</h3>
                        <div id="msf-steps-list" class="msf-steps-list">
                            <?php foreach ($form_data['steps'] as $index => $step): ?>
                                <div class="msf-step-item" data-step-index="<?php echo $index; ?>">
                                    <div class="msf-step-header">
                                        <span class="msf-step-number"><?php echo $index + 1; ?></span>
                                        <input type="text" name="steps[<?php echo $index; ?>][title]" value="<?php echo esc_attr($step['title']); ?>" placeholder="Step Title" class="msf-step-title-input">
                                        <div class="msf-step-actions">
                                            <button type="button" class="msf-icon-picker-btn" data-step-index="<?php echo $index; ?>">
                                                <i class="fas <?php echo esc_attr($step['icon'] ?? 'fa-circle'); ?>"></i>
                                            </button>
                                            <input type="hidden" name="steps[<?php echo $index; ?>][icon]" value="<?php echo esc_attr($step['icon'] ?? 'fa-circle'); ?>" class="msf-step-icon-input">
                                            <button type="button" class="msf-remove-step-btn" <?php echo count($form_data['steps']) <= 1 ? 'disabled' : ''; ?>>×</button>
                                        </div>
                                    </div>

                                    <div class="msf-fields-builder">
                                        <h4>Fields</h4>
                                        <div class="msf-fields-list">
                                            <?php if (isset($step['fields'])): ?>
                                                <?php foreach ($step['fields'] as $field_index => $field): ?>
                                                    <div class="msf-field-item">
                                                        <select name="steps[<?php echo $index; ?>][fields][<?php echo $field_index; ?>][type]" class="msf-field-type">
                                                            <option value="text" <?php selected($field['type'], 'text'); ?>>Text</option>
                                                            <option value="textarea" <?php selected($field['type'], 'textarea'); ?>>Textarea</option>
                                                            <option value="select" <?php selected($field['type'], 'select'); ?>>Select</option>
                                                            <option value="date" <?php selected($field['type'], 'date'); ?>>Date</option>
                                                            <option value="file" <?php selected($field['type'], 'file'); ?>>File Upload</option>
                                                        </select>
                                                        <input type="text" name="steps[<?php echo $index; ?>][fields][<?php echo $field_index; ?>][label]" value="<?php echo esc_attr($field['label']); ?>" placeholder="Field Label" class="msf-field-label">
                                                        <input type="text" name="steps[<?php echo $index; ?>][fields][<?php echo $field_index; ?>][name]" value="<?php echo esc_attr($field['name']); ?>" placeholder="Field Name" class="msf-field-name">
                                                        <button type="button" class="msf-remove-field-btn">×</button>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                        <button type="button" class="msf-add-field-btn">+ Add Field</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" id="msf-add-step-btn" class="button">+ Add Step</button>
                    </div>

                    <div class="msf-form-preview">
                        <h3>Preview</h3>
                        <div id="msf-form-preview-content">
                            <!-- Preview will be updated via JavaScript -->
                        </div>
                    </div>
                </div>

                <div class="msf-form-actions">
                    <input type="submit" name="save_form" class="button button-primary button-large" value="Save Form">
                    <input type="button" id="msf-export-json" class="button" value="Export JSON">
                    <input type="file" id="msf-import-json" accept=".json" style="display: none;">
                    <label for="msf-import-json" class="button">Import JSON</label>
                </div>
            </form>
        </div>

        <!-- Icon Picker Modal -->
        <div id="msf-icon-picker-modal" class="msf-modal" style="display: none;">
            <div class="msf-modal-content">
                <div class="msf-modal-header">
                    <h3>Choose Icon</h3>
                    <button type="button" class="msf-modal-close">&times;</button>
                </div>
                <div class="msf-modal-body">
                    <div class="msf-icon-search">
                        <input type="text" id="msf-icon-search-input" placeholder="Search icons...">
                    </div>
                    <div class="msf-icon-grid">
                        <?php
                        $fontawesome_icons = array(
                            'fa-user', 'fa-envelope', 'fa-phone', 'fa-map-marker-alt', 'fa-calendar',
                            'fa-credit-card', 'fa-building', 'fa-briefcase', 'fa-file', 'fa-camera',
                            'fa-upload', 'fa-download', 'fa-check', 'fa-times', 'fa-star',
                            'fa-heart', 'fa-home', 'fa-cog', 'fa-wrench', 'fa-key',
                            'fa-lock', 'fa-unlock', 'fa-shield-alt', 'fa-bell', 'fa-comment',
                            'fa-question', 'fa-info', 'fa-exclamation', 'fa-search', 'fa-filter',
                            'fa-sort', 'fa-arrow-up', 'fa-arrow-down', 'fa-arrow-left', 'fa-arrow-right',
                            'fa-plus', 'fa-minus', 'fa-edit', 'fa-trash', 'fa-save',
                            'fa-print', 'fa-share', 'fa-link', 'fa-external-link-alt', 'fa-sign-in-alt',
                            'fa-sign-out-alt', 'fa-user-plus', 'fa-user-minus', 'fa-users', 'fa-user-tie',
                            'fa-id-card', 'fa-address-card', 'fa-handshake', 'fa-chart-line', 'fa-chart-bar',
                            'fa-chart-pie', 'fa-dollar-sign', 'fa-euro-sign', 'fa-pound-sign', 'fa-yen-sign',
                            'fa-bitcoin', 'fa-exchange-alt', 'fa-money-bill-wave', 'fa-credit-card-front', 'fa-wallet',
                            'fa-piggy-bank', 'fa-coins', 'fa-hand-holding-usd', 'fa-file-invoice-dollar', 'fa-receipt',
                            'fa-building-columns', 'fa-landmark', 'fa-university', 'fa-bank', 'fa-money-check',
                            'fa-circle', 'fa-square', 'fa-check-circle', 'fa-times-circle', 'fa-exclamation-circle',
                            'fa-info-circle', 'fa-question-circle', 'fa-check-square', 'fa-minus-square', 'fa-plus-square'
                        );

                        foreach ($fontawesome_icons as $icon): ?>
                            <div class="msf-icon-item" data-icon="<?php echo $icon; ?>">
                                <i class="fas <?php echo $icon; ?>"></i>
                                <span><?php echo str_replace('fa-', '', $icon); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function register_settings() {
        register_setting('msf_settings', 'msf_font_family');
        register_setting('msf_settings', 'msf_font_weight');
        register_setting('msf_settings', 'msf_primary_color');
        register_setting('msf_settings', 'msf_secondary_color');
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>Multi-Step Forms Settings</h1>

            <form method="post" action="options.php">
                <?php settings_fields('msf_settings'); ?>
                <?php do_settings_sections('msf_settings'); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">Font Family</th>
                        <td>
                            <select name="msf_font_family" id="msf_font_family">
                                <option value="Moderna Serif" <?php selected(get_option('msf_font_family', 'Moderna Serif'), 'Moderna Serif'); ?>>Moderna Serif</option>
                                <option value="Arial" <?php selected(get_option('msf_font_family'), 'Arial'); ?>>Arial</option>
                                <option value="Helvetica" <?php selected(get_option('msf_font_family'), 'Helvetica'); ?>>Helvetica</option>
                                <option value="Georgia" <?php selected(get_option('msf_font_family'), 'Georgia'); ?>>Georgia</option>
                                <option value="Times New Roman" <?php selected(get_option('msf_font_family'), 'Times New Roman'); ?>>Times New Roman</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">Font Weight</th>
                        <td>
                            <select name="msf_font_weight" id="msf_font_weight">
                                <option value="400" <?php selected(get_option('msf_font_weight', '400'), '400'); ?>>Regular (400)</option>
                                <option value="500" <?php selected(get_option('msf_font_weight'), '500'); ?>>Medium (500)</option>
                                <option value="600" <?php selected(get_option('msf_font_weight'), '600'); ?>>Semi Bold (600)</option>
                                <option value="700" <?php selected(get_option('msf_font_weight'), '700'); ?>>Bold (700)</option>
                                <option value="800" <?php selected(get_option('msf_font_weight'), '800'); ?>>Extra Bold (800)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">Primary Color</th>
                        <td>
                            <input type="color" name="msf_primary_color" value="<?php echo esc_attr(get_option('msf_primary_color', '#1c3f74')); ?>" />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">Secondary Color</th>
                        <td>
                            <input type="color" name="msf_secondary_color" value="<?php echo esc_attr(get_option('msf_secondary_color', '#d8b25d')); ?>" />
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    private function process_form_submission($post_data) {
        $form_data = array('steps' => array());

        if (isset($post_data['steps']) && is_array($post_data['steps'])) {
            foreach ($post_data['steps'] as $step_index => $step) {
                $step_data = array(
                    'title' => sanitize_text_field($step['title']),
                    'icon' => sanitize_text_field($step['icon'] ?? 'fa-circle'),
                    'fields' => array()
                );

                if (isset($step['fields']) && is_array($step['fields'])) {
                    foreach ($step['fields'] as $field) {
                        if (!empty($field['label']) && !empty($field['name'])) {
                            $field_data = array(
                                'type' => sanitize_text_field($field['type']),
                                'label' => sanitize_text_field($field['label']),
                                'name' => sanitize_text_field($field['name'])
                            );

                            // Handle options for select fields
                            if ($field['type'] === 'select' && isset($field['options'])) {
                                $field_data['options'] = array_map('sanitize_text_field', $field['options']);
                            }

                            $step_data['fields'][] = $field_data;
                        }
                    }
                }

                $form_data['steps'][] = $step_data;
            }
        }

        return $form_data;
    }

    private function get_default_form_data($type) {
        if ($type === 'personal') {
            return array(
                'steps' => array(
                    array(
                        'title' => 'Account Type',
                        'icon' => 'fa-building-columns',
                        'fields' => array(
                            array('type' => 'radio', 'name' => 'account_type', 'label' => 'Select Account Type', 'options' => array('Savings', 'Custody', 'Numbered', 'Crypto'))
                        )
                    ),
                    array(
                        'title' => 'Personal Details',
                        'icon' => 'fa-user',
                        'fields' => array(
                            array('type' => 'text', 'name' => 'first_name', 'label' => 'First Name'),
                            array('type' => 'text', 'name' => 'last_name', 'label' => 'Last Name'),
                            array('type' => 'date', 'name' => 'dob', 'label' => 'Date of Birth'),
                            array('type' => 'text', 'name' => 'nationality', 'label' => 'Nationality'),
                            array('type' => 'text', 'name' => 'passport', 'label' => 'Passport/ID'),
                            array('type' => 'text', 'name' => 'occupation', 'label' => 'Occupation')
                        )
                    ),
                    array(
                        'title' => 'Address',
                        'icon' => 'fa-map-marker-alt',
                        'fields' => array(
                            array('type' => 'text', 'name' => 'street_address', 'label' => 'Street Address'),
                            array('type' => 'text', 'name' => 'city', 'label' => 'City'),
                            array('type' => 'text', 'name' => 'state', 'label' => 'State'),
                            array('type' => 'text', 'name' => 'postal_code', 'label' => 'Postal Code'),
                            array('type' => 'text', 'name' => 'country', 'label' => 'Country')
                        )
                    ),
                    array(
                        'title' => 'Transaction Profile',
                        'icon' => 'fa-exchange-alt',
                        'fields' => array(
                            array('type' => 'select', 'name' => 'monthly_volume', 'label' => 'Monthly Transaction Volume', 'options' => array('Under $1,000', '$1,000 - $10,000', '$10,000 - $50,000', 'Over $50,000')),
                            array('type' => 'select', 'name' => 'expected_deposit', 'label' => 'Expected Deposit', 'options' => array('Under $1,000', '$1,000 - $10,000', '$10,000 - $50,000', 'Over $50,000')),
                            array('type' => 'select', 'name' => 'primary_currency', 'label' => 'Primary Currency', 'options' => array('USD', 'EUR', 'GBP', 'CHF', 'Other'))
                        )
                    ),
                    array(
                        'title' => 'Source of Funds',
                        'icon' => 'fa-money-bill-wave',
                        'fields' => array(
                            array('type' => 'select', 'name' => 'source_of_funds', 'label' => 'Source of Funds', 'options' => array('Employment Income', 'Business Revenue', 'Investments', 'Inheritance', 'Other')),
                            array('type' => 'text', 'name' => 'employer_company', 'label' => 'Employer / Company')
                        )
                    ),
                    array(
                        'title' => 'KYC Upload',
                        'icon' => 'fa-id-card',
                        'fields' => array(
                            array('type' => 'file', 'name' => 'id_document', 'label' => 'ID Document'),
                            array('type' => 'file', 'name' => 'proof_of_address', 'label' => 'Proof of Address'),
                            array('type' => 'file', 'name' => 'additional_document', 'label' => 'Additional Document')
                        )
                    ),
                    array(
                        'title' => 'Review & Submit',
                        'icon' => 'fa-check-circle',
                        'fields' => array()
                    )
                )
            );
        } else {
            return array(
                'steps' => array(
                    array(
                        'title' => 'Business Type',
                        'icon' => 'fa-briefcase',
                        'fields' => array(
                            array('type' => 'radio', 'name' => 'business_type', 'label' => 'Select Business Type', 'options' => array('Sole Proprietorship', 'Partnership', 'Corporation', 'LLC'))
                        )
                    ),
                    array(
                        'title' => 'Business Details',
                        'icon' => 'fa-building',
                        'fields' => array(
                            array('type' => 'text', 'name' => 'business_name', 'label' => 'Business Name'),
                            array('type' => 'text', 'name' => 'registration_number', 'label' => 'Registration Number'),
                            array('type' => 'date', 'name' => 'incorporation_date', 'label' => 'Incorporation Date'),
                            array('type' => 'text', 'name' => 'industry', 'label' => 'Industry'),
                            array('type' => 'textarea', 'name' => 'business_description', 'label' => 'Business Description')
                        )
                    ),
                    array(
                        'title' => 'Business Address',
                        'icon' => 'fa-map-marker-alt',
                        'fields' => array(
                            array('type' => 'text', 'name' => 'business_street', 'label' => 'Street Address'),
                            array('type' => 'text', 'name' => 'business_city', 'label' => 'City'),
                            array('type' => 'text', 'name' => 'business_state', 'label' => 'State'),
                            array('type' => 'text', 'name' => 'business_postal', 'label' => 'Postal Code'),
                            array('type' => 'text', 'name' => 'business_country', 'label' => 'Country')
                        )
                    ),
                    array(
                        'title' => 'Financial Information',
                        'icon' => 'fa-chart-line',
                        'fields' => array(
                            array('type' => 'select', 'name' => 'annual_revenue', 'label' => 'Annual Revenue', 'options' => array('Under $100K', '$100K - $500K', '$500K - $1M', 'Over $1M')),
                            array('type' => 'select', 'name' => 'number_employees', 'label' => 'Number of Employees', 'options' => array('1-5', '6-20', '21-100', 'Over 100')),
                            array('type' => 'text', 'name' => 'business_bank', 'label' => 'Current Business Bank (if any)')
                        )
                    ),
                    array(
                        'title' => 'Authorized Signatory',
                        'icon' => 'fa-user-tie',
                        'fields' => array(
                            array('type' => 'text', 'name' => 'signatory_name', 'label' => 'Full Name'),
                            array('type' => 'text', 'name' => 'signatory_position', 'label' => 'Position'),
                            array('type' => 'text', 'name' => 'signatory_email', 'label' => 'Email'),
                            array('type' => 'text', 'name' => 'signatory_phone', 'label' => 'Phone')
                        )
                    ),
                    array(
                        'title' => 'Document Upload',
                        'icon' => 'fa-file-upload',
                        'fields' => array(
                            array('type' => 'file', 'name' => 'business_license', 'label' => 'Business License'),
                            array('type' => 'file', 'name' => 'articles_incorporation', 'label' => 'Articles of Incorporation'),
                            array('type' => 'file', 'name' => 'financial_statements', 'label' => 'Financial Statements')
                        )
                    ),
                    array(
                        'title' => 'Review & Submit',
                        'icon' => 'fa-check-circle',
                        'fields' => array()
                    )
                )
            );
        }
    }
}