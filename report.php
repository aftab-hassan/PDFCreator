<?php 

require('fpdf/fpdf.php');

class PDF_reciept extends FPDF 
{
	function __construct ($orientation = 'P', $unit = 'pt', $format = 'Letter', $margin=40) 
	{
		$this->FPDF($orientation, $unit, $format);
		$this->SetTopMargin($margin);
		$this->SetLeftMargin($margin);
		$this->SetRightMargin($margin);
		
		$this->SetAutoPageBreak(true, $margin);
	}
	
	function Header () 
	{
		$this->SetFont('Arial', 'B', 18);
		$this->SetFillColor(36, 96, 84);
		$this->SetTextColor(225);
		$this->Cell(0, 30, "Congestive Heart Failure Medication Discharge Instructions", 0, 1, 'C',true);
		$this->Ln(10);
	}
	
	/*function Footer () 
	{
		$this->SetFont('Arial', '', 12);
		$this->SetTextColor(0);
		$this->SetXY(40, -60);
		$this->Cell(0, 20, "Thank you for shopping at Nettuts+", 'T', 0, 'C');
	}*/
	
	/*function PriceTable($products, $prices) 
	{
		$this->SetFont('Arial', 'B', 12);
		$this->SetTextColor(0);
		$this->SetFillColor(36, 140, 129);
		$this->SetLineWidth(1);
		$this->Cell(427, 25, "Item Description", 'LTR', 0, 'C', true);
		$this->Cell(100, 25, "Price", 'LTR', 1, 'C', true);
		 
		$this->SetFont('Arial', '');
		$this->SetFillColor(238);
		$this->SetLineWidth(0.2);
		$fill = false;
		
		for ($i = 0; $i < count($products); $i++) 
		{
			$this->Cell(427, 20, $products[$i], 1, 0, 'L', $fill);
			$this->Cell(100, 20, '$' . $prices[$i], 1, 1, 'R', $fill);
			$fill = !$fill;
		}
		$this->SetX(367);
		$this->Cell(100, 20, "Total", 1);
		$this->Cell(100, 20, '$' . array_sum($prices), 1, 1, 'R');
	}*/
}//class

//$target_path = "/var/www/fileupload/uploads/";
/* uploading the input file - input.csv */
$target_path = "";
//$file = $target_path . basename($_FILES['file']['name']);
$file = $target_path . "input.csv";

//echo($file);
if (move_uploaded_file($_FILES['file']['tmp_name'], $file))
{
 chmod(basename($_FILES['file']['name']), 0777);
}

//reading data from csv file
function readCSV($csvFile){
        $file_handle = fopen($csvFile, 'r');
        while (!feof($file_handle) ) {
                $line_of_text[] = fgetcsv($file_handle, 1024);
        }
        fclose($file_handle);
        return $line_of_text[1];
}
//executing the R script to get recommended medications and risk scores
exec("Rscript recommend.R");
$csvFile = 'riskandrecommendations.csv';
$csv = readCSV($csvFile);


