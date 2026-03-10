# Multi-Step Forms Manager

A WordPress plugin to create and manage multi-step forms with Personal and Business account sections.

## Features

- Create multi-step forms for Personal and Business accounts
- Admin interface to manage forms, add/edit fields, and customize steps
- Toggle between Personal and Business forms on the frontend
- Responsive design
- Shortcode support for easy integration

## Installation

1. Upload the `multi-step-forms` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Multi-Step Forms in the admin menu to configure your forms

## Usage

### Admin Configuration

1. Navigate to **Multi-Step Forms > Personal Forms** or **Business Forms**
2. Configure the form steps and fields
3. Save your changes

### Frontend Display

Use the shortcode `[multi_step_form type="personal"]` or `[multi_step_form type="business"]` on any page or post to display the form.

### Customization

- Edit the form structure in the admin panel
- Modify CSS in `assets/css/msf-style.css` for styling changes
- Extend JavaScript functionality in `assets/js/msf-script.js`

## Form Fields Supported

- Text input
- Date picker
- Textarea
- Select dropdown
- Radio buttons

## Development

The plugin structure:

- `multi-step-forms.php` - Main plugin file
- `includes/` - PHP classes
  - `class-msf-admin.php` - Admin functionality
  - `class-msf-frontend.php` - Frontend display
  - `class-msf-form-builder.php` - Form building logic
- `assets/` - CSS and JavaScript files

## Changelog

### 1.0.0
- Initial release
- Basic multi-step form functionality
- Personal and Business form types
- Admin management interface