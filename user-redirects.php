<?php
/*
Plugin Name: User Redirects
Plugin URI: http://www.shubhcomputing.com
Description: Create a list of URLs that you would like to 301 redirect to another page or site.
Version: 1.00
Author: Shubh COmputing
Author URI: http://www.shubhcomputing.com/
*/

if(!class_exists(User_redirects))
{
	class User_redirects
	{

		function __construct()
		{
			add_action( 'admin_enqueue_scripts', array($this,'my_enqueue') );
		}

		/**
		 * my_enqueue function
		 * enqueue scripts and links
		 * @access public
		 * @return void
		 */

		function my_enqueue()
		{
			wp_register_style( 'custom_wp_admin_css', plugin_dir_url( __FILE__ ) .'assets/css/jquery.dataTables.min.css', false, '1.0.0' );
        	wp_enqueue_style( 'custom_wp_admin_css' );
			wp_enqueue_script('data_table_script',plugin_dir_url( __FILE__ ) .'assets/js/jquery.dataTables.min.js', array('jquery'),'1.0','true' );
			wp_enqueue_script( 'my_custom_script', plugin_dir_url( __FILE__ ) . 'script.js', array('jquery'),'1.0','true' );
		}

		/**
		 * create_menu function
		 * generate the link to the options page under settings
		 * @access public
		 * @return void
		 */

		function create_menu()
		{
			add_menu_page('User Redirects', 'User Redirects', 'administrator', __FILE__, array($this,'options_page') );
			// add_options_page('User Redirects','User Redirects','manage_options','user-redirects',array($this,'options_page'));
		}

		/**
		 * options_page function
		 * generate the options page in the wordpress admin
		 * @access public
		 * @return void
		 */

		function options_page()
		{
			$a = get_option('user_redirects');
			echo "<h1>User Redirects</h1>";	
			
			echo "<div class='user_redirects_wrapper'>";
			echo "<form method='post' action=''>";
			for ($i=0; $i < count($a); $i++) 
			{ 
				echo "<p>Older Url";
				echo "<input type='url' required name='user_redirects[".$i."][pre_link]' value='".$a[$i]['pre_link']."'>";
				echo "New Url";
				echo "<input type='url' required name='user_redirects[".$i."][next_link]' value='".$a[$i]['next_link']."'>";
				echo "<input type='button'  value='add' onclick='add_input(this,".$i.")'>";
				echo "<input type='button'  value='remove' onclick='remove_input(this)'>";
				echo "</p>";
			}
			echo "<input type='submit' value='Save'>";
			echo "</form>";
			?>
			<table id="links_table" class="display" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th>Id</th>
                <th>Prev. Link</th>
                <th>New Link</th>
                <th>Action</th>
                
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Id</th>
                <th>Prev. Link</th>
                <th>New Link</th>
                <th>Action</th>
                
            </tr>
        </tfoot>
        <tbody>
        	<?php for ($i=0; $i < count($a); $i++) { 
        		?>
        		 <tr>
        		 	<td><?php echo $i; ?></td>
                <td><?php echo $a[$i]['pre_link'] ?></td>
                <td><?php echo $a[$i]['next_link'] ?></td>
                <td>Delete</td>
                
            </tr>

        		<?php
        	} ?>
                   </tbody>
    </table>
			<?php
			echo "</div>";



		}

		/**
		 * save_redirects function
		 * save the redirects from the options page to the database
		 * @access public
		 * @param mixed $data
		 * @return void
		 */
		function save_redirects($data) {
			
				update_option('user_redirects', $_POST['user_redirects']);
		}


			/**
		 * redirect function
		 * Read the list of redirects and if the current page 
		 * is found in the list, send the visitor on her way
		 * @access public
		 * @return void
		 */
		function redirect() {
			// this is what the user asked for (strip out home portion, case insensitive)
			$current_page= $this->get_address();
			
			$redirects = get_option('user_redirects');
			for ($i=0; $i < count($redirects) ; $i++) { 
					if($current_page==$redirects[$i]['pre_link'])
			{
				// header ('HTTP/1.1 301 Moved Permanently');
						header ('Location: ' . $redirects[$i]['next_link']);
						exit();
			}
			}

		
			
						
					
		} // end funcion redirect



		/**
		 * getAddress function
		 * utility function to get the full address of the current request
		 * credit: http://www.phpro.org/examples/Get-Full-URL.html
		 * @access public
		 * @return void
		 */
		function get_address() {
			// return the full address
			return $this->get_protocol().'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		} // end function get_address
		
		function get_protocol() {
			// Set the base protocol to http
			$protocol = 'http';
			// check for https
			if ( isset( $_SERVER["HTTPS"] ) && strtolower( $_SERVER["HTTPS"] ) == "on" ) {
    			$protocol .= "s";
			}
			
			return $protocol;
		} // end function get_protocol





	}
}


$user_redirects = new User_redirects();
add_action('init', array($user_redirects,'redirect'), 1);
add_action('admin_menu',array($user_redirects,'create_menu'));
if (isset($_POST['user_redirects'])) {
		add_action('admin_init', array($user_redirects,'save_redirects'));
	}