//saving into variables
/*
    [0] => Readmit
    [1] => GenderCD
    [2] => Age
    [3] => LOS
    [4] => MaritalStatusDSC
    [5] => RespirationRateNBR
    [6] => HFPrimaryICD9DiagnosisFLG
    [7] => SecondaryNonHFICD9DiagnosisCNT
    [8] => DischargeDestinationID
    [9] => AdmitSourceCD
    [10] => AdmitTypeCD
    [11] => DischargeStatusCD
    [12] => DischargeAPRDRGRiskOfMortality
    [13] => DischargeAPRDRGSeverityOfIllne
    [14] => DischargeFollowupCategoryCD
    [15] => AvgInpatientBloodPressureCategoryCD
    [16] => Hematological
    [17] => PulseRateNBR
    [18] => AcuteCS
    [19] => Arrhythmias
    [20] => CRFailure
    [21] => Rheumatic
    [22] => Vascular
    [23] => Chronic
    [24] => UnspecifiedHD
    [25] => FuncDisability
    [26] => RenalFailure
    [27] => COPD
    [28] => FluidDisorder
    [29] => UrinaryTract
    [30] => Ulcer
    [31] => GastroIntestinal
    [32] => PepticUlcer
    [33] => Nephritis
    [34] => Dementia
    [35] => Leukemia
    [36] => Cancer
    [37] => Liver
    [38] => Dialysis
    [39] => Asthma
    [40] => Anemia
    [41] => Pneumonia
    [42] => Drug
    [43] => Psych
    [44] => Depression
    [45] => Psychiatric
    [46] => Lung
    [47] => MalNutrition
    [48] => Diabetes
    [49] => Stroke
    [50] => EFV
    [51] => EpicPatientID
    [52] => RecommendedMedications
    [53] => RiskScoreWithoutMedications
    [54] => RiskScoreWithMedications
*/
$RiskScoreWithMedications = $csv[127];
$RiskScoreWithoutMedications = $csv[126];
$RecommendedMedications = str_replace("||"," , ",$csv[125]);
$EpicPatientID = $csv[54];
$EFV = $csv[10];
$Stroke = $csv[9];
$Diabetes = $csv[8];
$MalNutrition = $csv[51];
$Lung = $csv[50];
$Psychiatric = 'not there';
$Depression = $csv[48];
$Psych = $csv[47];
$Drug = $csv[46];
$Pneumonia = $csv[45];
$Anemia = $csv[44];
$Asthma = $csv[43];
$Dialysis = $csv[28];
$Liver = $csv[41];
$Cancer = $csv[40];
$Leukemia = 'not there';
$Dementia = $csv[38];
$Nephritis = $csv[37];
$PepticUlcer = $csv[35];
$GastroIntestinal = $csv[34];
$Ulcer = $csv[33];
$UrinaryTract = $csv[32];
$FluidDisorder = $csv[31];
$COPD = $csv[30];
$RenalFailure = $csv[29];
$FuncDisability = 'not there';
$UnspecifiedHD = 'not there';
$Chronic = 'not there';
$Vascular = $csv[25];
$Rheumatic = $csv[24];
$CRFailure = $csv[23];
$Arrhythmias = $csv[22];
$AcuteCS = 'not there';
$PulseRateNBR = $csv[19];
$Hematological = $csv[36];
$AvgInpatientBloodPressureCategoryCD = $csv[18];
$DischargeFollowupCategoryCD = $csv[15];
$DischargeAPRDRGSeverityOfIllne = $csv[14];
$DischargeAPRDRGRiskOfMortality = $csv[13];
$DischargeStatusCD = $csv[60];
$AdmitTypeCD = $csv[59];
$AdmitSourceCD = $csv[58];
$DischargeDestinationID = $csv[57];
$SecondaryNonHFICD9DiagnosisCNT = $csv[12];
$HFPrimaryICD9DiagnosisFLG = $csv[11];
$RespirationRateNBR = $csv[20];
$MaritalStatusDSC = $csv[61];
$LOS = $csv[72];
$Age = 2015 - $csv[56];
$GenderCD = $csv[56];
#$Readmit = $csv[0];

//converting values from 1/0 to YES/NO
function yesNo(&$value)
{
 $value = $value == true ? 'YES' : 'NO';
}
function MaleFemale(&$value)
{
 $value = $value == 'F' ? 'Female' : 'Male';
}
function DiagnosisFlag(&$value)
{
 $value = $value == 'N' ? 'NO' : 'YES';
}
yesNo($Readmit);
MaleFemale($GenderCD);
//someFunc($Age)
//someFunc($LOS)
DiagnosisFlag($HFPrimaryICD9DiagnosisFLG);
yesNo($Hematological);
yesNo($AcuteCS);
yesNo($Arrhythmias);
yesNo($CRFailure);
yesNo($Rheumatic);
yesNo($Vascular);
yesNo($Chronic);
yesNo($UnspecifiedHD);
yesNo($FuncDisability);
yesNo($RenalFailure);
yesNo($COPD);
yesNo($FluidDisorder);
yesNo($UrinaryTract);
yesNo($Ulcer);
yesNo($GastroIntestinal);
yesNo($PepticUlcer);
yesNo($Nephritis);
yesNo($Dementia);
yesNo($Leukemia);
yesNo($Cancer);
yesNo($Liver);
yesNo($Dialysis);
yesNo($Asthma);
yesNo($Anemia);
yesNo($Pneumonia);
yesNo($Drug);
yesNo($Psych);
yesNo($Depression);
yesNo($Psychiatric);
yesNo($Lung);
yesNo($MalNutrition);
yesNo($Diabetes);
yesNo($Stroke);
//not needed for ($EFV);

