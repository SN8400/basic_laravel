<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('sqlsrv2')->unprepared("

-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date,,>
-- Description:	<Description,,>
-- =============================================
CREATE PROCEDURE dbo.ChangeOldAEtoNewAE
	-- Add the parameters for the stored procedure here
	@oldAEId integer,
	@newAEId integer,
	@cropId integer
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

    -- Insert statements for procedure here
	--SELECT @oldAEId as old_id , 	@newAEId as new_id  ,@cropId  as crop_id

	PRINT '============= Start =============';
	PRINT 'crop_id = ' + CAST( @cropId AS VARCHAR(10));
	PRINT 'old_id = ' +  CAST( @oldAEId AS VARCHAR(10));
	PRINT 'new_id = ' + CAST( @newAEId AS VARCHAR(10));

	--update old user to new user
	update [dbo].[user_farmer] set [user_id] = @newAEId where [crop_id] = @cropId and [user_id] =  @oldAEId;



	--update old manager to new manager
	update [dbo].[user_farmer] set [manager_id] = @newAEId where [crop_id] = @cropId and [manager_id] =  @oldAEId;



	update [dbo].[user_acts] set [user_id] = @newAEId where [crop_id] = @cropId and [user_id] =  @oldAEId;


	update [dbo].[user_acts] set [user_action_id] = @newAEId where [crop_id] = @cropId and [user_action_id] =  @oldAEId;


	
	update [dbo].[sowings] set [user_id] = @newAEId where [crop_id] = @cropId and [user_id] =  @oldAEId;


	update [dbo].[sowing_logs] set [user_id] = @newAEId where [crop_id] = @cropId and [user_id] =  @oldAEId;



	update [dbo].[sample_plans] set [user_id] = @newAEId where [crop_id] = @cropId and [user_id] =  @oldAEId;



	update [dbo].[harvest_plans] set [user_id] = @newAEId where [crop_id] = @cropId and [user_id] =  @oldAEId;



	update [dbo].[farmer_audits] set [user_id] = @newAEId where [crop_id] = @cropId and [user_id] =  @oldAEId;


	PRINT '============= End =============';
END
");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection('sqlsrv2')->unprepared("DROP PROCEDURE IF EXISTS ChangeOldAEtoNewAE");
    }
};
