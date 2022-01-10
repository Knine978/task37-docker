<?php
use Phppot\DataSource;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;



function get_to_id($conn,$region,$VC_posi,$sector) {
     //echo("Inizio funzione get_to_id <br/><br/>"); //cancellare
     $VC_posi = addslashes($VC_posi);
     $queryid = "SELECT id FROM company WHERE region_id=".$region." AND sector=".$sector." AND VC_position="."'$VC_posi'"."";
     //echo($queryid."<br/><br/>"); //cancellare
     if (!empty($region) && !empty($sector) && !empty($VC_posi)) {
        $result = mysqli_query($conn, $queryid);
     }
     else {
         $result = false;
     }
     
     
     if (!$result) {
       // echo("Errore nella query!"."<br/><br/>"); //cancellare
        //echo("Fine funzione get_to_id  - ritorno 0 ! <br/><br/>"); //cancellare 
        return 0;
     }
     $row = mysqli_fetch_all ($result);
     if (empty($row)) {
        //echo("Array VUOTO. Ritorno 0."."<br/><br/>"); //cancellare
        //echo("Fine funzione get_to_id  - ritorno 0 ! <br/><br/>"); //cancellare 
        return 0;
     }
     //print_r($row);echo("<br/><br/>"); //cancellare
     //echo("To ID: ".$row[0][0]."<br/><br/>"); //cancellare
     //echo("Fine funzione get_to_id  - uscita con risultato ! <br/><br/>"); //cancellare 
     return $row[0][0];
     
 }
 
 function get_from_id($conn){
     $queryid = "SELECT COUNT(*) FROM company";
     $result = mysqli_query($conn, $queryid);
     if (!$result) {
        return 0;
    }
    //$row = mysqli_fetch_all ($result);
    //echo("From ID: ".$row[0][0]."<br/><br/>"); //cancellare
    //return ($row[0][0]);
    //printf ("New Record has id %d.\n", mysqli_insert_id($conn));
    return mysqli_insert_id($conn); 
 }

 function invoicecontrol($conn,$invoice_num,$invoice_date) { //TRUE se non fattura non presente. FALSE se fattura esiste nel DB
   // echo("Inizio funzione invoicecontrol <br/><br/>"); //cancellare
    $queryid = "SELECT exchange_id FROM exchange WHERE invoice_number=".$invoice_num." AND invoice_date="."'$invoice_date'"."";
    //echo($queryid."<br/><br/>"); //cancellare
    if (! empty($invoice_date) && ! empty($invoice_num)) {
       $result = mysqli_query($conn, $queryid);
    }
    else {
        $result = false;
    }
    
    
    if (!$result) {
      // echo("Fattura non presente"."<br/><br/>"); //cancellare
       //echo("Fine funzione invoicecontrol  - ritorno TRUE ! <br/><br/>"); //cancellare 
       return TRUE;
    }
    $row = mysqli_fetch_all ($result);
    if (empty($row)) {
       //echo("Array VUOTO. Ritorno TRUE."."<br/><br/>"); //cancellare
       //echo("Fine funzione invoicecontrol  - ritorno TRUE ! <br/><br/>"); //cancellare 
       return TRUE;
    }
    //echo("Risultato invoicecontrol: "); print_r($row);echo("<br/><br/>"); //cancellare
    //echo("Fine funzione invoicecontrol  - uscita con risultato ! ritorno FALSE<br/><br/>"); //cancellare 
    return FALSE;
    
}
 
