<?php
/*
Plugin Name: Lawyer Excel Importer
Description: Import lawyers from an Excel (.xlsx) file and set featured image.
Version: 1.0
Author: Softvrafty
*/

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Load media handling functions
if (!function_exists('media_sideload_image')) {
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
}

// Register 'lawyer' custom post type
add_action('init', function () {
    register_post_type('lawyer', [
        'labels' => [
            'name' => 'Lawyers',
            'singular_name' => 'Lawyer'
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'thumbnail'],
    ]);
});

// Add admin menu
add_action('admin_menu', function () {
    add_menu_page('Import Lawyers', 'Import Lawyers', 'manage_options', 'import-lawyers', 'lawyer_importer_page');
});

// Admin page content
function lawyer_importer_page()
{
    if (!current_user_can('manage_options')) return;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['lawyer_excel'])) {
        import_lawyers_from_excel($_FILES['lawyer_excel']);
    }
?>
    <div class="wrap">
        <h1>Import Lawyers from Excel</h1>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="lawyer_excel" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
            <br><br>
            <button type="submit" class="button button-primary">Import</button>
        </form>
    </div>
<?php
}

// Import lawyers from Excel file
function import_lawyers_from_excel($file)
{
    // Load PhpSpreadsheet
    require_once __DIR__ . '/vendor/autoload.php';
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file['tmp_name']);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();

    // Remove header
    array_shift($rows);

    foreach ($rows as $row) {
        // Adjust index positions if your Excel columns differ
        list($name, $firm, $city, $state, $practice, $website, $number, $address, $image_url) = array_pad($row, 9, '');

        $post_id = wp_insert_post([
            'post_title'   => sanitize_text_field($name),
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'lawyer',
        ]);

        if ($post_id) {
            update_post_meta($post_id, '_ld_firm_name', sanitize_text_field($firm));
            update_post_meta($post_id, '_ld_city', sanitize_text_field($city));
            update_post_meta($post_id, '_ld_state', sanitize_text_field($state));
            update_post_meta($post_id, '_ld_specializations', sanitize_text_field($practice));
            update_post_meta($post_id, '_ld_website', esc_url_raw($website));
            update_post_meta($post_id, '_ld_number', sanitize_text_field($number));
            update_post_meta($post_id, '_ld_address', sanitize_text_field($address));

            // Check if an image URL is provided and valid
            if ($image_url) {
                // Make sure the URL is valid
                $image_url = esc_url_raw($image_url);
                if (filter_var($image_url, FILTER_VALIDATE_URL) && preg_match('/\.(jpg|jpeg|png|gif)$/i', $image_url)) {
                    // Attempt to download and set the featured image
                    $image_id = media_sideload_image($image_url, $post_id, null, 'id');

                    // Error handling for failed image upload
                    if (is_wp_error($image_id)) {
                        // Log the error and display a notice
                        $error_message = $image_id->get_error_message();
                        echo '<div class="notice notice-error"><p>Failed to add image for ' . esc_html($name) . ': ' . $error_message . '</p></div>';
                    } else {
                        set_post_thumbnail($post_id, $image_id);
                    }
                } else {
                    // Log a message if the image URL is invalid
                    echo '<div class="notice notice-warning"><p>Invalid image URL for ' . esc_html($name) . ': ' . esc_html($image_url) . '</p></div>';
                }
            }
        }
    }

    echo '<div class="notice notice-success"><p>Lawyers imported successfully from Excel.</p></div>';
}
