<?php
/*
Plugin Name: PentryForms - Lead Capture Software
Plugin URI: https://pentryforms.com
Description: Easy-to-use lead generation, lead capture, lead recovery, form recovery, and abandoned contact form recovery software. No advanced setup required; works well with all form builder software.
Version: 1.0
Author: pentryforms.com
Author URI: https://pentryforms.com
License: GPLv2
*/ 


if( is_admin() ) {

	error_reporting(0);


    /**
     * Menu structure
     */
	function register_plgxPentryForms_page_settings() 
	{
	    //add_menu_page( $page_title,         $menu_title,      $capability,        $menu_slug,            callable $function = '',          $icon_url = '' )
		add_menu_page('PentryForms_settings', 'PentryForms', 'activate_plugins', 'PentryForms_settings', 'plgxPentryForms_page_html', plugins_url('images/', __FILE__).'logo.png');
	}
    add_action('admin_menu', 'register_plgxPentryForms_page_settings');
    

    
    
    /**
     * Pages HTML
     */

	function plgxPentryForms_page_html() 
	{
	    plgxPentryForms_Init();
        
        
	    plgxPentryForms_TemplateHeader($title = 'PentryForms - Recover Lost Leads', $subtitle = '');
        
        $admin_email = get_option('admin_email');
        
        $params = plgxPentryForms_Get_Params();
        
        // Generate API Key
        if (!isset($params['api_key']) || trim($params['api_key']) == '')
        {
            $params['api_key'] = md5(rand(1,10000).$admin_email.time());
            plgxPentryForms_Set_Params( array('api_key' => $params['api_key']) );
            plgxPentryForms_JSON_Params();
        }
        
        
        // Check for account_email
        if (!isset($params['account_email']) || trim($params['account_email']) == '')
        {
            $params['account_email'] = $admin_email;
            plgxPentryForms_Set_Params( array('account_email' => $params['account_email']) );
            plgxPentryForms_JSON_Params();
        }
        else $admin_email = $params['account_email'];
        


		// Save params
		$action = isset($_POST['action']) ? trim($_POST['action']) : '';
        
		if ($action == 'save_settings' && check_admin_referer( '4E959CA58CDE05A76AC4B2B8' ))
        {
            $params['js_code'] = isset($_POST['js_code']) ? trim($_POST['js_code']) : '';
            plgxPentryForms_Set_Params($params);
            
            // Create params json file
            plgxPentryForms_JSON_Params();
            
            $msg_data = array(
                'type' => 'ok',
                'size' => 'small',
                'content' => 'Settings saved.',
            );
            plgxPentryForms_Print_MessageBox($msg_data);
        }

        $website_url = get_site_url();
        
        $encoded_password = trim($params['encoded_password']);
	    ?>
        
        <div class="ui placeholder segment">
          <div class="ui icon header">
            <i class="bullhorn icon"></i>
            Capture real-time data from users who abandoned your form before hitting "Submit" button
          </div>

            <form method="post" action="https://dashboard.pentryforms.com/cms-login" class="ui form">
              <input class="ui primary button" type="submit" value="Open Dashboard">
              <input type="hidden" name="action" value="">
              <input type="hidden" name="cms" value="wordpress">
              <input type="hidden" name="api_key" value="<?php echo $params['api_key']; ?>">
              <input type="hidden" name="callback_url" value="<?php echo $website_url; ?>">
              <input type="hidden" name="account_email" value="<?php echo $admin_email; ?>">
              <input type="hidden" name="encoded_password" value="<?php echo $encoded_password; ?>">
            </form>
            
          <p style="text-align: center;">Your default email is <b><?php echo $admin_email; ?></b> If you don't have an account, it will be created automatically.</p>
          
          <p style="text-align: center;" class="pf_blk1"><b>OR</b></p>
          
          <p style="text-align: center;" class="pf_blk1"><a href="javascript:jQuery('#pf_login').show();jQuery('.pf_blk1').hide();">I already have an account</a></p>
          
            <form method="post" action="https://dashboard.pentryforms.com/cms-login" class="ui form" id="pf_login" style="display: none;">
            <div class="ui horizontal section divider">Login</div>
              <div class="field" style="min-width: 400px;">
                <div class="two fields">
                  <div class="field">
                    <label>Email</label>
                    <input type="text" name="account_email" placeholder="Email" value="">
                  </div>
                  <div class="field">
                    <label>Password</label>
                    <input type="password" name="account_password" placeholder="Password"  value="">
                  </div>
                </div>
              </div>



    
              <input class="ui blue button" type="submit" value="Login">
              <input type="hidden" name="action" value="login">
              <input type="hidden" name="cms" value="wordpress">
              <input type="hidden" name="api_key" value="<?php echo $params['api_key']; ?>">
              <input type="hidden" name="callback_url" value="<?php echo $website_url; ?>">
            </form>

            
        </div>
        
        <div class="ui horizontal section divider">Settings</div>
        
        
        <form method="post" action="admin.php?page=PentryForms_settings" class="ui form">
          <div class="field">
          
            <div class="ui blue mini message">
              <div class="header">
                JavaScript code
              </div>
              <p>Go into your PentryForms account and copy special JavaScript code for your website. Or use <b>Open Dashboard</b> button above for automatic installation.</p>
            </div>
            
            <label>JavaScript code</label>
            <textarea name="js_code" rows="5"><?php echo $params['js_code']; ?></textarea>
          </div>
          <button class="ui blue button" type="submit">Save</button>
          
        <?php
        wp_nonce_field( '4E959CA58CDE05A76AC4B2B8' );
        ?>
        <input type="hidden" name="action" value="save_settings"/>
        </form>
        
        
        <div class="ui horizontal section divider">How it works?</div>
        
        <div class="ui grid">
            <div class="eight wide column">
                <div class="ui large header">Conversion Rates</div>
                <br><p style="font-size: 1.33em;line-height: 1.4285em;">Improve your conversion rates by capturing real-time data from users who didn’t complete your form before hitting the submit button. Improve the success rate of contact pages, event registrations, lead generations and more. With our services you can contact visitors who didn’t complete the forms to see if they need assistance, send them friendly reminders, coupon codes or promotions. This is a unique opportunity to capture lost leads and convert them into sales.</p>
            </div>
            <div class="eight wide column"><img style="max-width: 100%;" src="<?php echo plugins_url('images/Conversion_Rates.gif', __FILE__); ?>" /></div>
        </div>
        
        <div class="ui grid">
            <div class="eight wide column"><img style="max-width: 100%;" src="<?php echo plugins_url('images/Recover_Lost_Leads.gif', __FILE__); ?>" /></div>
            <div class="eight wide column">
                <div class="ui large header">Recover Lost Leads</div>
                <br><p style="font-size: 1.33em;line-height: 1.4285em;">Every uncompleted form is a missed opportunity and a loss in revenue. According to the latest statistics, more than 50% of visitors don’t complete contact forms. This means that 2 out of 4 website visitors never return to finish their form submission. With our services you can send automated emails, schedule automatic mailings, send reminders, and more. With our unique services you can: recover lost leads, gather more user behavior information, and fix your forms and pages to convert more visitors into leads.</p>
            </div>
            
        </div>
        
        
        <div class="ui horizontal section divider">Help & Support</div>
        
        <p>We're here with the help and advice you need to bring your idea to life. When you're ready to get online, we're prepped, trained, and ready to guide you from start to success.</p>
        
        <a class="ui blue button" href="https://pentryforms.com/en/contacts/" target="_blank"><i class="envelope outline icon"></i>Send a Ticket</a>
        <a class="ui orange button" href="https://pentryforms.com/livechat.html" target="_blank"><i class="comment outline icon"></i>Live Chat</a>
        
        <br /><br />
        <p>Copyright <i class="copyright outline icon"></i> <?php echo date("Y"); ?> PentryForms.com. All Rights Reserved.</p>
        <br />


        
        
        <?php
        plgxPentryForms_BottomHeader();
        
    }
    
    

    






    /**
     * Templating
     */

	add_action( 'admin_init', 'plgxPentryForms_admin_init' );
	function plgxPentryForms_admin_init()
	{
		wp_enqueue_script( 'plgxPentryForms_LoadSemantic_js', plugins_url( 'js/semantic.min.js', __FILE__ ));
		wp_register_style( 'plgxPentryForms_LoadSemantic_css', plugins_url('css/semantic.min.css', __FILE__) );
	}
    
    function plgxPentryForms_TemplateHeader($title = '', $subtitle = '')
    {
        wp_enqueue_style( 'plgxPentryForms_LoadSemantic_css' );
        wp_enqueue_script( 'plgxPentryForms_LoadSemantic_js', '', array(), false, true );
        ?>
        <script>
        jQuery(document).ready(function(){
            jQuery("#main_container_loader").hide();
            jQuery("#main_container").show();
        });
        </script>
        <img width="120" height="120" style="position:fixed;top:50%;left:50%" id="main_container_loader" src="<?php echo plugins_url('images/ajax_loader.svg', __FILE__); ?>" />
        <div id="main_container" class="ui main container" style="margin:20px 0 0 0!important; display: none;">
        <?php
        if ($title != '') {
        ?>
            <h2 class="ui dividing header">
                <?php echo $title; ?>
                <?php
                if ($subtitle != '')
                {
                    ?>
                    <div class="sub header"><?php echo $subtitle; ?></div>
                    <?php
                }
                ?>
            </h2>
        <?php
        }
        ?>

        <?php
    }
    
    function plgxPentryForms_BottomHeader()
    {
        ?>
        </div>
        <?php
    }
    




    
    /**
     * System actions
     */
    
	function plgxPentryForms_activation()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'plgxPentryForms_config';
		if( $wpdb->get_var( 'SHOW TABLES LIKE "' . $table_name .'"' ) != $table_name ) {
			$sql = 'CREATE TABLE IF NOT EXISTS '. $table_name . ' (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `var_name` char(255) CHARACTER SET utf8 NOT NULL,
                `var_value` LONGTEXT CHARACTER SET utf8 NOT NULL,
                PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql ); // Creation of the new TABLE
            
            plgxPentryForms_Set_Params( array('installation_date' => date("Y-m-d")) );
		}

        
        add_option('plgxPentryForms_activation_redirect', true);
	}
	register_activation_hook( __FILE__, 'plgxPentryForms_activation' );
	add_action('admin_init', 'plgxPentryForms_activation_do_redirect');
	
	function plgxPentryForms_activation_do_redirect() {
		if (get_option('plgxPentryForms_activation_redirect', false)) {
			delete_option('plgxPentryForms_activation_redirect');
			 wp_redirect("admin.php?page=PentryForms_settings");      // point to main window for plugin
			 exit;
		}
	}
    
	function plgxPentryForms_uninstall()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'plgxPentryForms_config';
		$wpdb->query( 'DROP TABLE ' . $table_name );
	}
	register_uninstall_hook( __FILE__, 'plgxPentryForms_uninstall' );    
    

}
else {
    $pentryform_verification = isset( $_REQUEST['pentryform_verification'] ) ? $_REQUEST['pentryform_verification'] : '';
    if ($pentryform_verification != '')
    {
        $params = plgxPentryForms_Get_Params(array('api_key'));
        $api_key = $params['api_key'];
        if (md5($api_key) == $pentryform_verification)
        {
            $action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
            if ($action == 'update')
            {
                $data = array();
                $data['account_email'] = isset( $_REQUEST['account_email'] ) ? trim($_REQUEST['account_email']) : '';
                $data['encoded_password'] = isset( $_REQUEST['encoded_password'] ) ? trim($_REQUEST['encoded_password']) : '';
                $data['js_code'] = isset( $_REQUEST['js_code'] ) ? trim($_REQUEST['js_code']) : '';
                if ($data['js_code'] == '') unset($data['js_code']);
                
                plgxPentryForms_Set_Params( $data );
                plgxPentryForms_JSON_Params();
            }
            
            die( md5($pentryform_verification.$api_key));
        }
        else die('Verification error '.$pentryform_verification);
    }
    
    // Adding JS if exists
    // Check if fronend
    if (!is_front_page())
    {
        $upload_dir   = wp_upload_dir();
        $pf_dir = $upload_dir['basedir'].'/pentryforms';
        
        $pf_json_file = $pf_dir.'/settings.json';
        
        if (file_exists($pf_json_file))
        {
            $fp = fopen($pf_json_file, "r");
            $json_settings = (array)json_decode(fread($fp, filesize($pf_json_file)), true);
            fclose($fp);
            
            if (isset($json_settings['js_code']) && trim($json_settings['js_code']) != '')
            {
                $GLOBALS['PentryForms_Insert_JavaScript_Code'] = $json_settings['js_code'];
                
                add_action('wp_footer', 'plgxPentryForms_Insert_JavaScript_Code');
                
                function plgxPentryForms_Insert_JavaScript_Code()
                {
                    echo $GLOBALS['PentryForms_Insert_JavaScript_Code'];
                }
            }
        }        
    }


}









