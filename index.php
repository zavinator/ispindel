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
    
    function SG($plato)
    {
        return 1 + $plato / (258.6 - 227.1 * $plato / 258.2);
    }
    
    // choose batch
    $show = $conn->escape_string($_GET['show']);
    if($show != '') 
    {
        $id = intval($show);
        $sql = "SELECT pk_id, name, timestamp, plato FROM batch WHERE pk_id = '$id'"; // selected
    }
    else
    {
        $sql = "SELECT pk_id, name, timestamp, plato FROM batch ORDER BY timestamp DESC"; // last
    }
    
    $result = $conn->query($sql);
    if($row = $result->fetch_assoc())
    {
        $beer_name = $row['name'];
        $plato_start = $row['plato'];
        $SG_start = SG($plato_start);
        $time_start = $row['timestamp'];
        $show = $row['pk_id'];
    }
    
    // find time_end if exists
    $sql = "SELECT timestamp FROM batch WHERE timestamp > '$time_start' ORDER BY timestamp ASC";
    $result = $conn->query($sql);
    if($row = $result->fetch_assoc()) $time_end_where = "AND timestamp < '" . $row['timestamp'] . "'";
    
    // ispindel data
    $sql = "SELECT timestamp, gravity, temperature, battery, angle FROM ispindel WHERE timestamp >= '$time_start' $time_end_where ORDER BY timestamp ASC";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) 
    {
        $plato_max = 0;
        while($row = $result->fetch_assoc()) 
        {
            $timestamp = $row["timestamp"];
            $plato = $row["gravity"];
            if($plato > $plato_max) $plato_max = $plato;
            $temp = $row["temperature"];
            $battery = $row["battery"];
            $angle = $row["angle"];
            $line_chart .= "[new Date('$timestamp'), $plato, $temp],";
        }
        
        if($plato_start < 0.001) // plato_start not set?
        {
            $plato_start = $plato_max;
            $SG_start = SG($plato_start);
        }
        $SG = SG($plato);
        $ABV = 133 * ($SG_start - $SG);
        $Re = 0.1808 * $plato_start + 0.8192 * $plato;
        $Az = 1 - $plato / $plato_start;
        $AzRe = 1 - $Re / $plato_start;
    }
?>

<html>
  <head>
   <style>
   table.data {
    border-collapse: collapse;
   }
   table.data th {
    padding: 2px;
    border: 1px solid black;
    text-align: left; 
    background: #A4C2F4; 
   }
   table.data td {
    padding: 2px;
    border: 1px solid black;
    text-align: center;
   }
   table.data td.noborder {
    border: none;
   }
   table.data th.r {
    background: #B6D7A8;
   }
   </style>
   <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
   <script type="text/javascript">
      google.charts.load('current', {'packages':['gauge', 'line', 'corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() 
      {
        // plato/temp chart
        var data = new google.visualization.DataTable();
        data.addColumn('datetime', 'Čas');
        data.addColumn('number', 'Plato (°P)');
        data.addColumn('number', 'Teplota (°C)');
        data.addRows([
            <?php echo $line_chart; ?>
        ]);
        var options = {
            hAxis: {
                format: 'd.M HH:mm',
                gridlines: {count: -1}
            },
            vAxis: {
                format: '#.##'
            },
            colors: ['red', 'blue'],
            width: 1400,
            height: 520
        };
        var chart = new google.charts.Line(document.getElementById('plato_temp_chart_div'));
        chart.draw(data, google.charts.Line.convertOptions(options));
        
        // gauge
        var dataPlato = google.visualization.arrayToDataTable([['Label', 'Value'], ['Plato (°P)', <?php echo(sprintf('%.2f', $plato)); ?>]]);
        var dataTemp = google.visualization.arrayToDataTable([['Label', 'Value'], ['Teplota (°C)', <?php echo(sprintf('%.2f', $temp)); ?>]]);
        var dataBattery = google.visualization.arrayToDataTable([['Label', 'Value'], ['Baterie (V)', <?php echo(sprintf('%.2f', $battery)); ?>]]);

        var optionsPlato = { width: 220, height: 220, redFrom: 15, redTo: 20, yellowFrom:10, yellowTo: 15, greenFrom: 5, greenTo: 10, min: 0, max: 20, minorTicks: 5};
        var optionsTemp = {width: 220, height: 220, redFrom: 18, redTo: 24, yellowFrom:12, yellowTo: 18, greenFrom: 6, greenTo: 12, min: 0, max: 24, minorTicks: 5};
        var optionsBattery = {width: 220, height: 220, redFrom: 3, redTo: 3.5, yellowFrom:3.5, yellowTo: 3.9, greenFrom: 3.9, greenTo: 4.2, min: 3, max: 4.2, minorTicks: 5};

        var chartPlato = new google.visualization.Gauge(document.getElementById('plato_chart_div'));
        var chartTemp = new google.visualization.Gauge(document.getElementById('temp_chart_div'));
        var chartBattery = new google.visualization.Gauge(document.getElementById('battery_chart_div'));

        chartPlato.draw(dataPlato, optionsPlato);
        chartTemp.draw(dataTemp, optionsTemp);
        chartBattery.draw(dataBattery, optionsBattery);
      }
    </script>
  </head>
  <body>
    <h1><?php echo $beer_name; ?></h1>
    <table class="data">
        <tr>
            <th width="170">Čas posledního bodu</th>
            <td width="170"><?php echo $timestamp; ?></td>
            <td width="50" class="noborder"></td>
            <th width="170" class="r">Počáteční síla</th>
            <td width="170"><?php echo(sprintf('%.2f', $plato_start)); ?> °P (<?php echo(sprintf('%.3f', $SG_start)); ?>)</td>
            <td width="50" class="noborder"></td>
            <td class="noborder"><a href="settings.php"><strong>Nastavení</strong></a></td>
        </tr>
        <tr>
            <th>Plato</th>
            <td><?php echo(sprintf('%.2f', $plato)); ?> °P (<?php echo(sprintf('%.3f', $SG)); ?>)</td>
            <td class="noborder"></td>
            <th class="r">Zdálivé prokvašení</th>
            <td><?php echo(sprintf('%.1f', 100 * $Az)); ?> %</td>
        </tr>
        <tr>
            <th>Teplota</th>
            <td><?php echo(sprintf('%.2f', $temp)); ?> °C</td>
            <td class="noborder"></td>
            <th class="r">ABV</th>
            <td><?php echo(sprintf('%.1f', $ABV)); ?> %</td>
            <td class="noborder"></td>
            <td class="noborder"><a href="values.php?show=<?php echo $show; ?>"><strong>Hodnoty</strong></a></td>
        </tr>
        <tr>
            <th>Náklon</th>
            <td><?php echo(sprintf('%.2f', $angle)); ?> °</td>
            <td class="noborder"></td>
            <th class="r">Reálný extrakt</th>
            <td><?php echo(sprintf('%.1f', $Re)); ?> °P</td>
        </tr>
        <tr>
            <th>Baterie</th>
            <td><?php echo(sprintf('%.2f', $battery)); ?> V</td>
            <td class="noborder"></td>
            <th class="r">Reálné prokvašení</th>
            <td><?php echo(sprintf('%.1f', 100 * $AzRe)); ?> %</td>
        </tr>
    </table>
    <table>
        <tr>
            <td><div id="plato_chart_div"></div></td>
            <td><div id="temp_chart_div"></div></td>
            <td><div id="battery_chart_div"></div></td>
        </tr>
    </table>
    <div id="plato_temp_chart_div"></div>
  </body>
</html>