//Current Risk Score : 85%
$pdf = new PDF_reciept();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetXY(-200,100);
$pdf->Cell(100, 20, "Current Risk Score",0,2);
$pdf->SetFont('Arial', 'B', 20);
$pdf->SetTextColor(255,0,0);
$pdf->Cell(100, 30,$RiskScoreWithoutMedications . "%",0,1,'C');
$pdf->SetTextColor(0,0,0);



/* ------------------------------ Patient Information: ----------------------- */
//Blue line division
$pdf->Ln(20);
$pdf->SetFillColor(0, 102, 204);
$pdf->Cell(0, 2, "", 0, 1, 'C',true);

//Patient Information:
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 20, "Patient Information:",0,2,'C');

//Children of Patient Information:
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(400, 20, "1. Epic Patient ID",0,0);
$pdf->Cell(100, 20, $EpicPatientID ,0,1);
$pdf->Cell(400, 20, "2. Gender",0,0);
$pdf->Cell(100, 20, $GenderCD ,0,1);
$pdf->Cell(400, 20, "3. Age",0,0);
$pdf->Cell(100, 20, $Age ,0,1);
$pdf->Cell(400, 20, "4. Length Of Stay",0,0);
$pdf->Cell(100, 20, $LOS ,0,1);
$pdf->Cell(400, 20, "5. Marital Status",0,0);
$pdf->Cell(100, 20, $MaritalStatusDSC ,0,1);




/* ------------------------------ Admit Information: ----------------------- */
//Blue line division
$pdf->Ln(20);
$pdf->SetFillColor(0, 102, 204);
$pdf->Cell(0, 2, "", 0, 1, 'C',true);

//Admit Information:
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 20, "Admit Information:",0,2,'C');

//Children of Admit Information:
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(400, 20, "1. AdmitTypeCD",0,0);
$pdf->Cell(100, 20, $AdmitTypeCD ,0,1);
$pdf->Cell(400, 20, "2. AdmitSourceCD",0,0);
$pdf->Cell(100, 20, $AdmitSourceCD ,0,1);



/* ------------------------------ Clinical Information: ----------------------- */
//Blue line division
$pdf->Ln(20);
$pdf->SetFillColor(0, 102, 204);
$pdf->Cell(0, 2, "", 0, 1, 'C',true);

//Clinical Information:
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 20, "Clinical Information:",0,2,'C');

//Children of Clinical Information:
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(400, 20, "1. Respiration Rate",0,0);
$pdf->Cell(100, 20, $RespirationRateNBR ,0,1);
$pdf->Cell(400, 20, "2. Pulse Rate",0,0);
$pdf->Cell(100, 20, $PulseRateNBR ,0,1);
$pdf->Cell(400, 20, "3. Ejection Fraction Value",0,0);
$pdf->Cell(100, 20, $EFV ,0,1);




/* ------------------------------ Diagnosis Information: ----------------------- */
//Blue line division
$pdf->Ln(20);
$pdf->SetFillColor(0, 102, 204);
$pdf->Cell(0, 2, "", 0, 1, 'C',true);

//Diagnosis Information:
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 20, "Diagnosis Information:",0,2,'C');

