<!DOCTYPE html>
<html lang="cs">
    <head>
        <meta charset="UTF-8">
        <title>add/remmove firewall rules</title>
        <link rel="stylesheet" href="firewall.css">     
    </head>

    <body>
     <form action="api.php" method="post">
        <label for="ip">IP:</label><br>
        <input type="text" id="ip" name="ip" value="255.255.255.255"><br>
        <label for="source">source:</label><br>
        <input type="text" id="source" name="source" value="manual-insert"><br><br>
        <label for="reason">reason:</label><br>
        <input type="text" id="reason" name="reason" value="ftp attack"><br><br>
        <label for="action">action: </label>
        <select id="action" name="action" size="1">
          <option value="0" selected>add to blacklist</option>
          <option value="1">add to whitelist</option>
          <option value="2">temporary remove</option>
          <option value="3">just in case</option>
        </select> 
        <input type="submit" value="Submit">
     </form>
    </body>
</html>
