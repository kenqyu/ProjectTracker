<?php
namespace app\modules\jobs\models;

use app\models\CWA;

class CwaForm extends CWA
{
    public function beforeValidate()
    {
        $this->due_date = date('Y-m-d', strtotime($this->due_date));
        return parent::beforeValidate();
    }
}
