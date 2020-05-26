<?php

/**
* The plugin bootstrap file
*
* This file is read by WordPress to generate the plugin information in the plugin
* admin area. This file also includes all of the dependencies used by the plugin,
* registers the activation and deactivation functions, and defines a function
* that starts the plugin.
*
* @link              https://marcointhemiddle.com
* @since             1.0.0
* @package           Submit your feedback
*
* @wordpress-plugin
* Plugin Name:       New Email track - Netrack (Submit your feedback)
* Description:       Track a feedback through email
* Version:           1.0.0
* Author:            Marco Maffei
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
*/


if ( ! defined( 'WPINC' ) ) die;
if ( ! defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'Netrack' ) ) {

	class Netrack {

		protected $NetrackTable;
		protected $wpdb;

		public function __construct() {

			define( 'Netrack_ROOTDIR', plugin_dir_path(__FILE__) );

			register_activation_hook(__FILE__, array($this ,'NetrackInstall') );
			register_deactivation_hook( __FILE__, array($this ,'NetrackUninstall') );

			add_shortcode('Netrack', array( $this,'Netrack_render') );
			add_shortcode('Netrack_rest_route', array( $this,'Netrack_rest_route') );

			add_action( 'wp_ajax_nopriv_Netrack_send', array( $this, 'Netrack_send' ) );
			add_action( 'wp_ajax_Netrack_send', array( $this, 'Netrack_send' ) );
			add_action( 'admin_menu' , array( $this,'Netrack_Menu') );
			add_action( 'rest_api_init',  array( $this,'my_register_route' ) );
			add_action( 'wp_enqueue_scripts' , array( $this,'NetrackScript') );

			global $wpdb;
			$this->wpdb = $wpdb;
			$this->NetrackTable = $this->wpdb->prefix . "Netrack";

		}

		public function NetrackScript() {
			
			if( ! wp_script_is( 'parsley', 'enqueued' ) ) {
				wp_enqueue_script( 'parsley', plugins_url('js/parsley.min.js', __FILE__ ), array(), null, true );
			}
			global $wp_scripts;
			

			if(is_admin()) return;
			$wp_scripts->registered['jquery-core']->src = 'https://code.jquery.com/jquery-3.5.1.min.js';
			$wp_scripts->registered['jquery']->deps = ['jquery-core'];

		}

		public function NetrackInstall() {

			$charset_collate = $this->wpdb->get_charset_collate();

			$sql = "CREATE TABLE $this->NetrackTable (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`fname` varchar(255) NOT NULL,
			`lname` varchar(255) NOT NULL,
			`email` varchar(255) NOT NULL,
			`subject` varchar(255) NOT NULL,
			`message` LONGTEXT NOT NULL,
			PRIMARY KEY (`id`) ) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta($sql);

		}

		public function NetrackUninstall() {

			$sql = "DROP TABLE IF EXISTS $this->NetrackTable";
			$this->wpdb->query($sql);

		}

		public function Netrack_render() {

			wp_enqueue_script( 'ajax', plugins_url('js/ajax.js', __FILE__ ) );

			wp_localize_script( 'ajax', 'settings', array(
				'ajaxurl'    => admin_url( 'admin-ajax.php' ),
				'plugin_dir' => plugins_url(),
				'ajax_nonce' => wp_create_nonce( "plugin_nonce" )
			) );

			$atts = shortcode_atts( array(
				'data'=>'0'
			) , $atts);

			$content =  (empty($content))? " " : $content;

			extract($atts);
			ob_start();

			include( dirname(__FILE__) . '/Netrack_render.php' );

			return ob_get_clean();

		}


		public function Netrack_send() {

			$error = [];

			$fName = isset($_POST["fName"]) ? $name = $_POST["fName"] : $error['fname'] = 'please, insert your name';
			$lName = isset($_POST["lName"]) ? $name = $_POST["lName"] : $error['lname'] = 'please, insert your last name';
			$subject = isset($_POST["subject"]) ? $name = $_POST["subject"] : $error['subject'] = 'please, insert your subject';
			$email = isset($_POST["email"]) ? $name = $_POST["email"] : $error['email'] = 'please, insert your email';
			$message = isset($_POST["message"]) ? $name = $_POST["message"] : $error['message'] = 'please, insert your message';
			$submit = $_POST["submit_form"];
			$honeypot = $_POST["honeypot"];

			$exists = $this->wpdb->get_var( $this->wpdb->prepare(
				"SELECT COUNT(*) FROM $this->NetrackTable WHERE email = %s", $email
			) );
			
			if (isset($_POST['submit_form']) && !$exists && !$honeypot && empty( $error )) {

				$this->wpdb->insert(
					$this->NetrackTable,
					array(
						'fName'  => $fName,
						'lName'  => $lName,
						'subject'  => $subject,
						'email'  => $email,
						'message'  => $message,
					),
					array( '%s', '%s', '%s', '%s', '%s' )
				);
				$message =[ true, "Thank you for sending us your feedback"];

			} else {

				$message = [ false, "The item inserted is already present on the database, please try again!"];
			}
			echo json_encode($message);
			die();

		}
		
		public function Netrack_Menu() {

			add_menu_page('Feedback emails ',
				'Feedback emails Crud',
				'manage_options',
				'Netrack_Menu_List',
				array($this, 'Netrack_Menu_List')
			);

		}

		public function Netrack_Menu_List() {

			wp_register_style( 'custom_wp_admin_css', plugins_url('/css/bootstrap.css', __FILE__ ), false, '1.0.0' );
			wp_enqueue_style( 'custom_wp_admin_css' );
			add_action( 'admin_enqueue_scripts', 'wpStyle' );
			
			
			$rows = $this->wpdb->get_results("SELECT * from $this->NetrackTable");

			require_once(Netrack_ROOTDIR . 'Netrack_Menu_List.php');

		}

		public function my_route_get( $request ) {

			if (isset($_GET['page'])) {
				$page = $_GET['page'];
			} else {
				$page = 1;
			}
			
			$no_of_records_per_page = 3;
			$offset = ($page-1) * $no_of_records_per_page; 
			$total_pages_sql = $this->wpdb->get_var( $this->wpdb->prepare(
				"SELECT COUNT(*) FROM $this->NetrackTable"
			) );;
			$total_pages = ceil($total_pages_sql / $no_of_records_per_page);
			
			$rows = $this->wpdb->get_results("SELECT * FROM $this->NetrackTable LIMIT $offset, $no_of_records_per_page");
			$post_data = [];

			foreach ($rows as $value ) {
				
				$post_data[] = array(
					'id' => $value->id,
					'fname' => $value->fname,
					'lname' => $value->lname,
					'email' => $value->email,
					'subject' => $value->subject,
					'message' => $value->message,
				);
			}
			
			return $post_data;
		}

		public function my_register_route() {

			register_rest_route( 'my-letter', 'my-letter-get', array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'callback' => array($this, 'my_route_get'),
					'args' => array(
						'page' => array (
							'required' => false
						),
					),
					'permission_callback' => function() {
						return current_user_can( 'edit_posts' );
					},
				)
			) );
		}

		public function Netrack_rest_route() {

			wp_register_script('front-main', plugins_url('js/rest.js' , __FILE__ ), '', '', true );
			wp_enqueue_script('front-main');

			wp_enqueue_style( 'mainrest', plugins_url( '/css/main.css', __FILE__ ) );

			$no_of_records_per_page = 3;
			$offset = ($page-1) * $no_of_records_per_page; 
			$total_pages_sql = $this->wpdb->get_var( $this->wpdb->prepare(
				"SELECT COUNT(*) FROM $this->NetrackTable"
			) );;
			$total_pages = ceil($total_pages_sql / $no_of_records_per_page);

			wp_localize_script( 'front-main', 'wpApiSettings', array(
				'root' => esc_url_raw( rest_url() ),
				'nonce' => wp_create_nonce( 'wp_rest' ),
				'page_num' => $last_chunk,
				'total' => $total_pages
			) );

			$atts = shortcode_atts( array(
				'data'=>'0'
			) , $atts);

			$content =  (empty($content))? " " : $content;

			extract($atts);
			ob_start();

			include( dirname(__FILE__) . '/Netrack_route.php' );

			return ob_get_clean();
		}

	}

}

global $Netrack;
$Netrack = new Netrack();
