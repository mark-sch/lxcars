<?php
/***********************************************************************************************************
lxcaufSuche Sucht nach Aufträgen
angefangen im Dezember 2010 von Ronny 
************************************************************************************************************/
include_once("../inc/template.inc");
require_once("./inc/lxcLib.php");
include_once("../inc/UserLib.php");
require_once("../inc/stdLib.php");
include_once("../inc/crmLib.php");
// Sucht nach Aufträgen 
//echo "Suche Aufträge ist derzeit nicht implementiert </br> </br>";



//$c_ow = Name2ID( $_POST['c_ow'] );
$gruppen = getGruppen();
//print_r($gruppen);
foreach($gruppen as $key=>$value){
	//echo "Der aktuelle Wert ist: " . $key . "</br>";
	if($gruppen[$key]['grpname']=="Werkstatt") {$schrauber=getMitglieder($gruppen[$key]['grpid']);}
}
//print_r( $schrauber );
//print_r( $_SESSION );

if( !$schrauber ){
	echo "<b>Gruppe Werstatt nicht angelegt oder ihr keine  Mitglieder zugewiesn. install.txt lesn!!</br>CRM->Admin->Gruppen</b>";
}
$emp = -1;
foreach( $schrauber as $key => $value ){
	if( $schrauber[$key]['login'] == $_SESSION['login'] ){
		$lxc_a_pos_emp = $key;
	}
}


$formdata = $_POST;

// " -> Berlin " entfernen

if( $pos = strpos( $formdata['c_ow'], " -> " ) ){
	$formdata['c_ow'] = substr( $formdata['c_ow'], 0, $pos );
}

if( $pos = strpos( $formdata['c_ln'], " -> " ) ){
	$formdata['c_ln'] = substr( $formdata['c_ln'], 0, $pos );
}
// 

$formdata['c_ow_id'] = $c_ow;
//print_r( $_SESSION );
$formdata["lxc_a_status".$formdata['lxc_a_status']] = "selected";
$formdata['emp'] = $_SESSION['employee'];
if( $formdata['selbstgeschraubt'] ){
	$formdata['lxc_a_pos_emp'] = $lxc_a_pos_emp;
}
if( $formdata[lxc_a_status] == "selected" ){
	$formdata[lxc_a_status] = 4;
	$formdata[lxc_a_status4] = "selected";
}
	
	
//print_r( $formdata );
$rs = SucheAuftrag( $formdata );


//print_r( $rs );
$t = new Template($base);
$t->set_file(array("tpl-file" => "lxcaufSuche.tpl"));
$t->set_block("tpl-file","Liste","Block");
if( !$formdata[reset] ){
	$t->set_var($formdata);
}
$i = 0;
if( $rs && ( $i < $listLimit ) ){//
	
	foreach( $rs as $zeile ){
		//print( $zeile['c_ln'] );
		//echo "FFFFGGGHJJJJKKLLII:   ".$i."</br>";
		$t->set_var(array(rs_c_ln => $zeile['c_ln'], lxc_a_id => $zeile['lxc_a_id'], LineCol => $bgcol[($i%2+1)], kdname => $zeile['name'], todo => $zeile['lxc_a_pos_todo'], adate => $zeile['to_char'], a_c_ow => $zeile['c_ow'], a_c_id => $zeile['c_id']));// end set_var
 		$t->parse("Block","Liste",true);
     	$i++;
     	
	}
}








$t->pparse("out",array("tpl-file"),$_SESSION["lang"],"firma");
?>
