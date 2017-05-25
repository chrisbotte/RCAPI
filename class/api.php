<?php

require_once 'config/globals.php';

if(DEVSTATUS === true){
  require_once('debug/PhpConsole/__autoload.php');
  PhpConsole\Helper::register(); 
  PC::getConnector()->getDebugDispatcher()->detectTraceAndSource = true;
}

class RCAPI{

  private $api_url;
  private $curl_output;
  private $token;

  public function __construct($token){
    $this->api_url = API_URL;
    $this->token = $token;
    (DEVSTATUS === true) ? PC::debug($this, 'Object After Construction of RCAPI') : "";
  }

  public function getProjectInfo(){
    $this->curlHelper('project');
    return $this->curl_output;
  }

  public function getArms(){
    $this->curlHelper('arm');
    return $this->curl_output;
  }

  public function getEvents(){
    $this->curlHelper('event');
    return $this->curl_output;
  }

  public function getFieldNames(){
    $this->curlHelper('exportFieldNames');
    return $this->curl_output;
  }

  public function getInstruments(){
    $this->curlHelper('instrument');
    return $this->curl_output;
  }

  public function getFormEventMap(){
    $this->curlHelper('formEventMapping');
    return $this->curl_output;
  }

  public function getMetadata(){
    $this->curlHelper('metadata');
    return $this->curl_output;
  }

  public function getFieldMetadata($fields){
    if(!is_array($fields))
      throw new Exception("Err getFieldMetadata.  Argument supplied is not an array");

    $this->curlHelper('metadata', $fields, null);
    return $this->curl_output;
  }

  public function getFormMetadata($forms){
    if(!is_array($forms))
      throw new Exception("Err getFormMetadata.  Argument supplied is not an array");

    $this->curlHelper('metadata', null, $forms);
    return $this->curl_output;
  }

  public function getData(){
    $this->curlHelper('record');
    return $this->curl_output;
  }

  public function getFormData($forms){
    if(!is_array($forms))
      throw new Exception("Err getFormData.  Argument supplied is not an array");

    $this->curlHelper('record', null, $forms);
    return $this->curl_output;
  }

  public function getFieldData($fields){
    if(!is_array($fields))
      throw new Exception("Err getFieldData.  Argument supplied is not an array");

    $this->curlHelper('record', $fields, null);
    return $this->curl_output;
  }

  public function getUsers(){
    $this->curlHelper('user');
    return $this->curl_output;
  }
//*******************************************************************************
//*******************************************************************************
//*******************************************************************************
//  
//  Sample Data Input:
//      Type: json
//      Def Overwrite Behavior: Normal
//      Type: flat
//      ReturnContent: ids
//      ReturnFormat: json
      
//  [  
//   {  
//      "study_id":"1",
//      "redcap_event_name":"event_1_arm_1",
//      "date_enrolled":"2017-05-24",
//      "patient_document":"",
//      "first_name":"Chris",
//      "last_name":"Botte",
//      "address":"19 Elm St Burlington MA",
//      "telephone_1":"(978) 433-0099",
//      "email":"aaa@aol.com",
//      "dob":"2011-05-18",
//      "age":"6",
//      "ethnicity":"1",
//      "race":"0",
//      "sex":"0",
//      "given_birth":"1",
//      "num_children":"3",
//      "gym___0":"0",
//      "gym___1":"0",
//      "gym___2":"1",
//      "gym___3":"1",
//      "gym___4":"0",
//      "aerobics___0":"0",
//      "aerobics___1":"0",
//      "aerobics___2":"0",
//      "aerobics___3":"1",
//      "aerobics___4":"0",
//      "eat___0":"0",
//      "eat___1":"1",
//      "eat___2":"1",
//      "eat___3":"0",
//      "eat___4":"0",
//      "drink___0":"1",
//      "drink___1":"0",
//      "drink___2":"0",
//      "drink___3":"1",
//      "drink___4":"1",
//      "specify_mood":"82",
//      "meds___1":"0",
//      "meds___2":"0",
//      "meds___3":"1",
//      "meds___4":"1",
//      "meds___5":"0",
//      "height":"120",
//      "weight":"100",
//      "bmi":"69.4",
//      "comments":"",
//      "demographics_complete":"0"
//   }
//]
  
  public function writeData($data, $ovbehav = 'normal'){
    $this->curlHelperWrite($data, $ovbehav);
    return $this->curl_output;
  }
 
//*******************************************************************************
//*******************************************************************************
//*******************************************************************************
  
  private function curlHelper($content, $fields=null, $forms=null){

    $data = array(
      'token' => $this->token,
      'content' => $content,
      'format' => 'json',
      'returnFormat' => 'json',
      'fields' => $fields,
      'forms' => $forms,
      'rawOrLabel' => 'raw',
      'rawOrLabelHeaders' => 'raw',
      'exportCheckboxLabel' => 'false',
      'exportSurveyFields' => 'false',
      'exportDataAccessGroups' => 'false'
    );

    (DEVSTATUS === true && DEV_SHOW_DATA_ARRAY === true) ? PC::debug($data, 'Curl Data Array For API: inside curlHelper') : "";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
    $output = curl_exec($ch);
    curl_close($ch);
    
    $this->curl_output = (ARRAYOUTPUT===true) ? json_decode($output, true) : $output;
    (DEVSTATUS === true && DEV_SHOW_API_RESPONSE === true) ? PC::debug (json_decode($output, true), 'Curl Response For API (Converted to ARRAY for readability): inside curlHelper') : "";
    (DEVSTATUS === true && SHOW_ORIG_JSON === true && SHOW_API_RESPONSE === true) ? PC::debug ($output, 'Curl Response For API (Original): inside curlHelper') : "";

    if(strpos($output, 'error'))
      throw new Exception("Err with API CURL READ:" . $output );
  }

  private function curlHelperWrite($data, $ovbehav){
    $data = array(
    'token' => $this->token,
    'content' => 'record',
    'format' => 'json',
    'type' => 'flat',
    'overwriteBehavior' => $ovbehav,
    'data' => $data,
    'returnContent' => 'ids',
    'returnFormat' => 'json'
    );
    
    (DEVSTATUS === true && DEV_SHOW_DATA_ARRAY === true) ? PC::debug($data, 'Curl Data Array For API: inside curlHelper') : "";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
    $output = curl_exec($ch);
    curl_close($ch);  
    
    $this->curl_output = (ARRAYOUTPUT===true) ? json_decode($output, true) : $output;
    (DEVSTATUS === true && DEV_SHOW_API_RESPONSE === true) ? PC::debug (json_decode($output, true), 'Curl Response For API (Converted to ARRAY for readability): inside curlHelper') : "";
    (DEVSTATUS === true && SHOW_ORIG_JSON === true && SHOW_API_RESPONSE === true) ? PC::debug ($output, 'Curl Response For API (Original): inside curlHelper') : "";
  
    if(strpos($output, 'error') || $output=NULL)
      throw new Exception("Err with API CURL WRITE:" . $output );
    
  }
  
  
  }




