-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date,,>
-- Description:	<Description,,>
-- =============================================
/*
-- Разрешаване на xp_cmdshell
USE master
GO
 
EXEC sp_configure 'advanced',1
RECONFIGURE WITH OVERRIDE
 
EXEC sp_configure 'xp_cmdshell',1
RECONFIGURE WITH OVERRIDE
*/
CREATE PROCEDURE [dbo].[Arduino1] @comm varchar(255) =''
AS
BEGIN
-- exec arduino1 'comm=31 temp=12'
--if exists (SELECT j.name FROM tempdb.dbo.sysobjects j where name like '#cmdshell_results%') 
 --  drop table #cmdshell_results


CREATE TABLE #cmdshell_results (line VARCHAR(260)   NULL)
declare @query varchar(500)
set @query='insert into #cmdshell_results exec xp_cmdshell ''c:\www\php72\php.exe c:\www\apache24\htdocs\is\arduino\arduino2.php '+@comm+''''
exec (@query)
--insert into #cmdshell_results  
--exec xp_cmdshell 'c:\www\php72\php.exe -c C:\www\php72\php.ini -f c:\www\apache24\htdocs\is\arduino\arduino2.php comm=2'

   
insert into results select  line, getdate() from #cmdshell_results where line is not null

--delete from results
select  line  from #cmdshell_results where line is not null
END


