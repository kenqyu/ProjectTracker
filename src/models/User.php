<?php

namespace app\models;

use app\models\enums\UserStatus;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $access_token
 * @property string $auth_key
 * @property string $username
 * @property string $password write-only password
 * @property string $password_reset_token
 * @property string $email
 * @property string $first_name
 * @property string $last_name
 * @property integer $status
 * @property boolean $approved
 * @property boolean $no_mails
 * @property boolean $default_ccc_contact
 * @property integer $role
 * @property string $created_at
 * @property string $updated_at
 * @property string $shortName
 * @property string $accessToken
 * @property string $fullName
 * @property string $authKey
 * @property string $phone
 * @property integer $organization_unit_id
 * @property string $organization_unit_other
 * @property integer $department_id
 * @property integer $sub_department_id
 *
 *
 * @property UserTypes[] $userTypes
 * @property Job[] $subscriptions
 * @property ProcessingUnit[] $processingUnits
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    public static function getDataList($withEmpty = false, $type = null, $activeOnly = false)
    {
        $r = static::find();
        if ($type !== null) {
            $r->joinWith(['userTypes userTypes'], true, 'INNER JOIN')->where(['userTypes.type_id' => $type]);
        }
        if ($activeOnly) {
            $r->andWhere(['status' => UserStatus::ACTIVE]);
        }
        $r->orderBy(['first_name' => SORT_ASC, 'last_name' => SORT_ASC]);
        $out = ArrayHelper::map($r->all(), 'id', function ($item) {
            return $item->first_name . ' ' . $item->last_name;
        });
        if (!$withEmpty) {
            return $out;
        }

        return ['' => 'None'] + $out;
    }

    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => UserStatus::ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = 60 * 60 * 24;
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'access_token',
                    'auth_key',
                    'username',
                    'password',
                    'email',
                    'first_name',
                    'last_name',
                    'role',
                    'phone'
                ],
                'required'
            ],
            [['status', 'role', 'organization_unit_id', 'department_id', 'sub_department_id'], 'integer'],
            [
                [
                    'access_token',
                    'auth_key',
                    'username',
                    'password',
                    'password_reset_token',
                    'email',
                    'first_name',
                    'last_name',
                    'phone',
                    'organization_unit_other'
                ],
                'string',
                'max' => 255
            ],
            [['approved', 'no_mails', 'default_ccc_contact'], 'boolean'],
            [['access_token'], 'unique'],
            [['auth_key'], 'unique'],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['status'], 'default', 'value' => \app\models\enums\UserStatus::ACTIVE],
            [['status'], 'in', 'range' => \app\models\enums\UserStatus::getKeys()],
            [['role'], 'in', 'range' => \app\models\enums\UserRoles::getKeys()]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'access_token' => 'Access Token',
            'auth_key' => 'Auth Key',
            'username' => 'Username',
            'password' => 'Password',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'status' => 'Status',
            'approved' => 'Approved',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriptions()
    {
        return $this->hasMany(Job::class, ['id' => 'job_id'])
            ->viaTable('subscription', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcessingUnits()
    {
        return $this->hasMany(ProcessingUnit::class, ['id' => 'processing_unit_id'])
            ->viaTable('user_processing_unit', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganizationUnit()
    {
        return $this->hasOne(OrganizationUnit::class, ['id' => 'organization_unit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Departments::class, ['id' => 'department_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubDepartment()
    {
        return $this->hasOne(SubDepartment::class, ['id' => 'sub_department_id']);
    }

    public function getProcessingUnitsIds()
    {
        return ArrayHelper::map($this->processingUnits, 'id', 'id');
    }

    /**
     * @inheritdoc
     * @return \app\models\queries\UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\queries\UserQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserTypes()
    {
        return $this->hasMany(UserTypes::class, ['user_id' => 'id']);
    }

    public function beforeValidate()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
        $this->auth_key = Yii::$app->security->generateRandomString();
        return parent::beforeValidate();
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()->where(['access_token' => $token])->one();
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()->where(['username' => $username])->one();
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::find()->where(['email' => $email])->one();
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }


    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
        $this->save(false, ['password_reset_token']);
    }

    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function updateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
        $this->save(false, ['access_token']);
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|integer an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns user account status - 0 is Disabled, 1 is Active.
     * @return integer corresponding to account status - 0 is Disabled, 1 is Active.
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function fields()
    {
        return [
            'id',
            'username',
            'email',
            'first_name',
            'last_name',
            'status',
            'created_at',
            'updated_at'
        ];
    }

    public function subscribe(Job $job)
    {
        if (!Subscription::find()->where(['job_id' => $job->id, 'user_id' => $this->id])->exists()) {
            $this->link('subscriptions', $job);
        }
    }

    public function unSubscribe(Job $job)
    {
        if (Subscription::find()->where(['job_id' => $job->id, 'user_id' => $this->id])->exists()) {
            $this->unlink('subscriptions', $job, true);
        }
    }

    public function activate()
    {
        if (!$this->approved) {
            $this->approved = true;
            if ($this->save(false, ['approved'])) {
                $message = Yii::$app->mailer->compose('account_approved', ['model' => $this]);
                $message->setFrom('no-reply@scemanagement.com');
                $message->setTo($this->email);
                $message->setSubject('(External):CX Project Tracker Account Activated');
                Yii::$app->mailer->send($message);
                return true;
            }
        }
        return false;
    }

    public function decline()
    {
        if (!$this->approved) {
            $this->approved = true;
            $this->status = UserStatus::DISABLED;
            if ($this->save(false, ['approved', 'status'])) {
                return true;
            }
        }
        return false;
    }

    public function getShortName()
    {
        return $this->first_name . ' ' . substr($this->last_name, 0, 1) . '.';
    }

    public function getFullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function subscribed($model)
    {
        foreach ($this->subscriptions as $item) {
            if ($item->id == $model->id) {
                return true;
            }
        }
        return false;
    }

    public function madeDefaultCCCContact()
    {
        if (collect($this->userTypes)->filter(function ($item) {
            return $item->type_id == \app\models\enums\UserTypes::CCC_CONTACT;
        })->isEmpty()
        ) {
            return false;
        }
        User::updateAll(['default_ccc_contact' => false], 'default_ccc_contact=1');
        $this->default_ccc_contact = true;
        return $this->save(false, ['default_ccc_contact']);
    }
}
