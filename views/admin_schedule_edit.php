<div class="cgss-schedule-edit-page">

        <div class="cgss-head">

            <div class="cgss-head-inside">
                <img class="cgss-head-logo" src="<?php echo esc_url( plugins_url( '../img/clone-guard-icon.png', __FILE__ ) ); ?>" alt="Logo" />
                <span class="cgss-head-text">
                    <h1 class="cgss-head-title">CloneGuard Security Scanning</h1>
                    <span class="cgss-head-subtitle">By <a href="https://www.clone-systems.com/" target="_blank">Clone Systems, Inc.</a></span>
                </span>
            </div>

        </div>

    <div class="cgss_main">
        <h1>Edit Schedule</h1>

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
                            <fieldset>
                                <table class="form-table editcomment" role="presentation">
                                    <tbody>
                                        <tr>
                                            <td class="first"><label for="name"><?php esc_html_e('* Name', 'cgss'); ?></label></td>
                                            <td><input type="text" name="name" size="30" value="<?php echo esc_attr($schedule['name']); ?>"></td>
                                        </tr>

                                        <tr>
                                            <td class="first"><label><?php esc_html_e('Frequency', 'cgss'); ?></label></td>
                                            <td class="schedule-frequency">
                                                <label><input type="radio" name="frequency" size="30" value="one_time" <?php echo ($frequency == 'one_time') ? 'checked' : ''; ?>> One Time</label>
                                                <label><input type="radio" name="frequency" size="30" value="daily" <?php echo ($frequency == 'daily') ? 'checked' : ''; ?>> Daily</label>
                                                <label><input type="radio" name="frequency" size="30" value="weekly" <?php echo ($frequency == 'weekly') ? 'checked' : ''; ?>> Weekly</label>
                                                <label><input type="radio" name="frequency" size="30" value="monthly" <?php echo ($frequency == 'monthly') ? 'checked' : ''; ?>> Monthly</label>
                                            </td>
                                        </tr>

                                        <tr class="schedule-repeat-input">
                                            <td class="first"><label for="period"><?php esc_html_e('* Repeat every', 'cgss'); ?></label></td>
                                            <td><input type="text" name="period" size="30" value="<?php echo esc_attr($schedule['period']); ?>"></td>
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
