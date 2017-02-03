<?php

require_once( 'RUH-captcha-classes.php' );

function _RUH_captcha_encode( $ruh_param )
{
   $param = "";
   $len = strlen( $ruh_param );
   for( $i =0; $i < $len; $i++ )
   {
      $ord = ord( substr( $ruh_param, $i, 1 ) );
      $param .= "\x" . $ord;
   }
   return( $param );
}

function _RUH_captcha_urlize( $raw_data )
{
   $params = "";

   foreach( $raw_data as $key => $value )
      $params .= $key . '=' . _RUH_captcha_encode( $value ) . '&';

   $params = substr( $params, 0, strlen( $params ) - 1 );

   return( $params );
}

function RUH_captcha_check( $ip, $click1, $click2, $click3 )
{
   $ruh_response = new ruhResponse();

   $ruh_response->is_valid = 0;
   $ruh_response->error = "Not called";

   if ( $click1 == $click2 || $click1 == "" || $click2 == "" )
   {
      $ruh_response->is_valid = 0;
      $ruh_response->error = "Bad input";
   }

// Let's figure out who tries to brute force me - call cgi even w/o clicks
   {
      $data->ipaddr = $ip;
      $data->click1 = $click1;
      $data->click2 = $click2;
      $data->click3 = $click3;
      $host = 'www.dimgord.com';
      $path = '/cgi-bin/ruh_check_w_cfg.cgi';
      $port = 80;
      $request = _RUH_captcha_urlize( $data );
//print( "request=" . $request );

      $http_request  = "POST $path HTTP/1.0\r\n";
      $http_request .= "Host: $host\r\n";
      $http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
      $http_request .= "Content-Length: " . strlen( $request ) . "\r\n";
      $http_request .= "User-Agent: RUHCAPTCHA/PHP\r\n";
      $http_request .= "\r\n";
      $http_request .= $request;

//print( $http_request );

      if ( ( $fs = @fsockopen( $host, $port, $errno, $errstr, 10 ) ) == false )
         die ('Could not open socket');

      fwrite( $fs, $http_request );
      while ( !feof( $fs ) )
         $response .= fgets( $fs, 1160 );
      fclose( $fs );

//print( "<br>response=" . $response );
      $response = explode( "\r\n\r\n", $response, 2 );
      $response = explode( "\n", $response[1], 20 );

      $sum = 0;
      $ruh_out = $response[2];
//print( "<br>ruh_out=" . $ruh_out );
      foreach( count_chars( $ruh_out, 1 ) as $i => $val )
         $sum += $i*$val; //$val - count, $i - ord of char
//print( "<br>sum=" . $sum );

      $ruh_response->is_valid = $sum && !($sum%2);
      $ruh_response->error = !($sum%2);
   }

   $ruh_option = new ruhOption();
   $ruh_option = get_option( 'RUH_captcha_data' );
   if ( $ruh_response->is_valid )
      $ruh_option->succ_hits++;
   $ruh_option->total_hits++;
   update_option( "RUH_captcha_data", $ruh_option );
   
   return( $ruh_response->is_valid );
}
?>
