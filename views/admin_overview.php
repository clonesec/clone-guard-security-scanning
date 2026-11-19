<?php defined('ABSPATH') || exit; ?>
<?php // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- this view is included from a class method, so its variables are local to that method, not global. ?>
<div class="cgss-overview-page" id="<?php echo esc_attr($action); ?>_page">

    <div class="cgss-head">

        <div class="cgss-head-inside">
            <img class="cgss-head-logo" src="<?php echo esc_url( plugins_url( '../img/cloneguard-icon.svg', __FILE__ ) ); ?>" width="40" height="40" alt="" />
            <span class="cgss-head-text">
                <h1 class="cgss-head-title"><?php echo esc_html($title); ?></h1>
                <span class="cgss-head-subtitle">By <a href="https://www.clone-systems.com/" target="_blank">Clone Systems, Inc.</a></span>
            </span>
            <?php $this->portalButton(); ?>
        </div>

    </div>

    <h1 class="cgss-title">Overview</h1>

    <div id="ajax_message" style="margin-top:15px;"></div>
        
    <div class="cgss_main">
        
        <form method="get" class="nav-tab-wrapper">
            <?php if(($this->userDetails['pciAvailable']) && ($app_type) !== 'pci'): ?>
                <a  href="#"
                    class="nav-tab update-app-type <?PHP echo ($app_type == 'pci')? 'nav-tab-active': ''; ?>"    
                    data-apptype="<?php echo esc_attr('pci'); ?>"
                    data-action="<?php echo esc_attr($this->key_ . 'update_user_app_type'); ?>"
                    data-nonce="<?php echo esc_attr($nonce_update_app_type); ?>">
                    <span>PCI ASV Scanning</span>
                </a>
            <?php elseif (($this->userDetails['pciAvailable']) && ($app_type) == 'pci'): ?>
                <a href="#" class="nav-tab nav-tab-active">
                    <span>PCI ASV Scanning</span>
                </a>
            <?php else: ?>
                <a class="nav-tab disabled-state">
                    <span>PCI ASV Scanning</span>    
                </a>
            <?php endif; ?>

            <?php if(($this->userDetails['vrmsAvailable']) && ($app_type) !== 'vrms'): ?>
                <a  href="#" 
                    class="nav-tab update-app-type <?PHP echo ($app_type == 'vrms')? 'nav-tab-active': ''; ?>"    
                    data-apptype="<?php echo esc_attr('vrms'); ?>"
                    data-action="<?php echo esc_attr($this->key_ . 'update_user_app_type'); ?>"
                    data-nonce="<?php echo esc_attr($nonce_update_app_type); ?>">
                    <span>Vulnerability Management</span>
                </a>
            <?php elseif (($this->userDetails['vrmsAvailable']) && ($app_type) == 'vrms'): ?>
                <a href="#" class="nav-tab nav-tab-active">
                    <span>Vulnerability Management</span>
                </a>
            <?php else: ?>
                <a class="nav-tab disabled-state">
                    <span>Vulnerability Management</span>
                </a>
            <?php endif; ?>

            <?php if(($this->userDetails['penetrationAvailable']) && ($app_type) !== 'penetration'): ?>
                <a  href="#" 
                    class="nav-tab update-app-type <?PHP echo ($app_type == 'penetration')? 'nav-tab-active': ''; ?>"    
                    data-apptype="<?php echo esc_attr('penetration'); ?>"
                    data-action="<?php echo esc_attr($this->key_ . 'update_user_app_type'); ?>"
                    data-nonce="<?php echo esc_attr($nonce_update_app_type); ?>">
                    <span>Penetration Testing</span>
                </a>
            <?php elseif (($this->userDetails['penetrationAvailable']) && ($app_type) == 'penetration'): ?>
                <a href="#" class="nav-tab nav-tab-active">
                    <span>Penetration Testing</span>    
                </a>
            <?php else: ?>
                <a class="nav-tab disabled-state">
                    <span>Penetration Testing</span> 
                </a>
            <?php endif; ?>
            <span class="spinner inline"></span>
        </form>

        <?php if(!empty($reports['reports'])) : ?>
            <div class="overview-row">
                <div class="section-general overview-row-box">
                    <div class="general-wrapper">
                    <?php if ($compliance_status == 'pass') : ?>
                        <span class="general-icon-container">
                            <?php if ($app_type == 'pci' || $app_type == 'penetration'): ?>
                                <img class="general-icon" src="<?php echo esc_url( plugins_url( '../img/status-pass.svg', __FILE__ ) ); ?>" width="96" height="96" alt="" />
                            <?php elseif ($app_type == 'vrms') : ?>
                                <img class="general-icon" src="<?php echo esc_url( plugins_url( '../img/status-pass.svg', __FILE__ ) ); ?>" width="96" height="96" alt="" />
                            <?php endif; ?>
                        </span>

                        <span class="general-text">
                            <?php if ($app_type == 'pci'): ?>
                                <h1 class="general-h1 success-pci-color ">Your Website is PCI Compliant</h1>
                                <p>You are in adherence with the external PCI DSS ASV scanning requirement 11.2.2</p>
                            <?php elseif ($app_type == 'vrms') : ?>
                                <p>None of the assets scanned pose a risk to your environment.</p>
                            <?php elseif ($app_type == 'penetration') : ?>
                                <h1 class="general-h1 success-pci-color ">Vulnerabilities Not Found</h1>
                                <p>There are no High or Medium level vulnerabilities detected</p>
                            <?php endif; ?>
                        </span>

                        <span class="reports-btn-container">
                            <a class="button btn-overview" href="<?php echo esc_url($this->adminLink('scans')); ?>">Scan Now</a>
                        </span>

                    <?php else: ?>
                        <span class="general-icon-container">
                            <img class="general-icon" src="<?php echo esc_url( plugins_url( '../img/status-warning.svg', __FILE__ ) ); ?>" width="96" height="96" alt="" />
                        </span>

                        <span class="general-text">
                            <?php if ($app_type == 'pci'): ?>
                                <h1 class="general-h1 warning-pci-color "><?php echo esc_html($vulnerabilities_sum); ?> Vulnerabilities Found</h1>
                                <p>Please address all vulnerabilities in order to adhere to the external PCI DSS ASV scanning requirement 11.2.2</p>
                            <?php elseif ($app_type == 'vrms') : ?>
                                <h1 class="general-h1 warning-pci-color "><?php echo esc_html($vulnerabilities_sum); ?> Vulnerabilities Found</h1>
                                <p>Assets scanned pose a risk to your environment</p>
                            <?php elseif ($app_type == 'penetration') : ?>
                                <h1 class="general-h1 warning-pci-color "><?php echo esc_html($vulnerabilities_sum); ?> Vulnerabilities Found</h1>
                                <p>Please address all vulnerabilities in order to improve your security posture and adhere to various compliance requirements</p>
                            <?php endif; ?>
                        </span>

                        <span class="reports-btn-container" style="margin-top:10px;">
                            <?php if ($app_type == 'pci' || $app_type == 'vrms'): ?>
                                <a class="button cgss-btn-cta" href="<?php echo esc_url($this->adminLink('vulnerabilities')); ?>">View Vulnerabilities</a>
                            <?php else: ?>
                                <a class="button cgss-btn-cta" href="<?php echo esc_url($this->adminLink('vulnerabilities')); ?>">View Findings</a>
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
                    </div>
                </div>

                <div class="section-reports overview-row-box">                
                    <h1 class="reports-h2">Recent Reports</h1>
                    <?php foreach($reports['reports'] as $key => $report): ?>
                        <div class="report">
                            <span class="report-date">
                                <span class="dashicons dashicons-format-aside" style="margin:0 5px;"></span>
                                <a href="<?php echo esc_url($this->adminLink('reports', 'report-view', $key, $reports['current_page'])); ?>">
                                    <?php echo esc_html(gmdate('D M j Y G:i:s', strtotime($report['name']))); ?>
                                </a>
                            </span>
                            <span class="report-name">
                                <?php echo esc_html($report['task']['name']); ?>
                            </span>

                            <span class="report-vulnerabilities">
                                <small class="report-high" title="high"><?php echo esc_html($report['results']['high']); ?></small>
                                <small class="report-medium" title="medium"><?php echo esc_html($report['results']['medium']); ?></small>
                                <small class="report-low" title="low"><?php echo esc_html($report['results']['low']); ?></small>
                            </span>
                            <span class="report-compliance">
                                <?php 
                                    if(isset($report['compliance']) && $report['compliance']) {
                                        echo '<span class="cgss-pill cgss-pill-pass"><span class="dashicons dashicons-yes"></span>Pass</span>';
                                    } else {
                                        echo '<span class="cgss-pill cgss-pill-fail"><span class="dashicons dashicons-no"></span>Fail</span>';
                                    }
                                ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        
        <?php else: ?>
            <div class="overview-row">
                <div class="overview-row-box" style="display:flex; flex-direction:column; align-items:center; flex:1; padding:50px;">                
                    <h1>Let's get started</h1>
                    <img style="width:96px; margin:8px 0 20px;" src="<?php echo esc_url( plugins_url( '../img/cloneguard-icon.svg', __FILE__ ) ); ?>" width="96" height="96" alt="" />
                    <span class="reports-btn-container">
                        <a class="button cgss-btn-cta" href="<?php echo esc_url($this->adminLink('scans', 'scan-create')); ?>">
                            Add Scan
                        </a>
                    </span>
                </div>

                <div class="overview-row-box" style="flex:3; margin-left:10px; padding:50px;">
                    <div class="overview-features">
                        <h1 class="features-heading">Features</h1>
                        <div class="features-list">
                            <div class="feature">
                                <span class="cgss-feature-icon" aria-hidden="true"><span class="dashicons dashicons-shield"></span></span>
                                <span class="feature-text">
                                    <h2 class="feature-heading">PCI ASV Scanning</h2>
                                    <p class="feature-description">Comply with the PCI DSS and safeguard your customer’s data</p>
                                </span>
                                <div class="feature-radio">
                                    <?php if ($this->userDetails['pciAvailable']): ?>
                                        <div class="checkbox-switch">
                                            <input type="checkbox" checked="" value="1" name="status" class="input-checkbox" disabled>
                                            <div class="checkbox-animate"></div>
                                        </div>
                                    <?php else: ?>
                                        <div class="checkbox-switch">
                                            <input type="checkbox" checked="" value="1" name="status" class="input-checkbox" disabled>
                                            <div class="checkbox-animate2"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="feature">
                                <span class="cgss-feature-icon" aria-hidden="true"><span class="dashicons dashicons-search"></span></span>
                                <span class="feature-text">
                                    <h2 class="feature-heading">Vulnerability Management</h2>
                                    <p class="feature-description">Assess the security and integrity of your infrastructure to identify vulnerabilities</p>
                                </span>
                                <div class="feature-radio">
                                    <?php if ($this->userDetails['vrmsAvailable']): ?>
                                        <div class="checkbox-switch">
                                            <input type="checkbox" checked="" value="1" name="status" class="input-checkbox" disabled>
                                            <div class="checkbox-animate"></div>
                                        </div>
                                    <?php else: ?>
                                        <div class="checkbox-switch">
                                            <input type="checkbox" checked="" value="1" name="status" class="input-checkbox" disabled>
                                            <div class="checkbox-animate2"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="feature">
                                <span class="cgss-feature-icon" aria-hidden="true"><span class="dashicons dashicons-admin-network"></span></span>
                                <span class="feature-text">
                                    <h2 class="feature-heading">Penetration Testing</h2>
                                    <p class="feature-description">Perform real world attack simulations on your infrastructure to identify existing exploits</p>
                                </span>
                                <div class="feature-radio">
                                    <?php if ($this->userDetails['penetrationAvailable']): ?>
                                        <div class="checkbox-switch">
                                            <input type="checkbox" checked="" value="1" name="status" class="input-checkbox" disabled>
                                            <div class="checkbox-animate"></div>
                                        </div>
                                    <?php else: ?>
                                        <div class="checkbox-switch">
                                            <input type="checkbox" checked="" value="1" name="status" class="input-checkbox" disabled>
                                            <div class="checkbox-animate2"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>    
                    </div>
                </div>
            </div>        
        <?php endif; ?>

        <div class="overview-row" style="margin-top:10px;">

            <div class="section-product overview-row-box">
                <div class="section-product-text" style="margin-right:30px;">
                    <h1>Website Security Report</h2>
                    <p>The CloneGuard security report is a comprehensive report which indicates that your website is scanned regularly and is secure against internet based emerging threats. Clone Systems, Inc. is a certified PCI ASV and has been performing scans and securing websites for over 20 years.</p>
                    <span class="general-btn-container">
                    <?php
                        $protocols = array('http://', 'https://');
                    ?>  
                        <?php if(!empty($reports['reports'])): ?>
                            <a class="button btn-overview p-0-35" href="<?php echo esc_url('https://www.clone-systems.com/report?dn=' . str_replace($protocols, '', get_bloginfo('wpurl'))); ?>" target="_blank">View Validation Report</a>
                        <?php else: ?>
                            <button class="button btn-overview p-0-35" disabled>View Validation Report</a>
                        <?php endif; ?>
                    </span>
                </div>
                <img class="section-product-img img-report" src="<?php echo esc_url( plugins_url( '../img/report-screenshot4.png', __FILE__ ) ); ?>" />
            </div>

        </div>

        <div class="overview-row" style="margin-top:10px;">

            <div class="overview-row-box cgss-seal-box">
                <h1>Optional add-on: Trust Seal</h1>
                <p class="cgss-seal-intro">Customers with an active Clone Systems account and current scans can display a Trust Seal on their site. This is optional. Clicking the Trust Seal opens your public security report.</p>

                <form class="cgss-seal-picker" id="cgss-seal-picker"
                    data-action="<?php echo esc_attr($this->key_ . 'site_seal'); ?>"
                    data-nonce="<?php echo esc_attr($nonce_site_seal); ?>"
                    data-image="<?php echo esc_attr(Clone_Guard_Seal::PREVIEW_URL); ?>"
                    data-pixels="<?php echo esc_attr(wp_json_encode(Clone_Guard_Seal::PIXELS)); ?>"
                    data-enabled="<?php echo $site_seal['enabled'] ? '1' : '0'; ?>">

                    <div class="cgss-seal-options">
                        <fieldset class="cgss-seal-icons">
                            <legend>Style</legend>
                            <?php foreach(Clone_Guard_Seal::ICONS as $icon => $label): ?>
                                <label class="cgss-seal-icon">
                                    <input type="radio" name="icon" value="<?php echo esc_attr($icon); ?>" <?php checked($site_seal['icon'], $icon); ?> />
                                    <img src="<?php echo esc_url(str_replace('{icon}', $icon, Clone_Guard_Seal::PREVIEW_URL)); ?>" width="125" height="45" alt="<?php echo esc_attr($label); ?>"/>
                                </label>
                            <?php endforeach; ?>
                        </fieldset>

                        <fieldset class="cgss-seal-choice">
                            <legend>Size</legend>
                            <?php foreach(Clone_Guard_Seal::SIZES as $size => $label): ?>
                                <label><input type="radio" name="size" value="<?php echo esc_attr($size); ?>" <?php checked($site_seal['size'], $size); ?> /> <?php echo esc_html($label); ?></label>
                            <?php endforeach; ?>
                        </fieldset>

                        <fieldset class="cgss-seal-choice">
                            <legend>Placement</legend>
                            <label><input type="radio" name="position" value="right" <?php checked($site_seal['position'], 'right'); ?> /> Bottom right corner of every page</label>
                            <label><input type="radio" name="position" value="left" <?php checked($site_seal['position'], 'left'); ?> /> Bottom left corner of every page</label>
                            <label><input type="radio" name="position" value="relative" /> Inline, where I place it</label>
                        </fieldset>
                    </div>

                    <div class="cgss-seal-side">
                        <div class="cgss-seal-preview" aria-hidden="true">
                            <span class="cgss-seal-preview-bar"></span>
                            <span class="cgss-seal-preview-line"></span>
                            <span class="cgss-seal-preview-line is-short"></span>
                            <img class="cgss-seal-preview-img" src="" alt="" />
                        </div>

                        <p class="cgss-seal-status" role="status" aria-live="polite"><?php echo $site_seal['enabled'] ? esc_html('Showing in the bottom ' . $site_seal['position'] . ' corner of your site.') : ''; ?></p>

                        <div class="cgss-seal-corner-actions">
                            <button type="submit" class="button cgss-btn-cta cgss-seal-save"><?php echo $site_seal['enabled'] ? 'Update the seal' : 'Show on my site'; ?></button>
                            <button type="button" class="button-link cgss-seal-remove" <?php echo $site_seal['enabled'] ? '' : 'hidden'; ?>>Remove from my site</button>
                            <a class="cgss-seal-view" href="<?php echo esc_url(home_url('/')); ?>" target="_blank" rel="noopener" <?php echo $site_seal['enabled'] ? '' : 'hidden'; ?>>View your site<span class="screen-reader-text"> (opens in a new tab)</span></a>
                        </div>

                        <div class="cgss-seal-inline-actions" hidden>
                            <p>Paste this shortcode where the seal should appear:</p>
                            <p class="cgss-seal-code"><code class="cgss-seal-shortcode"></code> <button type="button" class="button cgss-seal-copy">Copy</button></p>
                            <p>Or add the <a href="<?php echo esc_url($seal_editor_url); ?>">Trust Seal block</a> in the editor.</p>
                        </div>
                    </div>
                </form>
            </div>

        </div>

    </div>
</div> 
