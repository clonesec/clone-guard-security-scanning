<div class="cgss-reports-view-page">

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

        <h1>View Report</h1>

        <div id="ajax_message"></div>

        <div id="poststuff">
            <form id="post-body" class="metabox-holder columns-2 ajax_form" action="admin-ajax.php" method="POST" style="margin:0">
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
                                            <td class="first"><label for="date"><?php esc_html_e('Date', 'cgss'); ?></label></td>
                                            <td><?php echo esc_html($report['date']); ?></td>
                                        </tr>

                                        <tr>
                                            <td class="first"><label for="quarter"><?php esc_html_e('Quarter', 'cgss'); ?></label></td>
                                            <td><?php echo esc_html($report['quarter']); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="first"><label for="scan_name"><?php esc_html_e('Scan Name', 'cgss'); ?></label></td>
                                            <td><?php echo esc_html($report['scan_name']); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="first"><label for="display_vulnerabilities"><?php esc_html_e('Vulnerabilities', 'cgss'); ?></label></td>
                                            <td><?php echo esc_html($report['display_vulnerabilities']); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="first"><label for="display_compliance"><?php esc_html_e('Compliance', 'cgss'); ?></label></td>
                                            <td><?php echo $report['display_compliance']; ?></td>
                                        </tr>

                                        <tr>
                                            <td class="first"><label for="attestation"><?php esc_html_e('Attestation File', 'cgss'); ?></label></td>
                                            <td class="<?php echo $report['attestation_status']; ?>" data-action="<?php echo esc_attr($this->key_ . 'report_download'); ?>" data-id="<?php echo esc_attr($report['id']); ?>" data-type="attestation" data-nonce="<?php echo esc_attr($nonce_report_download); ?>">
                                                <button class="button generate">Generate</button>
                                                <button class="button download">Download</button>
                                                <button class="button regenerate">Regenerate</button>
                                                <span class="spinner inline"></span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="first"><label for="executive"><?php esc_html_e('Executive File', 'cgss'); ?></label></td>
                                            <td class="<?php echo $report['executive_status']; ?>" data-action="<?php echo esc_attr($this->key_ . 'report_download'); ?>" data-id="<?php echo esc_attr($report['id']); ?>" data-type="executive" data-nonce="<?php echo esc_attr($nonce_report_download); ?>">
                                                <button class="button generate">Generate</button>
                                                <button class="button download">Download</button>
                                                <button class="button regenerate">Regenerate</button>
                                                <span class="spinner inline"></span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="first"><label for="detailed"><?php esc_html_e('Detailed File', 'cgss'); ?></label></td>
                                            <td class="<?php echo $report['detailed_status']; ?>" data-action="<?php echo esc_attr($this->key_ . 'report_download'); ?>" data-id="<?php echo esc_attr($report['id']); ?>" data-type="detailed" data-nonce="<?php echo esc_attr($nonce_report_download); ?>">
                                                <button class="button generate">Generate</button>
                                                <button class="button download">Download</button>
                                                <button class="button regenerate">Regenerate</button>
                                                <span class="spinner inline"></span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="first"><label for="remediation"><?php esc_html_e('Remediation File', 'cgss'); ?></label></td>
                                            <td class="<?php echo $report['remediation_status']; ?>" data-action="<?php echo esc_attr($this->key_ . 'report_download'); ?>" data-id="<?php echo esc_attr($report['id']); ?>" data-type="remediation" data-nonce="<?php echo esc_attr($nonce_report_download); ?>">
                                                <button class="button generate">Generate</button>
                                                <button class="button download">Download</button>
                                                <button class="button regenerate">Regenerate</button>
                                                <span class="spinner inline"></span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="first"><label for="feedback"><?php esc_html_e('Feedback File', 'cgss'); ?></label></td>
                                            <td class="<?php echo $report['feedback_status']; ?>" data-action="<?php echo esc_attr($this->key_ . 'report_download'); ?>" data-id="<?php echo esc_attr($report['id']); ?>" data-type="feedback" data-nonce="<?php echo esc_attr($nonce_report_download); ?>">
                                                <a href="<?php echo esc_url($this->feedback_url); ?>" class="button" target="_blank">Download</a>
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
