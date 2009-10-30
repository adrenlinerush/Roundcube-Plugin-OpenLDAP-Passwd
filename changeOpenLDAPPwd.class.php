<?php
class csChangeOpenLDAPPasswd {
  private $logwriter;
  private $strHost;
  private $strOUDC;

  function __construct() {
    include_once ("changeOpenLDAPPwd.config.php"); 

    if (!$debug) {
      error_reporting(1);
    }

    require_once ("csLogging.class.php");

    $this->logwriter = new csLogging($errorlogfile,$debuglogfile,$debug);
  
    $this->strHost = $ldapHost;
    $this->strOUDC = $oudc;

  }

  public function changePWD($strUID, $strOldPwd, $strNewPwdOne, $strNewPwdTwo) {
    include_once ("changeOpenLDAPPwd.validate.php");
    if (validate_new_pwd($strNewPwdOne, $strNewPwdTwo)){
      $strUserDN = "uid=".$strUID.", ". $this->strOUDC;
      $objUserBind = $this->bindLDAP($strUserDN, $strOldPwd);
      return $this->changeOpenLDAPPwd($objUserBind, $strUserDN, $strNewPwdOne);
    }
    else {
      $this->failure(1, array($strNewPwdOne, $strNewPwdTwo));
      return false;
    }
  }

  private function bindLDAP($strDN, $strPWD) {
    ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
    $ldap = ldap_connect("ldaps://$this->strHost:636") or $ldap = false;
    if ($ldap) {
       //Connected successfully to ldap server
      $this->logwriter->debugwrite('Successfully Connected to LDAP Server');
      $res = ldap_bind($ldap,$strDN,$strPWD) or $res = false;
      if ($res) {
        //Succcessfully bound with search DN login
        $this->logwriter->debugwrite("Successfully Bound with Search DN: $strDN Passwd: $strPWD");
        return $ldap;
      }
      else {
        $this->failure(7, array($strDN, $strPWD, ldap_error($ldap)));
      }
    }
    else {
      $this->failure(6, array($this->strHost));
    }
  }

  private function changeOpenLDAPPwd($objLdapBinding, $strUserDN, $strNewPwd) { 
    include_once ("sambahash.php");             
    $entry["sambaNTPassword"] = nt_hash($strNewPwd);
    $this->logwriter->debugwrite('NT Hash:' . $entry["sambaNTPassword"]);
    $entry["sambaLMPassword"] = lm_hash($strNewPwd);
    $this->logwriter->debugwrite('LM Hash:' . $entry["sambaLMPassword"]);
    $date = time();
    $this->logwriter->debugwrite('Last Set:' . $date);
    $entry["sambaPwdLastSet"] = $date;
    $entry["sambaPwdMustChange"] = ($date+90*24*60*60);
    $this->logwriter->debugwrite('Must Change:'. $entry["sambaPwdMustChange"]);
  
    mt_srand((double)microtime()*1000000);
    $salt = pack("CCCC", mt_rand(), mt_rand(), mt_rand(), mt_rand());
    $hash = "{SSHA}" . base64_encode(pack("H*", sha1($strNewPwd . $salt)) . $salt);
    $entry["userPassword"] = $hash;
    $entry["shadowLastChange"] = (int)($date/86400);
    $this->logwriter->debugwrite('Shadow Last Change:'. $entry["shadowLastChange"]);
    
    $res = ldap_mod_replace($objLdapBinding, $strUserDN, $entry) or $res = false;
    
    if ($res) {
      $this->success($strNewPwd);
      return true;
    } 
    else {
      //Failed to change user Password  
      $this->failure(8, array($strNewPwd,$newpass,ldap_error($objLdapBinding)));
      return false;
    }
  }

  private function failure($iFailCode, $aryLogParams) {
    switch($iFailCode) {
      case 1:
        $this->logwriter->writelog("Failed Password Validation\n" . "Password: $aryLogParams[0]  Password Validate: $aryLogParams[1]");
        break;
      case 6:
        $this->logwriter->writelog("Failed to connect to LDAP Server\n" . "Server: $aryLogParams[0]\n" .  "Error: " . $aryLogParams[1]);
        break;
      case 7:
        $this->logwriter->writelog("Failed to Bind with user DN\n" . "BindDN: $aryLogParams[0]\nBindPwd: $aryLogParams[1]\n" .  "Error: " . $aryLogParams[2]);
        break;
      case 8:
        $this->logwriter->writelog("Failed to Change Password: $aryLogParams[0] Unicode: $aryLogParams[1] LDAP Error:" . $aryLogParams[2]);
        break;    
    }
  }

  private function success($strNewPwd) {
    $this->logwriter->debugwrite("Successfully Changed User Password: $strNewPwd");           
  }
}




