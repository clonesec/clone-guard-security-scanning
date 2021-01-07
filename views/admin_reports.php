<div class="cgss-reports-page">

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
        
        <h1 class="wp-heading-inline">Reports</h1>

        <div id="ajax_message"></div>

        <hr class="wp-header-end">

        <form id="reports-filter" method="get">
            <?php wp_nonce_field($this->key_ . 'reports'); ?>
            <input type="hidden" name="page" value="<?php echo esc_attr($this->key_ . 'reports'); ?>" />
            <div class="tablenav top">
                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
                        <select name="action" id="bulk-action-selector-top">
                            <option value="-1">Bulk Actions</option>
                            <option value="delete">Delete</option>
                        </select>
                        <input type="submit" id="doaction" class="button action" value="Apply">
                </div>


                <?php if($reports['total_pages'] > 1): ?>
                <h2 class="screen-reader-text">Pages list navigation</h2>
                <div class="tablenav-pages">
                <span class="displaying-num"><?php echo $reports['total']; ?> items</span>
                    <span class="pagination-links">
                        <?php if($reports['current_page'] == 1): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                        <?php else: ?>
                        <a class="first-page button" href="<?php echo $this->adminLink('reports', 1); ?>"><span class="screen-reader-text">First page</span><span aria-hidden="true">«</span></a>
                        <?php endif; ?>

                        <?php if($reports['current_page'] == 1): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                        <?php else: ?>
                        <a class="prev-page button" href="<?php echo $this->adminLink('reports', $reports['current_page'] - 1); ?>"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">‹</span></a>
                        <?php endif; ?>

                        <span class="paging-input">
                            <label for="current-page-selector" class="screen-reader-text">Current Page</label>
                            <input class="current-page" id="current-page-selector" type="text" name="paged" value="<?php echo $reports['current_page']; ?>" size="1" aria-describedby="table-paging">
                            <span class="tablenav-paging-text"> of <span class="total-pages"><?php echo $reports['total_pages']; ?></span></span>
                        </span>

                        <?php if($reports['current_page'] == $reports['total_pages']): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
                        <?php else: ?>
                        <a class="next-page button" href="<?php echo $this->adminLink('reports', $reports['current_page'] + 1); ?>"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>
                        <?php endif; ?>

                        <?php if($reports['current_page'] == $reports['total_pages']): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
                        <?php else: ?>
                        <a class="last-page button" href="<?php echo $this->adminLink('reports', $reports['total_pages']); ?>"><span class="screen-reader-text">Last page</span><span aria-hidden="true">»</span></a>
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
                        <th>Date</th>
                        <th>Quarter</th>
                        <th>Scan Name</th>
                        <th>Vulnerabilities</th>
                        <th>Compliance</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reports['reports'] as $key => $report): ?>
                    <tr>
                        <?php if(isset($report['name'])): ?>
                        <th scope="row" class="check-column"><input type="checkbox" name="reports[]" value="<?php echo esc_attr($report['id']); ?>"></th>
                        <td><strong><a href="<?php echo $this->adminLink('reports', 'report-view', $key, $reports['current_page']); ?>"><?php echo esc_html($report['date']); ?></a></strong></td>
                        <td><?php echo esc_html($report['quarter']); ?></td>
                        <td><?php echo esc_html($report['scan_name']); ?></td>
                        <td><?php echo esc_html($report['display_vulnerabilities']); ?></td>
                        <td><?php echo $report['display_compliance']; ?></td>
                        <td>
                            <a class="button" href="<?php echo $this->adminLink('reports', 'report-view', $key, $reports['current_page']); ?>">View</a>
                            <a class="button delete" href="#" data-id="<?php echo esc_attr($report['id']); ?>" data-action="<?php echo esc_attr($this->key_ . 'report_delete'); ?>" data-nonce="<?php echo esc_attr($nonce_report_delete); ?>">Delete</a>
                            <span class="spinner inline"></span>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>

                <tfoot>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-2" type="checkbox"></td>
                        <th>Date</th>
                        <th>Quarter</th>
                        <th>Scan Name</th>
                        <th>Vulnerabilities</th>
                        <th>Compliance</th>
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
                    <span class="displaying-num"><?php echo $reports['total']; ?> items</span>
                    <span class="pagination-links">
                        <?php if($reports['current_page'] == 1): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                        <?php else: ?>
                        <a class="first-page button" href="<?php echo $this->adminLink('reports', 1); ?>"><span class="screen-reader-text">First page</span><span aria-hidden="true">«</span></a>
                        <?php endif; ?>

                        <?php if($reports['current_page'] == 1): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                        <?php else: ?>
                        <a class="prev-page button" href="<?php echo $this->adminLink('reports', $reports['current_page'] - 1); ?>"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">‹</span></a>
                        <?php endif; ?>

                        <span class="screen-reader-text">Current Page</span>
                        <span id="table-paging" class="paging-input">
                            <span class="tablenav-paging-text"><?php echo $reports['current_page']; ?> of <span class="total-pages"><?php echo $reports['total_pages']; ?></span></span>
                        </span>

                        <?php if($reports['current_page'] == $reports['total_pages']): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
                        <?php else: ?>
                        <a class="next-page button" href="<?php echo $this->adminLink('reports', $reports['current_page'] + 1); ?>"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>
                        <?php endif; ?>

                        <?php if($reports['current_page'] == $reports['total_pages']): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
                        <?php else: ?>
                        <a class="last-page button" href="<?php echo $this->adminLink('reports', $reports['total_pages']); ?>"><span class="screen-reader-text">Last page</span><span aria-hidden="true">»</span></a>
                        <?php endif; ?>
                    </span>
                </div>
                <br class="clear">
            </div>

        </form>

    </div>

</div>