//Children of Diagnosis Information:
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(400, 20, "1. Primary Heart Failure",0,0);
$pdf->Cell(100, 20, $HFPrimaryICD9DiagnosisFLG ,0,1);
$pdf->Cell(400, 20, "2. SecondaryNonHFICD9DiagnosisCNT",0,0);
$pdf->Cell(100, 20, $SecondaryNonHFICD9DiagnosisCNT ,0,1);
$pdf->Cell(400, 20, "3. SevereHematologicalDisorder",0,0);
$pdf->Cell(100, 20, $Hematological ,0,1);
$pdf->Cell(400, 20, "4. AcuteCoronarySyndrome",0,0);
$pdf->Cell(100, 20, $AcuteCS ,0,1);
$pdf->Cell(400, 20, "5. Arrhythmias",0,0);
$pdf->Cell(100, 20, $Arrhythmias ,0,1);
$pdf->Cell(400, 20, "6. CardioRespiratoryFailureAndShock",0,0);
$pdf->Cell(100, 20, $CRFailure ,0,1);
$pdf->Cell(400, 20, "7. ValvularAndRheumaticHeartDisease",0,0);
$pdf->Cell(100, 20, $Rheumatic ,0,1);
$pdf->Cell(400, 20, "8. VascularOrCirculatoryDisease",0,0);
$pdf->Cell(100, 20, $Vascular ,0,1);
$pdf->Cell(400, 20, "9. ChronicAtherosclerosis",0,0);
$pdf->Cell(100, 20, $Chronic ,0,1);
$pdf->Cell(400, 20, "10. OtherAndUnspecifiedHeartDisease",0,0);
$pdf->Cell(100, 20, $UnspecifiedHD ,0,1);
$pdf->Cell(400, 20, "11. HemiplegiaParaplegiaParalysisFunctionalDisability",0,0);
$pdf->Cell(100, 20, $FuncDisability ,0,1);
$pdf->Cell(400, 20, "12. RenalFailure",0,0);
$pdf->Cell(100, 20, $RenalFailure ,0,1);
$pdf->Cell(400, 20, "13. COPD",0,0);
$pdf->Cell(100, 20, $COPD ,0,1);
$pdf->Cell(400, 20, "14. FluidDisorder",0,0);
$pdf->Cell(100, 20, $FluidDisorder ,0,1);
$pdf->Cell(400, 20, "15. OtherUrinaryTractDisorder",0,0);
$pdf->Cell(100, 20, $UrinaryTract ,0,1);
$pdf->Cell(400, 20, "16. Ulcer",0,0);
$pdf->Cell(100, 20, $Ulcer ,0,1);
$pdf->Cell(400, 20, "17. OtherGastrointestinalDisorder",0,0);
$pdf->Cell(100, 20, $GastroIntestinal ,0,1);
$pdf->Cell(400, 20, "18. PepticUlcer",0,0);
$pdf->Cell(100, 20, $PepticUlcer ,0,1);
$pdf->Cell(400, 20, "19. Nephritis",0,0);
$pdf->Cell(100, 20, $Nephritis ,0,1);
$pdf->Cell(400, 20, "20. Dementia",0,0);
$pdf->Cell(100, 20, $Dementia ,0,1);
$pdf->Cell(400, 20, "21. Leukemia",0,0);
$pdf->Cell(100, 20, $Leukemia ,0,1);
$pdf->Cell(400, 20, "22. Cancer",0,0);
$pdf->Cell(100, 20, $Cancer ,0,1);
$pdf->Cell(400, 20, "23. Liver",0,0);
$pdf->Cell(100, 20, $Liver ,0,1);
$pdf->Cell(400, 20, "24. Dialysis",0,0);
$pdf->Cell(100, 20, $Dialysis ,0,1);
$pdf->Cell(400, 20, "25. Asthma",0,0);
$pdf->Cell(100, 20, $Asthma ,0,1);
$pdf->Cell(400, 20, "26. Anemia",0,0);
$pdf->Cell(100, 20, $Anemia ,0,1);
$pdf->Cell(400, 20, "27. Pneumonia",0,0);
$pdf->Cell(100, 20, $Pneumonia ,0,1);
$pdf->Cell(400, 20, "28. Drug",0,0);
$pdf->Cell(100, 20, $Drug ,0,1);
$pdf->Cell(400, 20, "29. Psychiatric",0,0);
$pdf->Cell(100, 20, $Psychiatric ,0,1);
$pdf->Cell(400, 20, "30. Depression",0,0);
$pdf->Cell(100, 20, $Depression ,0,1);
$pdf->Cell(400, 20, "31. Lung",0,0);
$pdf->Cell(100, 20, $Lung ,0,1);
$pdf->Cell(400, 20, "32. Malnutrition",0,0);
$pdf->Cell(100, 20, $MalNutrition ,0,1);
$pdf->Cell(400, 20, "33. Diabetes",0,0);
$pdf->Cell(100, 20, $Diabetes ,0,1);
$pdf->Cell(400, 20, "34. Stroke",0,0);
$pdf->Cell(100, 20, $Stroke ,0,1);
$pdf->Cell(400, 20, "35. Psych",0,0);
$pdf->Cell(100, 20, $Psych ,0,1);



