<div class="wrap">
    <h1>Create Notification</h1>

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
                                        <td class="first"><label for="scan_status"><?php esc_html_e('* Scan Status', 'cgss'); ?></label></td>
                                        <td>
                                            <select name="scan_status">
                                                <option value="">Please select...</option>
                                                <option value="Done">Scan Completed</option>
                                                <option value="Running">Scan Started</option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="first"><label for="email_address"><?php esc_html_e('* Email Address', 'cgss'); ?></label></td>
                                        <td><input type="text" name="email_address" size="30" value=""></td>
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
