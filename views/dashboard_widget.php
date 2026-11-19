<?php defined('ABSPATH') || exit; ?>
<?php if($state == 'setup'): ?>
    <div class="cgss-dw cgss-dw-message">
        <img class="cgss-dw-logo" src="<?php echo esc_url( plugins_url( '../img/cloneguard-icon.svg', __FILE__ ) ); ?>" width="40" height="40" alt="" />
        <p>Connect your Clone Systems account to see your scan status and latest report here.</p>
        <p><a class="button button-primary" href="<?php echo esc_url($settings_url); ?>">Connect your account</a></p>
    </div>

<?php elseif($state == 'loading'): ?>
    <div id="cgss-dashboard-widget" class="cgss-dw" data-action="<?php echo esc_attr($this->key_ . 'dashboard_widget'); ?>" data-nonce="<?php echo esc_attr($nonce); ?>" aria-live="polite">
        <p class="cgss-dw-loading"><span class="spinner is-active"></span>Loading your scan status&hellip;</p>
    </div>

<?php elseif($state == 'access'): ?>
    <div class="cgss-dw-message">
        <p>The scanning portal did not accept the saved User Token and Key.</p>
        <p><a class="button" href="<?php echo esc_url($settings_url); ?>">Check your settings</a></p>
    </div>

<?php elseif($state == 'error'): ?>
    <div class="cgss-dw-message">
        <p><?php echo esc_html($error); ?></p>
    </div>

<?php elseif($state == 'empty'): ?>
    <div class="cgss-dw-message">
        <?php if($product): ?><p class="cgss-dw-product"><?php echo esc_html($product); ?></p><?php endif; ?>
        <p>No reports yet. Run your first scan to see your status here.</p>
        <p><a class="button button-primary" href="<?php echo esc_url($this->adminLink('scans', 'scan-create')); ?>">Create a scan</a></p>
    </div>

<?php else: ?>
    <?php if($product): ?><p class="cgss-dw-product"><?php echo esc_html($product); ?></p><?php endif; ?>

    <div class="cgss-dw-status <?php echo $pass ? 'is-pass' : 'is-fail'; ?>">
        <img src="<?php echo esc_url( plugins_url( $pass ? '../img/status-pass.svg' : '../img/status-warning.svg', __FILE__ ) ); ?>" width="44" height="44" alt="" />
        <div>
            <?php if($pass): ?>
                <p class="cgss-dw-headline"><?php echo $app_type == 'pci' ? 'Your site is PCI compliant' : 'No high or medium vulnerabilities'; ?></p>
                <p class="cgss-dw-sub">Based on the latest report of each scan.</p>
            <?php else: ?>
                <p class="cgss-dw-headline"><?php echo esc_html(number_format_i18n($vulnerabilities_sum) . ($vulnerabilities_sum == 1 ? ' vulnerability to fix' : ' vulnerabilities to fix')); ?></p>
                <p class="cgss-dw-sub"><?php echo $app_type == 'pci' ? 'Fix the high and medium findings to meet PCI DSS ASV scanning requirement 11.2.2.' : 'High and medium findings in the latest report of each scan.'; ?></p>
                <p><a href="<?php echo esc_url($this->adminLink('vulnerabilities')); ?>"><?php echo $app_type == 'penetration' ? 'View findings' : 'View vulnerabilities'; ?></a></p>
            <?php endif; ?>
            <?php if(!empty($summary['incomplete'])): ?>
                <p class="cgss-dw-sub">Some reports could not be loaded in time, so this may be incomplete.</p>
            <?php endif; ?>
        </div>
    </div>

    <ul class="cgss-dw-list">
        <li>
            <span class="cgss-dw-label">Next scan</span>
            <span class="cgss-dw-value">
                <?php if(!empty($summary['next_scan'])): ?>
                    <?php echo esc_html($this->siteDate($summary['next_scan']['time'])); ?>
                    <?php if($summary['next_scan']['name'] !== ''): ?><span class="cgss-dw-muted"><?php echo esc_html($summary['next_scan']['name']); ?></span><?php endif; ?>
                <?php else: ?>
                    None scheduled. <a href="<?php echo esc_url($this->adminLink('scans')); ?>">Schedule a scan</a>
                <?php endif; ?>
            </span>
        </li>
        <li>
            <span class="cgss-dw-label">Latest report</span>
            <span class="cgss-dw-value">
                <a href="<?php echo esc_url($this->adminLink('reports', 'report-view', $latest_id, 1)); ?>"><?php echo esc_html($report_date !== '' ? $report_date : 'View report'); ?></a>
                <?php if(!empty($latest['compliance'])): ?>
                    <span class="cgss-dw-pill is-pass">Pass</span>
                <?php else: ?>
                    <span class="cgss-dw-pill is-fail">Fail</span>
                <?php endif; ?>
                <?php if(!empty($latest['task']['name'])): ?><span class="cgss-dw-muted"><?php echo esc_html($latest['task']['name']); ?></span><?php endif; ?>
            </span>
        </li>
    </ul>

    <p class="cgss-dw-footer">
        <a href="<?php echo esc_url($this->adminLink('overview')); ?>">Open CloneGuard Security</a>
        <?php $cgss_portal = $this->portalPageUrl(); if($cgss_portal): ?>
            <span class="cgss-dw-sep" aria-hidden="true">|</span>
            <a href="<?php echo esc_url($cgss_portal); ?>" target="_blank" rel="noopener">Open the portal<span class="screen-reader-text"> (opens in a new tab)</span></a>
        <?php endif; ?>
    </p>
<?php endif; ?>
