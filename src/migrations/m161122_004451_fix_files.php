<?php

use yii\db\Migration;

class m161122_004451_fix_files extends Migration
{
    public function up()
    {
        foreach (\app\models\JobFile::find()->all() as $item) {
            /**
             * @var \app\models\JobFile $item
             */
            if (is_file($item->folder . $item->file_name)) {
                rename($item->folder . $item->file_name, $item->getPath());
            }
        }
    }

    public function down()
    {

    }
}
