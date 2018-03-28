<?php

use app\models\User;
use yii\db\Migration;

class m161116_192949_new_usernames extends Migration
{
    public function up()
    {
        foreach (\app\models\User::find()->all() as $item) {
            $i = 0;
            do {
                $item->username = mb_strtolower($item->first_name) . '.' . mb_strtolower($item->last_name) . ($i == 0 ? '' : $i);
                $i++;
            } while (User::find()->where(['username' => $item->username])->exists());
            $item->save(false, ['username']);
        }
    }

    public function down()
    {

    }
}
