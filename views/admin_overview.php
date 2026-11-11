<div class="cgss-overview-page" id="<?php echo $action; ?>_page">

    <div class="cgss-head">

        <div class="cgss-head-inside">
            <img class="cgss-head-logo" src="<?php echo esc_url( plugins_url( '../img/clone-guard-icon.png', __FILE__ ) ); ?>" alt="Logo" />
            <span class="cgss-head-text">
                <h1 class="cgss-head-title"><?php echo esc_html($title); ?></h1>
                <span class="cgss-head-subtitle">By <a href="https://www.clone-systems.com/" target="_blank">Clone Systems, Inc.</a></span>
            </span>
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

        <?php if(!empty($reports['reports'])) : ?>
            <div class="overview-row">
                <div class="section-general overview-row-box">
                    <div class="general-wrapper">
                    <?php if ($compliance_status == 'pass') : ?>
                        <span class="general-icon-container">
                            <?php if ($app_type == 'pci' || $app_type == 'penetration'): ?>
                                <img class="general-icon" src="<?php echo esc_url( plugins_url( '../img/vuln-success.png', __FILE__ ) ); ?>" alt="Success" />
                            <?php elseif ($app_type == 'vrms') : ?>
                                <img class="general-icon" src="<?php echo esc_url( plugins_url( '../img/vrms-success.png', __FILE__ ) ); ?>" alt="Success" style="width: 135px;"/>
                            <?php endif; ?>
                        </span>

                        <span class="general-text">
                            <?php if ($app_type == 'pci'): ?>
                                <h1 class="general-h1 success-pci-color ">Your Website is PCI Compliant</h1>
                                <p>You are in adherence with the external PCI. DSS ASV Scanning requirement 11.2.2</p>
                            <?php elseif ($app_type == 'vrms') : ?>
                                <p>None of the assets scanned pose a risk to your environment.</p>
                            <?php elseif ($app_type == 'penetration') : ?>
                                <h1 class="general-h1 success-pci-color ">Vulnerabilities Not Found</h1>
                                <p>There are no High or Medium level vulnerabilities detected</p>
                            <?php endif; ?>
                        </span>

                        <span class="reports-btn-container">
                            <a class="button btn-overview" href="<?php echo $this->adminLink('scans'); ?>">Scan Now</a>
                        </span>

                    <?php else: ?>
                        <span class="general-icon-container">
                            <img class="general-icon" src="<?php echo esc_url( plugins_url( '../img/vuln-warning.png', __FILE__ ) ); ?>" alt="Warning" />
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
                                <a class="button button-primary" style="background:#FECF1B; border:none;" href="<?php echo $this->adminLink('vulnerabilities'); ?>">View Vulnerabilities</a>
                            <?php else: ?>
                                <a class="button button-primary" style="background:#FECF1B; border:none;" href="<?php echo $this->adminLink('vulnerabilities'); ?>">View Findings</a>
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
                                <a href="<?php echo $this->adminLink('reports', 'report-view', $key, $reports['current_page']); ?>">
                                    <?php echo esc_html(date('D M j Y G:i:s', strtotime($report['name']))); ?>
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
                                        echo '<span style="color:#64b450;"><span class="dashicons dashicons-yes"></span>Pass</span>';
                                    } else {
                                        echo '<span style="color:#dc3232;"><span class="dashicons dashicons-no"></span>Fail</span>';
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
                    <img style="width:300px;" src="<?php echo esc_url( plugins_url( '../img/security-engineers.png', __FILE__ ) ); ?>" />
                    <span class="reports-btn-container">
                        <a style="font-size:1.2em!important; padding: 0 60px !important;" class="button button-primary" href="<?php echo $this->adminLink('scans', 'scan-create'); ?>">
                            Add Scan
                        </a>
                    </span>
                </div>

                <div class="overview-row-box" style="flex:3; margin-left:10px; padding:50px;">
                    <div class="overview-features">
                        <h1 class="features-heading">Features</h1>
                        <div class="features-list">
                            <div class="feature">
                                <img class="feature-img" src="<?php echo esc_url( plugins_url( '../img/pci.png', __FILE__ ) ); ?>" />
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
                                <img class="feature-img" src="<?php echo esc_url( plugins_url( '../img/vrms.png', __FILE__ ) ); ?>" />
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
                                <img class="feature-img" src="<?php echo esc_url( plugins_url( '../img/penetration.png', __FILE__ ) ); ?>" />
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
                            <a class="button btn-overview p-0-35" href=https://www.clone-systems.com/report?dn=<?php echo str_replace($protocols, '', get_bloginfo('wpurl')); ?> target="_blank">View Validation Report</a>
                        <?php else: ?>
                            <button class="button btn-overview p-0-35" disabled>View Validation Report</a>
                        <?php endif; ?>
                    </span>
                </div>
                <img class="section-product-img img-report" src="<?php echo esc_url( plugins_url( '../img/report-screenshot3.png', __FILE__ ) ); ?>" />
            </div>

        </div>

        <div class="overview-row" style="margin-top:10px;">

            <div class="section-product overview-row-box">
                <img class="section-product-img img-generate" src="<?php echo esc_url( plugins_url( '../img/generate-seal-screenshot3.png', __FILE__ ) ); ?>" />
                <div class="section-product-text" style="margin-left:30px;">
                     <h1>Implement Seal</h2>
                    <p>The CloneGuard security seal is a web site emblem which indicates the site has been scanned utilizing hundreds of thousands of security checks. Customers and users visiting your site will know their purchases, and personal data are securely stored and protected against internet based threats.</p>            
                    <span class="general-btn-container">
                        <a class="button btn-overview p-0-35" href="<?php echo esc_url(admin_url('widgets.php')); ?>">Add Seal</a>
                    </span>
                </div>
            </div>

        </div>

    </div>
</div> 
