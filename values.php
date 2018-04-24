<?php
    require "db.php";
    
    // basic auth
    if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] != $username || $_SERVER['PHP_AUTH_PW'] != $password) 
    {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        exit;
    }
    
    // delete value?
    $delete = $conn->escape_string($_GET['delete']);
    if($delete != '')
    {
        $id = intval($delete);
        $conn->query("DELETE FROM ispindel WHERE pk_id='$id'");
    }
    
    // choose batch
    $show = intval($conn->escape_string($_GET['show']));
    $sql = "SELECT name, timestamp FROM batch WHERE pk_id = '$show'";
    $result = $conn->query($sql);
    if($row = $result->fetch_assoc())
    {
        $beer_name = $row['name'];
        $time_start = $row['timestamp'];
    }
    // find time_end if exists
    $sql = "SELECT timestamp FROM batch WHERE timestamp > '$time_start' ORDER BY timestamp ASC";
    $result = $conn->query($sql);
    if($row = $result->fetch_assoc()) $time_end_where = "AND timestamp < '" . $row['timestamp'] . "'";
    
    // ispindel data
    $sql = "SELECT pk_id, timestamp, gravity, temperature FROM ispindel WHERE timestamp >= '$time_start' $time_end_where ORDER BY timestamp DESC";
    $result = $conn->query($sql);
    $values = '';
    while($row = $result->fetch_assoc()) 
    {
        $id = $row["pk_id"];
        $timestamp = $row["timestamp"];
        $plato = $row["gravity"];
        $temp = $row["temperature"];
        $str = "$timestamp - $plato °P - $temp °C";
        $values .= "<tr><td>$timestamp</td><td>$plato</td><td>$temp</td><td><a href=\"?show=$show&delete=$id\" onclick=\"return confirm('Vymazat? $str')\">Odstranit</a></td></tr>";
    }
?>

<html>
    <head>
        <style>
       table {
        border-collapse: collapse;
       }
       table th {
        padding: 5px;
        border: 1px solid black;
        text-align: center; 
        background: #A4C2F4; 
       }
       table td {
        padding: 5px;
        border: 1px solid black;
        text-align: center;
       }
       </style>
    </head>
    <body>
        <h1><a href="index.php?show=<?php echo $show; ?>"><?php echo "$beer_name"; ?></a></h1>
        <table>
            <tr>
                <th width="180">Timestamp</th>
                <th width="180">Plato °P</th>
                <th width="180">Temperature °C</th>
                <th width="180"></th>
                <?php echo $values; ?>
            </tr>
        </table>
    </body>
</html>
