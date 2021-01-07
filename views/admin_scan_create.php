<div class="cgss-scan-create-page">

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

        <h1>Create Scan</h1>

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
