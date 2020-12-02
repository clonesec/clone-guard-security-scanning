<div class="wrap">
    <h1>Create Scan</h1>

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
                                        <td class="first"><label for="name"><?php esc_html_e('Name', 'cgss'); ?></label></td>
                                        <td><input type="text" name="name" size="30" value="<?php echo esc_attr($scan['name']); ?>"></td>
                                    </tr>

                                    <tr>
                                        <td class="first"><label><?php _e('Schedule', 'cgss'); ?></label></td>
                                        <td>
                                            <select name="schedule">
                                                <option value=""><?php echo esc_html_e('Please select...', 'cgss'); ?></option>
                                                <?php foreach($schedules as $schedule): ?>
                                                    <?php if($scan['schedule']['id'] == $schedule['id']): ?>
                                                        <option value="<?php echo esc_attr($schedule['id']); ?>" selected><?php echo esc_html($schedule['name']); ?></option>
                                                    <?php else: ?>
                                                        <option value="<?php echo esc_attr($schedule['id']); ?>"><?php echo esc_html($schedule['name']); ?></option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                            <a class="return" href="<?php echo $this->adminLink('scans', 'schedule-create', $scan['id']); ?>" data-action="<?php echo esc_attr($this->key_ . 'scan_temp_save'); ?>" data-nonce="<?php echo esc_attr($nonce_scan_temp_save); ?>"><?php _e('Create New Schedule', 'cgss'); ?></a>
                                            <span class="spinner inline"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="first"><label><?php _e('Target', 'cgss'); ?></label></td>
                                        <td>
                                            <select name="target">
                                                <option value=""><?php echo esc_html_e('Please select...', 'cgss'); ?></option>
                                                <?php foreach($targets as $target): ?>
                                                    <?php if($scan['target']['id'] == $target['id']): ?>
                                                        <option value="<?php echo esc_attr($target['id']); ?>" selected><?php echo esc_html($target['name']); ?></option>
                                                    <?php else: ?>
                                                        <option value="<?php echo esc_attr($target['id']); ?>"><?php echo esc_html($target['name']); ?></option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                            <a class="return" href="<?php echo $this->adminLink('scans', 'target-create', $scan['id']); ?>" data-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-action="<?php echo esc_attr($this->key_ . 'scan_temp_save'); ?>" data-nonce="<?php echo esc_attr($nonce_scan_temp_save); ?>"><?php _e('Create New Target', 'cgss'); ?></a>
                                            <span class="spinner inline"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="first"><label><?php _e('Notifications', 'cgss'); ?></label></td>
                                        <td>
                                            <?php foreach($notifications as $notification): ?>
                                                <?php if(in_array($notification['id'], $scan['notification_list'])): ?>
                                                <label><input type="checkbox" name="notifications[]" value="<?php echo esc_attr($notification['id']); ?>" checked> <?php echo esc_html($notification['name']); ?></label><br>
                                                <br>
                                                <?php else: ?>
                                                <label><input type="checkbox" name="notifications[]" value="<?php echo esc_attr($notification['id']); ?>"> <?php echo esc_html($notification['name']); ?></label><br>
                                                <br>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <a class="return" href="<?php echo $this->adminLink('scans', 'notification-create', $scan['id']); ?>" data-action="<?php echo esc_attr($this->key_ . 'scan_temp_save'); ?>" data-nonce="<?php echo esc_attr($nonce_scan_temp_save); ?>"><?php _e('Create New Notification', 'cgss'); ?></a>
                                            <span class="spinner inline"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="first"><label><?php _e('Comment', 'cgss'); ?></label></td>
                                        <td><textarea name="comment" size="30"><?php echo esc_html($scan['comment']); ?></textarea></td>
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
