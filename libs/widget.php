<?php 

require_once 'suisseid.php';

class WPSUISSEID_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
				'suisseid_widget',
				'SuisseID Login Widget',
				array( 'description' => __('Shows the SuisseID Login Button', 'wpsuisseid'), )
				);
	}

	public function widget( $args, $instance ) {

		
		$admin = new WPSUISSEID_Admin(true);
		
		$request = new WPSUISSEID_Request();
		$authnRequest = $request->authnRequest();
		
		?>
		<form method="POST" action="<?php echo $admin->get_option('url'); ?>">
			<input type="hidden" name="SAMLRequest" value="<?php echo $authnRequest; ?>" />
			<input type="image" src="http://postsuisseid.ch/components/com_postlogin/assets/images/suisseid_login_button.jpg" border="0" name="submit" alt="Login" />
		</form>
		<?php 
	}
}

add_action( 'widgets_init', create_function( '', 'register_widget( "WPSUISSEID_Widget" );') );