# Lawyer Importer

**Lawyer Importer** is a custom WordPress plugin that allows you to import lawyer data from a CSV file into your WordPress site easily and efficiently.

## 🔧 Features

- Bulk import lawyer profiles from CSV files
- Create custom post types or user entries for lawyers
- Automatically map CSV fields to WordPress fields
- Simple and intuitive admin interface
- Error handling and success reporting

## 📥 Installation

### Method 1: Upload via WordPress Admin

1. Download the plugin as a `.zip` file
2. Go to your WordPress Dashboard
3. Navigate to `Plugins > Add New > Upload Plugin`
4. Upload the `.zip` file and click "Install Now"
5. Activate the plugin

### Method 2: Manual Upload via FTP

1. Clone this repository or download the ZIP
2. Extract the ZIP file
3. Upload the folder to your `/wp-content/plugins/` directory
4. Go to your WordPress admin panel and activate the plugin

```bash
git clone https://github.com/WebAhsan/Lawyer-Importer.git
🚀 Usage
Navigate to Lawyer Importer from the WordPress admin menu

Upload a properly formatted CSV file

Click "Import"

Done! Your lawyer data is now available as posts/users/custom entries

Make sure your CSV has the following columns: name, email, phone, specialization, etc.

🧰 Requirements
WordPress 5.0 or higher

PHP 7.4 or higher

Admin access to install and configure plugins

📸 Screenshots
(Add screenshots if available, e.g. import screen, settings page)

❓ FAQ
Q: What happens if my CSV has invalid rows?
A: Invalid rows will be skipped and an error message will be shown for each.

Q: Can I undo an import?
A: Not directly. Please back up your data before importing.

📝 Changelog
v1.0.0
Initial release with CSV import functionality

📄 License
This plugin is licensed under the GPLv2 or later.
