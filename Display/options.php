<?php
/**
 * Uninstall Post-to-Post Links II.
 *
 * This file is part of Post-to-Post Links II. Please see the
 * post-to-post-links.php file for copyright and license information.
 *
 * @author Michael Toppa
 * @version 0.2
 * @package Post2Post
 */

?>

<div class="wrap">
    <h2><?php _e("Post-to-Post Links II Settings", P2P_L10N_NAME); ?></h2>

    <?php if (strlen($message)) {
        require (P2P_DIR . '/display/include-message.php');
    } ?>

    <p><?php _e("Note much to do here, except uninstall.", P2P_L10N_NAME); ?></p>
    <h3><?php _e("Uninstall Post-to-Post Links II", P2P_L10N_NAME); ?></h3>

    <form action="<?php echo P2P_ADMIN_URL ?>" method="post">
    <input type="hidden" name="p2p_action" value="uninstall">
    <table border="0" cellspacing="3" cellpadding="3" class="form-table">
    <tr style="vertical-align: top;">
    <td nowrap="nowrap"><?php _e("Uninstall Post-to-Post Links II?", P2P_L10N_NAME); ?></td>
    <td><input type="checkbox" name="p2p_uninstall" value="y" /></td>
    <td><?php _e("Check this box if you want to completely remove Post-to-Post Links II. After uninstalling, you can then deactivate Post-to-Post Links II on your plugins management page.", P2P_L10N_NAME); ?></td>
    </tr>
    </table>

    <p class="submit"><input class="button-secondary" type="submit" name="save" value="<?php _e("Uninstall Post-to-Post Links II", P2P_L10N_NAME); ?>" onclick="return confirm('<?php _e("Are you sure you want to uninstall Post-to-Post Links II?", P2P_L10N_NAME); ?>');" /></p>
    </form>

    <h3><?php _e("Tipping: it isn't just for cows", P2P_L10N_NAME); ?></h3>

    <p><?php _e("I develop and maintain my WordPress plugins for the love of course, but a tip would be nice :-) Thanks!", P2P_L10N_NAME); ?></p>

    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_s-xclick">
    <input type="hidden" name="hosted_button_id" value="4228054">
    <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
    <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
    </form>

</div>

