<?php
defined('ABSPATH') || exit;

// Renders the Clone Systems Trust Seal with the same script the seal generator on
// clone-systems.com/trust-seal gives to website owners. Used by the widget and the shortcode.
class Clone_Guard_Seal {
    const SCRIPT_HANDLE = 'cgss-get-seal';
    const SCRIPT_URL = 'https://www.clone-systems.com/generate-seal/get-seal.js';

    const ICONS = [
        'cs-secure-dark' => 'Secure, dark',
        'cs-secure-light' => 'Secure, light',
        'cs-certified-dark' => 'Certified, dark',
        'cs-certified-light' => 'Certified, light',
        'cs-compliant-dark' => 'Compliant, dark',
        'cs-compliant-light' => 'Compliant, light',
        'cs-pci-asv-dark' => 'PCI ASV, dark',
        'cs-pci-asv-light' => 'PCI ASV, light',
        'cs-scanned-dark' => 'Scanned, dark',
        'cs-scanned-light' => 'Scanned, light',
    ];

    // Pixel sizes are the ones get-seal.js renders.
    const SIZES = [
        'lg' => 'Large (175 x 63)',
        'md' => 'Medium (150 x 54)',
        'sm' => 'Small (125 x 45)',
    ];

    const POSITIONS = [
        'relative' => 'Inline, where it is placed',
        'left' => 'Fixed to the bottom left corner',
        'right' => 'Fixed to the bottom right corner',
    ];

    const DEFAULTS = [
        'icon' => 'cs-secure-dark',
        'size' => 'md',
        'position' => 'relative',
    ];

    public static function init() {
        // No version (no ?ver=) and no defer/async: the inline call needs the script loaded
        // synchronously before it, and uses document.currentScript to find where to insert the seal.
        wp_register_script(self::SCRIPT_HANDLE, self::SCRIPT_URL, [], null, false); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- served and versioned by clone-systems.com.

        add_shortcode('cloneguard_seal', [__CLASS__, 'shortcode']);
    }

    // [cloneguard_seal icon="cs-secure-dark" size="md" position="relative"]
    public static function shortcode($atts) {
        return self::render(shortcode_atts(self::DEFAULTS, $atts, 'cloneguard_seal'));
    }

    // Returns each value if it is allowed, or the default.
    public static function sanitize(array $atts) {
        $icon = isset($atts['icon']) ? (string) $atts['icon'] : '';
        $size = isset($atts['size']) ? (string) $atts['size'] : '';
        $position = isset($atts['position']) ? (string) $atts['position'] : '';

        return [
            'icon' => isset(self::ICONS[$icon]) ? $icon : self::DEFAULTS['icon'],
            'size' => isset(self::SIZES[$size]) ? $size : self::DEFAULTS['size'],
            'position' => isset(self::POSITIONS[$position]) ? $position : self::DEFAULTS['position'],
        ];
    }

    // Returns the seal HTML. get-seal.js must run before every seal's inline call, and only once
    // (loading it twice throws an error).
    public static function render(array $atts) {
        $atts = self::sanitize($atts);

        if(!did_action('wp_print_scripts')) {
            // The <head> scripts are not printed yet. Block themes render the whole template before
            // the <head>, and not in page order (shortcodes and blocks run in separate passes), so load it there.
            wp_enqueue_script(self::SCRIPT_HANDLE);
            $html = '';
        } else {
            // Classic themes print the page in order: include the tag with the first seal.
            // WordPress marks the handle as done, so later seals do not repeat it.
            ob_start();
            wp_scripts()->do_items([self::SCRIPT_HANDLE]);
            $html = ob_get_clean();
        }

        $call = sprintf(
            "generateSecuritySeal('%s','%s','%s','svg');",
            esc_js($atts['icon']),
            esc_js($atts['size']),
            esc_js($atts['position'])
        );

        return $html . '<span class="cgss-seal">' . wp_get_inline_script_tag($call) . '</span>';
    }
}

add_action('init', ['Clone_Guard_Seal', 'init']);
