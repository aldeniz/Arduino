<!DOCTYPE html>
<html lang="bg">

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <meta charset="UTF-8">
    <meta http-equiv="Expires" content="Fri, Jan 01 1900 00:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-Control" content="no-cache">
    <title>Arduino example</title>

    <script language="JavaScript" type="text/JavaScript">
      // =========  Функции за мнимо отваряне на скрипт  - начало ==========

      function getXMLObject()  
      {   
        var xmlHttp = false;  

        try { xmlHttp =  new XMLHttpRequest(); }   
        catch (e) { try { xmlHttp = new ActiveXObject("Microsoft.XMLHTTP"); }  
          catch (e) { try { xmlHttp = new ActiveXObject("Msxml2.XMLHTTP.5.0"); }  
            catch (e) { try { xmlHttp = new ActiveXObject("Msxml2.XMLHTTP.4.0"); }  
              catch (e) { try { xmlHttp = new ActiveXObject("Msxml2.XMLHTTP.3.0"); }  
                catch (e) { try { xmlHttp = new ActiveXObject("Msxml2.XMLHTTP"); }  
                  catch (e) { xmlHttp = false; }}} } }}

        return xmlHttp;  
      }  

      // ===================function comm == begin
      function comm(file, value) {

        var xmlhttp = new getXMLObject();   //xmlhttp 

        if (xmlhttp) {
          xmlhttp.open("POST",file,true);
          try {  xmlhttp.timeout = 33000;  } catch (e)  {}
          try {  xmlhttp.ontimeout = onRequestTimeout; }    catch (e)  {}
          xmlhttp.onreadystatechange  = handleServerResponse;     
          xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');     

          var db=document.getElementById('database').value;
          var temper=document.getElementById("temp").value;
          var count=document.getElementById("br").value; //1;  // брой измервания
          xmlhttp.send("comm="+value+"&temp="+temper+"&count="+count+"&DB="+db); 
        } 

        function onRequestTimeout()        {
          //alert("Сървърът е натоварен!");
        }

        function handleServerResponse() { 

          if (xmlhttp.readyState == 4) {   
            if(xmlhttp.status == 200)   {   

              var data=xmlhttp.responseText;

              data = eval(data); var i=0;
              for (i = 0; i < data.length; i++) {
                for ( key in data[i] ) {

                  var d = formatAMPM(new Date());

                  document.getElementById("datas").value = 
                    document.getElementById("datas").value+d+' - '+data[i][key];
                  document.getElementById("datas").scrollTop = 
                    document.getElementById("datas").scrollHeight;

                  var n =data[i][key].search("L:");   
                  if (n!=-1) {
                    var res = data[i][key].substring(n+3, n+4);
                    if (res==1) document.getElementById("circle").className = "dot2"; else
                      if (res==0) document.getElementById("circle").className = "dot3"; else 
                        document.getElementById("circle").className = "dot1";

                  }
                  n =data[i][key].search("P:");
                  n1 =data[i][key].substring(n+3, n+300).search(" ");

                  if (n!=-1 && n1!=-1) {
                    var res = data[i][key].substring(n+3, n+3+n1);
                    document.getElementById('temp').value=res;
                    //alert(res);
                  }
                }
              }
            }      
            //else         {  alert("Грешка!"); }   
          } 

        }     // handleServerResponse()

        function formatAMPM(date) {
          var hours = date.getHours();
          var minutes = date.getMinutes();
          var sec=date.getSeconds();
          var ampm = hours >= 12 ? 'pm' : 'am';
          hours = hours % 12;
          hours = hours ? hours : 12; // the hour '0' should be '12'
          minutes = minutes < 10 ? '0'+minutes : minutes;
          sec = sec < 10 ? '0'+sec : sec;
          var strTime = hours + ':' + minutes + ':' +sec+ ampm;
          return strTime;
        }

      } //comm

      var interval;

      var processing = false;
      var operation=0;
      var period=10000; //10s

      function start(command) {
        if (processing==true) return;

        operation=0;    
        processing=true;

        interval  = setInterval(function st(){

          if (command!=0) {  comm("arduino2.php",command); command=0;}
          else
            // Измерване на температура и влажност
            if (operation==0) 
              comm("arduino2.php",1);

          // Спиране на измерване    
          if (operation==2) { 
            operation=0;
            processing=false;
            clearInterval(interval);
            comm('arduino2.php',2);
          }
          // установяване на нова прагова температура
          if (operation==4) { 
            operation=0;
            processing=false;
            clearInterval(interval);
            start(31);

          }
          // установяване на период на измерване
          if (operation==5) { 
            operation=0;
            processing=false;
            clearInterval(interval); 
            start(0);
          }        
          return st;

          }(), period);

      }

      function stop() {
        operation=2; 
      }

      function setTemp() { 
        operation=4;
        if (processing==false) comm('arduino2.php',31); 
      }

      function setPeriod() { 
        period= document.getElementById("per").value;
        operation=5;
        //if (processing==false) start();
      }

      function nul() {
        document.getElementById("datas").value="";
      }

      //   ========  Функции за мнимо отваряне на скрипт  - край ==============

    </script>
    <style>
      .dot1 {
        height: 40px; width: 40px; 
        background-color: #bbb; border-radius: 50%; display: inline-block;
      }
      .dot2 {
        height: 40px; width: 40px; 
        background-color: #ef1212; border-radius: 50%; display: inline-block;
      }
      .dot3 {
        height: 40px; width: 40px; 
        background-color: #12ef12; border-radius: 50%; display: inline-block;
      }
      td {
        text-align:center;
      }

    </style>

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
            <h3>Пример на Web базирано приложение за измерване на t° и 
              влажност от сензор DHT12 с използване на Arduino 
              и връзка с PC по сериен порт</h3>
          </div>

          <div class="w3-row" style="max-width:500px;">
            <div class="w3-quarter" style="overflow:none">

              <h4>Прагова t°</h4>
              <select id="temp"  class="w3-select w3-large" style="width:auto"
                OnChange="javascript: setTemp();" >
                <?php
                $i=0; $opt="<option value='' selected></option>";

                while ($i<80) {
                  //if ($i==22) $selected="selected"; else $selected="";
                  $opt.="<option value='$i' $selected>$i</option>\n";
                  $i++;
                }
                echo $opt;
                ?>
              </select>        
            </div>
            <div class="w3-quarter">
              <h4>Период</h4>

              <select  id="per"  class="w3-select  w3-large" style="width:auto" 
                OnChange="javascript: setPeriod();" >
                <option value="2000">2s</option>
                <option selected value="10000">10s</option>
                <option value="30000">30s</option>
                <option value="60000">60s</option>
              </select>       
            </div> 
            <div class="w3-quarter">
              <h4>Брой</h4>

              <select  id="br"   class="w3-select w3-large" 
                style="width:auto" OnChange="" >
                <option selected value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="5">5</option>
              </select>       
            </div>  
            <div class="w3-quarter">
              <h4>Запис в БД</h4>

              <select  id="database"   class="w3-select  w3-large" 
                style="width:auto"  style = "width:auto;" OnChange="" >
                <option value="1">Да</option>
                <option value="0" selected>Не</option>
              </select>       
            </div>                     
          </div>
          <br>
          <input type="button" onclick="javascript: start(32)" 
            class="w3-btn w3-teal " value="Старт">&nbsp;&nbsp;
          <input type="button" onclick="javascript: stop(); " 
            class="w3-btn w3-teal" value="Стоп">

          <h4>Измерени стойности</h4>
          <!-- <select multiple=10 name="datas1" id="datas1" class="datas1" style="width:200px; height:200px;"></select>
          <br><br>
          -->
          <textarea cols="40" rows="10" id="datas" class="w3-input w3-light-grey"
            style="max-width:500px; height:200px;"></textarea>
          <br>
          <input type="button" onclick="javascript: nul(); "
            class="w3-btn w3-teal" value="Изчисти">
          <br>
          <h4>Състояние на LED</h4>
          <span class="dot1" name="circle" id="circle"></span>

        </div>    
      </article>
    </section>
    <footer>    
    </footer>
  </body>
</html>