<?php
$servername = "localhost";
$username = "root";
$password = "56y1x047";
$dbname = "gravregister";
$graveId = $_REQUEST["graveId"];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sqlGrave = "SELECT * FROM grave WHERE id = '".$graveId."'";
$grave = $conn->query($sqlGrave);


if($grave->num_rows == 1){

    $sqlPerson = "SELECT * FROM person WHERE grave = '".$graveId."'";
    $persons =  $conn->query($sqlPerson);
    if($persons){
        $innerData = "";
        $mostRecent = "";

        while($row = $persons->fetch_assoc()) {

            //Räknar ut vilket datum som någon senast är begravd
            if($mostRecent < $row['buried']){
                $mostRecent  = $row['buried'];
            }

            $innerData =
                $innerData.
                "<tr>
                    <td>".htmlspecialchars($row['firstname']). " " .htmlspecialchars($row['lastname'])."</td>
                    <td>".htmlspecialchars($row['buried'])."</td>
                    <td>".htmlspecialchars($row['GraveType'])."</td>
                </tr>";
        }

        $year = (int)substr($mostRecent,0,-6);
        if ($year <= 1995){
            $expired = "Evighetsgrav";
        } else {
            //25 år efter den senaste är begravd
            $expired = (((int)substr($mostRecent,0,-6))+25).substr($mostRecent,-6);
        }

        $generalInfo = "";

        while($row = $grave->fetch_assoc()) {
            $generalInfo = $generalInfo."
            <div class = 'owner'>
            Gravrättsinnehavare:
            <br>
            <p>
            ".htmlspecialchars($row['graveowner'])."
            </p>          
            </div>
            <div class = 'expired'>
            Utgångsdatum:
                <p>".htmlspecialchars($expired)."</p>
            </div>
            <div class = 'comment'>
            Kommentar
                <p> ".htmlspecialchars($row['comment'])."</p>
            </div>
        ";
    }

    echo"
    <div class = 'grave_image'>
            <img src='grafik/testgrav.png' alt = 'Grav bild' style='pointer-events: none; top:50%'>
    </div>
    <div class = 'first_slot' id = 'first_slot'>
        <table style = 'width:100%'>
        <tr>
            <th style = 'width:fit-content'>Namn</th>
            <th> Begravningsdatum</th>
            <th> Typ</th>
        </tr>
            ".$innerData."
        </table>
    </div>
    <div class = 'second_slot'>
    </div>
    <div class = 'general_info'>
        ".$generalInfo."
    </div>
        ";
    }
} else {
    echo "<p style = 'position:absolute;align-self: center;justify-self: center;font-size: 50px;'>Graven är tom</p>";
    }