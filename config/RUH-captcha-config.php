<?php

function show_form()
{
   $reg_only = get_option( 'RUH_captcha_reg_only' );
   //if (!$reg_only)
   //   print( "reg only = FALSE" );
   //else
   //   print ("reg only = $reg_only");
?>
</br>
</br>
<a href='https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=QBUS7KLYAJ7WC'><img src='https://www.paypal.com/en_US/i/btn/x-click-but04.gif' /></a>
</br>
Please consider to make a donation if you found the plugin is useful.  Thank you in advance.
<form action="<?php echo $_SERVER['PHP_SELF'] . '?page=RUH-captcha-config'; ?>" method="POST">
<p>
<input type="checkbox" name="RUHregonly" <?php if ( $reg_only == 1 ) echo "checked ='checked'" ?> >
Activate plugin only for Registration page.
</p>
<p class="submit">
<input class="button-primary" type="submit" value="Save Changes">
<input class="button" type="reset" value="Reset">
</p>
<input type="hidden" name="_submit" value="1">
</form>
<?php
}

function process_form()
{
   if ( $_POST['RUHregonly'] )
      update_option( 'RUH_captcha_reg_only', 1 );
   else
      update_option( 'RUH_captcha_reg_only', 0 );

   show_form();
}

if ( array_key_exists( '_submit', $_POST ) )
{
   process_form();
}
else
{
   show_form();
}
?>
