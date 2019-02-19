# 1. Разрешаване на secure-file-priv в секция [mysqld] на php.ini в директория C:\ProgramData\MySQL\MySQL Server 5.7
# [mysqld]
# secure-file-priv = ""
# 2. Размаркиране на secure-file-priv може да се извърши и в Options File секция например на MySQL Workbench
# 3. Изтегляне на UDF (user defined function) от https://github.com/sqlmapproject/sqlmap/files/1793151/lib_mysqludf_sys.dll.zip
# (https://github.com/sqlmapproject/sqlmap/issues/2965)
# 4. Изтегляне на UDF може и от тук https://github.com/rapid7/metasploit-framework/tree/master/data/exploits/mysql
# (https://stackoverflow.com/questions/48233806/mysql-install-the-udf-library-mysqludf-sys-on-a-windows-server-2016)
# 5. Може да се използва (не е задължително) ръководството https://osandamalith.com/2018/02/11/mysql-udf-exploitation/
# 6. Инсталиране на UDF sys_eval, като lib_mysqludf_sys.dll се копира задължително в plugin папката 
# (C:\Program Files\MySQL\MySQL Server 5.7\lib\plugin)
create function sys_eval returns string soname 'lib_mysqludf_sys.dll';
# 7. Инсталиране на UDF sys_exec
create function sys_exec returns string soname 'lib_mysqludf_sys.dll';
# 8. Проверка на инсталираните  функции
select * from mysql.func where name = 'sys_eval';
select * from mysql.func where name = 'sys_exec';
# 9. Тест на функция 
select sys_eval('dir');
# 10. Други тестове:
select  sys_eval('c:\\www\\php72\\php.exe -f c:\\www\\apache24\\htdocs\\is\\arduino\\arduino2.php comm=2');

# ------------------------------------------------------------------
# 11. Създаване на процедура, която използва sys_eval

USE `arduino`;
DROP procedure IF EXISTS `arduino`;

DELIMITER $$
USE `arduino`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `arduino`()
BEGIN
 DECLARE cmd varCHAR(500);
 DECLARE result nvarchar(500);
 SET cmd = 'c:\\www\\php72\\php.exe -c C:\\www\\php72\\php.ini -f c:\\www\\apache24\\htdocs\\is\\arduino\\arduino2.php comm=1';
 SET result = sys_eval(cmd);
 
END$$

DELIMITER ;

# 12. Тестване на процедурата
call arduino.arduino();

# ------------------------------------------------------------------
# 13. Създаване на таблица results
CREATE TABLE `arduino`.`results` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `value` VARCHAR(255) NULL,
  `time` DATETIME NULL,
  PRIMARY KEY (`id`));
# ------------------------------------------------------------------  
# 14. Създаване на тригер BEFORE_INSERT за таблица results
USE `arduino`;

DELIMITER $$

DROP TRIGGER IF EXISTS arduino.results_BEFORE_INSERT$$
USE `arduino`$$
CREATE DEFINER=`root`@`localhost` TRIGGER `arduino`.`results_BEFORE_INSERT` BEFORE INSERT ON `results` FOR EACH ROW
BEGIN

	 DECLARE cmd varCHAR(500);
	 DECLARE result nvarCHAR(500);

if NEW.value='' then 
	 SET cmd = 'c:\\www\\php72\\php.exe -c C:\\www\\php72\\php.ini -f c:\\www\\apache24\\htdocs\\is\\arduino\\arduino2.php comm=1';
	 SET result = sys_eval(cmd);
	 SET NEW.value = result;
end if;

END;$$
DELIMITER ;

# 15. Тест на тригера
insert into results values (default,'',SYSDATE());

# ------------------------------------------------------------------
# 16. Разрешаване на Събития
set Global event_scheduler=ON;

# 17. Създаване на събитие през 10 сек и продължителност до 1 мин. 
# което съхранява данни от Arduino в results (Забранено за изпълнение)
DELIMITER $$

Create event arduino
ON schedule Every 10 second
STARTS CURRENT_TIMESTAMP
ENDS CURRENT_TIMESTAMP + INTERVAL 1 MINUTE
DISABLE
DO begin
 DECLARE cmd varCHAR(500);
 DECLARE result nvarCHAR(500);
 SET cmd = 'c:\\www\\php72\\php.exe -c C:\\www\\php72\\php.ini -f c:\\www\\apache24\\htdocs\\is\\arduino\\arduino2.php comm=1';
 SET result = sys_eval(cmd);
 insert into results values (default,result,SYSDATE());
END;$$
DELIMITER ;

# 18. Разрешаване на събитието
ALTER EVENT arduino ENABLE;

# 19. Преглед на състоянието на всички събития
show events;

# 20. Премахване на събитието
drop event arduino;
# ------------------------------------------------------------------

