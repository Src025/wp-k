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
                <?php foreach ($steps as $step_index => $step): 
                    $activeClass = $step_index === 0 ? 'active' : '';
                    ?>
                    <div class="msf-step <?php echo $activeClass; ?>" data-step="<?php echo $step_index; ?>">
                        <?php
                        // custom card UI for the very first personal step
                        if ($type === 'personal' && $step_index === 0):
                            // header and description from React example
                        ?>
                            <div class="personal-first-step">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-2 h-8 bg-accent rounded-full"></div>
                                    <span class="text-xs font-bold tracking-[0.2em] text-accent uppercase">Step <?php echo ($step_index + 1) . ' / ' . $total_steps; ?></span>
                                </div>
                                <h1 class="text-4xl font-bold text-primary font-headline mb-4"><?php echo esc_html($step['title']); ?></h1>
                                <?php if (!empty($step['description'])): ?>
                                    <p class="text-muted-foreground text-lg"><?php echo esc_html($step['description']); ?></p>
                                <?php endif; ?>

                                <div class="card border-2 shadow-lg">
                                    <div class="card-header bg-secondary/30">
                                        <h3 class="text-xl font-bold text-primary">Identity Information</h3>
                                        <p class="">All information is encrypted and processed via secure AS-992-SEC protocols.</p>
                                    </div>
                                    <div class="card-content pt-8">
                                        <div class="space-y-6">
                                            <?php
                                            // Iterate fields in a layout similar to React sample
                                            foreach ($step['fields'] as $field):
                                                // decide layout rows by name
                                                switch ($field['name']) {
                                                    case 'first_name':
                                                    case 'last_name':
                                                        if ($field['name'] === 'first_name') {
                                                            echo '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">';
                                                        }
                                                        ?>
                                                        <div class="space-y-2">
                                                            <label for="<?php echo esc_attr($field['name']); ?>" class="text-sm font-bold uppercase tracking-wider flex items-center gap-2">
                                                                <?php if ($field['name'] === 'first_name'): ?>
                                                                    <i class="fa-solid fa-user w-4 h-4 text-accent"></i>
                                                                <?php endif; ?>
                                                                <?php echo esc_html($field['label']); ?>
                                                            </label>
                                                            <?php
                                                            // simple input rendering (type/placeholder)
                                                            $typeAttr = isset($field['type']) ? esc_attr($field['type']) : 'text';
                                                            $placeholder = isset($field['placeholder']) ? esc_attr($field['placeholder']) : '';
                                                            echo '<input id="' . esc_attr($field['name']) . '" name="' . esc_attr($field['name']) . '" type="' . $typeAttr . '" placeholder="' . $placeholder . '" class="py-6 border-2 focus-visible:ring-accent" required>';
                                                            ?>
                                                        </div>
                                                        <?php
                                                        if ($field['name'] === 'last_name') {
                                                            echo '</div>'; // close grid row
                                                        }
                                                        break;
                                                    default:
                                                        // for other single-column fields
                                                        ?>
                                                        <div class="space-y-2">
                                                            <label for="<?php echo esc_attr($field['name']); ?>" class="text-sm font-bold uppercase tracking-wider flex items-center gap-2">
                                                                <?php
                                                                $iconClass = '';
                                                                if ($field['name'] === 'email') {
                                                                    $iconClass = 'mail';
                                                                } elseif ($field['name'] === 'phone') {
                                                                    $iconClass = 'phone';
                                                                } elseif ($field['name'] === 'address') {
                                                                    $iconClass = 'map-pin';
                                                                }
                                                                if ($iconClass):
                                                                    echo '<i class="fa-solid fa-' . esc_attr($iconClass) . ' w-4 h-4 text-accent"></i>';
                                                                endif;
                                                                ?>
                                                                <?php echo esc_html($field['label']); ?>
                                                            </label>
                                                            <?php
                                                            $typeAttr = isset($field['type']) ? esc_attr($field['type']) : 'text';
                                                            $placeholder = isset($field['placeholder']) ? esc_attr($field['placeholder']) : '';
                                                            echo '<input id="' . esc_attr($field['name']) . '" name="' . esc_attr($field['name']) . '" type="' . $typeAttr . '" placeholder="' . $placeholder . '" class="py-6 border-2 focus-visible:ring-accent" required>';
                                                            ?>
                                                        </div>
                                                        <?php
                                                        break;
                                                }
                                            endforeach;
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                        else:
                            // fallback to default rendering for other steps/types
                        ?>
                            <h2><?php echo esc_html($step['title']); ?></h2>
                            <?php if (!empty($step['description'])): ?>
                                <p><?php echo esc_html($step['description']); ?></p>
                            <?php endif; ?>

                            <?php if (!empty($step['info'])): ?>
                                <div class="msf-info-box">
                                    <i class="fa-solid fa-circle-info"></i>
                                    <div class="msf-info-box-content">
                                        <?php echo wp_kses_post($step['info']); ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($step['sections'])): ?>
                                <?php foreach ($step['sections'] as $section): ?>
                                    <?php if (!empty($section['title'])): ?>
                                        <div class="msf-section-title"><?php echo esc_html($section['title']); ?></div>
                                        <?php if (!empty($section['description'])): ?>
                                            <div class="msf-section-desc"><?php echo esc_html($section['description']); ?></div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <div class="msf-fields">
                                        <?php foreach ($section['fields'] as $field): ?>
                                            <div class="msf-field">
                                                <label><?php if (!empty($field['icon'])):
                                                        $icon = $field['icon'];
                                                        if (filter_var($icon, FILTER_VALIDATE_URL)) {
                                                            echo '<img src="' . esc_url($icon) . '" class="msf-field-icon-img" alt="">&nbsp;';
                                                        } else {
                                                            echo '<i class="fa-solid fa-' . esc_attr($icon) . '"></i>&nbsp;';
                                                        }
                                                    endif; ?><?php echo esc_html($field['label']); ?><?php if (!empty($field['required'])): ?> <span class="required">*</span><?php endif; ?></label>
                                                <?php self::render_field($field); ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php elseif (isset($step['fields'])): ?>
                                <div class="msf-fields">
                                    <?php foreach ($step['fields'] as $field): ?>
                                        <div class="msf-field">
                                            <label><?php if (!empty($field['icon'])):
                                                        $icon = $field['icon'];
                                                        if (filter_var($icon, FILTER_VALIDATE_URL)) {
                                                            echo '<img src="' . esc_url($icon) . '" class="msf-field-icon-img" alt="">&nbsp;';
                                                        } else {
                                                            echo '<i class="fa-solid fa-' . esc_attr($icon) . '"></i>&nbsp;';
                                                        }
                                                    endif; ?><?php echo esc_html($field['label']); ?><?php if (!empty($field['required'])): ?> <span class="required">*</span><?php endif; ?></label>
                                            <?php self::render_field($field); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
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
            case 'email':
            case 'number':
            case 'date':
                echo '<input type="' . $type . '" name="' . esc_attr($name) . '" placeholder="' . esc_attr(isset($field['label']) ? $field['label'] : '') . '" required>';
                break;
            case 'file':
            case 'file_upload':
                echo '<div class="msf-file-upload">';
                echo '<input type="file" name="' . esc_attr($name) . '" id="' . esc_attr($name) . '" required>';
                echo '<label for="' . esc_attr($name) . '" class="msf-file-label">';
                if (!empty($field['icon'])) {
                    echo '<i class="fa-solid fa-' . esc_attr($field['icon']) . '"></i>';
                }
                echo '<div class="msf-file-text">Click to upload or drag & drop<br><small>' . esc_html(isset($field['label']) ? $field['label'] : '') . '</small></div>';
                echo '</label>';
                echo '</div>';
                break;
            case 'textarea':
                echo '<textarea name="' . esc_attr($name) . '" placeholder="' . esc_attr(isset($field['label']) ? $field['label'] : '') . '" required></textarea>';
                break;
            case 'select':
                echo '<select name="' . esc_attr($name) . '" required>';
                echo '<option value="">Select...</option>';
                if (!empty($field['options']) && is_array($field['options'])) {
                    foreach ($field['options'] as $option) {
                        if (is_array($option)) {
                            $val = isset($option['value']) ? $option['value'] : '';
                            $lbl = isset($option['label']) ? $option['label'] : $val;
                        } else {
                            $val = $lbl = $option;
                        }
                        echo '<option value="' . esc_attr($val) . '">' . esc_html($lbl) . '</option>';
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
                        echo '<input id="' . $option_id . '" type="radio" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '" required>';
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