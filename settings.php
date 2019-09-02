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
            if($conn->query($sql) == TRUE) 
            {
                $form_name = $form_plato = '';
                // create webdir
                $sql = "SELECT timestamp FROM batch ORDER BY timestamp DESC";
                $result = $conn->query($sql);
                if($row = $result->fetch_assoc())
                {
                    $timestamp = $row["timestamp"];
                    $webdir = date('YmdHis', strtotime($timestamp));
                    $old_umask = umask(0);
                    mkdir("/var/www/html/webcam/$webdir", 0777);
                    umask($old_umask);
                }
            }
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
        <title>Pivo - nastavení</title>
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
