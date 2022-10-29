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
        $innerDataEdit = "";
        $mostRecent = "";

        while($row = $persons->fetch_assoc()) {

            //Räknar ut vilket datum som någon senast är begravd
            if($mostRecent < $row['buried']){
                $mostRecent  = $row['buried'];
            }

            $innerData =
                $innerData.
                "<tr>
                    <td style = 'width:40%'>".htmlspecialchars($row['firstname']). " " .htmlspecialchars($row['lastname'])."</td>
                    <td style = 'width:10%'>".htmlspecialchars($row['buried'])."</td>
                    <td style = 'width:10%'>".htmlspecialchars($row['GraveType'])."</td>
                </tr>";

            $innerDataEdit =
                $innerDataEdit.
                "<tr>
                    <td style = 'width:40%'><input type = text style = 'width:80%' maxlength='30' value = '".htmlspecialchars($row['firstname']). " " .htmlspecialchars($row['lastname'])."'></td>
                    <td style = 'width:10%'><input type = text style = 'width:60%' maxlength='10' value = '".htmlspecialchars($row['buried'])."'></td>
                    <td style = 'width:10%'><input type = text style = 'width:100%' maxlength='10' value = '".htmlspecialchars($row['GraveType'])."'></td>
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
        $generalInfoEdit = "";

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

        $generalInfoEdit = $generalInfoEdit."
        <div class = 'owner'>
        Gravrättsinnehavare:
        <br>
        <textarea  rows = 3; style = 'width: 50%; resize: none; margin-left: 20px; margin-top: 15px;' maxlength='100' >".htmlspecialchars($row['graveowner'])."</textarea>
        </div>
        <div class = 'expired'>
        Utgångsdatum:
        <br>
        <p>".htmlspecialchars($expired)."</p>
        </div>
        <div class = 'comment'>
        Kommentar
        <br>
        <textarea  rows = 4;  style = 'width: 50%; overflow:hidden; resize: none; margin-left: 20px; margin-top: 15px;' >".htmlspecialchars($row['comment'])."</textarea>
        </div>
        ";
    }

    echo"
    <div class = 'grave_image'>
            <img src='grafik/testgrav.png' alt = 'Grav bild' style='pointer-events: none; top:50%'>
    </div>
    <div class = 'first_slot' id = 'first_slot'>
        <table style ='border-spacing: 5px;'>
        <tr>
            <th>Namn</th>
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

    echo " || "; //delimiter

    echo "
    <div class = 'grave_image'>
        <img src='grafik/testgrav.png' alt = 'Grav bild' style='pointer-events: none; top:50%'>
    </div>
    <div class = 'first_slot' id = 'first_slot'>
        <table id = 'editTable' style ='border-spacing: 5px;'>
        <tr>
            <th>Namn</th>
            <th> Begravningsdatum</th>
            <th> Typ</th>
        </tr>
            ".$innerDataEdit." 
        </table>
        <button id = 'addRow' style = 'margin-left:6px' onclick = 'addPersonRow()'>Lägg till rad</button>
    </div>
    <div class = 'second_slot'>
    </div>
    <div class = 'general_info'>
        ".$generalInfoEdit."
    </div>";
    }
} else {
    echo "<p style = 'position:absolute;align-self: center;justify-self: center;font-size: 50px;'>Graven är tom</p>";
    }