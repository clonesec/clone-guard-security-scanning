<?php
defined('ABSPATH') || exit;

class Clone_Guard_Widget extends WP_Widget {
    public $key = 'cgss';
    public $key_ = 'cgss_';

    // The class constructor.
    public function __construct() {
        parent::__construct('clone_guard_widget', $name = 'CloneGuard Seal', array('description' => ''));

        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
    }

    // Load the asset files for specific admin pages.
    public function adminEnqueueScripts($hook) {
        global $cloneGuardSecurityScanning;
        if($hook == 'widgets.php') {
            wp_enqueue_style($this->key_ . 'admin_widgets', plugins_url('../css/admin_widgets.css', __FILE__), [], $cloneGuardSecurityScanning->version);
        }
    }

    // Output the widget content on the front end.
    public function widget($args, $instance) {
        $urlparts = parse_url(home_url());
        $domain = !empty($instance['domain']) ? $instance['domain'] : $urlparts['host']; 
        $image = !empty($instance['image']) ? $instance['image'] : 'https://seals.clone-systems.com/images/security-seal/certified1_lg.png'; 
        $size = !empty($instance['size']) ? $instance['size'] : '140x54'; 

        $sizes = explode('x', $size);
        if(count($sizes) == 2) {
            $width = $sizes[0];
            $height = $sizes[1];
        } else {
            $width = '140';
            $height = '54';
        }

        $htm = '';

        $htm .= '<div>';
        $htm .= '<a href="https://seals.clone-systems.com/report?dn=' . urlencode($domain) . '" target="_blank">';
        $htm .= '<img width="' . esc_attr($width) . '" height="' . esc_attr($height) . '" src ="' . esc_url($image) . '">';
        $htm .= '</a>';
        $htm .= '</div>';
        
        echo $htm;
    }

    // Output the option form field on the admin widgets screen.
    public function form( $instance ) {
        $urlparts = parse_url(home_url());
        $domain = !empty($instance['domain']) ? $instance['domain'] : $urlparts['host']; 
        $image = !empty($instance['image']) ? $instance['image'] : 'https://seals.clone-systems.com/images/security-seal/certified1_lg.png'; 
        $size = !empty($instance['size']) ? $instance['size'] : '140x54'; 
?>
<p>
    <label for="<?php echo $this->get_field_id('domain'); ?>">Website Domain</label>
    <input class="widefat" id="<?php echo $this->get_field_id('domain'); ?>" name="<?php echo $this->get_field_name('domain'); ?>" value="<?php echo esc_attr($domain); ?>" />
</p>
<p>
    <label>Select Your Security Seal Image</label>
    <div class="cgss_images">
    <?php $value = 'https://seals.clone-systems.com/images/security-seal/certified1_lg.png'; ?>
    <label><input type="radio" name="<?php echo $this->get_field_name('image'); ?>" value="<?php echo esc_url($value); ?>" <?php echo ($value == $image) ? 'checked' : ''; ?> /> <img src="<?php echo esc_url($value); ?>" alt="Seal Image" /></label>
    <?php $value = 'https://seals.clone-systems.com/images/security-seal/certified2_lg.png'; ?>
    <label><input type="radio" name="<?php echo $this->get_field_name('image'); ?>" value="<?php echo esc_url($value); ?>" <?php echo ($value == $image) ? 'checked' : ''; ?> /> <img src="<?php echo esc_url($value); ?>" alt="Seal Image" /></label>
    <?php $value = 'https://seals.clone-systems.com/images/security-seal/certified3_lg.png'; ?>
    <label><input type="radio" name="<?php echo $this->get_field_name('image'); ?>" value="<?php echo esc_url($value); ?>" <?php echo ($value == $image) ? 'checked' : ''; ?> /> <img src="<?php echo esc_url($value); ?>" alt="Seal Image" /></label>
    <?php $value = 'https://seals.clone-systems.com/images/security-seal/certified4_lg.png'; ?>
    <label><input type="radio" name="<?php echo $this->get_field_name('image'); ?>" value="<?php echo esc_url($value); ?>" <?php echo ($value == $image) ? 'checked' : ''; ?> /> <img src="<?php echo esc_url($value); ?>" alt="Seal Image" /></label>
    <?php $value = 'https://seals.clone-systems.com/images/security-seal/certified5_lg.png'; ?>
    <label><input type="radio" name="<?php echo $this->get_field_name('image'); ?>" value="<?php echo esc_url($value); ?>" <?php echo ($value == $image) ? 'checked' : ''; ?> /> <img src="<?php echo esc_url($value); ?>" alt="Seal Image" /></label>
    <?php $value = 'https://seals.clone-systems.com/images/security-seal/certified6_lg.png'; ?>
    <label><input type="radio" name="<?php echo $this->get_field_name('image'); ?>" value="<?php echo esc_url($value); ?>" <?php echo ($value == $image) ? 'checked' : ''; ?> /> <img src="<?php echo esc_url($value); ?>" alt="Seal Image" /></label>
    <?php $value = 'https://seals.clone-systems.com/images/security-seal/certified7_lg.png'; ?>
    <label><input type="radio" name="<?php echo $this->get_field_name('image'); ?>" value="<?php echo esc_url($value); ?>" <?php echo ($value == $image) ? 'checked' : ''; ?> /> <img src="<?php echo esc_url($value); ?>" alt="Seal Image" /></label>
    <?php $value = 'https://seals.clone-systems.com/images/security-seal/certified8_lg.png'; ?>
    <label><input type="radio" name="<?php echo $this->get_field_name('image'); ?>" value="<?php echo esc_url($value); ?>" <?php echo ($value == $image) ? 'checked' : ''; ?> /> <img src="<?php echo esc_url($value); ?>" alt="Seal Image" /></label>
    </div>
</p>
<p>
    <label>Select Image Size</label>
    <div class="cgss_sizes">
    <label><input type="radio" name="<?php echo $this->get_field_name('size'); ?>" value="140x54" <?php echo ('140x54' == $size) ? 'checked' : ''; ?> /> 140x54</label>
    <label><input type="radio" name="<?php echo $this->get_field_name('size'); ?>" value="106x42" <?php echo ('106x42' == $size) ? 'checked' : ''; ?> /> 106x42</label>
    <label><input type="radio" name="<?php echo $this->get_field_name('size'); ?>" value="82x32" <?php echo ('82x32' == $size) ? 'checked' : ''; ?> /> 82x32</label>
    </div>
</p>
<?php
    }

    // Save the options for the widget.
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['domain'] = strip_tags($new_instance['domain']);
        $instance['image'] = strip_tags($new_instance['image']);
        $instance['size'] = strip_tags($new_instance['size']);
        return $instance;
    }
}

