<!DOCTYPE html>
<html lang="cs">
    <head>
        <meta charset="UTF-8">
        <title>add/remmove firewall rules</title>
        <link rel="stylesheet" href="firewall.css">
        <script src="jquery-3.5.0.js"></script>
    </head>

    <body>
     <form action="api.php" method="post" id="apiAction">
        <label for="ip">IP:</label><br>
        <input type="text" id="ip" name="ip" value="255.255.255.255"><br>
        <label for="source">source:</label><br>
        <input type="text" id="source" name="source" value="manual-insert"><br><br>
        <label for="reason">reason:</label><br>
        <input type="text" id="reason" name="reason" value="ftp attack"><br><br>
        <label for="action">action: </label>
        <select id="action" name="action">
          <option value="0">add to blacklist temporarily</option>
          <option value="1" selected>add to blacklist</option>
          <option value="2">add to whitelist</option>
          <option value="3">temporary remove</option>
          <option value="4">just in case</option>
        </select> 
        <input type="submit" value="Submit">
     </form>
        
     <div id="result"></div>   
     <script>     
     $( "#apiAction" ).submit(function( event ) {
      event.preventDefault();
      var $form = $( this ),
      url = $form.attr( "action" );
      var posting = $.post( url, $( "#apiAction" ).serialize() );
      
      posting.done(function( data ) {
        var content = $( data ).find( "#content" );
        $( "#result" ).empty().append( content );
      });
     });
     </script>
   
    </body>
</html>
