<?php

use yii\db\Migration;

class m170915_214238_organization_units_sort extends Migration
{
    public function safeUp()
    {
        $this->addColumn('organization_unit', 'order', $this->integer()->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn('organization_unit', 'order');
    }
}