function plgxPentryForms_Get_Params($vars = array())
{
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'plgxPentryForms_config';
    
    $ppbv_table = $wpdb->get_results("SHOW TABLES LIKE '".$table_name."'" , ARRAY_N);
    if(!isset($ppbv_table[0])) return false;
    
    if (count($vars) == 0)
    {
        $rows = $wpdb->get_results( 
        	"
        	SELECT *
        	FROM ".$table_name."
        	"
        );
    }
    else {
        foreach ($vars as $k => $v) $vars[$k] = "'".$v."'";
        
        $rows = $wpdb->get_results( 
        	"
        	SELECT * 
        	FROM ".$table_name."
            WHERE var_name IN (".implode(',',$vars).")
        	"
        );
    }
    
    $a = array();
    if (count($rows))
    {
        foreach ( $rows as $row ) 
        {
        	$a[trim($row->var_name)] = trim($row->var_value);
        }
    }

    return $a;
}


function plgxPentryForms_Set_Params($data = array())
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'plgxPentryForms_config';

    if (count($data) == 0) return;   
    
    foreach ($data as $k => $v)
    {
        $tmp = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . $table_name . ' WHERE var_name = %s LIMIT 1;', $k ) );
        
        if ($tmp == 0)
        {
            // Insert    
            $wpdb->insert( $table_name, array( 'var_name' => $k, 'var_value' => $v ) ); 
        }
        else {
            // Update
            $datav = array('var_value'=>$v);
            $where = array('var_name' => $k);
            $wpdb->update( $table_name, $datav, $where );
        }
    } 
}

