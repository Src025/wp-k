<?php
/**
 * Default form templates for personal and business accounts
 */

function get_default_personal_form() {
    return array(
        'steps' => array(
            // primary personal‐details step moved to the front; will be rendered with the new card UI
            array(
                'title' => 'Personal Details',
                'description' => 'Please provide your official identification details to proceed with the account verification process.',
                'fields' => array(
                    array('type' => 'text', 'name' => 'first_name', 'label' => 'First Name', 'placeholder' => 'e.g. Alexander'),
                    array('type' => 'text', 'name' => 'last_name', 'label' => 'Last Name', 'placeholder' => 'e.g. Hamilton'),
                    array('type' => 'email', 'name' => 'email', 'label' => 'Email Address', 'placeholder' => 'alex@financial.com'),
                    array('type' => 'tel', 'name' => 'phone', 'label' => 'Phone Number', 'placeholder' => '+41 (0) 00 000 00 00'),
                    array('type' => 'text', 'name' => 'address', 'label' => 'Physical Address', 'placeholder' => 'Street, Suite, City, Postal Code'),
                )
            ),
            array(
                'title' => 'Account Type',
                'description' => 'Please choose the type of account you wish to open.',
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
                'title' => 'Address & Contact Information',
                'description' => 'Please provide your current residential address and contact details.',
                'sections' => array(
                    array(
                        'title' => 'RESIDENTIAL ADDRESS',
                        'fields' => array(
                            array('type' => 'text', 'name' => 'address_line1', 'label' => 'Address Line 1'),
                            array('type' => 'text', 'name' => 'address_line2', 'label' => 'Address Line 2 (optional)'),
                            array('type' => 'text', 'name' => 'city', 'label' => 'City'),
                            array('type' => 'text', 'name' => 'state', 'label' => 'State / Province'),
                            array('type' => 'text', 'name' => 'postal_code', 'label' => 'Postal / ZIP Code'),
                            array('type' => 'select', 'name' => 'country', 'label' => 'Country', 'options' => array('Select country...', 'United Kingdom', 'United States', 'Canada', 'Australia')),
                            array('type' => 'select', 'name' => 'address_duration', 'label' => 'How long at this address?', 'options' => array('Select...', 'Less than 1 year', '1-3 years', '3-5 years', '5+ years')),
                        )
                    ),
                    array(
                        'title' => 'CONTACT DETAILS',
                        'fields' => array(
                            array('type' => 'email', 'name' => 'email', 'label' => 'Email Address'),
                            array('type' => 'email', 'name' => 'email_confirm', 'label' => 'Confirm Email Address'),
                            array('type' => 'text', 'name' => 'phone_mobile', 'label' => 'Phone Number (Mobile)'),
                            array('type' => 'text', 'name' => 'phone_alt', 'label' => 'Phone Number (Alternative)'),
                            array('type' => 'select', 'name' => 'contact_method', 'label' => 'Prefered Contact Method', 'options' => array('Email', 'Phone', 'Post')),
                        )
                    )
                )
            ),
            array(
                'title' => 'Transaction Profile',
                'description' => 'Please tell us about your expected banking activity.',
                'fields' => array(
                    array('type' => 'select', 'name' => 'monthly_volume', 'label' => 'Monthly Transaction Volume', 'options' => array('Select...', 'Less than £10,000', '£10,000 - £50,000', '£50,000 - £100,000', 'More than £100,000')),
                    array('type' => 'select', 'name' => 'expected_deposit', 'label' => 'Expected Initial Deposit', 'options' => array('Select...', 'Less than £10,000', '£10,000 - £50,000', '£50,000 - £100,000', 'More than £100,000')),
                    array('type' => 'select', 'name' => 'primary_currency', 'label' => 'Primary Currency', 'options' => array('GBP', 'USD', 'EUR', 'CAD', 'AUD')),
                )
            ),
            array(
                'title' => 'Source of Funds',
                'description' => 'Please provide information about the source of your funds.',
                'fields' => array(
                    array('type' => 'select', 'name' => 'source_of_funds', 'label' => 'Source of Funds', 'options' => array('Employment Income', 'Business Revenue', 'Investments', 'Inheritance', 'Real Estate Sale', 'Other')),
                    array('type' => 'text', 'name' => 'employer_company', 'label' => 'Employer / Company Name'),
                )
            ),
            array(
                'title' => 'KYC Document Uploads',
                'description' => 'Please upload the required identity and address verification documents.',
                'sections' => array(
                    array(
                        'title' => 'IDENTITY DOCUMENT',
                        'description' => 'Please upload a copy of your valid passport, national ID, or driver\'s license.',
                        'fields' => array(
                            array('type' => 'select', 'name' => 'id_type', 'label' => 'Document Type', 'options' => array('Select...', 'Passport', 'National ID', 'Driver\'s Licence')),
                            array('type' => 'file_upload', 'name' => 'id_document', 'label' => 'Passport / National ID / Driver\'s Licence', 'icon' => 'file'),
                        )
                    ),
                    array(
                        'title' => 'PROOF OF ADDRESS',
                        'description' => 'Please upload a utility bill or bank statement dated within 3 months.',
                        'fields' => array(
                            array('type' => 'file_upload', 'name' => 'proof_address', 'label' => 'Utility bill / Bank statement (dated within 3 months)', 'icon' => 'home'),
                        )
                    ),
                    array(
                        'title' => 'SOURCE OF FUNDS EVIDENCE',
                        'description' => 'Please upload supporting documents for your source of funds.',
                        'fields' => array(
                            array('type' => 'file_upload', 'name' => 'source_evidence', 'label' => 'Payslip / Bank statement / Sale agreement', 'icon' => 'briefcase'),
                        )
                    )
                )
            ),
            array(
                'title' => 'Review & Submit',
                'description' => 'Please review your information before submitting.',
                'fields' => array()
            ),
        )
    );
}

function get_default_business_form() {
    return array(
        'steps' => array(
            array(
                'title' => 'Business Type',
                'description' => 'Please select the type of business entity.',
                'fields' => array(
                    array('type' => 'radio', 'name' => 'business_type', 'label' => 'Select Business Type', 'options' => array('Sole Proprietorship', 'Partnership', 'Corporation', 'LLC', 'Trust', 'Other'))
                )
            ),
            array(
                'title' => 'Business Details',
                'description' => 'Please provide information about your business.',
                'fields' => array(
                    array('type' => 'text', 'name' => 'business_name', 'label' => 'Business Name'),
                    array('type' => 'text', 'name' => 'registration_number', 'label' => 'Registration Number'),
                    array('type' => 'date', 'name' => 'incorporation_date', 'label' => 'Date of Incorporation'),
                    array('type' => 'select', 'name' => 'industry', 'label' => 'Industry', 'options' => array('Select...', 'Finance', 'Technology', 'Retail', 'Manufacturing', 'Services', 'Other')),
                    array('type' => 'textarea', 'name' => 'business_description', 'label' => 'Business Description'),
                )
            ),
        )
    );
}
