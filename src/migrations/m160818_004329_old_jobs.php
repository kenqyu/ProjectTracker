<?php

use yii\db\Migration;

class m160818_004329_old_jobs extends Migration
{
    public function up()
    {
        $this->createTable('old_job', [
            'id' => $this->primaryKey(),
            'number' => $this->string(),
            'name' => $this->string(),
            'description' => $this->text(),
            'submitted_by' => $this->string(),
            'submit_date' => $this->date(),
            'rush' => $this->boolean(),
            'due_date' => $this->date(),
            'work_type' => $this->string(),
            'justifications' => $this->text(),
            'dce_lead' => $this->string(),
            'status' => $this->string(),
            'last_date_user' => $this->string(),
            'last_update_date' => $this->dateTime(),
            'comments' => $this->text(),
            'it_notification' => $this->text(),
            'iwcm_publishing_assignee' => $this->string(),
            'complete_date' => $this->date(),
            'current_url' => $this->text(),
            'ccc_impact' => $this->boolean(),
            'ccc_contact' => $this->string(),
            'affiliate_compliance' => $this->string(),
            'imcli' => $this->boolean(),
            'related_olm' => $this->string(),
            'sce_approvers' => $this->text(),
            'accounting' => $this->text(),
            'cwa' => $this->text(),
            'estimate_amount' => $this->string(),
            'translation_needed' => $this->boolean(),
            'translation_rush' => $this->boolean(),
            'translation_request_date' => $this->date(),
            'translation_due_date' => $this->date(),
            'translation_status' => $this->string(),
            'attachment' => $this->string(),
            'invoice_number' => $this->string(),
            'invoice_amount' => $this->string(),
            'publishing_date' => $this->date(),
            'requestor_email' => $this->string(),
            'project_url' => $this->text(),
            'progress' => $this->string(),
            'size' => $this->string()
        ]);
    }

    public function down()
    {
        $this->dropTable('old_job');
    }
}
