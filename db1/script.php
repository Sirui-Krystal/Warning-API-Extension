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
$mulEN = array();
$orderEn = array();
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
        $t = $div1->textContent;
        array_push($mulEN,$t);
    }
    $counts = array_count_values($mulEN);
    arsort($counts);
    $orderEn = array_keys($counts);
}

// other pages
else{
    foreach($xpath->query('//div[@class="jd-descr "]/p/code/a') as $div2){
        $t = $div2->textContent;
        array_push($mulEN,$t);
        /*
           if (!in_array($t,$a)){
               array_push($a,$t);
           }
           */
    }
    $counts = array_count_values($mulEN);
    arsort($counts);
    $orderEn = array_keys($counts);
}
//print_r($counts);

// map id to EntityName
$Enid_EnName = array();
$Enid = array();
$sql1 = "SELECT id,EntityName FROM Entities WHERE EntityName IN ('". implode("','", $orderEn)."')";
$text1 = mysqli_query($bridge,$sql1);
if(mysqli_num_rows($text1) > 0){
    while($row = mysqli_fetch_assoc($text1)) {
        if (array_key_exists($row["EntityName"], $counts)){
            if (! in_array($row["id"], $Enid)){
                array_push($Enid, $row["id"]);
            }
            if (array_key_exists($row["id"], $Enid_EnName)) {
                if (!in_array($row["EntityName"],$Enid_EnName[$row["id"]])){
                    array_push($Enid_EnName[$row["id"]],$row["EntityName"]);
                }
            } else{
                $Enid_EnName[$row["id"]] = $row["EntityName"];
                //array_push($Enid_EnName[$row["id"]],$row["EntityName"]);
            }
        }

    }
}
//print_r($Enid_EnName);

// map EntitiesIndex to WarningIndex
$Enid_Warningid = array();
$hasWarning_Enid = array();
$Warningid = array();
$sql2 = "SELECT WarningIndex,EntitiesIndex FROM RecommandWarning WHERE EntitiesIndex IN ('".implode("','",$Enid)."')";
$text2 = mysqli_query($bridge,$sql2);
if(mysqli_num_rows($text2) > 0){
    while($row = mysqli_fetch_assoc($text2)) {
        if (! in_array($row["EntitiesIndex"],$hasWarning_Enid)){
            array_push($hasWarning_Enid,$row["EntitiesIndex"]);
        }
        if (! in_array($row["WarningIndex"], $Warningid)){
            array_push($Warningid, $row["WarningIndex"]);
        }
        if (array_key_exists($row["EntitiesIndex"], $Enid_Warningid)) {
            if (!in_array($row["WarningIndex"],$Enid_Warningid[$row["EntitiesIndex"]])){
                array_push($Enid_Warningid[$row["EntitiesIndex"]],$row["WarningIndex"]);
            }
        } else{
            $Enid_Warningid[$row["EntitiesIndex"]] = array();
            array_push($Enid_Warningid[$row["EntitiesIndex"]],$row["WarningIndex"]);
        }
    }
}
//print_r($Enid_Warningid);

// map id to WarningText and WarningType
$Warningid_TextType = array();
$sql3 = "SELECT id, WarningText, WarningType,WarningURL FROM Warning WHERE id IN ('".implode("','",$Warningid)."')";
$text3 = mysqli_query($bridge,$sql3);
if(mysqli_num_rows($text3) > 0){
    //$test = array();
    while($row = mysqli_fetch_assoc($text3)) {
        if(!in_array($row["id"],$Warningid_TextType)){
            $Warningid_TextType[$row["id"]] = array();
        }
        if(!array_key_exists($row["WarningType"],$Warningid_TextType[$row["id"]])){
            $Warningid_TextType[$row["id"]][$row["WarningType"]]=array();
            $Warningid_TextType[$row["id"]][$row["WarningType"]][$row["WarningText"]] = $row["WarningURL"];
        }else{
            /*if(!in_array($row["WarningText"],$Warningid_TextType[$row["id"]][$row["WarningType"]])){
                array_push($Warningid_TextType[$row["id"]][$row["WarningType"]],$row["WarningText"]);
            }
            */
            if(!array_key_exists($row["WarningText"],$Warningid_TextType[$row["id"]][$row["WarningType"]])){
                $Warningid_TextType[$row["id"]][$row["WarningType"]][$row["WarningText"]] = $row["WarningURL"];
            }
        }
    }
}
//print_r($Warningid_TextType);
//print_r(array_keys($Warningid_TextType));
//print sizeof($Warningid_TextType);
//print_r($hasWarning_Enid);

// pass data to crawler.js
$hasWarnEnName_count = array();
$hasWarnEnName_Enid = array();
$hasWarnEnName_Warnid = array();
$data_allType = array();
foreach($hasWarning_Enid as $hEnid){
    if(!array_key_exists($Enid_EnName[$hEnid],$hasWarnEnName_count)){
        $hasWarnEnName_count[$Enid_EnName[$hEnid]] = $counts[$Enid_EnName[$hEnid]];

    }
    if(!array_key_exists($Enid_EnName[$hEnid],$hasWarnEnName_Enid)){
        $hasWarnEnName_Enid[$Enid_EnName[$hEnid]] = array();
        array_push($hasWarnEnName_Enid[$Enid_EnName[$hEnid]],$hEnid);
    }else{
        array_push($hasWarnEnName_Enid[$Enid_EnName[$hEnid]],$hEnid);
    }

}
arsort($hasWarnEnName_count);

foreach ($hasWarnEnName_count as $key=>$value){
    $data_allType[$key] = array();
    $data_allType[$key]["Error"] = array();
    $data_allType[$key]["Exception"] = array();
    $data_allType[$key]["Warning"] = array();
    $data_allType[$key]["Note"] = array();
    $data_allType[$key]["Condition"] = array();
    $data_allType[$key]["Can"] = array();
    $data_allType[$key]["Must"] = array();
    $data_allType[$key]["Should"] = array();
    $data_allType[$key]["May"] = array();
    $data_allType[$key]["Will"] = array();
    $data_allType[$key]["Other"] = array();
}

foreach ($hasWarnEnName_Enid as $key=>$value){
    if (!array_key_exists($key, $hasWarnEnName_Warnid)){
        $hasWarnEnName_Warnid[$key] = array();
        foreach ($value as $v){
            foreach($Enid_Warningid[$v] as $vv){
                array_push($hasWarnEnName_Warnid[$key],$vv);
            }

        }
    }
}
//print_r($hasWarnEnName_Warnid);

// may have problems, need double check with new database
foreach($hasWarnEnName_Warnid as $key=>$value){
    foreach ($value as $v) {
        $buffer = $Warningid_TextType[$v];
        foreach($buffer as $k=>$vl){
            if(!in_array($vl, $data_allType[$key][$k])){
                array_push($data_allType[$key][$k],$vl);
            }
        }
    }
}
//print_r($data_allType);
$data = array();
foreach ($data_allType as $key=>$value){
    $data[$key]=array_filter($data_allType[$key]);
}
//print_r($data);
if ($data == null){
    echo 'invalid';
}else{
    echo json_encode($data);
    //print_r($data);
}

$bridge->close();
?>
