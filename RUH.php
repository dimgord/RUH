<?php
/*
Plugin Name: pRove yoU are a Human! (RUH) image based Captcha plugin
Plugin URI: http://www.dimgord.com/RUH
Description: RUH Captcha helps filtering out all robot attempts to register/comment.  A human being can identify easily (after a few moments of thinking, of course) two closest matching objects from nine.  Standard version comes with a static set of pictures, stored in my database.  Customization is possible, please contact me if you are interested.  Also, some security improvement may be added.
Version: 0.0.1
Author: Dmitry V. Gordiyevsky
Author URI: http://www.dimgord.com/
License: GNU LESSER GENERAL PUBLIC LICENSE
*/
// Added magnifier.  10/14/11  DVG
// Got rid of iframe.  10/20/11  DVG


function RUH_captcha_config_menu()
{
   if ( is_super_admin() )
      add_submenu_page( 'plugins.php', 'RUH Captcha Config',
                        'RUH Config', 'edit_plugins', 'RUH-captcha-config',
                        'RUH_captcha_config' );
}

function RUH_captcha_config()
{
   if ( is_super_admin() )
   {
      $ruh_form_dir = dirname( __FILE__ ) . '/form/';
      require_once('config/RUH-captcha-config.php');
   }
   else
      wp_die( __( 'You do not have sufficient permissions
                   to access this page.', 'RUH' ) );
}

function fb_count(){
    $link = get_permalink($post->ID);
    $content = file_get_contents("http://api.facebook.com/restserver.php?method=links.getStats&urls=".$link);
    $element = new SimpleXmlElement($content);
    $shareCount = $element->link_stat->comment_count + $element->link_stat->commentsbox_count;
    //print_r( $element );
    return $shareCount;
}

function RUH_captcha_comments_number( $count )
{
 global $id;
 $comments_by_type = &separate_comments(get_comments('post_id=' . $id . '&status=approve'));
 $count = count($comments_by_type['comment']);
 $total = $count + fb_count();
 return( $total );
}

function RUH_captcha_form_print( $ruh_reg_or_com, $ruh_is_reg )
{
   global $user_ID;
   if ( !isset( $user_ID ) || intval( $user_ID ) == 0 )
   {
      $ruh_title = "<h3" . ( 1 ? " style='text-align: center'>" : ">") . __('Hey, robots, get lost!<br />Are you a human?', 'RUH' ) . "<br/></h3>";
      $ruh_plug_url = plugins_url( '', __FILE__ );
      $ruh_gen_url = $ruh_plug_url . "/RUH-captcha-gen.php";
      $ruh_iframe_str = '<div class="RUHifr">';
      if ( !$ruh_is_reg )
         $ruh_iframe_str = '<div class="RUHifr_wide">';
      if ( $ruh_is_reg )
         echo $ruh_title;
      echo $ruh_iframe_str;
      $ruh_prompt =  __( "Please select the <strong>two</strong> most similar images.  If you cannot decide, click on the 'Refresh' button, and a new set will be shown.  Please make sure you made your choice before clicking on ", "RUH" );
?>
<script type="text/javascript">
function update_prompt()
{
   jQuery('.txt_sboku').html(
   "<span id='Check_t' class='b-shadow'><span class='ui-button-text'>" +
   <?php if ( 0 ) print( "\"" . $ruh_prompt . $ruh_reg_or_com . "\" + " ); ?>
   "</span></span>"
   );
}

function ruh_get_cgi()
{
   return( <?php print( "\"" . $ruh_gen_url . "\"" ); ?> );
}

function ruh_get_plug_url()
{
   return( <?php print( "\"" . $ruh_plug_url . "\"" ); ?> );
}
</script>
<link href="<?php echo $ruh_plug_url . '/'; ?>css/ui-lightness/jquery-ui-1.8.10.custom.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $ruh_plug_url . '/'; ?>css/ruho.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $ruh_plug_url . '/'; ?>js/jquery-1.6.3.js"></script>
<script src="<?php echo $ruh_plug_url . '/'; ?>js/jquery-ui-1.8.10.custom.min.js"></script>
<script src="<?php echo $ruh_plug_url . '/'; ?>js/ajax.js"></script>
<script src="<?php echo $ruh_plug_url . '/'; ?>js/ui.js"></script>
<script id="RUH_script_ready" type="text/javascript">
<!--
$(window).load(function()
{
  ruh_get_cgi_start();
});
-->
</script>

<div id='RUHinner' style="background-color:#dddddd; overflow:hidden;">

<?php
      if ( !$ruh_is_reg )
         echo $ruh_title;
?>

<div class='ruho'>
</div>

<?php
      if ( !$ruh_is_reg )
         echo '<div class="txt-sboku_wide">' . $ruh_prompt .
              $ruh_reg_or_com . '</div>';
      else
         echo "<font color='#dddddd'>__</font>";
?>

<div style="z-index:300">

<div style="float:left;">
<font color="#dddddd">__</font>
<?php
echo __( "Magnify:", "RUH" );
?>
</div>

<div style="float:left; z-index:300">
  <input class="mx" style="z-index:300" type="radio" name="magn" value="0" />x0<font color="#dddddd">__</font>
</div>

<div style="float:left; z-index:300">
  <input class="mx" style="z-index:300" type="radio" name="magn" value="2" />x2<font color="#dddddd">__</font>
</div>

<div style="float:left; z-index:300">
  <input class="mx" style="z-index:300" type="radio" name="magn" value="3" />x3<font color="#dddddd">__</font>
</div>

</div>

<?php
      echo '<div id="Magnifier" class="Magnifier"><img src="' . $ruh_plug_url . '/img/Magnifier.png"></img></div>';
?>

</div>
<?php
      if ( $ruh_is_reg )
         echo '<div class="txt-sboku">' . $ruh_prompt . $ruh_reg_or_com . '</div>';
?>

<?php
      require_once( 'RUH-captcha-classes.php' );
      $ruh_option = new ruhOption();
      $ruh_option = get_option( 'RUH_captcha_data' );
      $ruh_counter = __( '  Shown:', 'RUH' ) .
                    "<strong>" . $ruh_option->total_shown . "</strong>" .
                     __( '  Hits:', 'RUH' ) .
                    "<strong>" . $ruh_option->total_hits . "</strong>" .
                     __( '  Recognized as humans:', 'RUH' ) .
                    "<strong><u>" . $ruh_option->succ_hits . "</u></strong>" .
                    "<br /><br />";
      echo $ruh_counter; 
      echo '</div>';
   }
}
function RUH_captcha_form_print_reg()
{
  RUH_captcha_form_print( __( '\'Register\'', 'RUH' ), 1 );
}

function RUH_captcha_form_print_com()
{
  $reg_only = get_option( "RUH_captcha_reg_only" );
  if ( $reg_only != 1 )
     RUH_captcha_form_print( __( '\'Post Comment\'', 'RUH' ), 0 );
}

function __RUH_error_msg()
{
   $ruh_email_addr = get_option( "admin_email" );
   // obfuscator
   $ruh_email_addr = str_replace( '@', '-at-', $ruh_email_addr );
   $ruh_email_addr = str_replace( '.', '-dot-', $ruh_email_addr );
   $ruh_error = '<strong>RUH Captcha</strong>: ' .
                sprintf( __( 'Something wrong!  It seems that you\'ve chosen an unmatched pair.  Try again.  If you are sure that your choice was correct, please write me at %s. Thank you.', 'RUH' ), $ruh_email_addr );
   return $ruh_error;
}

function RUH_captcha_comment_check( $comment )
{
   global $user_ID, $_SERVER, $_POST;
   require_once( 'RUH-captcha-check.php' );
   $reg_only = get_option( "RUH_captcha_reg_only" );

   if (
        ( isset( $user_ID ) && intval( $user_ID ) > 0 )
        OR
        (
          $comment[ 'comment_type' ] != '' 
          AND
          $comment[ 'comment_type' ] != 'comment'
        )
        OR
        ( $reg_only == 1 )
        OR
        RUH_captcha_check( $_SERVER["REMOTE_ADDR"], $_POST["click1"],
                           $_POST["click2"], $_POST["click3"] )
      ) 
      return $comment;
   else
   {
      $ruh_reg_plz = '<br/>' . __( 'Please note that registered Users will have to match the pictures just once, during the registration.', 'RUH' );
      wp_die( __RUH_error_msg() . $ruh_reg_plz . '<br/><strong>' . __( 'Please use browser Back button to return to previous page.', 'RUH' ) . '</strong>' );
   }
}


function RUH_captcha_register_check( $errors )
{
   global $_SERVER, $_POST;
   require_once( 'RUH-captcha-check.php' );
   $ruh_email_addr = get_option( "admin_email" );
   // obfuscator
   $ruh_email_addr = str_replace( '@', '-at-', $ruh_email_addr );
   $ruh_email_addr = str_replace( '.', '-dot-', $ruh_email_addr );

   if ( !RUH_captcha_check( $_SERVER["REMOTE_ADDR"], $_POST["click1"], 
                            $_POST["click2"], $_POST["click3"] ) )
      $errors->add( 'ruh_captcha_error', __RUH_error_msg() );
   return $errors;
}

function RUH_captcha_init()
{
   // Place your language file in the languages subfolder and name it "RUH-{language}.mo
   // Replace {language} with your language value from wp-config.php
   load_plugin_textdomain("RUH", false, dirname( plugin_basename( __FILE__ ) ) . "/languages/" );
}

//Hooks

//Comments count filter
 add_filter( 'get_comments_number', 'RUH_captcha_comments_number', 10 );

//Comments hooks:
 global $wp_version;
 if ( version_compare( $wp_version, '3', '>=' ) )
 {
    //add_action( 'comment_form_after_fields', 'RUH_captcha_form_print', 1 );
    //add_action( 'comment_form_logged_in_after', 'RUH_captcha_form_print', 1 );
 }
 add_action( 'comment_form', 'RUH_captcha_form_print_com', 1 );
 add_filter( 'preprocess_comment', 'RUH_captcha_comment_check', 1 );


//Signup MU hooks:
 $RUH_captcha_ver = explode( '.', $wp_version );

 if ( $RUH_captcha_ver[0] > 2 )
 {
    add_action( 'signup_extra_fields', 'RUH_captcha_form_print_com' );
    add_filter( 'wpmu_validate_user_signup', 'RUH_captcha_register_check' );
 }

//Registration form hooks:
 add_action( 'register_form', 'RUH_captcha_form_print_reg', 10 );
 add_filter( 'registration_errors', 'RUH_captcha_register_check', 10 );
 if ( $wpmu )
 {
    // for buddypress 1.1 only
    add_action( 'bp_before_registration_submit_buttons',
                'RUH_captcha_form_print_reg' );
    add_action( 'bp_signup_validate', 'RUH_captcha_form_print_reg' );
    // for wpmu and buddypress versions before 1.1
    add_action( 'signup_extra_fields', 'RUH_captcha_form_print_reg' );
    add_filter( 'wpmu_validate_user_signup', 'RUH_captcha_register_check' );
 }


// Adding admin config menu
 add_action( 'admin_menu', 'RUH_captcha_config_menu' );

// Adding localization
 //Runs after WordPress has finished loading but before any headers are sent.
 add_action( "init", "RUH_captcha_init", 10, 1 );

function RUH_captcha_install()
{
/* Creates new database field */
require_once( 'RUH-captcha-classes.php' );
$ruh_def_opt = new ruhOption();
$ruh_def_opt->total_hits = 0;
$ruh_def_opt->succ_hits = 0;
$ruh_def_opt->total_shown = 0;
add_option("RUH_captcha_data", $ruh_def_opt);
}

function RUH_captcha_remove()
{
/* Deletes the database field */
delete_option('RUH_captcha_data');
}

/* Runs when plugin is activated */
register_activation_hook(__FILE__,'RUH_captcha_install');

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'RUH_captcha_remove' );

?>
