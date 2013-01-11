<?php
// create custom plugin settings menu
require_once 'xml2array.php';

class WPSUISSEID_Admin {

	public $settings_group = "wp-suisseid";

	function __construct($helper = false) {
		if (!$helper) add_action('admin_menu', array(&$this, 'create_menu'));
		
		add_action('wp_authenticate',array(&$this, 'suisseid_login'));
	}

	function create_menu() {
		add_options_page( 'SuisseID Settings', 'SuisseID Settings', 'manage_options', 'wpsuisseidsettings', array(&$this,'settings_page'));

		//call register settings function
		add_action( 'admin_init', array(&$this, 'register_settings') );
	}
	
	function suisseid_login () {
		if (!array_key_exists('SAMLResponse', $_POST)) return;
		$saml = base64_decode($_POST['SAMLResponse']);
		$response = xml2array($saml);
		$status = $response['samlp:Response']['samlp:Status']['samlp:StatusCode_attr']['Value'];
		if ( strtolower($status) == 'urn:oasis:names:tc:saml:2.0:status:success') {
			// LOGIN
			$attributes = $response['samlp:Response']['saml:Assertion']['saml:AttributeStatement']['saml:Attribute'];
			$email = $attributes[0]['saml:AttributeValue'];
			if (!$email) return;
			$user = get_user_by('email', $email);
			// Create User
			if (!$user) {
				// Create the User
				$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
				print $random_password;
				wp_create_user( $email, $random_password, $email );
				
				$user = get_user_by('email', $email);
			}
			
			if ( !is_user_logged_in() ) {
				print $user->ID;
				wp_set_current_user( $user->ID, $email );
				wp_set_auth_cookie( $user->ID );
				do_action( 'wp_login', $email );
				wp_redirect( home_url() );
				exit;
			}
		} 
		exit;
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
	<h3>General Settings</h3>
	<p>Settings for a basic operation</p>
	<form method="post" action="options.php">
		<?php settings_fields( $this->settings_group ); ?>
		<?php $options = get_option( 'IDP' ); ?>

		<?php //do_settings( $this->settings_group ); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">SAML SSO Endpoint URL</th>
				<td><input type="text" name="IDP[url]"
					value="<?php echo $options['url']; ?>" />
					<p class="description">The SAML Endpoint see 
					<a href="http://postsuisseid.ch/de/support/techdoc/371-integratoren-identity-provider-idp-integration" target="_new">Integration</a> 
					or <a href="http://postsuisseid.ch/de/support/techdoc/370-integratoren-identity-provider-idp-produktion" target="_new">Productioin</a>.</p>
					</td>
			</tr>
			<tr valign="top">
				<th scope="row">Service Provider Name</th>
				<td><input type="text" name="IDP[spname]"
					value="<?php echo $options['spname']; ?>" />
					<p class="description">It may be required that you have to <a href="http://postsuisseid.ch/de/component/rsform/form/14-idp-service-provider-registration" target="_new">register</a> your SP.</p>
					</td>
			</tr>
		</table>
		<h3>Requested Attributes (optional)</h3>
		<p>Select de Attributes you like to receive from the IDP</p>
		<table class="form-table">
			<?php 
			foreach ($attributes->getAttributes() as $attribute => $data) {
				if($attribute == "email") continue;
				?>
			<tr valign="top">
				<th scope="row"><?php echo $attribute; ?></th>
				<td><input name="IDP[attribute_<?php echo $attribute; ?>]"
					type="checkbox" value="1"
					<?php checked('1', $options["attribute_" . $attribute]);?> /></td>
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