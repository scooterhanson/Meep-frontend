<?php

////
// PINpad entry (to match against the pin defined in alarmConfig.php)
// plus device fingerprint detection.  Both values are submitted to auth.php
// for authentication.
////


$sNoCacheRandStr = rand(1,99999)
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <LINK REL=StyleSheet HREF="/css/login-alarm.css" TYPE="text/css">
  </head>
  <body>
    <img src="images/meep_header.png" style="width:280px; margin-bottom:10px; margin-top:-10px;">
    <form id="PINbox" name="PINbox" action="/service/auth.php"  method="post">
      <table class="table_fields">
        <tr class="table_pin">
          <td colspan="3">
            <span id="PINdisplay">&nbsp;</span>
            <input visible="false" id="PINbox" name="PINbox" type="text"></input>
            <input visible="false" id="fingerprint" name="fingerprint" type="0"></input>
          </td>
        </tr>
        <tr class="table_row">
          <td id="btn1" class="num_btn">1</td>
          <td id="btn3" class="num_btn">2</td>
          <td id="btn3" class="num_btn">3</td>
        </tr>
        <tr class="table_row">
          <td id="btn4" class="num_btn">4</td>
          <td id="btn5" class="num_btn">5</td>
          <td id="btn6" class="num_btn">6</td>
        </tr>
        <tr class="table_row">
          <td id="btn7" class="num_btn">7</td>
          <td id="btn8" class="num_btn">8</td>
          <td id="btn9" class="num_btn">9</td>
        </tr>
        <tr class="table_row">
          <td id="btnClear">CLEAR</td>
          <td id="btn0" class="num_btn">0</td>
          <td id="btnGo">GO</td>
        </tr>
      </table>
    </form>
  </body>
  <script src="js/jquery-1.11.3.min.js" type="text/javascript"></script>
  <script src="/js/fingerprint/fingerprint.js" type="text/javascript"></script>
  <script src="js/login-alarm.js?version=<?php echo($sNoCacheRandStr); ?>" type="text/javascript"></script>
</html>
