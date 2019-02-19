
--EXEC sp_xp_cmdshell_proxy_account 'RASHIDOV10\Administrator', 'rashidov76122'

-- Разрешаване на xp_cmdshell
USE master
 
EXEC sp_configure 'advanced',1
RECONFIGURE WITH OVERRIDE
 
EXEC sp_configure 'xp_cmdshell',1
RECONFIGURE WITH OVERRIDE

GRANT EXECUTE ON xp_cmdshell TO ibs

USE ARDUINO
 
EXEC sp_configure 'advanced',1
RECONFIGURE WITH OVERRIDE
 
EXEC sp_configure 'xp_cmdshell',1
RECONFIGURE WITH OVERRIDE


