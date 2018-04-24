<?php
    require "db.php";
    
    // basic auth
    if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] != $username || $_SERVER['PHP_AUTH_PW'] != $password) 
    {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        exit;
    }
    
    $form_name = test_input($_GET['name']);
    $form_plato = test_input($_GET['plato']);
    $edit = test_input($_GET['edit']);
    
    if($form_name != '')
    {
        if($edit != '')
        {
            $id = intval($edit);
            $sql = "UPDATE batch SET name = '$form_name', plato = '$form_plato' WHERE pk_id = '$id'";
            if($conn->query($sql) == TRUE) $form_name = $form_plato = $edit = '';
        }
        else
        {
            $sql = "INSERT INTO batch (name, plato) VALUES('$form_name', '$form_plato')";
            if($conn->query($sql) == TRUE) $form_name = $form_plato = '';
        }
    }
    $action = 'Nové';
    if($edit != '') $action = 'Uprav';
    
    // beer list
    $beers = '';
    $sql = "SELECT pk_id, timestamp, name, plato FROM batch ORDER BY timestamp DESC";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) 
    {
        $id = $row["pk_id"];
        $timestamp = $row["timestamp"];
        $plato = $row["plato"];
        $name = $row["name"];
        if($edit == $id)
        {
            $form_name = $name;
            $form_plato = $plato;
        }
        $beers .= "<tr><td><a href=\"index.php?show=$id\">$timestamp</a></td><td class=\"l\">$name</td><td>$plato</td><td><a href=\"?edit=$id\">Upravit</a></td><td><a href=\"values.php?show=$id\">Hodnoty</a></td></tr>";
    }
?>

<html>
    <head>
        <style>
       table.list {
        border-collapse: collapse;
       }
       table.list th {
        padding: 5px;
        border: 1px solid black;
        text-align: center; 
        background: #A4C2F4; 
       }
       table.list td {
        padding: 5px;
        border: 1px solid black;
        text-align: center;
       }
       table.list td.l {
        text-align: left;
       }
       </style>
    </head>
    <body>
        <h1>Nastavení</h1>
        <h2><?php echo $action; ?> pivo:</h2>
        <form>
            <table>
                <tr>
                    <th>Název:</th><td><input type="text" name="name" value="<?php echo $form_name; ?>" /></td>
                </tr>
                <tr>
                    <th>Plato:</th><td><input type="text" name="plato" value="<?php echo $form_plato; ?>" /></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" value="Odeslat"></td>
                </tr>
            </table>
            <input type="hidden" name="edit" value="<?php echo $edit; ?>" />
        </form>
        
        <h2>Seznam piv:</h2>
        <table class="list">
            <tr>
                <th width="200">Datum</th><th width="300">Název</th><th width="100">Plato (°P)</th><th width="120"></th><th width="120"></th>
            </tr>
            <?php echo $beers; ?>
        </table>
    </body>
</html>
