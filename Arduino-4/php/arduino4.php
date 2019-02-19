<?php
$result=0; $row=null; 
if (!is_null($_POST['fun'])) {
  $conn = sqlsrv_connect( "RASHIDOV10\SQLDEVELOP",  array("UID" => "ibs", 
    "PWD" => "123456", "Database"=>"ARDUINO","CharacterSet" => "UTF-8"));
  $query="EXEC [dbo].[ReturnData] 'comm=".$_POST['fun']."'";  
  $result = sqlsrv_query($conn,$query);
  $number = sqlsrv_has_rows($result); 
  if ($number) $row=sqlsrv_fetch_array($result);  
} 
?>
<!DOCTYPE html>
<html lang="bg">

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <meta charset="UTF-8">
    <meta http-equiv="Expires" content="Fri, Jan 01 1900 00:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-Control" content="no-cache">
    <title>Пример на Web базирано приложение за измерване на t° и влажност
     от сензор DHT12 с използване на Arduino и връзка с PC по сериен порт</title>

  </head>
  <body>
    <header >
    </header>
    <nav>
    </nav>
    <section>
      <article>

        <div align="center">

          <div style="max-width:800px">
            <h3>Измерване на влажност и t° с Arduino и сензор DHT12</h3></div>

          <br>
          <!-- https://www.w3schools.com/w3css/w3css_references.asp -->
          <form name="f1" method="post" >
            <input type="submit" class="w3-btn w3-teal" value="Измери">
            <input type="hidden" name="fun" value="1">
          </form>
          <br>
          <form name="f1" method="post" >
            <input type="submit" class="w3-btn w3-teal" value="Стоп">
            <input type="hidden" name="fun" value="2">
          </form> 


          <?php

          if ($row!=null) {
            echo "<h4>Резултат</h4>";

            if (stristr($row[0],'L: 1')) $red='#bf7f7f'; else 
              if (stristr($row[0],'L: 0')) $red='#7fAf7f';  else 
                $red='#7f7f7f'; 

            echo "<div style='color: white; font-weight: bold; max-width: 350px;
             background:$red'>".$row[1]."<br>".$row[0]."</div>";
          }
          ?>

        </div>    
      </article>
    </section>
    <footer>    
    </footer>
  </body>
</html>