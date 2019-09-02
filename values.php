<!--
Copyright © 2018, Martin Dušek. Všechna práva vyhrazena.
Redistribuce a použití zdrojových i binárních forem díla, v původním i upravovaném tvaru, jsou povoleny za následujících podmínek:

Šířený zdrojový kód musí obsahovat výše uvedenou informaci o copyrightu, tento seznam podmínek a níže uvedené zřeknutí se odpovědnosti.
Šířený binární tvar musí nést výše uvedenou informaci o copyrightu, tento seznam podmínek a níže uvedené zřeknutí se odpovědnosti ve své dokumentaci a/nebo dalších poskytovaných materiálech.
Ani jméno vlastníka práv, ani jména přispěvatelů nemohou být použita při podpoře nebo právních aktech souvisejících s produkty odvozenými z tohoto softwaru bez výslovného písemného povolení.
TENTO SOFTWARE JE POSKYTOVÁN DRŽITELEM LICENCE A JEHO PŘISPĚVATELI „JAK STOJÍ A LEŽÍ“ A JAKÉKOLIV VÝSLOVNÉ NEBO PŘEDPOKLÁDANÉ ZÁRUKY VČETNĚ, ALE NEJEN, PŘEDPOKLÁDANÝCH OBCHODNÍCH ZÁRUK A ZÁRUKY VHODNOSTI PRO JAKÝKOLIV ÚČEL JSOU POPŘENY. DRŽITEL, ANI PŘISPĚVATELÉ NEBUDOU V ŽÁDNÉM PŘÍPADĚ ODPOVĚDNI ZA JAKÉKOLIV PŘÍMÉ, NEPŘÍMÉ, NÁHODNÉ, ZVLÁŠTNÍ, PŘÍKLADNÉ NEBO VYPLÝVAJÍCÍ ŠKODY (VČETNĚ, ALE NEJEN, ŠKOD VZNIKLÝCH NARUŠENÍM DODÁVEK ZBOŽÍ NEBO SLUŽEB; ZTRÁTOU POUŽITELNOSTI, DAT NEBO ZISKŮ; NEBO PŘERUŠENÍM OBCHODNÍ ČINNOSTI) JAKKOLIV ZPŮSOBENÉ NA ZÁKLADĚ JAKÉKOLIV TEORIE O ZODPOVĚDNOSTI, AŤ UŽ PLYNOUCÍ Z JINÉHO SMLUVNÍHO VZTAHU, URČITÉ ZODPOVĚDNOSTI NEBO PŘEČINU (VČETNĚ NEDBALOSTI) NA JAKÉMKOLIV ZPŮSOBU POUŽITÍ TOHOTO SOFTWARE, I V PŘÍPADĚ, ŽE DRŽITEL PRÁV BYL UPOZORNĚN NA MOŽNOST TAKOVÝCH ŠKOD.
-->
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
       body, p, table {
        font: 10pt Verdana, Arial, sans-serif;
       }
       h1 {
         font-size: 16pt;
         font-weight: bold;
       }
       </style>
    </head>
    <body>
        <h1><a href="index.php?show=<?php echo $show; ?>"><?php echo "$beer_name"; ?></a></h1>
        <table>
            <tr>
                <th width="180">Čas</th>
                <th width="180">Plato °P</th>
                <th width="180">Teplota °C</th>
                <th width="180"></th>
                <?php echo $values; ?>
            </tr>
        </table>
    </body>
</html>
