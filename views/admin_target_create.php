<div class="wrap">
    <h1>Create Target</h1>

    <div id="ajax_message"></div>

    <div id="poststuff">
        <form id="post-body" class="metabox-holder columns-2 ajax_form" action="admin-ajax.php" method="POST">
            <?php wp_nonce_field($action); ?>
            <input name="action" type="hidden" value="<?php echo esc_attr($action); ?>" />
            <input name="key" type="hidden" value="<?php echo esc_attr($key); ?>" />

            <div id="post-body-content">
                <div class="stuffbox">
                    <div class="inside">
                        <h2>Fields</h2>
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
                                </tbody>
                            </table>
                        </fieldset>
                    </div>
                </div>

            </div><!-- /post-body-content -->

            <div id="postbox-container-1" class="postbox-container">
                <div id="submitdiv" class="stuffbox">
                    <h2>Save</h2>
                    <div class="inside">
                        <div class="submitbox" id="submitcomment">
                            <div id="major-publishing-actions">
                                <a href="<?php echo esc_url($url_back); ?>" class="button button-large button_cancel">Cancel</a>
                                <div id="publishing-action">
                                    <span class="spinner"></span>
                                    <input type="submit" name="save" id="save" class="button button-primary button-large" value="Save">
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div><!-- /submitdiv -->
            </div>
        </form><!-- /post-body -->
    </div>
</div>
