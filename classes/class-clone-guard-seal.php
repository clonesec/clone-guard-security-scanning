<?php
defined('ABSPATH') || exit;

// Renders the Clone Systems Trust Seal with the same script the seal generator on
// clone-systems.com/trust-seal gives to website owners. Used by the widget, the shortcode and the block.
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

    // Width and height for each size, used by the block editor preview.
    const PIXELS = [
        'lg' => [175, 63],
        'md' => [150, 54],
        'sm' => [125, 45],
    ];

    // Static seal image shown in the block editor ({icon} is replaced by the icon key).
    const PREVIEW_URL = 'https://www.clone-systems.com/generate-seal/images/svg/modified/{icon}_modified.svg';

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

        self::register_block();
    }

    // Registers the cloneguard/seal block (built files in blocks/seal/build). The classic widget stays for legacy sites.
    private static function register_block() {
        $block = register_block_type(dirname(__DIR__) . '/blocks/seal/build', [
            'render_callback' => [__CLASS__, 'render_block'],
        ]);
        if(!$block) {
            return;
        }

        // The editor script reads the labels from here, so the lists above stay the only copy.
        $handle = generate_block_asset_handle('cloneguard/seal', 'editorScript');
        wp_add_inline_script($handle, 'window.cgssSeal = ' . wp_json_encode([
            'icons' => self::ICONS,
            'sizes' => self::SIZES,
            'positions' => self::POSITIONS,
            'pixels' => self::PIXELS,
            'imageUrl' => self::PREVIEW_URL,
        ]) . ';', 'before');
        wp_set_script_translations($handle, 'clone-guard-security-scanning');
    }

    // Front-end output of the block: the seal inside the block wrapper (alignment classes).
    public static function render_block($attributes) {
        return '<div ' . get_block_wrapper_attributes() . '>' . self::render((array) $attributes) . '</div>';
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
