<?php

if (!defined('WP_PLUGIN_URL'))
{
   require_once( realpath('../../../').'/wp-config.php' );
}

require_once( 'RUH-captcha-classes.php' );
function RUH_captcha_gen()
{

   $host = 'www.dimgord.com';
   $path = '/cgi-bin/ruh_gen_w_cfg.cgi';
   $port = 80;
   $request = "ipaddr=" . $_SERVER["REMOTE_ADDR"];

   $http_request  = "POST $path HTTP/1.0\r\n";
   $http_request .= "Host: $host\r\n";
   $http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
   $http_request .= "Content-Length: " . strlen( $request ) . "\r\n";
   $http_request .= "User-Agent: RUHCAPTCHA/PHP\r\n";
   $http_request .= "\r\n";
   $http_request .= $request;

   if ( ( $fs = @fsockopen( $host, $port, $errno, $errstr, 10 ) ) == false )
      die ('Could not open socket');

   fwrite( $fs, $http_request );
   while ( !feof( $fs ) )
      $response .= fgets( $fs, 1160 );
   fclose( $fs );

   $response = explode( "\r\n\r\n", $response, 2 );

   $ruh_out = $response[1];

   $ruh_option = new ruhOption();
   $ruh_option = get_option( "RUH_captcha_data" );
   $ruh_option->total_shown++;
   update_option( "RUH_captcha_data", $ruh_option );

   return( $ruh_out );
}
echo "<root><data>" . RUH_captcha_gen() . "</data></root>";
?>
