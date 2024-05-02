<?php
/*
Plugin Name: Custom Steady Paywall
Description: Inserts a Steady Paywall in articles after a set number of paragraphs and days.
Version: 1.0
Author: Olivier Guillard
*/

// Hook to add plugin settings in the admin panel
add_action('admin_init', 'custom_steady_paywall_settings');

function custom_steady_paywall_settings() {
    // Add a settings section
    add_settings_section('steady_paywall_settings_section', 'Paywall Steady settings', null, 'reading');

    // Add settings fields
    add_settings_field('paywall_paragraph', 'Paragraph after which to insert the paywall', 'paywall_paragraph_callback', 'reading', 'steady_paywall_settings_section');
    register_setting('reading', 'paywall_paragraph');

    add_settings_field('paywall_days', 'Number of days after which to insert the paywall', 'paywall_days_callback', 'reading', 'steady_paywall_settings_section');
    register_setting('reading', 'paywall_days');
}

function paywall_paragraph_callback() {
    $value = get_option('paywall_paragraph', '3'); // Default value: after the 3rd paragraph
    echo '<input type="number" id="paywall_paragraph" name="paywall_paragraph" value="' . esc_attr($value) . '" />';
}

function paywall_days_callback() {
    $value = get_option('paywall_days', '14'); // Default value: 14 days
    echo '<input type="number" id="paywall_days" name="paywall_days" value="' . esc_attr($value) . '" />';
}

// Hook to change the content of items according to settings
add_filter('the_content', 'insert_paywall_tag_based_on_settings', 20);

function insert_paywall_tag_based_on_settings($content) {
    if (!is_single()) {
      return $content;
    }

    $paywall_paragraph = intval(get_option('paywall_paragraph', 3));
    $paywall_days = intval(get_option('paywall_days', 14));

    $post_date = get_the_date('U');
    $cut_off_date = strtotime("-{$paywall_days} days");

    if ($post_date > $cut_off_date) {
        return $content;
    }

    $paragraphs = explode('</p>', $content);
    if (count($paragraphs) > $paywall_paragraph) {
        // Insérer la balise de paywall mise à jour
        $paragraphs[$paywall_paragraph - 1] .= '<div id="steady_paywall" style="display: none;"></div>';
        $content = implode('</p>', $paragraphs);
    }
    
    return $content;
}
?>
