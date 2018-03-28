<?php

use yii\db\Migration;

class m160726_230610_job extends Migration
{
    public function up()
    {
        $this->createTable('work_type', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer()->defaultValue(0),
            'name' => $this->string()->notNull()->unique()
        ]);

        $this->createTable('job', [
            'id' => $this->primaryKey(),
            'legacy_id' => $this->string()->unique(),
            'name' => $this->string()->notNull(),
            'description' => $this->text(),
            'status' => $this->smallInteger(1)->defaultValue(0)->notNull(),

            'due_date' => $this->date()->notNull(),
            'mandate' => $this->boolean()->defaultValue(false),

            'budget' => $this->double(2)->notNull(),
            'size' => $this->smallInteger(1)->notNull(),

            'creator_id' => $this->integer()->notNull(), // RELATION
            'project_lead_id' => $this->integer(), // RELATION
            'project_manager_id' => $this->integer(), // RELATION
            'agency_id' => $this->integer(), // RELATION
            'iwcm_publishing_assignee_id' => $this->integer(), // RELATION
            'approver' => $this->string(),
            'translation_needed' => $this->boolean()->defaultValue(true),

            'internal_only' => $this->boolean()->defaultValue(true),
            'cwa' => $this->string(),
            'cwa_due_date' => $this->dateTime(),
            'estimate_amount' => $this->double(2),

            'ccc_impact' => $this->boolean()->defaultValue(false),
            'one_voice' => $this->boolean()->defaultValue(false),
            'ccc_contact_id' => $this->integer(), // RELATION
            'content_expiration_date' => $this->date(),
            'completed_on' => $this->date(),
            'published_on' => $this->date(),

            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->notNull() . ' ON UPDATE CURRENT_TIMESTAMP'
        ]);

        $this->addForeignKey(
            'fk-job-creator_id',
            'job',
            'creator_id',
            'user',
            'id'
        );
        $this->addForeignKey(
            'fk-job-project_lead_id',
            'job',
            'project_lead_id',
            'user',
            'id'
        );
        $this->addForeignKey(
            'fk-job-project_manager_id',
            'job',
            'project_manager_id',
            'user',
            'id'
        );
        $this->addForeignKey(
            'fk-job-agency_id',
            'job',
            'agency_id',
            'agency',
            'id'
        );
        $this->addForeignKey(
            'fk-job-iwcm_publishing_assignee_id',
            'job',
            'iwcm_publishing_assignee_id',
            'user',
            'id'
        );
        $this->addForeignKey(
            'fk-job-ccc_contact_id',
            'job',
            'ccc_contact_id',
            'user',
            'id'
        );

        $this->createTable('job_work_type', [
            'job_id' => $this->integer()->notNull(),
            'work_type_id' => $this->integer()->notNull(),
            'PRIMARY KEY(job_id, work_type_id)'
        ]);

        $this->addForeignKey(
            'fk-job_work_type-job_id',
            'job_work_type',
            'job_id',
            'job',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-job_work_type-work_type_id',
            'job_work_type',
            'work_type_id',
            'work_type',
            'id',
            'CASCADE'
        );

        $this->createTable('departments', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique()
        ]);

        $this->createTable('job_department', [
            'job_id' => $this->integer()->notNull(),
            'department_id' => $this->integer()->notNull(),
            'PRIMARY KEY(job_id, department_id)'
        ]);

        $this->addForeignKey(
            'fk-job_department-job_id',
            'job_department',
            'job_id',
            'job',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-job_department-department_id',
            'job_department',
            'department_id',
            'departments',
            'id',
            'CASCADE'
        );

        $this->createTable('job_cost_center', [
            'id' => $this->primaryKey(),
            'job_id' => $this->integer()->notNull(), // RELATION
            'cost_center' => $this->string()->notNull(), // RELATION
            'percent' => $this->integer()->notNull()
        ]);

        $this->addForeignKey(
            'fk-job_cost_center-job_id',
            'job_cost_center',
            'job_id',
            'job',
            'id',
            'CASCADE'
        );

        $this->createTable('job_invoice', [
            'id' => $this->primaryKey(),
            'job_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'number' => $this->string()->notNull(),
            'amount' => $this->double(2)->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->notNull() . ' ON UPDATE CURRENT_TIMESTAMP'
        ]);

        $this->addForeignKey(
            'fk-job_invoice-job_id',
            'job_invoice',
            'job_id',
            'job',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-job_invoice-user_id',
            'job_invoice',
            'user_id',
            'user',
            'id'
        );

    }

    public function down()
    {
        $this->dropTable('job_invoice');
        $this->dropTable('job_cost_center');
        $this->dropTable('job_department');
        $this->dropTable('departments');
        $this->dropTable('job_work_type');
        $this->dropTable('job');
        $this->dropTable('work_type');
    }
}