function plgxPentryForms_Print_MessageBox($data)
{
   /*
   $data = array(
        'type' => '',
        'size' => '',
        'icon' => '',
        'header' => '',
        'content' => '',
        'color' => '',
        'button' => array(
            'url' => '',
            'txt' => '',
            'target' => 1
            )
   );
   */
   
   if (isset($data['type']))
   {
        switch ($data['type'])
        {
            case 'error':
                $data['color'] = 'red';
                $data['icon'] = 'exclamation triangle';
                break;
                
            case 'info':
                $data['color'] = 'blue';
                $data['icon'] = 'exclamation';
                break;
                
            case 'ok':
                $data['color'] = 'green';
                $data['icon'] = 'check square outline';
                break;
                
            case 'warning':
                $data['color'] = 'yellow';
                $data['icon'] = 'exclamation circle';
                break;
        }
   }
   
   if (!isset($data['size'])) $data['size'] = 'large';
   if (isset($data['icon'])) 
   {
        $data['icon_class'] = 'icon';
        $data['icon_html'] = '<i class="'.$data['icon'].' icon"></i>';
   }
   else $data['icon_class'] = $data['icon_html'] = '';
   
   if (isset($data['button']) && !isset($data['button']['target'])) $data['button']['target'] = 1;

   ?>
        <div class="ui <?php echo $data['color']; ?> <?php echo $data['icon_class']; ?> <?php echo $data['size']; ?> message">
            <?php echo $data['icon_html']; ?>
            <div class="content">
              <?php if (isset($data['header'])) echo '<div class="header">'.$data['header'].'</div>'; ?>
              <?php if (isset($data['button'])) { ?> <a class="mini ui <?php echo $data['color']; ?> button right floated" <?php if ($data['button']['target'] == 1) echo 'target="_blank"'; ?> href="<?php echo $data['button']['url']; ?>"><?php echo $data['button']['txt']; ?></a> <?php } ?>
              <?php echo $data['content']; ?>
            </div>
        </div>
    <?php
}


function plgxPentryForms_JSON_Params()
{
    $upload_dir   = wp_upload_dir();
    $pf_dir = $upload_dir['basedir'].'/pentryforms';
    
    $pf_json_file = $pf_dir.'/settings.json';
    
    $params = plgxPentryForms_Get_Params();
    
   
    $fp = fopen($pf_json_file, 'w');
    fwrite($fp, json_encode($params));
    fclose($fp);
}


function plgxPentryForms_Init()
{
    // [basedir] => /home/xxx/wp-content/uploads
    // [baseurl] => http://xxx.com/wp-content/uploads
    $upload_dir   = wp_upload_dir();
    $pf_dir = $upload_dir['basedir'].'/pentryforms';
    
    if (!file_exists($pf_dir)) mkdir($pf_dir);
    
    $htaccess_file = $pf_dir.'/.htaccess';
    
    if (!file_exists($htaccess_file))
    {
        $fp = fopen($htaccess_file, 'w');
        fwrite($fp, 'deny from all');
        fclose($fp);
    }
    
    
}
