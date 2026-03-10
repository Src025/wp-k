# Multi-Step Forms Manager

A WordPress plugin to create and manage multi-step forms with Personal and Business account sections.

## Features

- Create multi-step forms for Personal and Business accounts
- Admin interface to manage forms, add/edit fields, and customize steps
- Toggle between Personal and Business forms on the frontend
- Premium icons for each form step
- Customizable fonts (Moderna Serif included)
- Color customization options
- Responsive design
- Shortcode support for easy integration

## Installation

1. Upload the `multi-step-forms` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Multi-Step Forms in the admin menu to configure your forms

## Admin Management Features

The plugin provides a comprehensive admin interface for managing forms:

### Form Builder Interface
- **Visual Step Management**: Add, remove, and reorder form steps
- **Step Customization**: Edit step titles and assign Font Awesome icons
- **Field Management**: Add/remove form fields with different types (text, textarea, select, date, file)
- **Live Preview**: See changes in real-time preview pane

### Icon Management
- **Icon Picker**: Choose from 70+ Font Awesome icons for each step
- **Search Functionality**: Find icons quickly with search
- **Visual Selection**: Click to select and preview icons

### Form Configuration
- **Personal Account Form**: Manage all 7 steps with custom icons and fields
- **Business Account Form**: Manage all 7 steps with custom icons and fields
- **Field Types**: Support for text, textarea, select dropdowns, date pickers, and file uploads

### Import/Export
- **JSON Export**: Download form configuration as JSON
- **JSON Import**: Upload and restore form configurations
- **Backup/Restore**: Easy migration between sites

### Settings Management
- **Font Selection**: Choose from Moderna Serif or web-safe fonts
- **Font Weight**: Select from multiple font weights (400-800)
- **Color Customization**: Set primary and secondary colors
- **Real-time Updates**: Changes apply immediately to forms

## Form Structure

**Personal Account Form**:
- 🏛️ Account Type *(Premium Cards)*
- 👤 Personal Details
- 📍 Address
- ↔️ Transaction Profile
- 💰 Source of Funds
- 🆔 KYC Upload
- ✅ Review & Submit

**Business Account Form**:
- 💼 Business Type *(Premium Cards)*
- 🏢 Business Details
- 📍 Business Address
- 📈 Financial Information
- 👔 Authorized Signatory
- 📤 Document Upload
- ✅ Review & Submit

### Premium Account Type Cards

The Account Type and Business Type steps feature modern, premium card interfaces:

- **Interactive Cards**: Hover effects and smooth transitions
- **Rich Icons**: Large, contextual Font Awesome icons
- **Descriptive Text**: Clear explanations for each account type
- **Visual Selection**: Checkmark indicators and color changes
- **Responsive Design**: Adapts beautifully to mobile devices
- **Premium Badges**: Special compatibility indicators (SWIFT, ETF)

## Fonts

The plugin includes the Moderna Serif font family with multiple weights:
- Regular (400)
- Medium (500)
- Semi Bold (600)
- Bold (700)
- Extra Bold (800)
- Italic variants

## Development

The plugin structure:

- `multi-step-forms.php` - Main plugin file
- `includes/` - PHP classes
  - `class-msf-admin.php` - Admin functionality
  - `class-msf-frontend.php` - Frontend display
  - `class-msf-form-builder.php` - Form building logic
- `assets/` - CSS, JavaScript, and fonts
  - `css/msf-style.css` - Main styles
  - `css/msf-fonts.css` - Font definitions
  - `js/msf-script.js` - Frontend JavaScript
  - `fonts/` - Moderna Serif font files

## Changelog

### 1.0.0
- Initial release
- Basic multi-step form functionality
- Personal and Business form types
- Admin management interface
- Premium icons for steps
- Custom fonts and colors
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