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

        $steps = $form_data['steps'];
        $total_steps = count($steps);

        ?>
        <!-- topbar / hero copied from template -->
        <div class="topbar">
            <div class="msf-container nav">
                <div class="logo">
                    <div class="logo-box">P</div>
                    PROMINENCE BANK
                </div>
                <div>🔒 Secure Application Portal</div>
            </div>
        </div>

        <div class="hero">
            <div class="msf-container hero-row">
                <div>
                    <h1>Open Your <span>Account Today</span></h1>
                    <p>Complete the onboarding process securely.</p>
                </div>
                <div class="stats">
                    <div><strong>7</strong>STEPS</div>
                    <div><strong>48h</strong>REVIEW</div>
                    <div><strong>100%</strong>SECURE</div>
                </div>
            </div>
        </div>

        <div class="msf-form-container" data-type="<?php echo $type; ?>">
            <div class="msf-form-toggle">
                <a href="?msf_type=personal" class="msf-toggle-btn <?php echo $type === 'personal' ? 'active' : ''; ?>">Personal Account</a>
                <a href="?msf_type=business" class="msf-toggle-btn <?php echo $type === 'business' ? 'active' : ''; ?>">Business Account</a>
            </div>

            <div class="msf-steps">
                <ul>
                    <?php for ($i = 0; $i < $total_steps; $i++): ?>
                        <li class="<?php echo $i === 0 ? 'active' : ''; ?>"><?php echo ($i + 1) . ' ' . $steps[$i]['title']; ?></li>
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

    private static function process_form_submission($type) {
        // Process the form data
        // In a real implementation, you'd validate and save to database
        $form_data = $_POST;
        unset($form_data['msf_submit'], $form_data['msf_form_type']);

        // For now, just show a success message
        echo '<div class="msf-success-message">Thank you! Your ' . ucfirst($type) . ' account application has been submitted successfully.</div>';
    }

    private static function render_field($field) {
        $name = isset($field['name']) ? $field['name'] : '';
        $type = isset($field['type']) ? $field['type'] : 'text';

        switch ($type) {
            case 'text':
            case 'date':
                echo '<input type="' . $type . '" name="' . esc_attr($name) . '" required>';
                break;
            case 'textarea':
                echo '<textarea name="' . esc_attr($name) . '" required></textarea>';
                break;
            case 'select':
                echo '<select name="' . esc_attr($name) . '" required>';
                echo '<option value="">Select...</option>';
                if (!empty($field['options']) && is_array($field['options'])) {
                    foreach ($field['options'] as $option) {
                        echo '<option value="' . esc_attr($option) . '">' . esc_html($option) . '</option>';
                    }
                }
                echo '</select>';
                break;
            case 'radio':
                // support enhanced option objects (icon, description, badge)
                if (!empty($field['options']) && is_array($field['options'])) {
                    echo '<div class="msf-options">';
                    foreach ($field['options'] as $option) {
                        // option may be string or array
                        if (is_array($option)) {
                            $value = isset($option['value']) ? $option['value'] : '';
                            $label = isset($option['label']) ? $option['label'] : $value;
                            $icon = isset($option['icon']) ? $option['icon'] : '';
                            $description = isset($option['description']) ? $option['description'] : '';
                            $badge = isset($option['badge']) ? $option['badge'] : '';
                        } else {
                            $value = $label = $option;
                            $icon = $description = $badge = '';
                        }
                        $option_id = 'msf_' . esc_attr($name) . '_' . esc_attr(sanitize_title($value));
                        echo '<label class="msf-option" for="' . $option_id . '">';
                        echo '<input id="' . $option_id . '" type="radio" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '" required style="display:none;">';
                        if ($icon) {
                            echo '<div class="icon"><i class="fa-solid fa-' . esc_attr($icon) . '"></i></div>';
                        }
                        echo '<div class="msf-option-text">';
                        echo '<strong>' . esc_html($label) . '</strong>';
                        if ($description) {
                            echo '<div class="msf-option-description">' . esc_html($description) . '</div>';
                        }
                        if ($badge) {
                            echo '<div class="msf-badge">' . esc_html($badge) . '</div>';
                        }
                        echo '</div>'; // msf-option-text
                        echo '</label>';
                    }
                    echo '</div>'; // msf-options
                }
                break;
            // Add more field types as needed
        }
    }
}