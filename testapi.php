<?php

define("DEVSTATUS", true);
define("ARRAYOUTPUT", true);
define("SHOWORIGJSON", false);

require_once 'class/api.php';

try{
  
  $a = new RCAPI('2C688C77C71DC34C8C5C46649B0A1FEB');
  $a->getProjectInfo();
  $a->getFormMetadata(array('demographics'));
  $a->getFormData(array('demographics'));

}

catch(Exception $e) {
  $msg = $e->getMessage();
  $err='<div class="alert alert-danger fade in"><strong>Error!<br></strong>'. $msg . '</div>';
  echo $err;
}