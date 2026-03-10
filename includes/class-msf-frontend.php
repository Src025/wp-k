<?php
/**
 * Frontend class for Multi-Step Forms Manager
 */

class MSF_Frontend {

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
    }

    public function enqueue_frontend_scripts() {
        // Scripts are enqueued in the main plugin file
    }

    public static function display_form($type = 'personal') {
        $form_data = get_option("msf_{$type}_form_data", array());

        if (empty($form_data)) {
            echo '<p>Form not configured yet.</p>';
            return;
        }

        // Handle form submission
        if (isset($_POST['msf_submit']) && $_POST['msf_form_type'] === $type) {
            self::process_form_submission($type);
        }

        // Get settings
        $font_family = get_option('msf_font_family', 'Moderna Serif');
        $font_weight = get_option('msf_font_weight', '400');
        $primary_color = get_option('msf_primary_color', '#1c3f74');
        $secondary_color = get_option('msf_secondary_color', '#d8b25d');

        $steps = $form_data['steps'];
        $total_steps = count($steps);

        ?>
        <style>
            .msf-form-container { font-family: '<?php echo $font_family; ?>', serif; font-weight: <?php echo $font_weight; ?>; }
            .msf-progress-bar { background: <?php echo $primary_color; ?>; }
            .msf-btn-next, .msf-btn-submit { background: <?php echo $primary_color; ?>; }
            .msf-steps li i { color: <?php echo $primary_color; ?>; }
            .msf-toggle-btn.active { background: <?php echo $primary_color; ?>; }
        </style>

        <div class="msf-form-container" data-type="<?php echo $type; ?>">
            <div class="msf-form-toggle">
                <a href="?msf_type=personal" class="msf-toggle-btn <?php echo $type === 'personal' ? 'active' : ''; ?>">Personal Account</a>
                <a href="?msf_type=business" class="msf-toggle-btn <?php echo $type === 'business' ? 'active' : ''; ?>">Business Account</a>
            </div>

            <div class="msf-steps">
                <ul>
                    <?php for ($i = 0; $i < $total_steps; $i++): ?>
                        <li class="<?php echo $i === 0 ? 'active' : ''; ?>">
                            <i class="fas <?php echo isset($steps[$i]['icon']) ? $steps[$i]['icon'] : self::get_step_icon($steps[$i]['title'], $i); ?>"></i>
                            <?php echo ($i + 1) . ' ' . $steps[$i]['title']; ?>
                        </li>
                    <?php endfor; ?>
                </ul>
            </div>

            <div class="msf-progress">
                <div class="msf-progress-bar" style="width: <?php echo (1 / $total_steps * 100); ?>%"></div>
            </div>

            <form class="msf-form" method="post">
                <input type="hidden" name="msf_form_type" value="<?php echo $type; ?>">
                <?php foreach ($steps as $step_index => $step): ?>
                    <div class="msf-step <?php echo $step_index === 0 ? 'active' : ''; ?>" data-step="<?php echo $step_index; ?>">
                        <h2><?php echo $step['title']; ?></h2>

                        <?php if (isset($step['fields'])): ?>
                            <div class="msf-fields">
                                <?php foreach ($step['fields'] as $field): ?>
                                    <div class="msf-field">
                                        <label><?php echo $field['label']; ?></label>
                                        <?php self::render_field($field); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <div class="msf-actions">
                    <button type="button" class="msf-btn msf-btn-back" disabled>Back</button>
                    <button type="button" class="msf-btn msf-btn-next">Next</button>
                    <input type="submit" name="msf_submit" class="msf-btn msf-btn-submit" style="display: none;" value="Submit">
                </div>
            </form>
        </div>
        <?php
    }

    private static function get_step_icon($title, $index) {
        $icons = array(
            'Account' => 'fa-building-columns',
            'Personal' => 'fa-user',
            'Address' => 'fa-map-marker-alt',
            'Transaction' => 'fa-exchange-alt',
            'Source' => 'fa-money-bill-wave',
            'KYC' => 'fa-id-card',
            'Review' => 'fa-check-circle',
            'Business' => 'fa-briefcase',
            'Details' => 'fa-info-circle',
            'Type' => 'fa-list',
            'Upload' => 'fa-upload',
            'Contact' => 'fa-phone',
            'Funds' => 'fa-dollar-sign'
        );

        // Try to match title keywords
        foreach ($icons as $keyword => $icon) {
            if (stripos($title, $keyword) !== false) {
                return $icon;
            }
        }

        // Default icons based on step number
        $default_icons = array(
            'fa-star',
            'fa-user',
            'fa-map',
            'fa-cog',
            'fa-file',
            'fa-camera',
            'fa-check'
        );

        return $default_icons[$index % count($default_icons)] ?? 'fa-circle';
    }

    private static function render_field($field) {
        $name = $field['name'];
        $type = $field['type'];

        switch ($type) {
            case 'text':
            case 'date':
                echo '<input type="' . $type . '" name="' . $name . '" required>';
                break;
            case 'file':
                echo '<input type="file" name="' . $name . '" accept=".jpg,.jpeg,.png,.pdf" required>';
                break;
            case 'textarea':
                echo '<textarea name="' . $name . '" required></textarea>';
                break;
            case 'select':
                echo '<select name="' . $name . '" required>';
                echo '<option value="">Select...</option>';
                if (isset($field['options'])) {
                    foreach ($field['options'] as $option) {
                        echo '<option value="' . $option . '">' . $option . '</option>';
                    }
                }
                echo '</select>';
                break;
            case 'radio':
                if ($name === 'account_type' && isset($field['options'])) {
                    self::render_account_type_cards($field['options']);
                } elseif ($name === 'business_type' && isset($field['options'])) {
                    self::render_business_type_cards($field['options']);
                } else {
                    if (isset($field['options'])) {
                        foreach ($field['options'] as $option) {
                            echo '<label><input type="radio" name="' . $name . '" value="' . $option . '" required> ' . $option . '</label><br>';
                        }
                    }
                }
                break;
            // Add more field types as needed
        }
    }

    private static function render_account_type_cards($options) {
        $account_types = array(
            'Savings' => array(
                'icon' => 'fa-building-columns',
                'description' => 'Regular savings account',
                'badge' => 'SWIFT Compatible',
                'color' => '#1c3f74'
            ),
            'Custody' => array(
                'icon' => 'fa-shield-halved',
                'description' => 'Asset custody account',
                'badge' => 'ETF Compatible',
                'color' => '#2e5c8a'
            ),
            'Numbered' => array(
                'icon' => 'fa-lock',
                'description' => 'Private coded account',
                'badge' => '',
                'color' => '#3a6ba5'
            ),
            'Crypto' => array(
                'icon' => 'fa-bitcoin',
                'description' => 'Digital asset account',
                'badge' => 'ETF Compatible',
                'color' => '#4a7ac0'
            )
        );

        echo '<div class="msf-account-cards">';
        foreach ($options as $option) {
            $type_data = isset($account_types[$option]) ? $account_types[$option] : array(
                'icon' => 'fa-circle',
                'description' => '',
                'badge' => '',
                'color' => '#1c3f74'
            );

            echo '<label class="msf-account-card">';
            echo '<input type="radio" name="account_type" value="' . $option . '" required>';
            echo '<div class="msf-card-content">';
            echo '<div class="msf-card-icon" style="color: ' . $type_data['color'] . '">';
            echo '<i class="fas ' . $type_data['icon'] . '"></i>';
            echo '</div>';
            echo '<div class="msf-card-text">';
            echo '<h3>' . $option . ' Account</h3>';
            if ($type_data['description']) {
                echo '<p>' . $type_data['description'] . '</p>';
            }
            if ($type_data['badge']) {
                echo '<span class="msf-card-badge">' . $type_data['badge'] . '</span>';
            }
            echo '</div>';
            echo '</div>';
            echo '</label>';
        }
        echo '</div>';
    }

    private static function render_business_type_cards($options) {
        $business_types = array(
            'Sole Proprietorship' => array(
                'icon' => 'fa-user',
                'description' => 'Single owner business',
                'color' => '#1c3f74'
            ),
            'Partnership' => array(
                'icon' => 'fa-users',
                'description' => 'Multiple owners partnership',
                'color' => '#2e5c8a'
            ),
            'Corporation' => array(
                'icon' => 'fa-building',
                'description' => 'Legal corporation entity',
                'color' => '#3a6ba5'
            ),
            'LLC' => array(
                'icon' => 'fa-handshake',
                'description' => 'Limited Liability Company',
                'color' => '#4a7ac0'
            )
        );

        echo '<div class="msf-account-cards">';
        foreach ($options as $option) {
            $type_data = isset($business_types[$option]) ? $business_types[$option] : array(
                'icon' => 'fa-circle',
                'description' => '',
                'color' => '#1c3f74'
            );

            echo '<label class="msf-account-card">';
            echo '<input type="radio" name="business_type" value="' . $option . '" required>';
            echo '<div class="msf-card-content">';
            echo '<div class="msf-card-icon" style="color: ' . $type_data['color'] . '">';
            echo '<i class="fas ' . $type_data['icon'] . '"></i>';
            echo '</div>';
            echo '<div class="msf-card-text">';
            echo '<h3>' . $option . '</h3>';
            if ($type_data['description']) {
                echo '<p>' . $type_data['description'] . '</p>';
            }
            echo '</div>';
            echo '</div>';
            echo '</label>';
        }
        echo '</div>';
    }
}