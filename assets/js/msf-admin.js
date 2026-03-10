/**
 * Multi-Step Forms Manager Admin Script
 */

jQuery(document).ready(function($) {
    // basic builder for steps and fields
    var formData = (typeof msfAdminData !== 'undefined' && msfAdminData.initial) ? msfAdminData.initial : { steps: [] };

    function renderBuilder() {
        var $steps = $('#msf-steps').empty();
        formData.steps.forEach(function(step, i) {
            var $step = $('<li class="msf-step-item" data-step-index="' + i + '"></li>');
            $step.append('<span class="msf-drag-handle dashicons dashicons-menu"></span>');
            $step.append('<input type="text" class="msf-step-title" placeholder="Step Title" value="' + (step.title || '') + '">');
            $step.append('<button type="button" class="msf-remove-step button-link-delete" title="Remove step">&times;</button>');
            $step.append('<button type="button" class="msf-add-field button">Add Field</button>');

            var $fields = $('<ul class="msf-fields-list"></ul>');
            (step.fields || []).forEach(function(field, j) {
                $fields.append(renderField(field));
            });

            $step.append($fields);
            $steps.append($step);
        });
        makeSortable();
    }

    function renderField(field) {
        var $field = $('<li class="msf-field-item"></li>');
        var type = field.type || 'text';
        var name = field.name || '';
        var label = field.label || '';
        var validation = field.validation || '';
        var condition = field.condition || '';

        $field.append('<span class="msf-drag-handle dashicons dashicons-menu"></span>');

        var $type = $('<select class="msf-field-type">'
            + '<option value="text">Text</option>'
            + '<option value="date">Date</option>'
            + '<option value="textarea">Textarea</option>'
            + '<option value="select">Select</option>'
            + '<option value="radio">Radio</option>'
            + '</select>');
        $type.val(type);
        $field.append($type);

        $field.append('<input type="text" class="msf-field-name" placeholder="Name" value="' + name + '">');
        $field.append('<input type="text" class="msf-field-label" placeholder="Label" value="' + label + '">');
        $field.append('<select class="msf-field-validation"><option value="">Validation</option>'
            + '<option value="required">Required</option>'
            + '<option value="email">Email</option>'
            + '<option value="number">Number</option>'
            + '</select>');
        $field.find('.msf-field-validation').val(validation);
        $field.append('<input type="text" class="msf-field-condition" placeholder="Condition (e.g. country=US)" value="' + condition + '">');
        $field.append('<button type="button" class="msf-remove-field button-link-delete" title="Remove field">&times;</button>');

        var $optionsWrapper = $('<div class="msf-field-options-wrapper"></div>');
        if (type === 'select' || type === 'radio') {
            $optionsWrapper.append(renderOptionsEditor(field.options || []));
        }
        $field.append($optionsWrapper);
        return $field;
    }

    function makeSortable() {
        $('#msf-steps').sortable({
            handle: '.msf-drag-handle',
            placeholder: 'msf-step-placeholder',
            update: saveData
        });
        $('.msf-fields-list').sortable({
            handle: '.msf-drag-handle',
            placeholder: 'msf-field-placeholder',
            update: saveData
        });
    }

    function collectData() {
        var steps = [];
        $('#msf-steps .msf-step-item').each(function() {
            var $step = $(this);
            var title = $step.find('.msf-step-title').val();
            var fields = [];
            $step.find('.msf-field-item').each(function() {
                var $field = $(this);
                var type = $field.find('.msf-field-type').val();
                var name = $field.find('.msf-field-name').val();
                var label = $field.find('.msf-field-label').val();
                var validation = $field.find('.msf-field-validation').val();
                var condition = $field.find('.msf-field-condition').val();
                var f = { type: type, name: name, label: label };
                if (validation) {
                    f.validation = validation;
                }
                if (condition) {
                    f.condition = condition;
                }
                if ((type === 'select' || type === 'radio')) {
                    f.options = [];
                    $field.find('.msf-option-row').each(function() {
                        var $row = $(this);
                        var val = $row.find('.msf-option-value').val();
                        var lbl = $row.find('.msf-option-label').val();
                        var icon = $row.find('.msf-option-icon').val();
                        var desc = $row.find('.msf-option-desc').val();
                        var badge = $row.find('.msf-option-badge').val();
                        if (val) {
                            var opt = { value: val, label: lbl || val };
                            if (icon) opt.icon = icon;
                            if (desc) opt.description = desc;
                            if (badge) opt.badge = badge;
                            f.options.push(opt);
                        }
                    });
                }
                fields.push(f);
            });
            steps.push({ title: title, fields: fields });
        });
        return { steps: steps };
    }

    function saveData() {
        formData = collectData();
        $('#form_data').val(JSON.stringify(formData, null, 4));
    }

    // event handlers
    $('#msf-add-step').on('click', function() {
        formData.steps.push({ title: '', fields: [] });
        renderBuilder();
        saveData();
    });

    $('#msf-steps').on('click', '.msf-remove-step', function() {
        $(this).closest('.msf-step-item').remove();
        saveData();
    });

    $('#msf-steps').on('click', '.msf-add-field', function() {
        var $step = $(this).closest('.msf-step-item');
        $step.find('.msf-fields-list').append(renderField({ type: 'text', name: '', label: '' }));
        makeSortable();
        saveData();
    });

    $('#msf-steps').on('click', '.msf-remove-field', function() {
        $(this).closest('.msf-field-item').remove();
        saveData();
    });

    $('#msf-steps').on('change', '.msf-field-type', function() {
        var $field = $(this).closest('.msf-field-item');
        var type = $(this).val();
        var $wrapper = $field.find('.msf-field-options-wrapper');
        $wrapper.empty();
        if (type === 'select' || type === 'radio') {
            $wrapper.append(renderOptionsEditor());
        }
        saveData();
    });

    $('#msf-admin-form').on('submit', function() {
        saveData();
    });


    // helper for options editor
    function renderOptionsEditor(options) {
        options = options || [];
        var $container = $('<div class="msf-field-options-wrapper"></div>');
        var $list = $('<div class="msf-options-list"></div>');
        options.forEach(function(opt) {
            $list.append(renderOptionRow(opt));
        });
        $container.append($list);
        $container.append('<button type="button" class="msf-add-option button">Add Option</button>');
        return $container;
    }

    function renderOptionRow(opt) {
        opt = opt || {};
        var $row = $('<div class="msf-option-row"></div>');
        $row.append('<input class="msf-option-value" placeholder="value" style="width:80px;" value="' + (opt.value||'') + '">');
        $row.append('<input class="msf-option-label" placeholder="label" style="width:120px;" value="' + (opt.label||'') + '">');
        $row.append('<input class="msf-option-icon" placeholder="icon" style="width:80px;" value="' + (opt.icon||'') + '">');
        $row.append('<input class="msf-option-desc" placeholder="description" style="width:140px;" value="' + (opt.description||'') + '">');
        $row.append('<input class="msf-option-badge" placeholder="badge" style="width:100px;" value="' + (opt.badge||'') + '">');
        $row.append('<span class="msf-remove-option">&times;</span>');
        return $row;
    }

    // option events
    $('#msf-steps').on('click', '.msf-add-option', function() {
        var $wrapper = $(this).closest('.msf-field-options-wrapper');
        $wrapper.find('.msf-options-list').append(renderOptionRow());
        saveData();
    });

    $('#msf-steps').on('click', '.msf-remove-option', function() {
        $(this).closest('.msf-option-row').remove();
        saveData();
    });

    // initialize
    renderBuilder();
    saveData();
});