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

function haxor_get_login_slug() {
	return 'login';
}

function haxor_get_login_link() {
	if (get_option("haxor_login_page_ensure", 1) >= 2) {
		haxor_create_login_page();
	}
	$page = get_page_by_path(haxor_get_login_slug());
	if ($page && $page->post_status == 'publish') {
		$parsed = parse_url(get_permalink($page->ID));
	} else {
		$link = get_template_directory_uri() . '/page-' . haxor_get_login_slug() . '.php';
		$parsed = parse_url($link);
	}
	$link = $parsed['path'];
	$home_url = home_url();
	$home_url = parse_url($home_url);
	if (isset($home_url['path'])) {
		$home_url = $home_url['path'];
		$link = preg_replace("|^$home_url|i", '', $link);
	}
	if ($parsed['query']) {
		$link .= '?' . $parsed['query'];
	}
	return $link;
}

function haxor_loginout_url_filter($url) {
	$parsed = parse_url($url);
	$new_url = site_url(haxor_get_login_link(), 'login');
	if (!empty($parsed['query'])) {
		if (false === strpos($new_url, '?')) {
			$new_url .= '?';
		} else {
			$new_url .= '&';
		}
		$new_url .= $parsed['query'];
	}
	return $new_url;
}

function haxor_register_link_filter($link) {
	$regex = '/"(.*:\/\/.*\/.*\?.*)"/i';
	if (preg_match($regex, $link, $matches)) {
		$url = haxor_loginout_url_filter($matches[1]);
		return preg_replace($regex, '"' . $url . '"', $link);
	} else {
		return $link;
	}
}

function haxor_pages_exclude_filter($exclude) {
	$page = get_page_by_path(haxor_get_login_slug());
	if ($page) {
		$exclude[] = $page->ID;
	}
	return $exclude;
}

if (get_option("haxor_login_enable")) {
	add_filter( 'login_url',  haxor_loginout_url_filter, 10, 1);
	add_filter( 'logout_url', haxor_loginout_url_filter, 10, 1);
	add_filter( 'lostpassword_url', haxor_loginout_url_filter, 10, 1);
	add_filter( 'register', haxor_register_link_filter, 10, 1);
	if (get_option("haxor_login_page_hide", true)) {
		add_filter( 'wp_list_pages_excludes', haxor_pages_exclude_filter, 10, 1);
	}
}

function haxor_content_filter($content) {
	if (get_option("haxor_replace_tags") >= 2 || $GLOBALS['post']->post_name == haxor_get_login_slug()) {
		$content = str_replace('[blogname]', get_bloginfo('name'), $content);
		$content = str_replace('[version]', get_bloginfo('version'), $content);
	}
	return $content;
}

if (get_option("haxor_replace_tags")) {
	add_filter( 'the_content', haxor_content_filter, 10, 1);
}

function get_post_by_path($path) {
	$post_types = get_post_types();
	foreach($post_types as $type) {
		$post = get_page_by_path($path, OBJECT, $type);
		if ($post) {
			return $post;
		}
	}
	return null;
}

function haxor_create_login_page() {
	$page = get_page_by_path(haxor_get_login_slug());
	if (!$page) {
		$page = array(); //get_default_post_to_edit('page');
		$page['post_type'] = 'page';
		$page['post_name'] = haxor_get_login_slug();
		$page['post_title'] = 'Login';
		$page['post_stauts'] = 'publish';
		$page['post_content'] = 'WordPress <a href="' . site_url('/') . '">' . get_bloginfo('name') . '</a> tty1
<a href="#login">Login</a> | <a href="#lostpassword">Lost your password?</a> | <a href="#register">Register</a>';
		$id = wp_insert_post($page);
		if (!$id) return 4;
		$page = get_post($id);
		if ($page->post_name != haxor_get_login_slug()) return 3;
	} else {
    	$id = $page->ID;
	}
	if (get_post_status($id) != 'publish') {
		wp_publish_post($id);
	}
	if (get_post_status($id) != 'publish') return 1;
	return 0;
}

