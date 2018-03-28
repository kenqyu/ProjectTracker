<?php

use yii\db\Migration;

class m160726_230026_agency extends Migration
{
    public function up()
    {
        $this->createTable('agency', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->unique()->notNull()
        ]);
    }

    public function down()
    {
        $this->dropTable('agency');
    }
}
