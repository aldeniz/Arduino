/*
-- Разрешаване на xp_cmdshell
USE master
GO
 
EXEC sp_configure 'advanced',1
RECONFIGURE WITH OVERRIDE
 
EXEC sp_configure 'xp_cmdshell',1
RECONFIGURE WITH OVERRIDE
*/
CREATE TRIGGER [dbo].[trgInsteadOfInsert] ON [dbo].[results] 
INSTEAD OF INSERT
AS
--insert into results values('comm=32',null)
--insert into results values('comm=2',null)
	declare @value varchar(100);
	declare @time datetime;
	set nocount on
	select @value=d.result from inserted d;
	select @time=d.time from inserted d;
	set nocount off

	BEGIN
	/*
		if(@value>1200)
		begin
			RAISERROR('Cannot insert where salary > 1200',16,1);
			ROLLBACK;
		end
		else
	*/
		if (@time is null)
		begin
		set nocount on
			CREATE TABLE #cmdshell_results (line VARCHAR(260)   NULL)
			declare @query varchar(500)
			set @query='insert into #cmdshell_results exec xp_cmdshell ''c:\www\php72\php.exe -c C:\www\php72\php.ini -f c:\www\apache24\htdocs\is\arduino\arduino2.php '+@value+''''
			exec (@query)
			--insert into #cmdshell_results  exec xp_cmdshell "c:\www\php72\php.exe -c C:\www\php72\php.ini -f c:\www\apache24\htdocs\is\arduino\arduino2.php comm=2"
			insert into results select  line, getdate()  from #cmdshell_results where line is not null
		set nocount off
		end
	END


