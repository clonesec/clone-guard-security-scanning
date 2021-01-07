<div class="cgss-scans-page scans_wrap">

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
        <span class="scans-table-top-section">
            <h1>Scans</h1>

            <a href="<?php echo $this->adminLink('scans', 'scan-create'); ?>" id="scans-add-scan-btn" class="button action page-title-action">Add Scan</a>
        </span>
        
        <div id="ajax_message"></div>

        <hr class="wp-header-end">

        <form id="scans-filter" method="get">
            <?php wp_nonce_field($this->key_ . 'scans'); ?>
            <input type="hidden" name="page" value="<?php echo esc_attr($this->key_ . 'scans'); ?>" />
            <div class="tablenav top">
                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
                        <select name="action" id="bulk-action-selector-top">
                            <option value="-1">Bulk Actions</option>
                            <option value="delete">Delete</option>
                        </select>
                        <input type="submit" id="doaction" class="button action" value="Apply">
                </div>


                <?php if($scans['total_pages'] > 1): ?>
                <h2 class="screen-reader-text">Pages list navigation</h2>
                <div class="tablenav-pages">
                <span class="displaying-num"><?php echo $scans['total']; ?> items</span>
                    <span class="pagination-links">
                        <?php if($scans['current_page'] == 1): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                        <?php else: ?>
                        <a class="first-page button" href="<?php echo $this->adminLink('scans', 1); ?>"><span class="screen-reader-text">First page</span><span aria-hidden="true">«</span></a>
                        <?php endif; ?>

                        <?php if($scans['current_page'] == 1): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                        <?php else: ?>
                        <a class="prev-page button" href="<?php echo $this->adminLink('scans', $scans['current_page'] - 1); ?>"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">‹</span></a>
                        <?php endif; ?>

                        <span class="paging-input">
                            <label for="current-page-selector" class="screen-reader-text">Current Page</label>
                            <input class="current-page" id="current-page-selector" type="text" name="paged" value="<?php echo $scans['current_page']; ?>" size="1" aria-describedby="table-paging">
                            <span class="tablenav-paging-text"> of <span class="total-pages"><?php echo $scans['total_pages']; ?></span></span>
                        </span>

                        <?php if($scans['current_page'] == $scans['total_pages']): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
                        <?php else: ?>
                        <a class="next-page button" href="<?php echo $this->adminLink('scans', $scans['current_page'] + 1); ?>"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>
                        <?php endif; ?>

                        <?php if($scans['current_page'] == $scans['total_pages']): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
                        <?php else: ?>
                        <a class="last-page button" href="<?php echo $this->adminLink('scans', $scans['total_pages']); ?>"><span class="screen-reader-text">Last page</span><span aria-hidden="true">»</span></a>
                        <?php endif; ?>
                    </span>
                </div>
                <?php endif; ?>
                <br class="clear">
            </div>
            <table class="wp-list-table widefat fixed striped pages">
                <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-1" type="checkbox"></td>
                        <th>Actions</th>
                        <th>Scan Name</th>
                        <th>Target Name</th>
                        <th>Progress</th>
                        <th>Status</th>
                        <th>Last Scan</th>
                        <th>Next Scan</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($scans['scans'] as $key => $scan): ?>
                    <tr>
                        <?php if(isset($scan['name'])): ?>
                        <th scope="row" class="check-column"><input type="checkbox" name="scans[]" value="<?php echo esc_attr($scan['id']); ?>"></th>
                        <td data-id="<?php echo esc_attr($scan['id']); ?>" data-nonce="<?php echo esc_attr($nonce_scan_action); ?>"
                            <?php 
                            if(isset($scan['schedule']) && isset($scan['schedule']['id']) && $scan['schedule']['id']) {
                                $scheduled = true;
                            } else {
                                $scheduled = false;
                            }
                            ?>
                            <?php if($scheduled && in_array($scan['status'], ['Requested', 'Running'])): ?>
                            class="scheduled_processing"
                            <?php elseif($scheduled): ?>
                            class="scheduled"
                            <?php elseif(in_array($scan['status'], ['Requested', 'Running'])): ?>
                            class="basic_processing"
                            <?php else: ?>
                            class="basic"
                            <?php endif; ?>
                        >
                            <span class="show action_schedule" href="<?php echo $this->adminLink('scans', 'schedule-update', $scan['schedule']['id']); ?>"><span class="dashicons dashicons-clock"></span></span></span>
                            <a href="#" class="action_play"><span class="dashicons dashicons-controls-play"></span></a>

                            <a href="#" class="action_stop"><span class="stopicon"></span></a>
                            <span class="action_stop_disabled"><span class="stopicon"></span></span>

                            <span class="action_spinner"><span class="spinner inline is-active"></span></span>
                        </td>
                        <td><strong><a href="<?php echo $this->adminLink('scans', $key); ?>"><?php echo esc_html($scan['name']); ?></a></strong></td>
                        <td><?php echo esc_html($scan['target']['name']); ?></td>
                        <td>
                            <div class="progress">
                                <div class="percent"><?php echo esc_html($scan['progress'] . '%'); ?></div>
                                <div class="bar" style="width: <?php echo esc_attr($scan['progress'] . '%'); ?>;"></div>
                            </div>
                        </td>
                        <td><?php echo esc_html($scan['status']); ?></td>
                        <td>
                            <?php if($scan['last_report']['name']): ?>
                            <?php echo esc_html(date('D M j Y G:i:s', strtotime($scan['last_report']['name']))); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($scan['schedule']['next_time'] != 'n/a'): ?>
                            <?php echo esc_html(date('D M j Y G:i:s', strtotime($scan['schedule']['next_time']))); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a class="button" href="<?php echo $this->adminLink('scans', $key); ?>">Edit</a>
                            <a class="button delete" href="#" data-id="<?php echo esc_attr($scan['id']); ?>" data-action="<?php echo esc_attr($this->key_ . 'scan_delete'); ?>" data-nonce="<?php echo esc_attr($nonce_scan_delete); ?>">Delete</a>
                            <span class="spinner inline"></span>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>

                <tfoot>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-2" type="checkbox"></td>
                        <th>Actions</th>
                        <th>Scan Name</th>
                        <th>Target Name</th>
                        <th>Progress</th>
                        <th>Status</th>
                        <th>Last Scan</th>
                        <th>Next Scan</th>
                        <th>Options</th>
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
                    <span class="displaying-num"><?php echo $scans['total']; ?> items</span>
                    <span class="pagination-links">
                        <?php if($scans['current_page'] == 1): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                        <?php else: ?>
                        <a class="first-page button" href="<?php echo $this->adminLink('scans', 1); ?>"><span class="screen-reader-text">First page</span><span aria-hidden="true">«</span></a>
                        <?php endif; ?>

                        <?php if($scans['current_page'] == 1): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                        <?php else: ?>
                        <a class="prev-page button" href="<?php echo $this->adminLink('scans', $scans['current_page'] - 1); ?>"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">‹</span></a>
                        <?php endif; ?>

                        <span class="screen-reader-text">Current Page</span>
                        <span id="table-paging" class="paging-input">
                            <span class="tablenav-paging-text"><?php echo $scans['current_page']; ?> of <span class="total-pages"><?php echo $scans['total_pages']; ?></span></span>
                        </span>

                        <?php if($scans['current_page'] == $scans['total_pages']): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
                        <?php else: ?>
                        <a class="next-page button" href="<?php echo $this->adminLink('scans', $scans['current_page'] + 1); ?>"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>
                        <?php endif; ?>

                        <?php if($scans['current_page'] == $scans['total_pages']): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
                        <?php else: ?>
                        <a class="last-page button" href="<?php echo $this->adminLink('scans', $scans['total_pages']); ?>"><span class="screen-reader-text">Last page</span><span aria-hidden="true">»</span></a>
                        <?php endif; ?>
                    </span>
                </div>
                <br class="clear">
            </div>

        </form>
    </div>

</div>