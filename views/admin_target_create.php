<div class="cgss-target-create-page">

    <div class="cgss-head">
        <div class="cgss-head-inside">
            <img class="cgss-head-logo" src="<?php echo esc_url( plugins_url( '../img/clone-guard-icon.png', __FILE__ ) ); ?>" alt="Logo" />
            <span class="cgss-head-text">
                <h1 class="cgss-head-title">CloneGuard Security Scanning</h1>
                <p class="cgss-head-subtitle">By Clone Systems, Inc.</p>
            </span>
        </div>
    </div>

    <div class="cgss_main">
        
        <h1>Create Target</h1>

        <div id="ajax_message"></div>

        <div id="poststuff">
            <form id="post-body" class="metabox-holder columns-2 ajax_form" action="admin-ajax.php" method="POST" style="margin:0">
                <?php wp_nonce_field($action); ?>
                <input name="action" type="hidden" value="<?php echo esc_attr($action); ?>" />
                <input name="key" type="hidden" value="<?php echo esc_attr($key); ?>" />

                <div id="post-body-content">
                    <div class="stuffbox">
                        <div class="inside">
                            <fieldset>
                                <table class="form-table editcomment" role="presentation">
                                    <tbody>
                                        <tr>
                                            <td class="first"><label for="name"><?php esc_html_e('* Name', 'cgss'); ?></label></td>
                                            <td><input type="text" name="name" size="30" value=""></td>
                                        </tr>

                                        <tr>
                                            <td class="first"><label><?php _e('* Hostnames or IP addresses', 'cgss'); ?></label></td>
                                            <td><textarea name="hosts" size="30"></textarea></td>
                                        </tr>

        <?php /*
                                        <tr>
                                            <td class="first"><label><?php _e('Exclude hosts', 'cgss'); ?></label></td>
                                            <td><textarea name="exclude_hosts" size="30"></textarea></td>
                                        </tr>
        */ ?>

                                        <tr>
                                            <td class="first"><label><?php _e('Comment', 'cgss'); ?></label></td>
                                            <td><textarea name="comment" size="30"></textarea></td>
                                        </tr>

                                        <tr>
                                            <td colspan="2"><label><input type="checkbox" name="attest" value="yes" /> <?php _e('By selecting this checkbox you attest that this target includes all components which should be in scope for PCI DSS and any component considered out-of-scope for this target is properly segmented from your cardholder data environment. You also acknowledge that the proper scoping of this external target is your responsibility.', 'cgss'); ?></label></td>
                                        </tr>
                                        <tr>
                                            <td class="first"></td>
                                            <td class="table-buttons">
                                                <div>
                                                    <span class="spinner"></span>
                                                    <input type="submit" name="save" id="save" class="table-buttons-save button button-primary button-large" value="Save">
                                                </div>
                                                <a href="<?php echo esc_url($url_back); ?>" class="table-buttons-cancel button button-large button_cancel">Cancel</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </fieldset>
                        </div>
                    </div>

                </div><!-- /post-body-content -->
            </form><!-- /post-body -->
        </div>  

    </div>

</div>
