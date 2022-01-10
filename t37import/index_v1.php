<?php
use Phppot\DataSource;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

require_once 'DataSource.php';
$db = new DataSource();
$conn = $db->getConnection();
require_once ('./vendor/autoload.php');

function get_company_id($region, $VC_position, $sector) {
   // $this = new DataSource();
    //$conn1 = $this->getConnection();
    //$query = "insert into tbl_info(name,description) values('" . $name . "','" . $description . "')";
    $queryid = "SELECT id FROM company WHERE region_id=$region AND sector=$sector AND VC_position=$VC_position";
    $result = mysqli_query($conn, $queryid);
    return $result;
}


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
           /* echo "region_id " . $region_id . "<br>";
            echo "sector " . $sector . "<br>";1
            echo "VC_position " . $VC_position . "<br>";
            echo "domain " . $domain . "<br>";*/
            if (! empty($region_id) || ! empty($sector) || ! empty($VC_position) || ! empty($domain)) {
               // echo "sono nel IF di controllo valori";
                $query = "insert into company(region_id,sector,VC_position,domain) values(?,?,?,?)";
                $paramType = "ssss";
                $paramArray = array(
                    $region_id,
                    $sector,
                    $VC_position,
                    $domain
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
        //fine inserimento dati company

        //inserimento dati company_branches
        $excelSheet = $spreadSheet->getSheetByName('company_branches');
        $spreadSheetAry = $excelSheet->toArray();
        $sheetCount = count($spreadSheetAry);

        for ($i = 1; $i <= $sheetCount; $i ++) {
         $HQ_id = "";
            if (isset($spreadSheetAry[$i][0])) {
                $HQ_id = mysqli_real_escape_string($conn, $spreadSheetAry[$i][0]);
            }
            $BU_id = "";
            if (isset($spreadSheetAry[$i][1])) {
                $BU_id = mysqli_real_escape_string($conn, $spreadSheetAry[$i][1]);
            }

            if (! empty($HQ_id) || ! empty($BU_id)) {
                $query = "insert into company_branches(HQ_id,BU_id) values(?,?)";
                $paramType = "ss";
                $paramArray = array(
                    $HQ_id,
                    $BU_id
                );
                $insertId = $db->insert($query, $paramType, $paramArray);
                // $query = "insert into tbl_info(name,description) values('" . $name . "','" . $description . "')";
                // $result = mysqli_query($conn, $query);

                if (! empty($insertId)) {
                    $type = "success";
                    $message = "Excel Data Imported into the Database";
                } else {
                    $type = "error";
                    $message = "Problem in Importing Excel Data";
                }
            }
        }
        //fine inserimento dati company_branches

        //inserimento dati composition
        /*
        $excelSheet = $spreadSheet->getSheetByName('composition');
        $spreadSheetAry = $excelSheet->toArray();
        $sheetCount = count($spreadSheetAry);

        for ($i = 1; $i <= $sheetCount; $i ++) {
            $composition = "";
            if (isset($spreadSheetAry[$i][0])) {
                $composition = mysqli_real_escape_string($conn, $spreadSheetAry[$i][0]);
            }
            $composition_estimation = "";
            if (isset($spreadSheetAry[$i][1])) {
                $composition_estimation = mysqli_real_escape_string($conn, $spreadSheetAry[$i][1]);
            }

            if (! empty($composition) || ! empty($composition_estimation)) {
                $query = "insert into composition(composition,composition_estimation) values(?,?)";
                $paramType = "ss";
                $paramArray = array(
                    $composition,
                    $composition_estimation
                );
                $insertId = $db->insert($query, $paramType, $paramArray);
                // $query = "insert into tbl_info(name,description) values('" . $name . "','" . $description . "')";
                // $result = mysqli_query($conn, $query);

                if (! empty($insertId)) {
                    $type = "success";
                    $message = "Excel Data Imported into the Database";
                } else {
                    $type = "error";
                    $message = "Problem in Importing Excel Data";
                }
            }
        }*/
        //fine inserimento dati composition
    
        //inserimento dati excess_material
        $excelSheet = $spreadSheet->getSheetByName('excess_material');
        $spreadSheetAry = $excelSheet->toArray();
        $sheetCount = count($spreadSheetAry);

        for ($i = 1; $i <= $sheetCount; $i ++) {
            $compo = "";
            if (isset($spreadSheetAry[$i][0])) {
                $compo = mysqli_real_escape_string($conn, $spreadSheetAry[$i][0]);
            }
            
            if (! empty($compo) ) {
                $query = "insert into excess_material(compo) values(?)";
                $paramType = "s";
                $paramArray = array(
                    $compo
                    );
                $insertId = $db->insert($query, $paramType, $paramArray);
                // $query = "insert into tbl_info(name,description) values('" . $name . "','" . $description . "')";
                // $result = mysqli_query($conn, $query);

                if (! empty($insertId)) {
                    $type = "success";
                    $message = "Excel Data Imported into the Database";
                } else {
                    $type = "error";
                    $message = "Problem in Importing Excel Data";
                }
            }
        }
        //fine inserimento dati excess_material
        
        //inserimento dati exchange
        $excelSheet = $spreadSheet->getSheetByName('exchange');
        $spreadSheetAry = $excelSheet->toArray();
        $sheetCount = count($spreadSheetAry);
        
        for ($i = 1; $i <= $sheetCount; $i ++) {
            $circular_entity_name = "";
            if (isset($spreadSheetAry[$i][0])) {
                $circular_entity_name = mysqli_real_escape_string($conn, $spreadSheetAry[$i][0]);
            }
            $from_id = "";
            if (isset($spreadSheetAry[$i][1])) {
                $from_id = mysqli_real_escape_string($conn, $spreadSheetAry[$i][1]);
            }
            $to_id = "";
            if (isset($spreadSheetAry[$i][2])) {
                $to_id = mysqli_real_escape_string($conn, $spreadSheetAry[$i][2]);
            }
            $exchange_moment = "";
            if (isset($spreadSheetAry[$i][3])) {
                $exchange_moment = mysqli_real_escape_string($conn, $spreadSheetAry[$i][3]);
            }
            $Kilograms = "";
            if (isset($spreadSheetAry[$i][4])) {
                $Kilograms = mysqli_real_escape_string($conn, $spreadSheetAry[$i][4]);
            }
            $n_units = "";
            if (isset($spreadSheetAry[$i][5])) {
                $n_units = mysqli_real_escape_string($conn, $spreadSheetAry[$i][5]);
            }
            $KG_unit_estime = "";
            if (isset($spreadSheetAry[$i][6])) {
                $KG_unit_estime = mysqli_real_escape_string($conn, $spreadSheetAry[$i][6]);
            }
            $composition_exchange = "";
            if (isset($spreadSheetAry[$i][7])) {
                $composition_exchange = mysqli_real_escape_string($conn, $spreadSheetAry[$i][7]);
            }

            if (! empty($circular_entity_name) || ! empty($from_id) || ! empty($to_id) || ! empty($exchange_moment) || ! empty($Kilograms) || ! empty($n_units) || ! empty($KG_unit_estime) || ! empty($composition_exchange)) {
                $query = "insert into exchange(circular_entity_name,from_id,to_id,exchange_moment,Kilograms,n_units,KG_unit_estime,composition) values(?,?,?,?,?,?,?,?)";
                $paramType = "ssssssss";
                $paramArray = array(
                    $circular_entity_name,
                    $from_id,
                    $to_id,
                    $exchange_moment,
                    $Kilograms,
                    $n_units,
                    $KG_unit_estime,
                    $composition_exchange
                );
                $insertId = $db->insert($query, $paramType, $paramArray);
                // $query = "insert into tbl_info(name,description) values('" . $name . "','" . $description . "')";
                // $result = mysqli_query($conn, $query);

                if (! empty($insertId)) {
                    $type = "success";
                    $message = "Excel Data Imported into the Database";
                } else {
                    $type = "error";
                    $message = "Problem in Importing Excel Data";
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
             /*echo "Material_Name " . $Material_Name . "<br>";
             echo "Percentage " . $Percentage . "<br>";
             echo "Loops " . $Loops . "<br>";
             echo "CRM " . $CRM . "<br>";*/
             if (! empty($Material_Name) || ! empty($Percentage) || ! empty($Loops) || ! empty($CRM)) {
                // echo "sono nel IF di controllo valori";
                 $query = "insert into material(Material_Name,Percentage,Loops,CRM) values(?,?,?,?)";
                 $paramType = "ssss";
                 $paramArray = array(
                     $Material_Name,
                     $Percentage,
                     $Loops,
                     $CRM
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