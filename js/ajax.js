function myXHR( serverSideScript, javaScriptFunction, clientSideData )
{
    jQuery.support.cors = true;
    $.ajax
        (
            {
                type: "POST",
                url: serverSideScript,
		timeout: 18000,
                data: clientSideData,
                crossDomain: true,
                success: function( serverSideData )
                {
                   selectFunction( serverSideData, javaScriptFunction );

                   $('.ruhoimg').each(function () {
                          this.title = '';
                   });
                   update_display();
                },
                error: function()
                {
                    $('.ruho').html( "Please wait..." );
                                    //+serverSideScript+"\n"
                                    //+javaScriptFunction+"\n"
                                    //+clientSideData );
                }
            }
        );
}
/*
 * Select a function that will process the server side data.
 */
function selectFunction( serverSideData, javaScriptFunction )
{
    switch( javaScriptFunction )
    {
        case "theFunc":
        {
            theFunc( serverSideData );
            break;
        }

        default:
            document.write( "Unrecognized call - selectFunction. "
                            + javaScriptFunction
                            + " Server response:" + serverSideData );
            break;
    }
}

function theFunc( serverData )
{
    var strout = serverData;
    //strout = strout.replace( /^[\s\S]*<<<<</m, "" );
    //strout = strout.replace( />>>>>[\s\S]*$/m, "" );
    $('.ruho').html( strout );
    $("button").button();
}

var ruh_cgi = "";

var getRuhCgiTimerID = -1;

function ruh_get_cgi_start()
{
  if ( typeof( ruh_get_cgi ) != 'function' )
  {
     if ( getRuhCgiTimerID == -1 ) 
        getRuhCgiTimerID = setInterval( "ruh_get_cgi_start()", 300 );
  }
  else
  {
     ruh_cgi = ruh_get_cgi();
     clearInterval( getRuhCgiTimerID );
     sendRequest();
  }
}

function sendRequest()
{
    var jsonStr = "";

    var $sels = $('.clicked'); // selected cells
    for( var i = 0; i < $sels.length; i++ )
        jsonStr += 'checkbox' + '=' + $sels[i].parentNode.id + '&';

    $sels = $('.ruhdiv'); // all divs with imgs 
    for( var i = 0; i < $sels.length; i++ )
        jsonStr += 'checkbox' + '=' + $sels[i].id + '&';
    myXHR( ruh_cgi, "theFunc", jsonStr );
}