function haxor_theme_settings_menu() {
	if (!current_user_can('manage_options')) {
		wp_die('You do not have sufficient permissions to access this page.');
	}
	if (isset($_POST['update_settings'])) {
		$login_prefix = esc_attr($_POST['login_prefix']);
		update_option("haxor_login_prefix", $login_prefix);
		$replace_tags = esc_attr($_POST['replace_tags']);
		update_option("haxor_replace_tags", $replace_tags);
		$login_enable = esc_attr($_POST['login_enable']);
		update_option("haxor_login_enable", $login_enable);
		$menu_enable = esc_attr($_POST['menu_enable']);
		update_option("haxor_header_menu_enable", $menu_enable);
		$themed_admin_bar = esc_attr($_POST['themed_admin_bar']);
		update_option("haxor_themed_admin_bar", $themed_admin_bar);
		$login_page_hide = esc_attr($_POST['login_page_hide']);
		update_option("haxor_login_page_hide", $login_page_hide);
		$login_page_ensure = esc_attr($_POST['login_page_ensure']);
		update_option("haxor_login_page_ensure", $login_page_ensure);
		?>
		<div id="message" class="updated">Settings saved
		<?php
		
		if ($login_enable && $login_page_ensure > 0) {
			$err = haxor_create_login_page();
		}
		if (0 === $err) {
			echo '<br/>The login page exists and is published.';
		}
		?></div>
	    <?php
		if ($err) {
			switch ($err) {
			case 1:
				$err_msg = 'Unable to publish the login page.';
				break;
			case 3:
				$err_msg = "Created login page, but couldn't set its slug to '" . haxor_get_login_slug() . "'";
				break;
			case 4:
				$err_msg = 'Unable to create login page. Pleas create a page with slug: ' . haxor_get_login_slug();
				break;
			}
			?>
			<div class="error"><strong>ERROR:</strong> <?php echo $err_msg; ?></div>
			<?php
		}
	}
	
	$login_prefix = get_option("haxor_login_prefix", get_bloginfo('name') . ' ');
	$replace_tags = get_option("haxor_replace_tags", 0);
	$login_enable = get_option("haxor_login_enable");
	$menu_enable = get_option("haxor_header_menu_enable", true);
	$themed_admin_bar = get_option("haxor_themed_admin_bar", true);
	$login_page_hide = get_option("haxor_login_page_hide", true);
	$login_page_ensure = get_option("haxor_login_page_ensure", 1);
	
	if ($login_enable) {
		$page = get_page_by_path(haxor_get_login_slug());
		if (!$page) {
			?>
			<div class="error"><strong>WARNING:</strong> The login page doesn't exist. Please create a page with slug: <?php echo haxor_get_login_slug(); ?> </div>
			<?php
		} elseif ($page->post_status != 'publish') {
			?>
			<div class="error" style="border-color:rgb(230, 219, 85);"><strong>WARNING:</strong> The login page isn't published. Please publish it <a href="<?php echo admin_url('post.php?post=' . $page->ID . '&action=edit'); ?>">here</a>.</div>
			<?php
		}
	}
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
	    <h3>Custom login page</h3>
	    <table class="form-table">
	      <tr valgin="top">
	        <th scope="row">
	          Enable:
	        </th>
	        <td>
	          <label for="login_enable">
	            <input type="checkbox" name="login_enable" id="login_enable" value="true" <?php if ($login_enable) {echo 'checked="checked"';}?>/>
	            Enable h4x0r TTY-like login
	          </label>
	        </td>
	      </tr>
	      <tr valgin="top">
	        <th scope="row">
	          <label for="login_prefix">Login prompt prefix:</label>
	        </th>
	        <td>
	          <input type="text" name="login_prefix" id="login_prefix" value="<?php echo $login_prefix;?>"/>
	        </td>
	      </tr>
	      <tr valgin="top">
	        <th scope="row">
	          Ensure login page exists and is published:
	        </th>
	        <td>
	          <label for="login_page_ensure_always">
	            <input type="radio" name="login_page_ensure" id="login_page_ensure_always" value="2" <?php if ($login_page_ensure >= 2) {echo 'checked="checked"';}?>/>
	            Every time a link to the login page is displayed or H4x0r settings are saved
	          </label><br/>
	          <label for="login_page_ensure_settings">
	            <input type="radio" name="login_page_ensure" id="login_page_ensure_settings" value="1" <?php if ($login_page_ensure == 1) {echo 'checked="checked"';}?>/>
	            Every time H4xor settings are saved
	          </label><br/>
	          <label for="login_page_ensure_never">
	            <input type="radio" name="login_page_ensure" id="login_page_ensure_never" value="0" <?php if ($login_page_ensure <= 0) {echo 'checked="checked"';}?>/>
	            Never
	          </label>
	        </td>
	      </tr>
	      <tr valgin="top">
	        <th scope="row">
	          Visibility:
	        </th>
	        <td>
	          <label for="login_page_hide">
	            <input type="checkbox" name="login_page_hide" id="login_page_hide" value="true" <?php if ($login_page_hide) {echo 'checked="checked"';}?>/>
	            Hide h4x0r login page from page listing on sidebar and in menus.
	          </label>
	        </td>
	      </tr>
	      <tr valgin="top">
	        <th scope="row">
	          Additional content codes:
	        </th>
	        <td>
	          <label for="replace_tags_everywhere">
	            <input type="radio" name="replace_tags" id="replace_tags_everywhere" value="2" <?php if ($replace_tags >= 2) {echo 'checked="checked"';}?>/>
	            Everywhere
	          </label><br/>
	          <label for="replace_tags_login">
	            <input type="radio" name="replace_tags" id="replace_tags_login" value="1" <?php if ($replace_tags == 1) {echo 'checked="checked"';}?>/>
	            Only on the login page
	          </label><br/>
	          <label for="replace_tags_no">
	            <input type="radio" name="replace_tags" id="replace_tags_no" value="0" <?php if ($replace_tags <= 0) {echo 'checked="checked"';}?>/>
	            Nowhere
	          </label>
	          <p class="description">Replace [version] and [blogname] with Wordpress version and blog name.</p>
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
