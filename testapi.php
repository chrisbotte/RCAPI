<?php
define("ARRAYOUTPUT", true);

define("DEVSTATUS", true);
define("DEV_SHOW_ORIG_JSON", false);
define("DEV_SHOW_DATA_ARRAY", TRUE);
define("DEV_SHOW_API_RESPONSE", true);


require_once 'class/api.php';

try{
  
  $a = new RCAPI('664337CD4431B8CC249CE44EF268233A');
  $a->getProjectInfo();
  $a->getFormMetadata(array('demographics'));
  $a->getFormData(array('demographics'));

  $data = '[{"study_id":"99","redcap_event_name":"event_1_arm_1","date_enrolled":"2017-05-24","patient_document":"","first_name":"CCChris","last_name":"Botte","address":"19 Elm St Burlington MA","telephone_1":"(978) 433-0099","email":"aaa@aol.com","dob":"2011-05-18","age":"6","ethnicity":"1","race":"0","sex":"0","given_birth":"1","num_children":"3","gym___0":"0","gym___1":"0","gym___2":"1","gym___3":"1","gym___4":"0","aerobics___0":"0","aerobics___1":"0","aerobics___2":"0","aerobics___3":"1","aerobics___4":"0","eat___0":"0","eat___1":"1","eat___2":"1","eat___3":"0","eat___4":"0","drink___0":"1","drink___1":"0","drink___2":"0","drink___3":"1","drink___4":"1","specify_mood":"82","meds___1":"0","meds___2":"0","meds___3":"1","meds___4":"1","meds___5":"0","height":"120","weight":"100","bmi":"69.4","comments":"","demographics_complete":"0"}]';
  $data='[{"study_id":"99"}]';
  $a->writeData($data, 'overwrite');
  
}  


catch(Exception $e) {
  $msg = $e->getMessage();
  $err='<div class="alert alert-danger fade in"><strong>Error!<br></strong>'. $msg . '</div>';
  echo $err;
}