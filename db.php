<!--
Copyright © 2018, Martin Dušek. Všechna práva vyhrazena.
Redistribuce a použití zdrojových i binárních forem díla, v pùvodním i upravovaném tvaru, jsou povoleny za následujících podmínek:

Šíøený zdrojový kód musí obsahovat výše uvedenou informaci o copyrightu, tento seznam podmínek a níže uvedené zøeknutí se odpovìdnosti.
Šíøený binární tvar musí nést výše uvedenou informaci o copyrightu, tento seznam podmínek a níže uvedené zøeknutí se odpovìdnosti ve své dokumentaci a/nebo dalších poskytovaných materiálech.
Ani jméno vlastníka práv, ani jména pøispìvatelù nemohou být použita pøi podpoøe nebo právních aktech souvisejících s produkty odvozenými z tohoto softwaru bez výslovného písemného povolení.
TENTO SOFTWARE JE POSKYTOVÁN DRŽITELEM LICENCE A JEHO PØISPÌVATELI „JAK STOJÍ A LEŽÍ“ A JAKÉKOLIV VÝSLOVNÉ NEBO PØEDPOKLÁDANÉ ZÁRUKY VÈETNÌ, ALE NEJEN, PØEDPOKLÁDANÝCH OBCHODNÍCH ZÁRUK A ZÁRUKY VHODNOSTI PRO JAKÝKOLIV ÚÈEL JSOU POPØENY. DRŽITEL, ANI PØISPÌVATELÉ NEBUDOU V ŽÁDNÉM PØÍPADÌ ODPOVÌDNI ZA JAKÉKOLIV PØÍMÉ, NEPØÍMÉ, NÁHODNÉ, ZVLÁŠTNÍ, PØÍKLADNÉ NEBO VYPLÝVAJÍCÍ ŠKODY (VÈETNÌ, ALE NEJEN, ŠKOD VZNIKLÝCH NARUŠENÍM DODÁVEK ZBOŽÍ NEBO SLUŽEB; ZTRÁTOU POUŽITELNOSTI, DAT NEBO ZISKÙ; NEBO PØERUŠENÍM OBCHODNÍ ÈINNOSTI) JAKKOLIV ZPÙSOBENÉ NA ZÁKLADÌ JAKÉKOLIV TEORIE O ZODPOVÌDNOSTI, A UŽ PLYNOUCÍ Z JINÉHO SMLUVNÍHO VZTAHU, URÈITÉ ZODPOVÌDNOSTI NEBO PØEÈINU (VÈETNÌ NEDBALOSTI) NA JAKÉMKOLIV ZPÙSOBU POUŽITÍ TOHOTO SOFTWARE, I V PØÍPADÌ, ŽE DRŽITEL PRÁV BYL UPOZORNÌN NA MOŽNOST TAKOVÝCH ŠKOD.
-->
<?php 
    $servername = "localhost";
    $username = "USERNAME";
    $password = "PASSWORD";
    $dbname = "beer";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) 
    {
        die("Connection failed: " . $conn->connect_error);
    } 

    function test_input($data) 
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
?>
