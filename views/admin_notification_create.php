<div class="cgss-notification-create-page">

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
        <h1>Create Notification</h1>

        <div id="ajax_message"></div>

        <div id="poststuff">
            <form id="post-body" class="metabox-holder columns-2 ajax_form" action="admin-ajax.php" method="POST" style="margin:0;">
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
