<?php

function validate_new_pwd($pwdOne, $pwdTwo) {
  if ($pwdOne == $pwdTwo) {
    return (true);
  }
  else {
    return (false);
  }
}
?>
