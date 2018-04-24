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
