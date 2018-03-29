<?php
namespace app\modules\api\controllers;

use app\components\ActiveApiController;
use app\models\Job;
use app\models\SavedSearch;
use yii\base\InvalidParamException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecordInterface;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class JobsController extends ActiveApiController
{
    public $modelClass = Job::class;

    /**
     * Returns the data model based on the primary key given.
     * If the data model is not found, a 404 HTTP exception will be raised.
     * @param string $id the ID of the model to be loaded. If the model has a composite primary key,
     * the ID must be a string of the primary key values separated by commas.
     * The order of the primary key values should follow that returned by the `primaryKey()` method
     * of the model.
     * @return ActiveRecordInterface the model found
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModel($id)
    {
        $model = Job::find()->where(['job.id' => $id])->one();

        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException("Object not found: $id");
        }
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return ArrayHelper::merge(parent::verbs(), [
            'search' => ['POST'],
        ]);
    }

    public function actions()
    {
        return [
            'view' => [
                'class' => 'yii\rest\ViewAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'create' => [
                'class' => 'yii\rest\CreateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->createScenario,
            ],
            'update' => [
                'class' => 'yii\rest\UpdateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->updateScenario,
            ],
            'delete' => [
                'class' => 'yii\rest\DeleteAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return new ActiveDataProvider([
            'query' => Job::find(),
            'pagination' => false
        ]);
    }

    public function actionSearch()
    {
        $model = new Job();
        $query = Job::find()->withAll();

        foreach (\Yii::$app->request->getBodyParam('filters', []) as $item) {
            $rule = [];

            $split = mbsplit('\.', $item['field']);
            if (count($split) > 2) {
                throw new InvalidParamException('Invalid field: ' . $item['field']);
            }
            if (count($split) == 2) {
                if (!in_array($split[0], $model->getRelations())
                ) {
                    throw new InvalidParamException('Cant find \'' . $split[0] . '\' relation');
                }
            }

            switch ($item['type']) {
                case 'match':
                    $rule = [$item['field'] => $item['value']];
                    break;
                case 'like':
                    $rule = ['like', $item['field'], $item['value']];
                    break;
                case 'in':
                    $rule = ['in', $item['field'], $item['value']];
                    break;
                case 'not in':
                    $rule = ['not in', $item['field'], $item['value']];
                    break;
                case 'between':
                    $rule = ['between', $item['field'], $item['value']];
                    break;
                case 'boolean':
                    $rule = [$item['field'] => !!$item['value']];
                    break;
                case 'null':
                    $rule = [$item['field'] => null];
                    break;
                case 'not_null':
                    $rule = ['IS NOT', $item['field'], null];
                    break;
                case '>':
                    $rule = ['>', $item['field'], $item['value']];
                    break;
                case '>=':
                    $rule = ['>=', $item['field'], $item['value']];
                    break;
                case '<':
                    $rule = ['<', $item['field'], $item['value']];
                    break;
                case '<=':
                    $rule = ['<=', $item['field'], $item['value']];
                    break;
            }

            if ($item['and']) {
                $query->andWhere($rule);
            } else {
                $query->orWhere($rule);
            }
        }

        return new ActiveDataProvider([
            'query' => $query
        ]);
    }
}
