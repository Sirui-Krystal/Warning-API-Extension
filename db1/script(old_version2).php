<?php

$servename = "localhost";
$username = "root";
$password = "";
$dbname = "AndroidGuideAPI";
$link = $_POST["currentUrl"];
$part = explode("/",$link);
// create connection
$bridge = mysqli_connect("$servename", "$username","$password", "$dbname");

//check connection
if (!$bridge){
    echo "not connect with database";
    die();
}

$a = array();
$html = @file_get_contents($link) or die("Could not access file: $link");
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($html);
libxml_use_internal_errors(false);
$xpath = new DOMXPath($dom);

// reference pages
if ($part[3]=="reference"){
    foreach ($xpath->query('//div[@class="jd-descr "]/div[contains(@class,"api apilevel")]/table/tr[contains(@class,"api apilevel")]/td/code/a') as $div1){
        //echo string_normalize($div->textContent), "\n\n";
        $t = $div1->textContent;
        if (!in_array($t,$a)){
            array_push($a,$div1->textContent);
        }
    }
    //var_dump($a);
}

// other pages
else{
    foreach($xpath->query('//div[@class="jd-descr "]/p/code/a') as $div2){
        $t = $div2->textContent;
        if (!in_array($t,$a)){
            array_push($a,$div2->textContent);
        }
    }
    //var_dump($a);
}
/*
$sql = "SELECT Warning.WarningText,Warning.WarningType FROM Warning WHERE Warning.id IN (
SELECT RecommandWarning.WarningIndex FROM RecommandWarning WHERE RecommandWarning.EntitiesIndex IN(
SELECT Entities.id FROM Entities WHERE Entities.EntityName IN ('".implode("','",$a)."')
)
)
";
*/
$sql = "SELECT Warning.WarningText,Warning.WarningType FROM Warning WHERE Warning.id IN (
SELECT RecommandWarning.WarningIndex FROM RecommandWarning WHERE RecommandWarning.EntitiesIndex IN(
SELECT Entities.id FROM Entities WHERE Entities.EntityName IN ('" . implode("','", $a) . "')
) 
)
OR Warning.WarningSentId IN (
SELECT EntitiesRelation.Sentenceid FROM EntitiesRelation WHERE EntitiesRelation.EntityOne IN ('".implode("','",$a)."')
OR EntitiesRelation.EntityTwo IN ('".implode("','",$a)."')
)
";

$text = mysqli_query($bridge,$sql);
//var_dump($text);
if(mysqli_num_rows($text) > 0){
    $warningText = array();
    while($row = mysqli_fetch_assoc($text)) {
        if (array_key_exists($row["WarningType"], $warningText)) {
            if (!in_array($row["WarningText"],$warningText[$row["WarningType"]])){
                array_push($warningText[$row["WarningType"]],$row["WarningText"]);
            }
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
