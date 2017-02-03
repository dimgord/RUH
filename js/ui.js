// James Padolsey's REGEX.  Many thanks, James!
jQuery.expr[':'].regex = function(elem, index, match) {
    var matchParams = match[3].split(','),
        validLabels = /^(data|css):/,
        attr = {
            method: matchParams[0].match(validLabels) ? 
                        matchParams[0].split(':')[0] : 'attr',
            property: matchParams.shift().replace(validLabels,'')
        },
        regexFlags = 'ig',
        regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g,''), regexFlags);
    return regex.test(jQuery(elem)[attr.method](attr.property));
}

 var ruh_plug_url = "";

 var getRuhPlugUrlTimerID = -1;

 function ruh_get_plug_url_start()
 {
   if ( typeof( ruh_get_plug_url ) != 'function' )
   {
      if ( getRuhPlugUrlTimerID == -1 )
         getRuhPlugUrlTimerID = setInterval( "ruh_get_plug_url_start()", 300 );
   }
   else
   {
      ruh_plug_url = ruh_get_plug_url();
      clearInterval( getRuhPlugUrlTimerID );
   }
 }

 var updatePromptTimerID = -1;

 function update_prompt_start()
 {
   if ( typeof( update_prompt ) != 'function' )
      updatePromptTimerID = setTimeout( "update_prompt_start()", 100 );
   else
   {
      update_prompt();
      ruh_get_plug_url_start();
      if ( updatePromptTimerID >= 0 )
         clearTimeout( updatePromptTimerID );
   }
 }


