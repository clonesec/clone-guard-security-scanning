<?php
defined('ABSPATH') || exit;

class Clone_Guard_Widget extends WP_Widget {
    public $key = 'cgss';
    public $key_ = 'cgss_';

    // Sizes saved by plugin versions before 2.6, which showed an image instead of the seal script.
    const LEGACY_SIZES = [
        '140x54' => 'lg',
        '106x42' => 'md',
        '82x32' => 'sm',
    ];

    // The class constructor.
    public function __construct() {
        parent::__construct('clone_guard_widget', $name = 'CloneGuard Seal', array('description' => 'Shows the Clone Systems Trust Seal.'));
    }

    // Returns the seal settings of a widget instance. Instances saved before 2.6 have no icon
    // or position, and a size like 140x54 (empty meant 140x54): they get the default icon and the matching size.
    private function seal_atts($instance) {
        $size = isset($instance['size']) ? $instance['size'] : '';
        if($size === '' && !isset($instance['icon']) && (isset($instance['domain']) || isset($instance['image']))) {
            $size = '140x54';
        }
        if(isset(self::LEGACY_SIZES[$size])) {
            $size = self::LEGACY_SIZES[$size];
        }

        return Clone_Guard_Seal::sanitize([
            'icon' => isset($instance['icon']) ? $instance['icon'] : '',
            'size' => $size,
            'position' => isset($instance['position']) ? $instance['position'] : '',
        ]);
    }

    // Output the widget content on the front end.
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '', $instance, $this->id_base);

        echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup from the theme's sidebar.
        if($title !== '') {
            echo $args['before_title'] . esc_html($title) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup from the theme's sidebar.
        }
        echo Clone_Guard_Seal::render($this->seal_atts($instance)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from whitelisted values.
        echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup from the theme's sidebar.
    }

    // Output the option form field on the admin widgets screen.
    public function form( $instance ) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $atts = $this->seal_atts($instance);
        $fields = [
            'icon' => ['Seal', Clone_Guard_Seal::ICONS],
            'size' => ['Size', Clone_Guard_Seal::SIZES],
            'position' => ['Position', Clone_Guard_Seal::POSITIONS],
        ];
?>
<p>
    <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Title (optional)</label>
    <input class="widefat" type="text" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($title); ?>" />
</p>
<?php foreach($fields as $field => $field_data): ?>
<p>
    <label for="<?php echo esc_attr($this->get_field_id($field)); ?>"><?php echo esc_html($field_data[0]); ?></label>
    <select class="widefat" id="<?php echo esc_attr($this->get_field_id($field)); ?>" name="<?php echo esc_attr($this->get_field_name($field)); ?>">
        <?php foreach($field_data[1] as $value => $label): ?>
        <option value="<?php echo esc_attr($value); ?>" <?php selected($atts[$field], $value); ?>><?php echo esc_html($label); ?></option>
        <?php endforeach; ?>
    </select>
</p>
<?php endforeach; ?>
<p class="description">The seal only appears on domains with an active Clone Systems account and current scans. It links to the security report for this site's domain.</p>
<?php
    }

    // Save the options for the widget.
    public function update( $new_instance, $old_instance ) {
        $instance = Clone_Guard_Seal::sanitize($new_instance);
        $instance['title'] = isset($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }
}
