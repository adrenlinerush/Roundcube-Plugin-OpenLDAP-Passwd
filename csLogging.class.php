<?php
/*
Logging Class 
Written by: adrenlinerush (Austin D. Mount)
For Use at Countrystone Inc.
Released under GPL

Usage:
On construct define a configuration php file
config file must include 3 variables
$logfile => where to write errors to
$debuglogfile => where to write debugging information
$debug => booleon as whether to write debugging information
*/
class csLogging {
  private $error;
  private $debugf;
  private $debugb;
  function __construct($errorlog = 'errorlog.log', $debuglog = 'debuglog.log', $debug = false) {
    //echo "CONSTRUCT: INPUT: error:$errorlog debug:$debuglog bool:$debug";
    $this->error = $errorlog;
    $this->debugf = $debuglog;
    $this->debugb = $debug;
    //echo "CONSTRUCT: SET: error:$this->error debug:$this->debugf bool:$this->debugb";
  }
  function writelog ($data) {
    $dtStamp = date("m/d/y: H:i:s", time());
    file_put_contents($this->error,"$dtStamp: $data\n", FILE_APPEND | LOCK_EX);
  }

  function debugwrite ($data) {
    //echo "DEBUG FUNCTION: file:$this->debugf bool:$this->debugb";    
    if ($this->debugb) {    
      $dtStamp = date("m/d/y: H:i:s", time());
      file_put_contents($this->debugf,"DEBUG: $dtStamp: $data\n", FILE_APPEND | LOCK_EX);
    }
  }
}

?>
