/**
 * Multi-Step Forms Manager Frontend Script
 */

jQuery(document).ready(function($) {
    $('.msf-form-container').each(function() {
        const container = $(this);
        let currentStep = 0;
        const steps = container.find('.msf-step');
        const totalSteps = steps.length;
        const progressBar = container.find('.msf-progress-bar');
        const nextBtn = container.find('.msf-btn-next');
        const backBtn = container.find('.msf-btn-back');
        const submitBtn = container.find('.msf-btn-submit');
        const stepIndicators = container.find('.msf-steps li');

        function updateUI() {
            // Update step visibility
            steps.removeClass('active').eq(currentStep).addClass('active');

            // Update progress bar
            const progress = ((currentStep + 1) / totalSteps) * 100;
            progressBar.css('width', progress + '%');

            // Update step indicators
            stepIndicators.removeClass('active').eq(currentStep).addClass('active');

            // Update buttons
            backBtn.prop('disabled', currentStep === 0);
            nextBtn.toggle(currentStep < totalSteps - 1);
            submitBtn.toggle(currentStep === totalSteps - 1);
        }

        // when an enhanced radio option is clicked, toggle active state
        container.on('click', '.msf-option', function() {
            const radio = $(this).find('input[type=radio]');
            if (radio.length) {
                radio.prop('checked', true).trigger('change');
            }
        });

        container.on('change', 'input[type=radio]', function() {
            const name = $(this).attr('name');
            // deactivate siblings
            container.find('input[name="' + name + '"]')
                .closest('.msf-option')
                .removeClass('active');
            // activate current
            $(this).closest('.msf-option').addClass('active');
        });

        nextBtn.on('click', function() {
            if (currentStep < totalSteps - 1) {
                currentStep++;
                updateUI();
            }
        });

        backBtn.on('click', function() {
            if (currentStep > 0) {
                currentStep--;
                updateUI();
            }
        });

        // Toggle between personal and business forms
        container.find('.msf-toggle-btn').on('click', function() {
            const type = $(this).data('type');
            // This would typically reload the form with the new type
            // For now, just update the active button
            container.find('.msf-toggle-btn').removeClass('active');
            $(this).addClass('active');
        });

        // Initialize
        updateUI();
    });
});