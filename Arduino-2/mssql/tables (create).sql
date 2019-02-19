CREATE TABLE [dbo].[results](
	[id] [int] PRIMARY KEY IDENTITY(1,1) NOT NULL,
	[result] [nvarchar](250) NULL,
	[time] [datetime] NULL
)



CREATE TABLE [dbo].[arduino](
	[id] [int] PRIMARY KEY IDENTITY(1,1) NOT NULL,
	[time] [datetime] NULL,
	[hum] [real] NULL,
	[temp] [real] NULL
)