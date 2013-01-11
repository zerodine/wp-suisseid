<?php
/*
 * Plugin Name: WP-SuisseID
 * Plugin URI:  http://codaero.com
 * Description: This plugins adds an suisseid badge as a widget which allows a SAML2 Authentication with a SuisseID
 * Version:		0.1
 * Author:		Thomas Spycher
 * Author URI:	https://tspycher.com
 */

require_once 'libs/suisseid.php';
require_once 'libs/adminstuff.php';
require_once 'libs/widget.php';

$_suisseid_admin = new WPSUISSEID_Admin();
