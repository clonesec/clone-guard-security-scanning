<div class="wrap admin_setting_basic_page" id="<?php echo $action; ?>_page">
	<h1 class="wp-heading-inline"><?php echo esc_html($title); ?></h1>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
        <div id="ajax_message" class="notice notice-success"><p>All updated.</p></div>
    <?php elseif(isset($_GET['msg']) && $_GET['msg'] == 'access'): ?>
        <div id="ajax_message" class="notice notice-error"><p>Please enter valid values for the items below. If you have entered valid values, please check the status of the Clone Guard server for maintenance.</p></div>
    <?php else: ?>
        <div id="ajax_message"></div>
    <?php endif; ?>
        
    <div class="cgss_flex">
            <form action="admin-ajax.php" method="post" class="ajax_form">
                <?php wp_nonce_field( $action ); ?>
            <input name="action" type="hidden" value="<?php echo $action; ?>" />

            <table class="form-table">
                <tbody>
                    <tr class="form-field">
                        <th scope="row"><label>User Token:</label></th>
                        <td>
                            <input type="text" name="user_token" value="<?php echo esc_attr($user_token); ?>" placeholder="">
                            <p class="description">You can find your User Token in your account.</p>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label>API Key:</label></th>
                        <td>
                            <input type="text" name="api_key" value="<?php echo esc_attr($api_key); ?>" placeholder="">
                            <p class="description">You can find your API Key in your account.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
                            
            <p class="submit">
                <input type="submit" class="button button-primary" value="Submit">
                <span class="spinner"></span>
            </p>
        </form> 
        <div>
            <div class="purchase">
                <h2>Purchase a License to Secure Your Site</h2>
                <ul>
                    <li>PCI ASV Compliant Scanning</li>
                    <li>Certified ASV Reports</li>
                    <li>Online SAQ v3.2 Wizard</li>
                    <li>Nothing to install</li>
                    <li>24/7 email support</li>
                    <li>IPv4, and IPv6</li>
                </ul>
                <a href="https://www.clone-systems.com/purchase-pci-compliance-scanning/" target="_blank">Purchase a License</a>
            </div>
        </div>
    </div>
</div> 
