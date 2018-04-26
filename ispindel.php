<!--
Copyright � 2018, Martin Du�ek. V�echna pr�va vyhrazena.
Redistribuce a pou�it� zdrojov�ch i bin�rn�ch forem d�la, v p�vodn�m i upravovan�m tvaru, jsou povoleny za n�sleduj�c�ch podm�nek:

���en� zdrojov� k�d mus� obsahovat v��e uvedenou informaci o copyrightu, tento seznam podm�nek a n�e uveden� z�eknut� se odpov�dnosti.
���en� bin�rn� tvar mus� n�st v��e uvedenou informaci o copyrightu, tento seznam podm�nek a n�e uveden� z�eknut� se odpov�dnosti ve sv� dokumentaci a/nebo dal��ch poskytovan�ch materi�lech.
Ani jm�no vlastn�ka pr�v, ani jm�na p�isp�vatel� nemohou b�t pou�ita p�i podpo�e nebo pr�vn�ch aktech souvisej�c�ch s produkty odvozen�mi z tohoto softwaru bez v�slovn�ho p�semn�ho povolen�.
TENTO SOFTWARE JE POSKYTOV�N DR�ITELEM LICENCE A JEHO P�ISP�VATELI �JAK STOJ� A LE�͓ A JAK�KOLIV V�SLOVN� NEBO P�EDPOKL�DAN� Z�RUKY V�ETN�, ALE NEJEN, P�EDPOKL�DAN�CH OBCHODN�CH Z�RUK A Z�RUKY VHODNOSTI PRO JAK�KOLIV ��EL JSOU POP�ENY. DR�ITEL, ANI P�ISP�VATEL� NEBUDOU V ��DN�M P��PAD� ODPOV�DNI ZA JAK�KOLIV P��M�, NEP��M�, N�HODN�, ZVL��TN�, P��KLADN� NEBO VYPL�VAJ�C� �KODY (V�ETN�, ALE NEJEN, �KOD VZNIKL�CH NARU�EN�M DOD�VEK ZBO�� NEBO SLU�EB; ZTR�TOU POU�ITELNOSTI, DAT NEBO ZISK�; NEBO P�ERU�EN�M OBCHODN� �INNOSTI) JAKKOLIV ZP�SOBEN� NA Z�KLAD� JAK�KOLIV TEORIE O ZODPOV�DNOSTI, A� U� PLYNOUC� Z JIN�HO SMLUVN�HO VZTAHU, UR�IT� ZODPOV�DNOSTI NEBO P�E�INU (V�ETN� NEDBALOSTI) NA JAK�MKOLIV ZP�SOBU POU�IT� TOHOTO SOFTWARE, I V P��PAD�, �E DR�ITEL PR�V BYL UPOZORN�N NA MO�NOST TAKOV�CH �KOD.
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
