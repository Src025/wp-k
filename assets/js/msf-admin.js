/**
 * Multi-Step Forms Manager Admin Script
 */

jQuery(document).ready(function($) {
    // basic builder for steps and fields
    var formData = msfInitialData || { steps: [] };

    function renderBuilder() {
        var $steps = $('#msf-steps').empty();
        formData.steps.forEach(function(step, i) {
            var $step = $('<li class="msf-step-item" data-step-index="' + i + '"></li>');
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
        $field.append('<select class="msf-field-type">'
            + '<option value="text">Text</option>'
            + '<option value="date">Date</option>'
            + '<option value="textarea">Textarea</option>'
            + '<option value="select">Select</option>'
            + '<option value="radio">Radio</option>'
            + '</select>');
        $field.find('.msf-field-type').val(type);
        $field.append('<input type="text" class="msf-field-name" placeholder="Name" value="' + name + '">');
        $field.append('<input type="text" class="msf-field-label" placeholder="Label" value="' + label + '">');
        $field.append('<button type="button" class="msf-remove-field button-link-delete" title="Remove field">&times;</button>');
        var $optionsWrapper = $('<div class="msf-field-options"></div>');
        if (type === 'select' || type === 'radio') {
            var opts = (field.options || []).join('\n');
            $optionsWrapper.append('<textarea class="msf-field-options-input" placeholder="One option per line">' + opts + '</textarea>');
        }
        $field.append($optionsWrapper);
        return $field;
    }

    function makeSortable() {
        $('#msf-steps').sortable({
            handle: '.msf-step-title',
            placeholder: 'msf-step-placeholder',
            update: saveData
        });
        $('.msf-fields-list').sortable({
            handle: '.msf-field-name',
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
                var f = { type: type, name: name, label: label };
                if ((type === 'select' || type === 'radio')) {
                    var opts = $field.find('.msf-field-options-input').val() || '';
                    f.options = opts.split('\n').map(function(o) { return o.trim(); }).filter(Boolean);
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
        var $opts = $field.find('.msf-field-options');
        $opts.empty();
        if (type === 'select' || type === 'radio') {
            $opts.append('<textarea class="msf-field-options-input" placeholder="One option per line"></textarea>');
        }
        saveData();
    });

    $('#msf-admin-form').on('submit', function() {
        saveData();
    });

    // initialize
    renderBuilder();
    saveData();
});