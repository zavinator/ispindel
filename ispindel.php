<!--
Copyright © 2018, Martin Dušek. Všechna práva vyhrazena.
Redistribuce a použití zdrojových i binárních forem díla, v pùvodním i upravovaném tvaru, jsou povoleny za následujících podmínek:

Šíøený zdrojový kód musí obsahovat výše uvedenou informaci o copyrightu, tento seznam podmínek a níže uvedené zøeknutí se odpovìdnosti.
Šíøený binární tvar musí nést výše uvedenou informaci o copyrightu, tento seznam podmínek a níže uvedené zøeknutí se odpovìdnosti ve své dokumentaci a/nebo dalších poskytovaných materiálech.
Ani jméno vlastníka práv, ani jména pøispìvatelù nemohou být použita pøi podpoøe nebo právních aktech souvisejících s produkty odvozenými z tohoto softwaru bez výslovného písemného povolení.
TENTO SOFTWARE JE POSKYTOVÁN DRŽITELEM LICENCE A JEHO PØISPÌVATELI „JAK STOJÍ A LEŽÍ“ A JAKÉKOLIV VÝSLOVNÉ NEBO PØEDPOKLÁDANÉ ZÁRUKY VÈETNÌ, ALE NEJEN, PØEDPOKLÁDANÝCH OBCHODNÍCH ZÁRUK A ZÁRUKY VHODNOSTI PRO JAKÝKOLIV ÚÈEL JSOU POPØENY. DRŽITEL, ANI PØISPÌVATELÉ NEBUDOU V ŽÁDNÉM PØÍPADÌ ODPOVÌDNI ZA JAKÉKOLIV PØÍMÉ, NEPØÍMÉ, NÁHODNÉ, ZVLÁŠTNÍ, PØÍKLADNÉ NEBO VYPLÝVAJÍCÍ ŠKODY (VÈETNÌ, ALE NEJEN, ŠKOD VZNIKLÝCH NARUŠENÍM DODÁVEK ZBOŽÍ NEBO SLUŽEB; ZTRÁTOU POUŽITELNOSTI, DAT NEBO ZISKÙ; NEBO PØERUŠENÍM OBCHODNÍ ÈINNOSTI) JAKKOLIV ZPÙSOBENÉ NA ZÁKLADÌ JAKÉKOLIV TEORIE O ZODPOVÌDNOSTI, A UŽ PLYNOUCÍ Z JINÉHO SMLUVNÍHO VZTAHU, URÈITÉ ZODPOVÌDNOSTI NEBO PØEÈINU (VÈETNÌ NEDBALOSTI) NA JAKÉMKOLIV ZPÙSOBU POUŽITÍ TOHOTO SOFTWARE, I V PØÍPADÌ, ŽE DRŽITEL PRÁV BYL UPOZORNÌN NA MOŽNOST TAKOVÝCH ŠKOD.
-->
<?php 
    require "db.php";
    
    $json_data = file_get_contents('php://input');
    //$json_data = '{"name":"iSpindel","ID":3764823,"token":"xxx","angle":66.04179,"temperature":22.5,"battery":4.194025,"gravity":24.2814}';
    if(strlen($json_data) > 0)
    {
        $json_obj = json_decode($json_data, true);
        
        $stmt = $conn->prepare("INSERT INTO ispindel (name, ID, token, angle, temperature, battery, gravity) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdddd", $name, $ID, $token, $angle, $temperature, $battery, $gravity);

        $name = $conn->escape_string($json_obj["name"]);
        $ID = $conn->escape_string($json_obj["ID"]);
        $token = $conn->escape_string($json_obj["token"]);
        $angle = $json_obj["angle"];
        $temperature = $json_obj["temperature"];
        $battery = $json_obj["battery"];
        $gravity = $json_obj["gravity"];

        if ($stmt->execute() != TRUE) 
        {
            echo("Error: " . $conn->error);
        }
    }
?>
