
-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date,,>
-- Description:	<Description,,>
-- =============================================
CREATE PROCEDURE dbo.ReturnData @comm varchar(50) AS
BEGIN
--EXEC [dbo].[ReturnData] 'comm=1' 
	SET NOCOUNT ON;
	declare @a numeric(18);
	insert into results values (@comm,null)
	select @a=@@IDENTITY ;
	set nocount off;
	select result, [time]=convert(varchar,getdate(),103)+
	' '+ convert(varchar, getdate(), 108) from results where id=@a

END
GO
