<?php
// create custom plugin settings menu

class WPSUISSEID_Admin {
	
	public $settings_group = "wp-suisseid";
	
	function __construct($helper = false) {
		if (!$helper) add_action('admin_menu', array(&$this, 'create_menu'));
	}
	
	function create_menu() {
		add_options_page( 'SuisseID Settings', 'SuisseID Settings', 'manage_options', 'wpsuisseidsettings', array(&$this,'settings_page'));
		
		//call register settings function
		add_action( 'admin_init', array(&$this, 'register_settings') );
	}
	
	
	function register_settings() {
		//register our settings
		register_setting( $this->settings_group, 'IDP' );
	}
	
	function get_option($name) {
		$options = get_option('IDP');
		return $options[$name];
	}
	
	function settings_page() {
		// Example request
		$request = new WPSUISSEID_Request();
		$authnRequest = htmlspecialchars(base64_decode($request->authnRequest()));
		$attributes = new WPSUISSEID_Attributes();
		?>
	<div class="wrap">
	<h2>Suisse ID Login Settings</h2>
	
	<form method="post" action="options.php">
	    <?php settings_fields( $this->settings_group ); ?>
	    <?php $options = get_option( 'IDP' ); ?>
	    
	    <?php //do_settings( $this->settings_group ); ?>
	    <table class="form-table">
	        <tr valign="top">
	        <th scope="row">SAML SSO Endpoint URL</th>
	        <td><input type="text" name="IDP[url]" value="<?php echo $options['url']; ?>" /></td>
	        </tr>
	        <tr valign="top">
	        <th scope="row">Service Provider Name</th>
	        <td><input type="text" name="IDP[spname]" value="<?php echo $options['spname']; ?>" /></td>
	        </tr>
	        <?php 
	        foreach ($attributes->getAttributes() as $attribute => $data) {
				?>
				<tr valign="top">
	        	<th scope="row"><?php echo $attribute; ?></th>
				<td><input name="IDP[attribute_<?php echo $attribute; ?>]" type="checkbox" value="1" <?php checked('1', $options["attribute_" . $attribute]);?> /></td>
				</tr>
				<?php
			}
	        ?>
	    </table>
	    
	    <?php submit_button(); ?>
	</form>
		<h3>Example SAML 2.0 Request</h3>
		<pre>
		<?php echo $authnRequest; ?>
		</pre>
	</div>
	<?php 
	}
}