function companycontrol($conn,$region_id,$sector,$VC_position) { //TRUE se compagnia non presente. FALSE se gia presente nel DB.
    // echo("Inizio funzione invoicecontrol <br/><br/>"); //cancellare
     $queryid = "SELECT id FROM company WHERE region_id=".$region_id." AND sector="."'$sector'"." AND VC_position="."'$VC_position'"."";
     //echo($queryid."<br/><br/>"); //cancellare
     if (! empty($region_id) && ! empty($sector) && ! empty($VC_position)) {
        $result = mysqli_query($conn, $queryid);
     }
     else {
         $result = false;
     }
     
     
     if (!$result) {
       // echo("Fattura non presente"."<br/><br/>"); //cancellare
        //echo("Fine funzione invoicecontrol  - ritorno TRUE ! <br/><br/>"); //cancellare 
        return TRUE;
     }
     $row = mysqli_fetch_all ($result);
     if (empty($row)) {
        //echo("Array VUOTO. Ritorno TRUE."."<br/><br/>"); //cancellare
        //echo("Fine funzione invoicecontrol  - ritorno TRUE ! <br/><br/>"); //cancellare 
        return TRUE;
     }
     //echo("Risultato invoicecontrol: "); print_r($row);echo("<br/><br/>"); //cancellare
     //echo("Fine funzione invoicecontrol  - uscita con risultato ! ritorno FALSE<br/><br/>"); //cancellare 
     global $insertId;
     $insertId = $row[0][0];

     return FALSE;
     
 }

