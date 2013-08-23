<?php

function haxor_register_main_menu() {
	register_nav_menu('header-menu',__( 'Header Menu' ));
}

add_action( 'init', 'haxor_register_main_menu' );

function haxor_theme_settings_menu() {
	if (!current_user_can('manage_options')) {
		wp_die('You do not have sufficient permissions to access this page.');
	}
	if (isset($_POST['update_settings'])) {
		$menu_enable = esc_attr($_POST['menu_enable']);
		update_option("haxor_header_menu_enable", $menu_enable);
		?>
		<div id="message" class="updated">Settings saved
		<?php
		
		?></div>
	    <?php
	}
	$menu_enable = get_option("haxor_header_menu_enable", true);

	?>
	<div class="wrap">
	  <?php screen_icon('themes'); ?> <h2>H4x0r Theme Settings</h2>
	  <form method="POST" action="">
	    <table class="form-table">
	      <tr valgin="top">
	        <th scope="row">
	          Menus:
	        </th>
	        <td>
	          <label for="menu_enable">
	            <input type="checkbox" name="menu_enable" id="menu_enable" value="true" <?php if ($menu_enable) {echo 'checked="checked"';}?>/>
	            Show header menu.
	          </label>
	        </td>
	      </tr>
	    </table>
	    <input type="hidden" name="update_settings" value="Y" />
	    <p>
	      <input type="submit" value="Save settings" class="button-primary"/>
	    </p>
	  </form>
	</div>
  <?php
}
	      
function haxor_setup_theme_menu() {
	add_submenu_page('themes.php', 'H4x0r Theme Settings', 'H4x0r Settings', 'manage_options', 'haxor-settings', 'haxor_theme_settings_menu');
}

add_action("admin_menu", "haxor_setup_theme_menu");

?>
