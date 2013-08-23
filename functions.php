<?php

add_action( 'after_setup_theme', 'haxor_theme_setup' );


function haxor_theme_setup() {
	if (get_option('haxor_themed_admin_bar', true)) {
		add_theme_support( 'admin-bar', array( 'callback' => 'haxor_admin_bar_callback') );
	}
	add_theme_support( 'menus' );
}

function haxor_admin_bar_callback() { ?>
    <link rel="stylesheet" id="haxor_admin_bar"  href="<?php echo get_bloginfo( 'stylesheet_directory' ) . '/admin-bar.css'; ?>" type="text/css" media="all" />
<?php
}

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
		$themed_admin_bar = esc_attr($_POST['themed_admin_bar']);
		update_option("haxor_themed_admin_bar", $themed_admin_bar);
		?>
		<div id="message" class="updated">Settings saved
		<?php
		
		?></div>
	    <?php
	}
	
	$menu_enable = get_option("haxor_header_menu_enable", true);
	$themed_admin_bar = get_option("haxor_themed_admin_bar", true);
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
	      <tr valgin="top">
	        <th scope="row">
	          Admin bar:
	        </th>
	        <td>
	          <label for="themed_admin_bar">
	            <input type="checkbox" name="themed_admin_bar" id="themed_admin_bar" value="true" <?php if ($themed_admin_bar) {echo 'checked="checked"';}?>/>
	            H4x0r-themed admin bar.
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