require_once 'DataSource.php';
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
        //$excelSheet = $spreadSheet->getActiveSheet();

        //inserimento dati company
        $excelSheet = $spreadSheet->getSheetByName('company');
        $spreadSheetAry = $excelSheet->toArray();
        $sheetCount = count($spreadSheetAry);

        for ($i = 1; $i <= $sheetCount; $i ++) {
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
            if (isset($spreadSheetAry[$i][0])) {
                $HQ_id = mysqli_real_escape_string($conn, $spreadSheetAry[$i][4]);
            }
            
            $BU_id = "";
            if (isset($spreadSheetAry[$i][1])) {
                $BU_id = mysqli_real_escape_string($conn, $spreadSheetAry[$i][5]);
            }
           /* echo "region_id " . $region_id . "<br>";
            echo "sector " . $sector . "<br>";1
            echo "VC_position " . $VC_position . "<br>";
            echo "domain " . $domain . "<br>";*/
            if (companycontrol($conn,$region_id,$sector,$VC_position)) {
                if (! empty($region_id) || ! empty($sector) || ! empty($VC_position) || ! empty($domain) || ! empty($HQ_id) || ! empty($HQ_id)) {
                    // echo "sono nel IF di controllo valori";
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
                    $insertId = $db->insert($query, $paramType, $paramArray);
                    // $query = "insert into tbl_info(name,description) values('" . $name . "','" . $description . "')";
                    // $result = mysqli_query($conn, $query);
                    //echo "RISULTATO QUERY INSERT: " . $insertId . "<br>";
                
                
                    if (! empty($insertId)) {
                        $type = "success";
                        $message = "Excel Data Imported into the Database";
                    } else {
                        $type = "error";
                        $message = "Problem in Importing Excel Data";
                    }
                }
            }
        }    
        //fine inserimento dati company

     
        
        //inserimento dati exchange
        $excelSheet = $spreadSheet->getSheetByName('exchange');
        $spreadSheetAry = $excelSheet->toArray();
        $sheetCount = count($spreadSheetAry);
        //var_dump("righe exchange ".$sheetCount);echo("<br/><br/>"); //cancellare
        //echo("righe exchange ".$sheetCount."<br/><br/>"); //cancellare
        for ($i = 1; $i < $sheetCount; $i ++) {
            //echo("Indice FOR: ".$i."<br/><br/>"); //cancellare
            $circular_entity_name = "";
            if (isset($spreadSheetAry[$i][0])) {
                $circular_entity_name = mysqli_real_escape_string($conn, $spreadSheetAry[$i][0]);
            }
            /*
            $from_id = "";
            if (isset($spreadSheetAry[$i][1])) {
                $from_id = mysqli_real_escape_string($conn, $spreadSheetAry[$i][1]);
            }
            $to_id = "";
            if (isset($spreadSheetAry[$i][2])) {
                $to_id = mysqli_real_escape_string($conn, $spreadSheetAry[$i][2]);
            }
            */
            $exchange_moment = "";
            if (isset($spreadSheetAry[$i][1])) {
                $exchange_moment = mysqli_real_escape_string($conn, $spreadSheetAry[$i][1]);
            }
            $Kilograms = "";
            if (isset($spreadSheetAry[$i][2])) {
                $Kilograms = mysqli_real_escape_string($conn, $spreadSheetAry[$i][2]);
            }
            $n_units = "";
            if (isset($spreadSheetAry[$i][3])) {
                $n_units = mysqli_real_escape_string($conn, $spreadSheetAry[$i][3]);
            }
            $KG_unit_estime = "";
            if (isset($spreadSheetAry[$i][4])) {
                $KG_unit_estime = mysqli_real_escape_string($conn, $spreadSheetAry[$i][4]);
            }
            $composition_exchange = "";
            if (isset($spreadSheetAry[$i][5])) {
                $composition_exchange = mysqli_real_escape_string($conn, $spreadSheetAry[$i][5]);
            }

            $invoice_num = "";
            if (isset($spreadSheetAry[$i][6])) {
                $invoice_num = mysqli_real_escape_string($conn, $spreadSheetAry[$i][6]);
            }

            $invoice_date = "";
            if (isset($spreadSheetAry[$i][7])) {
                $invoice_date = mysqli_real_escape_string($conn, $spreadSheetAry[$i][7]);
            }

            $to_region = "";
            if (isset($spreadSheetAry[$i][8])) {
                $to_region = mysqli_real_escape_string($conn, $spreadSheetAry[$i][8]);
            }
            //echo("Region: ".$to_region."<br/><br/>"); //cancellare
            $to_vcposition = "";
            if (isset($spreadSheetAry[$i][9])) {
                $to_vcposition = mysqli_real_escape_string($conn, $spreadSheetAry[$i][9]);
            }
            //echo("VC Position: ".$to_vcposition."<br/><br/>"); //cancellare
            $to_sector = "";
            if (isset($spreadSheetAry[$i][10])) {
                $to_sector = mysqli_real_escape_string($conn, $spreadSheetAry[$i][10]);
            }
            //echo("Sector: ".$to_sector."<br/><br/>"); //cancellare
           // $from_id = get_from_id($conn);
            
            $to_id = get_to_id($conn,$to_region,$to_vcposition,$to_sector);
            $from_id = $insertId;
            $invoice = invoicecontrol($conn,$invoice_num,$invoice_date);
            //echo("Invoice control: ");var_dump($invoice); echo("<br/><br/>");//cancellare
            if ($circular_entity_name<>"" && $invoice) {

                if (! empty($circular_entity_name) || ! empty($from_id) || ! empty($to_id) || ! empty($exchange_moment) || ! empty($Kilograms) 
                || ! empty($n_units) || ! empty($KG_unit_estime) || ! empty($composition_exchange) || ! empty($invoice_num) || ! empty($invoice_date)
                || ! empty($to_region) || ! empty($to_vcposition) || ! empty($to_sector) )
                {   
                    //echo("Inserisco nuovo scambio <br/><br/>"); //cancellare
                    //echo("Var invoice: ".$invoice);echo("<br/><br/>");
                    //echo("Var circ ent name: ".$circular_entity_name);echo("<br/><br/>");
                    $query = "insert into exchange(circular_entity_name,from_id,to_id,exchange_moment,Kilograms,n_units,KG_unit_estime,invoice_number,invoice_date,to_region,to_vcposition,to_sector) values(?,?,?,?,?,?,?,?,?,?,?,?)";
                    //echo("Query: ".$query);echo("<br/><br/>");//cancellare
                    $paramType = "ssssssssssss";
                    $paramArray = array(
                        $circular_entity_name,
                        $from_id,
                        $to_id,
                        $exchange_moment,
                        $Kilograms,
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
                        $type = "success";
                        $message = "Excel Data Imported into the Database";
                    } else {
                        $type = "error";
                        $message = "Problem in Importing Excel Data";
                    }
                }
           
            }
        }
        //fine inserimento dati exchange

         //inserimento dati material
         $excelSheet = $spreadSheet->getSheetByName('material');
         $spreadSheetAry = $excelSheet->toArray();
         $sheetCount = count($spreadSheetAry);
 
         for ($i = 1; $i <= $sheetCount; $i ++) {
             $Material_Name = "";
             if (isset($spreadSheetAry[$i][0])) {
                 $Material_Name = mysqli_real_escape_string($conn, $spreadSheetAry[$i][0]);
             }
 
             $Percentage = "";
             if (isset($spreadSheetAry[$i][1])) {
                 $Percentage = mysqli_real_escape_string($conn, $spreadSheetAry[$i][1]);
             }
 
             $Loops = "";
             if (isset($spreadSheetAry[$i][2])) {
                 $Loops = mysqli_real_escape_string($conn, $spreadSheetAry[$i][2]);
             }
 
             $CRM = "";
             if (isset($spreadSheetAry[$i][3])) {
                 $CRM = mysqli_real_escape_string($conn, $spreadSheetAry[$i][3]);
             }

             $exchange_id = "";
             if (isset($spreadSheetAry[$i][4]) && ! empty($material_array)) {
                $ind = strval(mysqli_real_escape_string($conn, $spreadSheetAry[$i][4]));
                //var_dump($material_array);
                //echo("ind: ".$ind);
                $exchange_id = $material_array[$ind];
                //echo("Exchange id: ".$exchange_id);
            }
            else {
                $exchange_id = 0;
            }
             /*echo "Material_Name " . $Material_Name . "<br>";
             echo "Percentage " . $Percentage . "<br>";
             echo "Loops " . $Loops . "<br>";
             echo "CRM " . $CRM . "<br>";*/
             if ((! empty($Material_Name) || ! empty($Percentage) || ! empty($Loops) || ! empty($CRM) || ! empty($exchange_id)) && ! empty($material_array)) {
                // echo "sono nel IF di controllo valori";
                 $query = "insert into material(Material_Name,Percentage,Loops,CRM,exchange_id) values(?,?,?,?,?)";
                 $paramType = "sssss";
                 $paramArray = array(
                     $Material_Name,
                     $Percentage,
                     $Loops,
                     $CRM,
                     $exchange_id
                 );
                 $insertId = $db->insert($query, $paramType, $paramArray);
                 // $query = "insert into tbl_info(name,description) values('" . $name . "','" . $description . "')";
                 // $result = mysqli_query($conn, $query);
 //echo "RISULTATO QUERY INSERT: " . $insertId . "<br>";
                 if (! empty($insertId)) {
                     $type = "success";
                     $message = "Excel Data Imported into the Database";
                 } else {
                     $type = "error";
                     $message = "Problem in Importing Excel Data";
                 }
             }
         }
         //fine inserimento dati material

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
	width: 550px;
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
    <h2>Import page for DigiPrime Task 3.7 database</h2>

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
        class="<?php if(!empty($type)) { echo $type . " display-block"; } ?>"><?php if(!empty($message)) { echo $message; } ?></div>


<?php
/*
$sqlSelect = "SELECT * FROM tbl_info";
$result = $db->select($sqlSelect);
if (! empty($result)) {
    ?>

    <table class='tutorial-table'>
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>

            </tr>
        </thead>
<?php
    foreach ($result as $row) { // ($row = mysqli_fetch_array($result))
        ?>
        <tbody>
            <tr>
                <td><?php  echo $row['name']; ?></td>
                <td><?php  echo $row['description']; ?></td>
            </tr>
<?php
    }
    ?>
        </tbody>
    </table>
<?php
}
*/
?>

</body>
</html>