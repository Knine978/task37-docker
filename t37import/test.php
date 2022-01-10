<?php
use Phppot\DataSource;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

require_once 'DataSource.php';
$db = new DataSource();
$conn = $db->getConnection();
require_once ('./vendor/autoload.php');

function get_to_id($conn,$region,$VC_posi,$sector) {
   // $this = new DataSource();
    //$conn1 = $this->getConnection();
    //$query = "insert into tbl_info(name,description) values('" . $name . "','" . $description . "')";
    //$queryid = "SELECT id FROM company WHERE region_id = " .$region. " AND sector = " .$sector. " AND VC_position = " .$VC_position. "";
    $VC_posi = addslashes($VC_posi);
    $queryid = "SELECT id FROM company WHERE region_id=".$region." AND sector=".$sector." AND VC_position="."'$VC_posi'"."";
    //echo $queryid;
    $result = mysqli_query($conn, $queryid);
    $row = mysqli_fetch_all ($result);
    //print($result);
    //print_r($row);
    return $row[0][0];
}

function get_from_id($conn){
    $queryid = "SELECT COUNT(*) FROM company";
    $result = mysqli_query($conn, $queryid);
    $row = mysqli_fetch_all ($result);
    //$row = mysqli_free_result($result);
    return ($row[0][0]+1);
    
}

function get_id($conn,$region,$VC_posi,$sector) {
    //restituisce ID della compagnia. restituisce 0 se non presente.
         
        $VC_posi = addslashes($VC_posi);
        $queryid = "SELECT id FROM company WHERE region_id=".$region." AND sector=".$sector." AND VC_position="."'$VC_posi'"."";
         
        if (!empty($region) && !empty($sector) && !empty($VC_posi)) {
           $result = mysqli_query($conn, $queryid);
        }
        else {
            $result = false;
            return 0;
        }
         
        $row = mysqli_fetch_all ($result);
        
        if (empty($row)) {
            return 0;
         }
         
        return $row[0][0];
         
     }

$reg = 350;
$VCp = "Refurbish";
$sec = 208;
$toid = get_id($conn,$reg,$VCp,$sec);
echo "$toid"."<br/><br/>";
//$temp = '';
//$isempty = !isset($temp);
//echo("is empty: ");var_dump($isempty);echo("<br/><br/>");//cancellare
?>