var magt = 0;
var magl = 0;

 function update_display()
 {
   var forma = '#noform';

   if ( $('#registerform').length != 0 )
      forma = '#registerform';
   else
      if ( $('#commentform').length != 0 )
         forma = '#commentform';

   if ( $('#C1').length == 0 )
   {
    $(forma).append(
        $('<input/>')
            .attr('type', 'hidden')
            .attr('name', 'click1')
            .attr('id', 'C1')
            .val('')
    );
   }
   if ( $('#C2').length == 0 )
   {
    $(forma).append(
        $('<input/>')
            .attr('type', 'hidden')
            .attr('name', 'click2')
            .attr('id', 'C2')
            .val('')
    );
   }
   if ( $('#C3').length == 0 )
   {
    $(forma).append(
        $('<input/>')
            .attr('type', 'hidden')
            .attr('name', 'click3')
            .attr('id', 'C3')
            .val($('div:regex(id, ^B.*W)').attr('id'))
    );
   }

  var imgw = parseInt( $('.ruhoimg').first().css("width") );
  var imgh = parseInt( $('.ruhoimg').first().css("height") );
  var imgm = parseInt( $('.ruhoimg').first().css("margin-left") );

  var denom = 2;
  if ( $('.mx:checked').length == 0 )
  {
     denom = 2;
     $('.mx').val(["2"]);
  }
  else
  {
     denom = parseInt( $('.mx:checked').val() );
     //if ( denom == 0 )
        //denom = 0.05;
  }

  var ifrw = parseInt( $('#RUHifr').css( "width" ) );
  var ifroffset = $('.ruho').first().offset();
  var ifrt = ifroffset.top;
  var ifrl = ifroffset.left;
  var ifrPoffset = $('.ruho').first().parent().offset();
  var ifrPt = ifrPoffset.top;
  var ifrPl = ifrPoffset.left;
  var ifrh = $('#RUHifr').css('height');
  magt = ifrt - ifrPt - parseInt( ifrh );
  magl = imgw*3 + 10*imgm;

  //alert( "top=" + ifrt + " topP=" + ifrPt + " ifrh=" + ifrh + " magt=" + magt );
// Refresh button and magnifier size and position
$('#Check').css("width", imgw*3 + imgm*2*2);
$('#Check').css("top",   imgm);
$('#Check').css("left",  imgm);
//$("#Magnifier").css( "width", imgw*denom );
//$("#Magnifier").css( "height", imgh*denom );


$('.mx').each( function() { $(this).click( function() {
      denom = parseInt( $('input:radio[name=magn]:checked').val() );
      
      //if ( denom == 0 )
         //denom = 0.05;
});});

$('.ruho').each( function() {
   //$(this).css("position", "relative");
   $(this).css("width", (imgw + 2*imgm)*3);
});

$('.ruhoimg').each( function() {
   $(this).css("border-radius", 5);
});

$('.ruhoimg').each( function() { $(this).mousemove(function ( e ) {
   var offs = $(this).offset();
   var curLeft = offs.left;
   var curTop = offs.top;
   //magt-=curTop;
   //magl-=curLeft;
   //magl+=(imgw + 2*imgm)*3 + 10;

   //if ( magt >= curTop && magt <= curTop + imgh
        //&&
        //magl >= curLeft && magl <= curLeft + imgh )
   //{
      //if ( magl > ifrw - imgw*denom - 10 )
         //magl = ifrw - imgw*denom - 10;
   //}
   //$("#Magnifier").css( "top", magt );
   //$("#Magnifier").css( "left", magl );
});});

$('.ruhoimg').each( function() { $(this).mouseover(function ( e ) {
    $(this).css("opacity", 0.7);
    if ( denom > 0 )
    {
       //$("#Magnifier").css( "display", "block" );
       var imgsrc = $(this).attr( "src" );
       $("#Magnifier").html( "<img src=\"" + imgsrc + "\" width=\"" +
                             imgw*denom +
                             "\" height = \"" + imgh*denom + "\"></img>" );
    }
    else
    {
       var imgsrc = ruh_plug_url + "/img/Magnifier.png";
       $("#Magnifier").html( "<img src=\"" + imgsrc + "\" width=\"" +
                             imgw*1 +
                             "\" height = \"" + imgh*1 + "\"></img>" );
    }
});});

$('.ruhoimg').each( function() { $(this).mouseout(function () {
    $(this).css("opacity", 1);
    var imgsrc = ruh_plug_url + "/img/Magnifier.png";
    if ( denom == 0 )
       denom = 1;
    $("#Magnifier").html( "<img src=\"" + imgsrc + "\" width=\"" +
                          imgw*denom +
                          "\" height = \"" + imgh*denom + "\"></img>" );
    if ( denom == 1 )
       denom = 0;
    //$("#Magnifier").html( "" );
    //$("#Magnifier").css( "display", "none" );
});});

$('.ruhoimg').each(function() { $(this).click(function() {
   $this = $(this);
   var topc = $this.position().top + imgm;
   var leftc = $this.position().left + imgm;
   var clicks = 0;

   $(".clicked").each(function (i) {
        clicks++;
      });

   if ( clicks < 2 ) // don't allow more than two clicked objs
   {
      $this.toggleClass( 'clicked' );
      if ( $this.hasClass( 'clicked' ) )
      {
         var appendix = "<div class='checkmark'>"+
                        "<img class='checkmarkimg' src='" + ruh_plug_url +
                        "/img/check_border.png'>"+
                        "</img></div>";

         $this.parent().append( appendix );
         $this.parent().children('.checkmark').css("top", topc); 
         $this.parent().children('.checkmark').css("left", leftc); 
         $this.parent().children('.checkmark').children('.checkmarkimg').css("width", imgw); 
         $this.parent().children('.checkmark').children('.checkmarkimg').css("height", imgh); 
         if ( !clicks ) // always add to first first :)
         {
            $("#C1").val( $this.parent().attr( 'id' ) );
            $this.parent().children('.checkmark').addClass( 'chk1st' );
         }
         else // see who is vacant
         {
            if ( $("#C2").val() == "" )
            {
               $("#C2").val( $this.parent().attr( 'id' ) );
               $this.parent().children('.checkmark').addClass( 'chk2nd' );
            }
            else
            {
               $("#C1").val( $this.parent().attr( 'id' ) );
               $this.parent().children('.checkmark').addClass( 'chk1st' );
            }
         }
         $('#C3').val($('div:regex(id, ^B.*W)').attr('id'));
      }
   }

})});

$('.checkmark').live({
  click: function() {

    if ( $(this).hasClass( 'chk2nd' ) ) // remove from second
       $("#C2").val( "" );
    else                                // remove from first
       $("#C1").val( "" );

    $(this).parent().children('.ruhoimg').toggleClass( 'clicked' );
    $(this).remove();
  },
  mouseover: function() {
    $(this).parent().children('.ruhoimg').css("opacity", 0.7);
  },
  mouseout: function() {
    $(this).parent().children('.ruhoimg').css("opacity", 1);
  }
});

update_prompt_start();
}
