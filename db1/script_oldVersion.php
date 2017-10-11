<?php
$servename = "localhost";
$username = "root";
$password = "";
$dbname = "AndroidGuideAPI";
$link = $_POST["currentUrl"];

// create connection
$bridge = mysqli_connect("$servename", "$username","$password", "$dbname");


//check connection
if (!$bridge){
    echo "not connect with database";
    die();
}

//do SQL query
/*
$sql = "SELECT Warning.WarningText,Warning.WarningType FROM Warning WHERE Warning.id IN (
SELECT RecommandWarning.WarningIndex FROM RecommandWarning WHERE RecommandWarning.EntitiesIndex IN(
SELECT EntitiesRelation.index from EntitiesRelation WHERE EntitiesRelation.EntityOne IN(
SELECT Entities.EntityName FROM Entities WHERE Entities.EntityURL='".$link."'
) OR EntitiesRelation.EntityTwo IN(
    SELECT Entities.EntityName FROM Entities WHERE Entities.EntityURL='".$link."'
    )
)
)";
*/
$sql1 = "SELECT Warning.WarningText,Warning.WarningType FROM Warning WHERE Warning.WarningSentId IN (
SELECT EntitiesRelation.Sentenceid FROM EntitiesRelation WHERE EntitiesRelation.EntityOne IN(
SELECT Entities.EntityName FROM Entities WHERE Entities.EntityURL='".$link."'
) OR EntitiesRelation.EntityTwo IN(
    SELECT Entities.EntityName FROM Entities WHERE Entities.EntityURL='".$link."'
    )
)
";
$text = mysqli_query($bridge,$sql1);
if(mysqli_num_rows($text) > 0){
    $warningText = array();
    while($row = mysqli_fetch_assoc($text)) {
        if (array_key_exists($row["WarningType"], $warningText)) {
            array_push($warningText[$row["WarningType"]],$row["WarningText"]);
        }
        else{
            $warningText[$row["WarningType"]] = array();
            array_push($warningText[$row["WarningType"]],$row["WarningText"]);
        }
    }
    echo json_encode($warningText);
}else{
    echo "invalid";
}
$bridge->close();
?>
