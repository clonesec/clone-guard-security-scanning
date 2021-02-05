<div class="cgss-options-page" id="<?php echo $action; ?>_page">

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

        <h1 class="wp-heading-inline">Options</h1>

        <form method="get" class="nav-tab-wrapper">
            <?php if(($this->userDetails['pciAvailable']) && ($app_type) !== 'pci'): ?>
                <a  href="#"
                    class="nav-tab update-app-type <?PHP echo ($app_type == 'pci')? 'nav-tab-active': ''; ?>"    
                    data-apptype="<?php echo esc_attr('pci'); ?>"
                    data-action="<?php echo esc_attr($this->key_ . 'update_user_app_type'); ?>"
                    data-nonce="<?php echo esc_attr($nonce_update_app_type); ?>">
                    <img src="<?php echo esc_url( plugins_url( '../img/pci.png', __FILE__ ) ); ?>" />
                    <span>PCI ASV Scanning</span>
                </a>
            <?php elseif (($this->userDetails['pciAvailable']) && ($app_type) == 'pci'): ?>
                <a href="#" class="nav-tab nav-tab-active">
                    <img src="<?php echo esc_url( plugins_url( '../img/pci.png', __FILE__ ) ); ?>" />
                    <span>PCI ASV Scanning</span>
                </a>
            <?php else: ?>
                <a class="nav-tab disabled-state">
                    <img src="<?php echo esc_url( plugins_url( '../img/pci.png', __FILE__ ) ); ?>" />
                    <span>PCI ASV Scanning</span>    
                </a>
            <?php endif; ?>

            <?php if(($this->userDetails['vrmsAvailable']) && ($app_type) !== 'vrms'): ?>
                <a  href="#" 
                    class="nav-tab update-app-type <?PHP echo ($app_type == 'vrms')? 'nav-tab-active': ''; ?>"    
                    data-apptype="<?php echo esc_attr('vrms'); ?>"
                    data-action="<?php echo esc_attr($this->key_ . 'update_user_app_type'); ?>"
                    data-nonce="<?php echo esc_attr($nonce_update_app_type); ?>">
                    <img src="<?php echo esc_url( plugins_url( '../img/vrms.png', __FILE__ ) ); ?>" />
                    <span>Vulnerability Management</span>
                </a>
            <?php elseif (($this->userDetails['vrmsAvailable']) && ($app_type) == 'vrms'): ?>
                <a href="#" class="nav-tab nav-tab-active">
                    <img src="<?php echo esc_url( plugins_url( '../img/vrms.png', __FILE__ ) ); ?>" />
                    <span>Vulnerability Management</span>
                </a>
            <?php else: ?>
                <a class="nav-tab disabled-state">
                    <img src="<?php echo esc_url( plugins_url( '../img/vrms.png', __FILE__ ) ); ?>" />
                    <span>Vulnerability Management</span>
                </a>
            <?php endif; ?>

            <?php if(($this->userDetails['penetrationAvailable']) && ($app_type) !== 'penetration'): ?>
                <a  href="#" 
                    class="nav-tab update-app-type <?PHP echo ($app_type == 'penetration')? 'nav-tab-active': ''; ?>"    
                    data-apptype="<?php echo esc_attr('penetration'); ?>"
                    data-action="<?php echo esc_attr($this->key_ . 'update_user_app_type'); ?>"
                    data-nonce="<?php echo esc_attr($nonce_update_app_type); ?>">
                    <img src="<?php echo esc_url( plugins_url( '../img/penetration.png', __FILE__ ) ); ?>" />
                    <span>Penetration Testing</span>
                </a>
            <?php elseif (($this->userDetails['penetrationAvailable']) && ($app_type) == 'penetration'): ?>
                <a href="#" class="nav-tab nav-tab-active">
                    <img src="<?php echo esc_url( plugins_url( '../img/penetration.png', __FILE__ ) ); ?>" />
                    <span>Penetration Testing</span>    
                </a>
            <?php else: ?>
                <a class="nav-tab disabled-state">
                    <img src="<?php echo esc_url( plugins_url( '../img/penetration.png', __FILE__ ) ); ?>" />
                    <span>Penetration Testing</span> 
                </a>
            <?php endif; ?>
            <span class="spinner inline"></span>
        </form>

        <div id="ajax_message"></div>

        <hr class="wp-header-end">

        <form id="schedules-filter" method="get">
            <?php wp_nonce_field($this->key_ . 'options'); ?>
            <input type="hidden" name="page" value="<?php echo esc_attr($this->key_ . 'options'); ?>" />

            <div class="tablenav top">
                <div class="alignleft actions bulkactions">

                    <span class="table-top-section">
                        <h2 class="cgss-sub-title">Schedules</h2>

                        <a href="<?php echo $this->adminLink('options', 'schedule-create'); ?>" 
                        class="table-top-add-btn button action page-title-action">
                        Add Schedule
                        </a>
                    </span>

                </div>
            </div>

            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-1" type="checkbox"></td>
                        <th>Name</th>
                        <th>Frequency</th>
                        <th>Comment</th>
                        <th class="table-min-col">Options</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($schedules['schedules'] as $key => $schedule): ?>
                        <tr>
                            <?php if(isset($schedule['name'])): ?>
                                <th scope="row" class="check-column"><input type="checkbox" name="schedules[]" value="<?php echo esc_attr($schedule['id']); ?>"></th>
                                <td><strong>
                                    <a href="<?php echo $this->adminLink('options', 'schedule-update', $schedule['id']); ?>">
                                        <?php echo esc_html($schedule['name']); ?>
                                    </a></strong>
                                </td>
                                <td><?php echo "Every " . $schedule['period'] . " " . $schedule['period_unit']; ?></td>
                                <td><?php echo esc_html($schedule['comment']); ?></td>
                                <td class="table-min-col">
                                    <a class="button" href="<?php echo $this->adminLink('options', 'schedule-update', $schedule['id']); ?>">
                                        Edit
                                    </a>
                                    <a  class="button delete" href="#" 
                                        data-id="<?php echo esc_attr($schedule['id']); ?>" 
                                        data-action="<?php echo esc_attr($this->key_ . 'schedule_delete'); ?>" 
                                        data-nonce="<?php echo esc_attr($nonce_schedule_delete); ?>">
                                        Delete
                                    </a>
                                    <span class="spinner inline"></span>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

                <tfoot>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-2" type="checkbox"></td>
                        <th>Name</th>
                        <th>Frequency</th>
                        <th>Comment</th>
                        <th class="table-min-col">Options</th>
                    </tr>
                </tfoot>

            </table>

            <div class="tablenav bottom">
                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
                        <select name="action2" id="bulk-action-selector-top">
                            <option value="-1">Bulk Actions</option>
                            <option value="delete">Delete</option>
                        </select>
                        <input type="submit" id="doaction" class="button action" value="Apply">
                </div>

                <div class="tablenav-pages">
                    <span class="displaying-num"><?php echo $schedules['total']; ?> items</span>
                    <span class="pagination-links">
                        <?php if($schedules['current_page'] == 1): ?>
                            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                        <?php else: ?>
                            <a class="first-page button" href="<?php echo $this->optionsPaginationLink('schedules', 1); ?>"><span class="screen-reader-text">First page</span><span aria-hidden="true">«</span></a>
                        <?php endif; ?>

                        <?php if($schedules['current_page'] == 1): ?>
                            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                        <?php else: ?>
                            <a class="prev-page button" href="<?php echo $this->optionsPaginationLink('schedules', $schedules['current_page'] - 1); ?>"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">‹</span></a>
                        <?php endif; ?>

                        <span class="screen-reader-text">Current Page</span>
                        <span id="table-paging" class="paging-input">
                            <span class="tablenav-paging-text"><?php echo $schedules['current_page']; ?> of <span class="total-pages"><?php echo $schedules['total_pages']; ?></span></span>
                        </span>

                        <?php if($schedules['current_page'] == $schedules['total_pages']): ?>
                            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
                        <?php else: ?>
                            <a class="next-page button" href="<?php echo $this->optionsPaginationLink('schedules', $schedules['current_page'] + 1) ?>"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>
                        <?php endif; ?>

                        <?php if($schedules['current_page'] == $schedules['total_pages']): ?>
                            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
                        <?php else: ?>
                            <a class="last-page button" href="<?php echo $this->optionsPaginationLink('schedules', $schedules['total_pages']); ?>"><span class="screen-reader-text">Last page</span><span aria-hidden="true">»</span></a>
                        <?php endif; ?>
                    </span>
                </div>
                <br class="clear">
            </div>

        </form>

        <form id="targets-filter" method="get">
            <?php wp_nonce_field($this->key_ . 'options'); ?>
            <input type="hidden" name="page" value="<?php echo esc_attr($this->key_ . 'options'); ?>" />

            <div class="tablenav top">
                <div class="alignleft actions bulkactions">

                    <span class="table-top-section">
                        <h2 class="cgss-sub-title">Targets</h2>

                        <a href="<?php echo $this->adminLink('options', 'target-create'); ?>" 
                            class="table-top-add-btn button action page-title-action">
                            Add Target
                        </a>
                    </span>

                </div>
            </div>

            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-1" type="checkbox"></td>
                        <th>Name</th>
                        <th>Hosts</th>
                        <th>IPs</th>
                        <th>Comment</th>
                        <th class="table-min-col">Options</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($targets['targets'] as $key => $target): ?>
                        <tr>
                            <?php if(isset($target['name'])): ?>
                                <th scope="row" class="check-column"><input type="checkbox" name="targets[]" value="<?php echo esc_attr($target['id']); ?>"></th>
                                <td><strong>
                                    <a href="<?php echo $this->adminLink('options', 'target-update', $target['id']); ?>">
                                        <?php echo esc_html($target['name']); ?>
                                    </a></strong>
                                </td>
                                <td>
                                <?php 
                                    $hostsString = $target['hosts'];
                                    
                                    if (strpos($hostsString, ",") !== false) {
                                        // $modifiedString = substr($hostsString, 0 , strpos($hostsString, ","));
                                        $modifiedString = explode(",", $hostsString);
                                        echo ($modifiedString[0] . $modifiedString[1] . $modifiedString[2]);
                                    } else {
                                        echo $hostsString;
                                    }
                                ?>
                                </td>
                                <td><?php echo esc_html($target['max_hosts']); ?></td>
                                <td><?php echo esc_html($target['comment']); ?></td>
                                <td class="table-min-col">
                                    <a class="button" href="<?php echo $this->adminLink('options', 'target-update', $target['id']); ?>">
                                        Edit
                                    </a>
                                    <a  class="button delete" href="#" 
                                        data-id="<?php echo esc_attr($target['id']); ?>" 
                                        data-action="<?php echo esc_attr($this->key_ . 'target_delete'); ?>" 
                                        data-nonce="<?php echo esc_attr($nonce_target_delete); ?>">
                                        Delete
                                    </a>
                                    <span class="spinner inline"></span>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                
                <tfoot>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-2" type="checkbox"></td>
                        <th>Name</th>
                        <th>Hosts</th>
                        <th>IPs</th>
                        <th>Comment</th>
                        <th class="table-min-col">Options</th>
                    </tr>
                </tfoot>

            </table>

            <div class="tablenav bottom">
                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
                        <select name="action3" id="bulk-action-selector-top">
                            <option value="-1">Bulk Actions</option>
                            <option value="delete">Delete</option>
                        </select>
                        <input type="submit" id="doaction" class="button action" value="Apply">
                </div>
                <div class="tablenav-pages">
                    <span class="displaying-num"><?php echo $targets['total']; ?> items</span>
                    <span class="pagination-links">
                        <?php if($targets['current_page'] == 1): ?>
                            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                        <?php else: ?>
                            <a class="first-page button" href="<?php echo $this->optionsPaginationLink('targets', 1); ?>"><span class="screen-reader-text">First page</span><span aria-hidden="true">«</span></a>
                        <?php endif; ?>

                        <?php if($targets['current_page'] == 1): ?>
                            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                        <?php else: ?>
                            <a class="prev-page button" href="<?php echo $this->optionsPaginationLink('targets', $targets['current_page'] - 1); ?>"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">‹</span></a>
                        <?php endif; ?>

                        <span class="screen-reader-text">Current Page</span>
                        <span id="table-paging" class="paging-input">
                            <span class="tablenav-paging-text"><?php echo $targets['current_page']; ?> of <span class="total-pages"><?php echo $targets['total_pages']; ?></span></span>
                        </span>

                        <?php if($targets['current_page'] == $targets['total_pages']): ?>
                            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
                        <?php else: ?>
                            <a class="next-page button" href="<?php echo $this->optionsPaginationLink('targets', $targets['current_page'] + 1); ?>"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>
                        <?php endif; ?>

                        <?php if($targets['current_page'] == $targets['total_pages']): ?>
                            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
                        <?php else: ?>
                            <a class="last-page button" href="<?php echo $this->optionsPaginationLink('targets', $targets['total_pages']); ?>"><span class="screen-reader-text">Last page</span><span aria-hidden="true">»</span></a>
                        <?php endif; ?>
                    </span>
                </div>
                <br class="clear">
            </div>

        </form>

        <form id="notifications-filter" method="get">
            <?php wp_nonce_field($this->key_ . 'options'); ?>
            <input type="hidden" name="page" value="<?php echo esc_attr($this->key_ . 'options'); ?>" />

            <div class="tablenav top">
                <div class="alignleft actions bulkactions">

                    <span class="table-top-section">
                        <h2 class="cgss-sub-title">Notifications</h2>

                        <a href="<?php echo $this->adminLink('options', 'notification-create'); ?>" 
                            class="table-top-add-btn button action page-title-action">
                            Add Notification
                        </a>
                    </span>

                </div>
            </div>

            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-1" type="checkbox"></td>
                        <th>Name</th>
                        <th>Scan Status</th>
                        <th>Email Address</th>
                        <th class="table-min-col">Options</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($notifications['notifications'] as $key => $notification): ?>
                        <tr>
                            <?php if(isset($notification['name'])): ?>
                                <th scope="row" class="check-column"><input type="checkbox" name="notifications[]" value="<?php echo esc_attr($notification['id']); ?>"></th>
                                <td><strong>
                                    <a href="<?php echo $this->adminLink('options', 'notification-update', $notification['id']); ?>">
                                        <?php echo esc_html($notification['name']); ?>
                                    </a></strong>
                                </td>
                                <td><?php 
					switch($notification["status_changed"]){
					    case "Done":
					        echo "Scan Completed";
					        break;
					    case "Running":
						echo "Scan Started";
						break;
					    default:
					        echo $notification["status_changed"];
					}
				    ?>


				</td>
                                <td><?php echo esc_html($notification['email_options']['to_address']); ?></td>
                                <td>
                                    <a class="button" href="<?php echo $this->adminLink('options', 'notification-update', $notification['id']); ?>">
                                        Edit
                                    </a>
                                    <a class="button delete" href="#" 
                                        data-id="<?php echo esc_attr($notification['id']); ?>" 
                                        data-action="<?php echo esc_attr($this->key_ . 'notification_delete'); ?>" 
                                        data-nonce="<?php echo esc_attr($nonce_notification_delete); ?>">
                                        Delete
                                    </a>
                                    <span class="spinner inline"></span>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

                <tfoot>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-2" type="checkbox"></td>
                        <th>Name</th>
                        <th>Scan Status</th>
                        <th>Email Address</th>
                        <th class="table-min-col">Options</th>
                    </tr>
                </tfoot>

            </table>

            <div class="tablenav bottom">
                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
                        <select name="action" id="bulk-action-selector-top">
                            <option value="-1">Bulk Actions</option>
                            <option value="delete">Delete</option>
                        </select>
                        <input type="submit" id="doaction" class="button action" value="Apply">
                </div>

                <div class="tablenav-pages">
                    <span class="displaying-num"><?php echo $notifications['total']; ?> items</span>
                    <span class="pagination-links">
                        <?php if($notifications['current_page'] == 1): ?>
                            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                        <?php else: ?>
                            <a class="first-page button" href="<?php echo $this->optionsPaginationLink('notifications', 1); ?>"><span class="screen-reader-text">First page</span><span aria-hidden="true">«</span></a>
                        <?php endif; ?>

                        <?php if($notifications['current_page'] == 1): ?>
                            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                        <?php else: ?>
                            <a class="prev-page button" href="<?php echo $this->optionsPaginationLink('notifications', $notifications['current_page'] - 1); ?>"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">‹</span></a>
                        <?php endif; ?>

                        <span class="screen-reader-text">Current Page</span>
                        <span id="table-paging" class="paging-input">
                            <span class="tablenav-paging-text"><?php echo $notifications['current_page']; ?> of <span class="total-pages"><?php echo $notifications['total_pages']; ?></span></span>
                        </span>

                        <?php if($notifications['current_page'] == $notifications['total_pages']): ?>
                            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
                        <?php else: ?>
                            <a class="next-page button" href="<?php echo $this->optionsPaginationLink('notifications', $notifications['current_page'] + 1); ?>"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>
                        <?php endif; ?>

                        <?php if($notifications['current_page'] == $notifications['total_pages']): ?>
                            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
                        <?php else: ?>
                            <a class="last-page button" href="<?php echo $this->optionsPaginationLink('notifications', $notifications['total_pages']); ?>"><span class="screen-reader-text">Last page</span><span aria-hidden="true">»</span></a>
                        <?php endif; ?>
                    </span>
                </div>
                <br class="clear">
            </div>

        </form>

    </div>

</div> 
