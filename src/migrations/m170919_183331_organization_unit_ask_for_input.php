<?php

use yii\db\Migration;

class m170919_183331_organization_unit_ask_for_input extends Migration
{
    public function safeUp()
    {
        $this->addColumn('organization_unit', 'ask_for_input', $this->boolean()->defaultValue(false));
    }

    public function safeDown()
    {
        $this->dropColumn('organization_unit', 'ask_for_input');
    }
}
