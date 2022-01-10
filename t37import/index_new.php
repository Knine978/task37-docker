<?php
use Phppot\DataSource;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

function get_company_id($conn,$region,$VC_posi,$sector) {//ritorna 0 se azienda non presente, altrimenti ritorna ID
    //echo("Inizio funzione get_to_id <br/><br/>"); //cancellare
    $VC_posi = addslashes($VC_posi);
    $region = strval($region);
    $queryid = "SELECT id FROM company WHERE region_id="."'$region'"." AND sector="."'$sector'"." AND VC_position="."'$VC_posi'"."";
    
    if (!empty($region) && !empty($sector) && !empty($VC_posi)) {
       $result = mysqli_query($conn, $queryid);
       
    }
    else {
        $result = false;
        
    }
    
    
    if (!$result) {
       return 0;
    }
    
    $row = mysqli_fetch_all ($result);
    
    if (empty($row)) {
       return 0;
    }

    return $row[0][0];
    
}

function invoicecontrol($invoice_num,$invoice_date) { //0 se fattura non presente. ID se fattura esiste nel DB
    // echo("Inizio funzione invoicecontrol <br/><br/>"); //cancellare
     global $conn;
     
     $queryid = "SELECT exchange_id FROM exchange WHERE invoice_number="."'$invoice_num'"." AND invoice_date="."'$invoice_date'"."";
     
     if (! empty($invoice_date) && ! empty($invoice_num)) {
        $result = mysqli_query($conn, $queryid);
     }
     else {
         $result = false;
     }
     
     if (!$result) {
        return 0;
     }
     
     $row = mysqli_fetch_all ($result);
     
     if (empty($row)) {
        return 0;
     }
     
     return $row[0][0];
     
 }

