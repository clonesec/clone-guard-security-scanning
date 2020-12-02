<div class="wrap">
    <h1>Create Schedule</h1>

    <div id="ajax_message"></div>

    <div id="poststuff">
        <form id="post-body" class="metabox-holder columns-2 ajax_form" action="admin-ajax.php" method="POST">
            <?php wp_nonce_field($action); ?>
            <input name="action" type="hidden" value="<?php echo esc_attr($action); ?>" />
            <input name="key" type="hidden" value="<?php echo esc_attr($key); ?>" />
            <input name="subkey" type="hidden" value="<?php echo esc_attr($subkey); ?>" />

            <div id="post-body-content">
                <div class="stuffbox">
                    <div class="inside">
                        <h2>Fields</h2>
                        <fieldset>
                            <table class="form-table editcomment" role="presentation">
                                <tbody>
                                    <tr>
                                        <td class="first"><label for="name"><?php esc_html_e('* Name', 'cgss'); ?></label></td>
                                        <td><input type="text" name="name" size="30" value="<?php echo esc_attr($schedule['name']); ?>"></td>
                                    </tr>

                                    <tr>
                                        <td class="first"><label><?php esc_html_e('Frequency', 'cgss'); ?></label></td>
                                        <td>
                                        <label><input type="radio" name="frequency" size="30" value="one_time" <?php echo ($frequency == 'one_time') ? 'checked' : ''; ?>> One Time</label>
                                            <label><input type="radio" name="frequency" size="30" value="daily" <?php echo ($frequency == 'daily') ? 'checked' : ''; ?>> Daily</label>
                                            <label><input type="radio" name="frequency" size="30" value="weekly" <?php echo ($frequency == 'weekly') ? 'checked' : ''; ?>> Weekly</label>
                                            <label><input type="radio" name="frequency" size="30" value="monthly" <?php echo ($frequency == 'monthly') ? 'checked' : ''; ?>> Monthly</label>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="first"><label for="first_time"><?php esc_html_e('* First Time', 'cgss'); ?></label></td>
                                        <td><input class="datetimepicker" type="text" name="first_time" size="30" value="<?php echo esc_attr(date('Y-m-d H:i:s', strtotime($schedule['first_time']))); ?>"></td>
                                    </tr>

                                    <tr>
                                        <td class="first"><label for="timezone"><?php esc_html_e('* Timezone', 'cgss'); ?></label></td>
                                        <td>
                                            <select name="timezone" class="select2">
                                                <option value="">Please select...</option>
                                                <?php foreach($this->getTimezones() as $timezone): ?>
                                                <option value="<?php echo esc_attr($timezone); ?>" <?php echo ($schedule['timezone'] == $timezone) ? 'selected' : ''; ?>><?php echo esc_html($timezone); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            </td>
                                    </tr>

                                    <tr>
                                        <td class="first"><label><?php _e('Comment', 'cgss'); ?></label></td>
                                        <td><textarea name="comment" size="30"><?php echo esc_html($schedule['comment']); ?></textarea></td>
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
