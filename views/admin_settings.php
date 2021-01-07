<div class="cgss-settings-page" id="<?php echo $action; ?>_page">

    <div class="cgss-head">

        <div class="cgss-head-inside">
            <img class="cgss-head-logo" src="<?php echo esc_url( plugins_url( '../img/clone-guard-icon.png', __FILE__ ) ); ?>" alt="Logo" />
            <span class="cgss-head-text">
                <h1 class="cgss-head-title"><?php echo esc_html($title); ?></h1>
                <p class="cgss-head-subtitle">By Clone Systems, Inc.</p>
            </span>
        </div>

    </div>

    <h1 class="cgss-settings-title">Settings</h1>

    <!-- TODO -->
    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
    <div id="ajax_message" class="notice notice-success"><p>All updated.</p></div>
    <?php elseif(isset($_GET['msg']) && $_GET['msg'] == 'access'): ?>
        <div id="ajax_message" class="notice notice-error"><p>Please enter valid values for the items below. If you have entered valid values, please check the status of the CloneGuard server for maintenance.</p></div>
    <?php else: ?>
        <div id="ajax_message"></div>
    <?php endif; ?>
        
    <div class="cgss_main">

        <div class="main-inside">
            <div class="cgss-purchase">
                <h2 class="cgss-purchase-title">Step 1: Purchase a License to Secure Your Site</h2>
                <ul class="cgss-purchase-list">
                    <li class="cgss-purchase-list-item">PCI ASV Compliant Scanning</li>
                    <li class="cgss-purchase-list-item">Certified ASV Reports</li>
                    <li class="cgss-purchase-list-item">Online SAQ v3.2 Wizard</li>
                    <li class="cgss-purchase-list-item">Nothing to install</li>
                    <li class="cgss-purchase-list-item">24/7 email support</li>
                    <li class="cgss-purchase-list-item">IPv4, and IPv6</li>
                </ul>
                <a class="cgss-purchase-link" href="https://www.clone-systems.com/purchase-pci-compliance-scanning/" target="_blank">Purchase a License</a>
            </div>

            <div class="cgss-settings-token">
                <h2 class="cgss-settings-token-title">Step 2: Gather your Token & Key</h2>
                <img class="cgss-settings-token-img" src="<?php echo esc_url( plugins_url( '../img/settings-step-2.png', __FILE__ ) ); ?>" alt="Logo" />
            </div>

            <form class="cgss-settings-form ajax_form" action="admin-ajax.php" method="post">
                <h2 class="cgss-settings-form-title">Step 3: Input Token & Key below</h2>

                <?php wp_nonce_field( $action ); ?>
                <input name="action" type="hidden" value="<?php echo $action; ?>" />

                <table class="form-table">
                    <tbody>
                        <tr class="form-field">
                            <th scope="row"><label>User Token:</label></th>
                            <td>
                                <input class="settings-form-input" type="password" name="user_token" value="<?php echo esc_attr($user_token); ?>" placeholder="">
                                <p class="description">You can find your User Token in your account.</p>
                            </td>
                        </tr>
                        <tr class="form-field">
                            <th scope="row"><label>API Key:</label></th>
                            <td>
                                <input class="settings-form-input" type="password" name="api_key" value="<?php echo esc_attr($api_key); ?>" placeholder="">
                                <p class="description">You can find your API Key in your account.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p class="submit">
                    <input id="submit-btn" type="submit" class="button button-primary" value="Submit">
                    <span class="spinner"></span>
                </p>

            </form> 

        </div>

    </div>
</div> 
