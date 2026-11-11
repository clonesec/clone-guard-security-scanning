<div class="cgss-vulnerabilities-page">
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
        
        <h1 class="wp-heading-inline">Vulnerabilities</h1>

        <form method="GET" class="nav-tab-wrapper">
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

        <form id="vulnerabilities-filter" method="GET">

            <div id="newExceptionModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <span class="close">&times;</span>
                        <h2>Add Justification Text</h2>
                    </div>
                    <div class="modal-body">
                        <textarea name="modal_justification_text"></textarea>
                        <p class="modal-validation-text hide">Please enter a justification</p>
                    </div>
                    <div class="modal-footer">
                        <button class="table-buttons-save button button-primary" type="button">OK</button>
                    </div>
                </div>
            </div>

            <?php wp_nonce_field($this->key_ . 'vulnerabilities'); ?>
            <input type="hidden" name="page" value="<?php echo esc_attr($this->key_ . 'vulnerabilities'); ?>" />
            <div class="tablenav top">
                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
                        <select name="action" id="bulk-action-selector-top" class="select-bulk-add-exception">
                            <option value="-1">Bulk Actions</option>
                            <?php if ($hide_add_exception_option == false): ?>
                                <option value="add-exception">Add Exception</option>
                            <?php endif; ?>
                        </select>
                        <input type="submit" id="doaction" class="button action" value="Apply">
                </div>

                <?php if($vulnerabilities['total_pages'] > 1): ?>
                <h2 class="screen-reader-text">Pages list navigation</h2>
                <div class="tablenav-pages">
                <span class="displaying-num"><?php echo $vulnerabilities['total']; ?> items</span>
                    <span class="pagination-links">
                        <?php if($vulnerabilities['current_page'] == 1): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                        <?php else: ?>
                        <a class="first-page button" href="<?php echo $this->adminLink('vulnerabilities', 1); ?>"><span class="screen-reader-text">First page</span><span aria-hidden="true">«</span></a>
                        <?php endif; ?>

                        <?php if($vulnerabilities['current_page'] == 1): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                        <?php else: ?>
                        <a class="prev-page button" href="<?php echo $this->adminLink('vulnerabilities', $vulnerabilities['current_page'] - 1); ?>"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">‹</span></a>
                        <?php endif; ?>

                        <span class="paging-input">
                            <label for="current-page-selector" class="screen-reader-text">Current Page</label>
                            <input class="current-page" id="current-page-selector" type="text" name="paged" value="<?php echo $vulnerabilities['current_page']; ?>" size="1" aria-describedby="table-paging">
                            <span class="tablenav-paging-text"> of <span class="total-pages"><?php echo $vulnerabilities['total_pages']; ?></span></span>
                        </span>

                        <?php if($vulnerabilities['current_page'] == $vulnerabilities['total_pages']): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
                        <?php else: ?>
                        <a class="next-page button" href="<?php echo $this->adminLink('vulnerabilities', $vulnerabilities['current_page'] + 1); ?>"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>
                        <?php endif; ?>

                        <?php if($vulnerabilities['current_page'] == $vulnerabilities['total_pages']): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
                        <?php else: ?>
                        <a class="last-page button" href="<?php echo $this->adminLink('vulnerabilities', $vulnerabilities['total_pages']); ?>"><span class="screen-reader-text">Last page</span><span aria-hidden="true">»</span></a>
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
                        <th>Vulnerability Summary</th>
                        <th>CVSS Score (Severity)</th>
                        <th>Host</th>
                        <th>Port</th>
                        <th>Exception</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($vulnerabilities['results'] as $key => $vulnerability): ?>
                        <tr>
                            <?php if(isset($vulnerability['name'])): ?>
                                <th scope="row" class="check-column">
                                    <input type="checkbox" 
                                        name="vulnerabilities[]"
                                        value="<?php echo esc_attr($vulnerability['id']); ?>"
                                        <?php if (isset($vulnerability['overrides']['status'])) {
                                            if ($vulnerability['overrides']['status'] == 'requested') { echo 'disabled'; }
                                        }
                                        ?> 
                                    >
                                </th>
                                <td>
                                    <strong>
                                        <a href="<?php echo $this->adminLink('vulnerabilities', 'vulnerability-view', $key, $vulnerabilities['current_page']); ?>">
                                            <?php echo esc_html($vulnerability['name']); ?>
                                        </a>
                                    </strong>
                                </td>
                                <td><?php echo esc_html($vulnerability['severity']); ?> (<?php echo esc_html(ucfirst($vulnerability['threat'])); ?>)</td>
                                <td><?php echo esc_html($vulnerability['host']); ?></td>
                                <td><?php echo esc_html($vulnerability['port']); ?> 
                                    <br>
                                    <?php // echo esc_html($vulnerability['nvt']['oid']); ?>
                                </td>
                                <td>
                                    <?php if (!empty($vulnerability['overrides'])):?>
                                        <strong>Status: </strong><?php echo esc_html(ucfirst($vulnerability['overrides']['status'])); ?>
                                        <br>
                                        <strong>Justification: </strong><?php echo esc_html($vulnerability['overrides']['text']); ?>

                                    <?php else: ?>
                                        <span>-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a class="button" href="<?php echo $this->adminLink('vulnerabilities', 'vulnerability-view', $key, $vulnerabilities['current_page']); ?>">
                                        View
                                    </a>                                    
                                    <?php if (!isset($vulnerability['overrides']['status'])):?>
                                        <a class="button return" 
                                            href="<?php echo $this->adminLink('vulnerabilities', 'vulnerability-add-exception', $key); ?>"
                                            >
                                            <?php _e('Add Exception', 'cgss'); ?>
                                        </a>
                                    <?php else: ?>
                                        <a class="button return" 
                                            href="<?php echo $this->adminLink('vulnerabilities', 'vulnerability-edit-exception', $key); ?>"
                                            >
                                            <?php _e('Edit Exception', 'cgss'); ?>
                                        </a>
                                    <?php endif; ?>

                                    <span class="spinner inline"></span>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

                <tfoot>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-1" type="checkbox"></td>
                        <th>Vulnerability Summary</th>
                        <th>CVSS Score (Severity)</th>
                        <th>Host</th>
                        <th>Port</th>
                        <th>Exception</th>
                        <th>Options</th>
                    </tr>
                </tfoot>

            </table>

            <div class="tablenav bottom">
                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
                        <select name="action2" id="bulk-action-selector-top">
                            <option value="-1">Bulk Actions</option>
                            <?php if ($hide_add_exception_option == false): ?>
                                <option value="add-exception">Add Exception</option>
                            <?php endif; ?>
                        </select>
                        <input type="submit" id="doaction" class="button action " value="Apply">
                </div>

                <div class="tablenav-pages">
                    <span class="displaying-num"><?php echo $vulnerabilities['total']; ?> items</span>
                    <span class="pagination-links">
                        <?php if($vulnerabilities['current_page'] == 1): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                        <?php else: ?>
                        <a class="first-page button" href="<?php echo $this->adminLink('vulnerabilities', 1); ?>"><span class="screen-reader-text">First page</span><span aria-hidden="true">«</span></a>
                        <?php endif; ?>

                        <?php if($vulnerabilities['current_page'] == 1): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                        <?php else: ?>
                        <a class="prev-page button" href="<?php echo $this->adminLink('vulnerabilities', $vulnerabilities['current_page'] - 1); ?>"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">‹</span></a>
                        <?php endif; ?>

                        <span class="screen-reader-text">Current Page</span>
                        <span id="table-paging" class="paging-input">
                            <span class="tablenav-paging-text"><?php echo $vulnerabilities['current_page']; ?> of <span class="total-pages"><?php echo $vulnerabilities['total_pages']; ?></span></span>
                        </span>

                        <?php if($vulnerabilities['current_page'] == $vulnerabilities['total_pages']): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
                        <?php else: ?>
                        <a class="next-page button" href="<?php echo $this->adminLink('vulnerabilities', $vulnerabilities['current_page'] + 1); ?>"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>
                        <?php endif; ?>

                        <?php if($vulnerabilities['current_page'] == $vulnerabilities['total_pages']): ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
                        <?php else: ?>
                        <a class="last-page button" href="<?php echo $this->adminLink('vulnerabilities', $vulnerabilities['total_pages']); ?>"><span class="screen-reader-text">Last page</span><span aria-hidden="true">»</span></a>
                        <?php endif; ?>
                    </span>
                </div>
                <br class="clear">
            </div>

        </form>

    </div>

</div>
