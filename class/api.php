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

    (DEVSTATUS === true) ? PC::debug($data, 'Curl Data Array For API: inside curlHelper') : "";

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
    (DEVSTATUS === true) ? PC::debug (json_decode($output, true), 'Curl Response For API (Converted to ARRAY for readability): inside curlHelper') : "";
    (DEVSTATUS === true && SHOWORIGJSON === true) ? PC::debug ($output, 'Curl Response For API (Original): inside curlHelper') : "";

    if(strpos($output, 'error'))
      throw new Exception("Err with API CURL:" . $output );
  }
}




