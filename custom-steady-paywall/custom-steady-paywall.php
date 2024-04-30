<?php
/*
Plugin Name: Custom Steady Paywall
Description: Insère un paywall dans les articles après un nombre configuré de paragraphes et de jours.
Version: 1.0
Author: Olivier Guillard
*/

// Hook pour ajouter les paramètres du plugin dans l'admin
add_action('admin_init', 'custom_steady_paywall_settings');

function custom_steady_paywall_settings() {
    // Ajouter une section de réglages
    add_settings_section('steady_paywall_settings_section', 'Réglages du Paywall Steady', null, 'reading');

    // Ajouter les champs de réglages
    add_settings_field('paywall_paragraph', 'Paragraphe après lequel insérer le paywall', 'paywall_paragraph_callback', 'reading', 'steady_paywall_settings_section');
    register_setting('reading', 'paywall_paragraph');

    add_settings_field('paywall_days', 'Nombre de jours après lesquels insérer le paywall', 'paywall_days_callback', 'reading', 'steady_paywall_settings_section');
    register_setting('reading', 'paywall_days');
}

function paywall_paragraph_callback() {
    $value = get_option('paywall_paragraph', '3'); // Valeur par défaut : après le 3e paragraphe
    echo '<input type="number" id="paywall_paragraph" name="paywall_paragraph" value="' . esc_attr($value) . '" />';
}

function paywall_days_callback() {
    $value = get_option('paywall_days', '14'); // Valeur par défaut : 14 jours
    echo '<input type="number" id="paywall_days" name="paywall_days" value="' . esc_attr($value) . '" />';
}

// Hook pour modifier le contenu des articles selon les réglages
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
        $paragraphs[$paywall_paragraph - 1] .= '___STEADY_PAYWALL___';
        $content = implode('</p>', $paragraphs);
    }
    
    return $content;
}
?>
