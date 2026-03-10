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

        // Handle account type card selection
        container.find('.msf-account-card').on('click', function() {
            const card = $(this);
            const radio = card.find('input[type="radio"]');
            const cardContent = card.find('.msf-card-content');

            // Remove selection from other cards
            container.find('.msf-account-card').removeClass('selected');
            container.find('.msf-account-card .msf-card-content').css('border-color', '#e5e8ef');

            // Select this card
            card.addClass('selected');
            cardContent.css('border-color', '#1c3f74');
            radio.prop('checked', true);
        });

        // Initialize
        updateUI();