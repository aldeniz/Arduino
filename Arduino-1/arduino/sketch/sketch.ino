#include "DHT.h"

#define DHTPIN 2        // pin на който е свързан сензора
#define DHTTYPE DHT22   // DHT 22  (AM2302)

int red_light_pin= 9;
int green_light_pin = 10;
int blue_light_pin = 11;

int maxHum = 65;
int maxTemp = 22;
char b=char(0);

DHT dht(DHTPIN, DHTTYPE); // дефиниране на обект dht

void setup() {
  pinMode(red_light_pin, OUTPUT);
  pinMode(green_light_pin, OUTPUT);
  pinMode(blue_light_pin, OUTPUT);
  Serial.begin(9600); 
  dht.begin();
}

void loop() {
  String str="";
  
  while (Serial.available() > 0) {
     // Прочита номер на операция
     b =  Serial.read();

     
     // Операция за прекратяване на измервания
     if (b==char(2)) {
        RGB_color(0, 0, 0);
        b=char(0);
        //maxTemp=22;
        str=str+"S: 1 | ";
     }

     // Операция за установяване на начална температура
     if (b==char(3)) {
        delay(200);
        if (Serial.available() > 0) {
          char nt =  Serial.read();
          if (!isnan(nt)) maxTemp=nt;
          RGB_color(0, 0, 0);
        } 
        str+="P: "+String(maxTemp)+" | ";
     }

     // Операция за установяване на начална температура и продължи измерване
     if (b==char(31)) {
        delay(200);
        if (Serial.available() > 0) {
          char nt =  Serial.read();
          if (!isnan(nt)) maxTemp=nt;
          RGB_color(0, 0, 0);
        } 
        b=char(1);
        str=str+"P: "+maxTemp+" | ";
     }     
     
     // Операция за измерване
     if (b==char(1)  || b==char(32)) {
      
        if (b==char(32)) str+="P: "+String(maxTemp)+" | ";
        delay(200);
        int count=1;
        if (Serial.available() > 0) {
          count =  Serial.read();
        }        
        int br=0;
        // Измерването на температурата или влажността отнема около 250 ms!
        // Готовността на сензора може да продължи до 2s
        do {
            delay(200);
            // Прочита влажността в %
            float h = dht.readHumidity();
            // Прочита температура в целзий
            float t = dht.readTemperature();
            
            // Проверка за грешка при четене
            if (isnan(h) || isnan(t)) {
              //Serial.println("Грешка при четене от DHT сензора!");
              return;
            } else {

              str+="V: "+String(h)+" | T: "+String(t)+" | ";

              if(h >=maxHum || t >=maxTemp) {
                RGB_color(255, 0, 0); // Red
                str=str+"L: 1 | ";
              } else {
                RGB_color(255, 255, 0); // Yellow 
                str+="L: 0 | "; 
              }
              //Serial.println(str); 
              str+="\n";

            }

            // Изчаква да приключи предаването на изходящите серийни данни.
            Serial.flush();
            br++;
        } while (br<count);
        
     }
     
     
     
     
   b=char(0);
   
 }
 
 if (str!="") Serial.println(str);
   
}

void RGB_color(int red_light_value, int green_light_value, int blue_light_value)
 {
  analogWrite(red_light_pin, red_light_value);
  analogWrite(green_light_pin, green_light_value);
  analogWrite(blue_light_pin, blue_light_value);
}
