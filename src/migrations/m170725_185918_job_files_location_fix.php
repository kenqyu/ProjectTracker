<?php

use yii\db\Migration;
use yii\helpers\FileHelper;

class m170725_185918_job_files_location_fix extends Migration
{
    public function safeUp()
    {
        foreach (\app\models\JobFile::find()->all() as $item) {
            if (!is_file(__DIR__ . '/../web/static/uploads/files/' . $item->id . DIRECTORY_SEPARATOR . $item->file_name)) {
                $item->delete();
                continue;
            }
            FileHelper::createDirectory(__DIR__ . '/../web/static/uploads/files/' . $item->job_id);
            echo "\nMoving file #" . $item->id . " to " . $item->job_id . " ";
            echo rename(
                __DIR__ . '/../web/static/uploads/files/' . $item->id . DIRECTORY_SEPARATOR . $item->file_name,
                __DIR__ . '/../web/static/uploads/files/' . $item->job_id . DIRECTORY_SEPARATOR . $item->file_name
            ) ? 'true' : 'false';
            echo "\n";
        }
        $this->checkFolders();
    }

    public function safeDown()
    {
        foreach (\app\models\JobFile::find()->all() as $item) {
            if (!is_file(__DIR__ . '/../web/static/uploads/files/' . $item->job_id . DIRECTORY_SEPARATOR . $item->file_name)) {
                $item->delete();
            }
            FileHelper::createDirectory(__DIR__ . '/../web/static/uploads/files/' . $item->id);
            echo "\nMoving file #" . $item->id . " back to " . $item->id . " ";
            echo rename(
                __DIR__ . '/../web/static/uploads/files/' . $item->job_id . DIRECTORY_SEPARATOR . $item->file_name,
                __DIR__ . '/../web/static/uploads/files/' . $item->id . DIRECTORY_SEPARATOR . $item->file_name
            ) ? 'true' : 'false';
            echo "\n";
        }
        $this->checkFolders();
    }

    private function checkFolders()
    {
        foreach (glob(__DIR__ . '/../web/static/uploads/files/' . "*") as $file) {
            if (is_dir($file)) {
                if (\app\helpers\AlexBond::is_dir_empty($file)) {
                    FileHelper::removeDirectory($file);
                }
            }
        }
    }
}
