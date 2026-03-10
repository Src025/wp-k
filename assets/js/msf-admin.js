/**
 * Multi-Step Forms Manager Admin Script
 */

jQuery(document).ready(function($) {
    let stepIndex = $('.msf-step-item').length;
    let currentIconStep = null;

    // Add new step
    $('#msf-add-step-btn').on('click', function() {
        const stepHtml = `
            <div class="msf-step-item" data-step-index="${stepIndex}">
                <div class="msf-step-header">
                    <span class="msf-step-number">${stepIndex + 1}</span>
                    <input type="text" name="steps[${stepIndex}][title]" value="New Step" placeholder="Step Title" class="msf-step-title-input">
                    <div class="msf-step-actions">
                        <button type="button" class="msf-icon-picker-btn" data-step-index="${stepIndex}">
                            <i class="fas fa-circle"></i>
                        </button>
                        <input type="hidden" name="steps[${stepIndex}][icon]" value="fa-circle" class="msf-step-icon-input">
                        <button type="button" class="msf-remove-step-btn">×</button>
                    </div>
                </div>

                <div class="msf-fields-builder">
                    <h4>Fields</h4>
                    <div class="msf-fields-list"></div>
                    <button type="button" class="msf-add-field-btn">+ Add Field</button>
                </div>
            </div>
        `;

        $('#msf-steps-list').append(stepHtml);
        stepIndex++;
        updateStepNumbers();
        updateRemoveButtons();
    });

    // Remove step
    $(document).on('click', '.msf-remove-step-btn:not(:disabled)', function() {
        $(this).closest('.msf-step-item').remove();
        updateStepNumbers();
        updateRemoveButtons();
    });

    // Add field to step
    $(document).on('click', '.msf-add-field-btn', function() {
        const stepItem = $(this).closest('.msf-step-item');
        const stepIndex = stepItem.data('step-index');
        const fieldsList = stepItem.find('.msf-fields-list');
        const fieldIndex = fieldsList.children().length;

        const fieldHtml = `
            <div class="msf-field-item">
                <select name="steps[${stepIndex}][fields][${fieldIndex}][type]" class="msf-field-type">
                    <option value="text">Text</option>
                    <option value="textarea">Textarea</option>
                    <option value="select">Select</option>
                    <option value="date">Date</option>
                    <option value="file">File Upload</option>
                </select>
                <input type="text" name="steps[${stepIndex}][fields][${fieldIndex}][label]" value="" placeholder="Field Label" class="msf-field-label">
                <input type="text" name="steps[${stepIndex}][fields][${fieldIndex}][name]" value="" placeholder="Field Name" class="msf-field-name">
                <button type="button" class="msf-remove-field-btn">×</button>
            </div>
        `;

        fieldsList.append(fieldHtml);
    });

    // Remove field
    $(document).on('click', '.msf-remove-field-btn', function() {
        $(this).closest('.msf-field-item').remove();
    });

    // Icon picker
    $(document).on('click', '.msf-icon-picker-btn', function() {
        currentIconStep = $(this).data('step-index');
        $('#msf-icon-picker-modal').show();
    });

    // Close modal
    $('.msf-modal-close').on('click', function() {
        $('#msf-icon-picker-modal').hide();
    });

    // Icon search
    $('#msf-icon-search-input').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.msf-icon-item').each(function() {
            const iconName = $(this).find('span').text().toLowerCase();
            if (iconName.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Select icon
    $(document).on('click', '.msf-icon-item', function() {
        const iconClass = $(this).data('icon');
        const iconBtn = $(`.msf-icon-picker-btn[data-step-index="${currentIconStep}"]`);
        const iconInput = iconBtn.siblings('.msf-step-icon-input');

        iconBtn.find('i').attr('class', `fas ${iconClass}`);
        iconInput.val(iconClass);

        $('#msf-icon-picker-modal').hide();
    });

    // Export JSON
    $('#msf-export-json').on('click', function() {
        const formData = collectFormData();
        const jsonString = JSON.stringify(formData, null, 2);
        const blob = new Blob([jsonString], { type: 'application/json' });
        const url = URL.createObjectURL(blob);

        const a = document.createElement('a');
        a.href = url;
        a.download = 'form-config.json';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });

    // Import JSON
    $('#msf-import-json').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const formData = JSON.parse(e.target.result);
                    loadFormData(formData);
                } catch (error) {
                    alert('Invalid JSON file');
                }
            };
            reader.readAsText(file);
        }
    });

    function updateStepNumbers() {
        $('.msf-step-item').each(function(index) {
            $(this).find('.msf-step-number').text(index + 1);
        });
    }

    function updateRemoveButtons() {
        const stepCount = $('.msf-step-item').length;
        $('.msf-remove-step-btn').prop('disabled', stepCount <= 1);
    }

    function collectFormData() {
        const formData = { steps: [] };

        $('.msf-step-item').each(function() {
            const step = {
                title: $(this).find('.msf-step-title-input').val(),
                icon: $(this).find('.msf-step-icon-input').val(),
                fields: []
            };

            $(this).find('.msf-field-item').each(function() {
                const field = {
                    type: $(this).find('.msf-field-type').val(),
                    label: $(this).find('.msf-field-label').val(),
                    name: $(this).find('.msf-field-name').val()
                };
                step.fields.push(field);
            });

            formData.steps.push(step);
        });

        return formData;
    }

    function loadFormData(formData) {
        $('#msf-steps-list').empty();
        stepIndex = 0;

        formData.steps.forEach(function(step) {
            const stepHtml = `
                <div class="msf-step-item" data-step-index="${stepIndex}">
                    <div class="msf-step-header">
                        <span class="msf-step-number">${stepIndex + 1}</span>
                        <input type="text" name="steps[${stepIndex}][title]" value="${step.title}" placeholder="Step Title" class="msf-step-title-input">
                        <div class="msf-step-actions">
                            <button type="button" class="msf-icon-picker-btn" data-step-index="${stepIndex}">
                                <i class="fas ${step.icon}"></i>
                            </button>
                            <input type="hidden" name="steps[${stepIndex}][icon]" value="${step.icon}" class="msf-step-icon-input">
                            <button type="button" class="msf-remove-step-btn">×</button>
                        </div>
                    </div>

                    <div class="msf-fields-builder">
                        <h4>Fields</h4>
                        <div class="msf-fields-list">
                            ${step.fields.map((field, fieldIndex) => `
                                <div class="msf-field-item">
                                    <select name="steps[${stepIndex}][fields][${fieldIndex}][type]" class="msf-field-type">
                                        <option value="text" ${field.type === 'text' ? 'selected' : ''}>Text</option>
                                        <option value="textarea" ${field.type === 'textarea' ? 'selected' : ''}>Textarea</option>
                                        <option value="select" ${field.type === 'select' ? 'selected' : ''}>Select</option>
                                        <option value="date" ${field.type === 'date' ? 'selected' : ''}>Date</option>
                                        <option value="file" ${field.type === 'file' ? 'selected' : ''}>File Upload</option>
                                    </select>
                                    <input type="text" name="steps[${stepIndex}][fields][${fieldIndex}][label]" value="${field.label}" placeholder="Field Label" class="msf-field-label">
                                    <input type="text" name="steps[${stepIndex}][fields][${fieldIndex}][name]" value="${field.name}" placeholder="Field Name" class="msf-field-name">
                                    <button type="button" class="msf-remove-field-btn">×</button>
                                </div>
                            `).join('')}
                        </div>
                        <button type="button" class="msf-add-field-btn">+ Add Field</button>
                    </div>
                </div>
            `;

            $('#msf-steps-list').append(stepHtml);
            stepIndex++;
        });

        updateRemoveButtons();
    }

    // Initialize
    updateStepNumbers();
    updateRemoveButtons();
});