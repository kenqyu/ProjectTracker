<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "old_job".
 *
 * @property integer $id
 * @property string $number
 * @property string $name
 * @property string $description
 * @property string $submitted_by
 * @property string $submit_date
 * @property integer $rush
 * @property string $due_date
 * @property string $work_type
 * @property string $justifications
 * @property string $dce_lead
 * @property string $status
 * @property string $last_date_user
 * @property string $last_update_date
 * @property string $comments
 * @property string $it_notification
 * @property string $iwcm_publishing_assignee
 * @property string $complete_date
 * @property string $current_url
 * @property integer $ccc_impact
 * @property string $ccc_contact
 * @property string $affiliate_compliance
 * @property integer $imcli
 * @property string $related_olm
 * @property string $sce_approvers
 * @property string $accounting
 * @property string $cwa
 * @property string $estimate_amount
 * @property integer $translation_needed
 * @property integer $translation_rush
 * @property string $translation_request_date
 * @property string $translation_due_date
 * @property string $translation_status
 * @property string $attachment
 * @property string $invoice_number
 * @property string $invoice_amount
 * @property string $publishing_date
 * @property string $requestor_email
 * @property string $project_url
 * @property string $progress
 * @property string $size
 */
class OldJob extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'old_job';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'description',
                    'justifications',
                    'comments',
                    'sce_approvers',
                    'accounting',
                    'cwa',
                    'it_notification',
                    'current_url',
                    'project_url'
                ],
                'string'
            ],
            [
                [
                    'submit_date',
                    'due_date',
                    'last_update_date',
                    'complete_date',
                    'translation_request_date',
                    'translation_due_date',
                    'publishing_date'
                ],
                'safe'
            ],
            [['rush', 'ccc_impact', 'imcli', 'translation_needed', 'translation_rush'], 'boolean'],
            [
                [
                    'number',
                    'name',
                    'submitted_by',
                    'work_type',
                    'dce_lead',
                    'status',
                    'last_date_user',
                    'iwcm_publishing_assignee',
                    'ccc_contact',
                    'affiliate_compliance',
                    'related_olm',
                    'estimate_amount',
                    'translation_status',
                    'attachment',
                    'invoice_number',
                    'invoice_amount',
                    'requestor_email',
                    'progress',
                    'size'
                ],
                'string',
                'max' => 255
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Number',
            'name' => 'Name',
            'description' => 'Description',
            'submitted_by' => 'Submitted By',
            'submit_date' => 'Submit Date',
            'rush' => 'Rush',
            'due_date' => 'Due Date',
            'work_type' => 'Work Type',
            'justifications' => 'Justifications',
            'dce_lead' => 'Dce Lead',
            'status' => 'Status',
            'last_date_user' => 'Last Date User',
            'last_update_date' => 'Last Update Date',
            'comments' => 'Comments',
            'it_notification' => 'It Notification',
            'iwcm_publishing_assignee' => 'Iwcm Publishing Assignee',
            'complete_date' => 'Complete Date',
            'current_url' => 'Current Url',
            'ccc_impact' => 'Ccc Impact',
            'ccc_contact' => 'Ccc Contact',
            'affiliate_compliance' => 'Affiliate Compliance',
            'imcli' => 'Imcli',
            'related_olm' => 'Related Olm',
            'sce_approvers' => 'Sce Approvers',
            'accounting' => 'Accounting',
            'cwa' => 'Cwa',
            'estimate_amount' => 'Estimate Amount',
            'translation_needed' => 'Translation Needed',
            'translation_rush' => 'Translation Rush',
            'translation_request_date' => 'Translation Request Date',
            'translation_due_date' => 'Translation Due Date',
            'translation_status' => 'Translation Status',
            'attachment' => 'Attachment',
            'invoice_number' => 'Invoice Number',
            'invoice_amount' => 'Invoice Amount',
            'publishing_date' => 'Publishing Date',
            'requestor_email' => 'Requestor Email',
            'project_url' => 'Project Url',
            'progress' => 'Progress',
            'size' => 'Size',
        ];
    }
}