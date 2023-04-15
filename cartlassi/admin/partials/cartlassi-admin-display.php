<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Cartlassi
 * @subpackage Cartlassi/admin/partials
 */

function admin_notice_no_payout_method() {
?>
    <div class="notice notice-warning">
    <p>We're able to use your shop's abandoned carts data to generate extra revenue for you. However, until you add a payout method, we can't really pay you, can we? Please <a href="/admin.php?page=cartlassi&tab=billing">add a payout method</p>
    </div>
<?php    
}
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