function insert_company() {
    //inserimento dati company
    global $db,$conn,$spreadSheet;
    $excelSheet = $spreadSheet->getSheetByName('company');
    $spreadSheetAry = $excelSheet->toArray();
    $sheetCount = count($spreadSheetAry);

    for ($i = 3; $i <= 3; $i ++) {
        $region_id = "";
        if (isset($spreadSheetAry[$i][0])) {
            $region_id = mysqli_real_escape_string($conn, $spreadSheetAry[$i][0]);
        }

        $sector = "";
        if (isset($spreadSheetAry[$i][1])) {
            $sector = mysqli_real_escape_string($conn, $spreadSheetAry[$i][1]);
        }

        $VC_position = "";
        if (isset($spreadSheetAry[$i][2])) {
            $VC_position = mysqli_real_escape_string($conn, $spreadSheetAry[$i][2]);
        }

        $domain = "";
        if (isset($spreadSheetAry[$i][3])) {
            $domain = mysqli_real_escape_string($conn, $spreadSheetAry[$i][3]);
        }

        $HQ_id = "";
        if (isset($spreadSheetAry[$i][4])) {
            $HQ_id = mysqli_real_escape_string($conn, $spreadSheetAry[$i][4]);
        }
        
        $BU_id = "";
        if (isset($spreadSheetAry[$i][5])) {
            $BU_id = mysqli_real_escape_string($conn, $spreadSheetAry[$i][5]);
        }
        
        global $from_id;
        
        $from_id = get_company_id($conn,$region_id,$VC_position,$sector);
        
        if ($from_id == 0) {
            if (! empty($region_id) || ! empty($sector) || ! empty($VC_position) || ! empty($domain) || ! empty($HQ_id) || ! empty($HQ_id)) {
                $query = "insert into company(region_id,sector,VC_position,domain,HQ_id,BU_id) values(?,?,?,?,?,?)";
                $paramType = "ssssss";
                $paramArray = array(
                    $region_id,
                    $sector,
                    $VC_position,
                    $domain,
                    $HQ_id,
                    $BU_id
                );
                //$result = mysql_query('SET foreign_key_checks = 0');
               
                $insertId = $db->insert($query, $paramType, $paramArray);
                // $query = "insert into tbl_info(name,description) values('" . $name . "','" . $description . "')";
                // $result = mysqli_query($conn, $query);
                
               
                $from_id = $insertId;
            
                if (! empty($insertId)) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }    
    //fine inserimento dati company

}

function insert_exchange() {
    //inserimento dati exchange
    global $db,$conn,$spreadSheet,$from_id,$to_id,$OK;
    $excelSheet = $spreadSheet->getSheetByName('exchange');
    $spreadSheetAry = $excelSheet->toArray();
    $sheetCount = count($spreadSheetAry);
    //var_dump("righe exchange ".$sheetCount);echo("<br/><br/>"); //cancellare
    //echo("righe exchange ".$sheetCount."<br/><br/>"); //cancellare
    for ($i = 3; $i < $sheetCount; $i ++) {
        //echo("Indice FOR: ".$i."<br/><br/>"); //cancellare
        
        $circular_entity_name = "";
        if (isset($spreadSheetAry[$i][0])) {
            $circular_entity_name = mysqli_real_escape_string($conn, $spreadSheetAry[$i][0]);
        }
        
        $exchange_moment = "";
        if (isset($spreadSheetAry[$i][1])) {
            $exchange_moment = mysqli_real_escape_string($conn, $spreadSheetAry[$i][1]);
        }
        
        $invoice_num = "";
        if (isset($spreadSheetAry[$i][2])) {
            $invoice_num = mysqli_real_escape_string($conn, $spreadSheetAry[$i][2]);
        }
        
        $Kilograms = "";
        if (isset($spreadSheetAry[$i][3])) {
            $Kilograms = mysqli_real_escape_string($conn, $spreadSheetAry[$i][3]);
        }
        
        $n_units = "";
        if (isset($spreadSheetAry[$i][4])) {
            $n_units = mysqli_real_escape_string($conn, $spreadSheetAry[$i][4]);
        }
        
        $KG_unit_estime = "";
        if (isset($spreadSheetAry[$i][5])) {
            $KG_unit_estime = mysqli_real_escape_string($conn, $spreadSheetAry[$i][5]);
        }
        
        $composition_exchange = "";
        if (isset($spreadSheetAry[$i][6])) {
            $composition_exchange = mysqli_real_escape_string($conn, $spreadSheetAry[$i][6]);
        }
        
        $invoice_date = "";
        if (isset($spreadSheetAry[$i][1])) {
            $invoice_date = mysqli_real_escape_string($conn, $spreadSheetAry[$i][1]);
        }

        $to_region = "";
        if (isset($spreadSheetAry[$i][7])) {
            $to_region = mysqli_real_escape_string($conn, $spreadSheetAry[$i][7]);
        }
        //echo("Region: ".$to_region."<br/><br/>"); //cancellare
        
        $to_vcposition = "";
        if (isset($spreadSheetAry[$i][9])) {
            $to_vcposition = mysqli_real_escape_string($conn, $spreadSheetAry[$i][9]);
        }
        //echo("VC Position: ".$to_vcposition."<br/><br/>"); //cancellare
        
        $to_sector = "";
        if (isset($spreadSheetAry[$i][8])) {
            $to_sector = mysqli_real_escape_string($conn, $spreadSheetAry[$i][8]);
        }
        //echo("Sector: ".$to_sector."<br/><br/>"); //cancellare
       // $from_id = get_from_id($conn);
        
        $to_id = get_company_id($conn,$to_region,$to_vcposition,$to_sector);
        //echo("to_id: ".$to_id."<br/><br/>");
        
        $invoice = invoicecontrol($invoice_num,$invoice_date);
        //echo("Invoice control: ");var_dump($invoice); echo("<br/><br/>");//cancellare
        if ($circular_entity_name<>"" && $invoice == 0) {

            if (! empty($circular_entity_name) || ! empty($from_id) || ! empty($to_id) || ! empty($exchange_moment) || ! empty($Kilograms) 
            || ! empty($n_units) || ! empty($KG_unit_estime) || ! empty($composition_exchange) || ! empty($invoice_num) || ! empty($invoice_date)
            || ! empty($to_region) || ! empty($to_vcposition) || ! empty($to_sector) )
            {   
                //echo("Inserisco nuovo scambio <br/><br/>"); //cancellare
                //echo("Var invoice: ".$invoice);echo("<br/><br/>");
                //echo("Kilograms: ".$Kilograms);echo("<br/><br/>");
                $query = "insert into exchange(circular_entity_name,from_id,to_id,exchange_moment,Kilograms,n_units,KG_unit_estime,invoice_number,invoice_date,to_region,to_vcposition,to_sector) values(?,?,?,?,?,?,?,?,?,?,?,?)";
                //echo("Query: ".$query);echo("<br/><br/>");//cancellare
                $paramType = "ssssssssssss";
                $paramArray = array(
                    $circular_entity_name,
                    $from_id,
                    $to_id,
                    $exchange_moment,
                    floatval($Kilograms),
                    $n_units,
                    $KG_unit_estime,
                    $invoice_num,
                    $invoice_date,
                    $to_region,
                    $to_vcposition,
                    $to_sector
                );
                $insertId = $db->insert($query, $paramType, $paramArray);
                // $query = "insert into tbl_info(name,description) values('" . $name . "','" . $description . "')";
                // $result = mysqli_query($conn, $query);
                
                $material_array[$composition_exchange] = $insertId;
                var_dump($material_array);
                echo("Material array: ".$material_array[$composition_exchange]);
                if (! empty($insertId)) {
                    return true;
                } else {
                    return false;
                }
            }
       
        }
    }
    //fine inserimento dati exchange
}

require_once 'DataSource.php';
$from_id = '';
$db = new DataSource();
$conn = $db->getConnection();
require_once ('./vendor/autoload.php');
$material_array = [];
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (isset($_POST["import"])) {

    $allowedFileType = [
        'application/vnd.ms-excel',
        'text/xls',
        'text/xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    if (in_array($_FILES["file"]["type"], $allowedFileType)) {

        $targetPath = 'uploads/' . $_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);

        $Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();


        
        $spreadSheet = $Reader->load($targetPath);

        $company_bool=insert_company();
        
        $exchange_bool=insert_exchange();

        if ($company_bool) {
            $type_company = "success";
            $message_company = "Azienda inserita nel database";
        } else {
            $type_company = "error";
            $message_company = "Azienda non inserita, gia presente nel database";
        }

        if ($exchange_bool) {
            $type_exchange = "success";
            $message_exchange = "Scambi inseriti nel database";
        } else {
            $type_exchange = "error";
            $message_exchange = "Scambi non inseriti, gia presenti nel database";
        }

    } else {
        $type = "error";
        $message = "Invalid File Type. Upload Excel File.";
        }    
    }
?>


<!DOCTYPE html>
<html>
<head>
<style>
body {
	font-family: Arial;
	width: 600px;
    margin: auto;
}

.outer-container {
	background: #F0F0F0;
	border: #e0dfdf 1px solid;
	padding: 40px 20px;
	border-radius: 2px;
}

.btn-submit {
	background: #333;
	border: #1d1d1d 1px solid;
	border-radius: 2px;
	color: #f0f0f0;
	cursor: pointer;
	padding: 5px 20px;
	font-size: 0.9em;
}

.tutorial-table {
	margin-top: 40px;
	font-size: 0.8em;
	border-collapse: collapse;
	width: 100%;
}

.tutorial-table th {
	background: #f0f0f0;
	border-bottom: 1px solid #dddddd;
	padding: 8px;
	text-align: left;
}

.tutorial-table td {
	background: #FFF;
	border-bottom: 1px solid #dddddd;
	padding: 8px;
	text-align: left;
}

#response {
	padding: 10px;
	margin-top: 10px;
	border-radius: 2px;
	display: none;
}

.success {
	background: #c7efd9;
	border: #bbe2cd 1px solid;
}

.error {
	background: #fbcfcf;
	border: #f3c6c7 1px solid;
}

div#response.display-block {
	display: block;
}
</style>
</head>

<body>
    <h2>Import page for DigiPrime Task 3.7 database v. 0.2b</h2>

    <div class="outer-container">
        <form action="" method="post" name="frmExcelImport"
            id="frmExcelImport" enctype="multipart/form-data">
            <div>
                <label>Choose Excel File</label> <input type="file"
                    name="file" id="file" accept=".xls,.xlsx">
                <button type="submit" id="submit" name="import"
                    class="btn-submit">Import</button>

            </div>

        </form>

    </div>
    <div id="response"
        class="<?php if(!empty($type_company)) { echo $type_company . " display-block"; } ?>"><?php if(!empty($message_company)) { echo $message_company; } ?></div>
        <div id="response"
        class="<?php if(!empty($type_exchange)) { echo $type_exchange . " display-block"; } ?>"><?php if(!empty($message_exchange)) { echo $message_exchange; } ?></div>        
    
</body>
</html>