/* ------------------------------ Medication Information: ----------------------- */
//Blue line division
$pdf->Ln(20);
$pdf->SetFillColor(0, 102, 204);
$pdf->Cell(0, 2, "", 0, 1, 'C',true);

//Medication Information:
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 20, "Medication Information:",0,1,'C');
$pdf->Ln(10);
$pdf->Cell(400, 20, "Recommended Medications",1,0,'C');
$pdf->Cell(130, 20, "Risk Score",1,1,'C');

//Children of Medication Information:
$pdf->SetFont('Arial', '', 12);

$x = $pdf->GetX();
$ybefore = $pdf->GetY();
$pdf->MultiCell(400, 20, $RecommendedMedications,1);
$yafter = $pdf->GetY();
$pdf->SetFillColor(153,255,0);
$pdf->SetXY($x+400,$ybefore);
$pdf->Cell(130, $yafter-$ybefore, $RiskScoreWithMedications . "%" ,1,2,'C',1);



/* ------------------------------ Discharge Information: ----------------------- */
//Blue line division
$pdf->Ln(20);
$pdf->SetFillColor(0, 102, 204);
$pdf->Cell(0, 2, "", 0, 1, 'C',true);

//Discharge Information:
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 20, "Discharge Information:",0,2,'C');

//Children of Discharge Information:
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(400, 20, "1. DischargeDestinationID",0,0);
$pdf->Cell(100, 20, $DischargeDestinationID ,0,1);
$pdf->Cell(400, 20, "2. DischargeStatusCD",0,0);
$pdf->Cell(100, 20, $DischargeStatusCD ,0,1);
$pdf->Cell(400, 20, "3. DischargeAPRDRGRiskOfMortality",0,0);
$pdf->Cell(100, 20, $DischargeAPRDRGRiskOfMortality ,0,1);
$pdf->Cell(400, 20, "4. DischargeAPRDRGSeverityOfIllness",0,0);
$pdf->Cell(100, 20, $DischargeAPRDRGSeverityOfIllne ,0,1);
$pdf->Cell(400, 20, "5. DischargeFollowupCategoryCD",0,0);
$pdf->Cell(100, 20, $DischargeFollowupCategoryCD ,0,1);
$pdf->Cell(400, 20, "6. AvgInpatientBloodPressureCategoryCD",0,0);
$pdf->Cell(100, 20, $AvgInpatientBloodPressureCategoryCD ,0,1);


/*$pdf->SetFont('Arial', '');

$pdf->Cell(100, 13, $_POST['name']);

$pdf->SetFont('Arial', 'B');
$pdf->Cell(50, 13, "Date:");
$pdf->SetFont('Arial', '');
$pdf->Cell(100, 13, date('F j, Y'), 0, 1);

$pdf->SetFont('Arial', 'I');
$pdf->SetX(140);
$pdf->Cell(200, 15, $_POST['address'], 0, 2);
$pdf->Cell(200, 15, $_POST['city'] . ',' . $_POST['province'] , 0, 2);
$pdf->Cell(200, 15, $_POST['postal_code'] . ' ' . $_POST['country'], 0, 2);

$pdf->Ln(100);

$pdf->PriceTable($_POST['product'], $_POST['price']);

$pdf->Ln(50);

$message = "Thank you for ordering at the Nettuts+ online store. Our policy is to ship your materials within two busness days of purchase. On all orders over $20.00, we offer free 2-3 day shipping. If you haven't recieved your items in 3 busines days, let us know and we'll reimburse you 5%.\n\nWe hope you enjoy the items you have purchased. If you have any questions, you can email us at the following email address:";

$pdf->MultiCell(0, 15, $message);

$pdf->SetFont('Arial', 'U', 12);
$pdf->SetTextColor(1, 162, 232);

$pdf->Write(13, "store@nettuts.com", "mailto:example@example.com");
*/

$ReportName = 'report_' . $EpicPatientID;
$pdf->Output($ReportName, 'I');
//$pdf->Output('report.pdf', 'I');
//$pdf->Output();
?>
