<?php

namespace app\modules\reports\services;

use app\models\Departments;
use app\models\enums\JobStatus;
use app\models\Job;
use app\models\OrganizationUnit;
use app\models\ProcessingUnit;
use app\models\RequestType;
use app\models\SubDepartment;
use app\models\User;
use yii\base\NotSupportedException;
use yii\db\Expression;
use yii\db\Query;

class HardcodedFieldReportService
{
    private $field;
    private $dateFrom;
    private $dateTo;
    private $additionalFilters;
    private $processing_unit_id;

    private static function getFieldsProcessors()
    {
        return [
            'project_manager_id' => function ($item) {
                $m = User::findOne($item);
                if (!$m) {
                    return null;
                }
                return $m->getShortName();
            },
            'project_lead_id' => function ($item) {
                $m = User::findOne($item);
                if (!$m) {
                    return null;
                }
                return $m->getShortName();
            },
            'creator_id' => function ($item) {
                $m = User::findOne($item);
                if (!$m) {
                    return null;
                }
                return $m->getShortName();
            },
            'request_type_id' => function ($item) {
                $m = RequestType::findOne($item);
                if (!$m) {
                    return null;
                }
                return $m->name;
            },
            'job.status' => function ($item) {
                return JobStatus::getByValue($item)->text();
            },
            'processing_unit_id' => function ($item) {
                $m = ProcessingUnit::findOne($item);
                if (!$m) {
                    return null;
                }
                return $m->name;
            },
            'creator.organization_unit_id' => function ($item) {
                $m = OrganizationUnit::findOne($item);
                if (!$m) {
                    return null;
                }
                return $m->name;
            },
            'creator.department_id' => function ($item) {
                $m = Departments::findOne($item);
                if (!$m) {
                    return null;
                }
                return $m->name;
            },
            'creator.sub_department_id' => function ($item) {
                $m = SubDepartment::findOne($item);
                if (!$m) {
                    return null;
                }
                return $m->name;
            },
            'completion_time_frame' => function ($item) {
                return $item;
            }
        ];
    }

    public function __construct(
        $field,
        \DateTime $dateFrom,
        \DateTime $dateTo,
        $additionalFilters = [],
        $processing_unit_id = null
    ) {
        if (!$this->isFieldAllowed($field)) {
            throw new NotSupportedException('Field \'' . $field . '\' not supported yet');
        }

        $this->field = $field;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->additionalFilters = $additionalFilters;
        $this->processing_unit_id = $processing_unit_id;
    }

    private function isFieldAllowed($field): bool
    {
        return isset(static::getFieldsProcessors()[$field]);
    }

    private function requestData(): array
    {
        $q = (new Query())
            ->select([$this->field, 'COUNT(' . $this->field . ') as c'])
            ->from('job')
            ->innerJoin('user creator', new Expression('creator.id = job.creator_id'))
            ->andWhere(['>=', 'DATE(job.created_at)', $this->dateFrom->format('Y-m-d')])
            ->andWhere(['<=', 'DATE(job.created_at)', $this->dateTo->format('Y-m-d')])
            ->groupBy([$this->field]);

        foreach ($this->additionalFilters as $key => $value) {
            $q->andWhere([$key => $value]);
        }
        return $q->all();
    }

    private function requestCompletingTimeFrameData(): array
    {
        $q = (new Query())
            ->select(['job.id', 'DATEDIFF( job.completed_on, job.created_at ) as `days`'])
            ->from('job')
            ->innerJoin('user creator', new Expression('creator.id = job.creator_id'))
            ->andWhere(['>=', 'DATE(job.created_at)', $this->dateFrom->format('Y-m-d')])
            ->andWhere(['<=', 'DATE(job.created_at)', $this->dateTo->format('Y-m-d')]);

        foreach ($this->additionalFilters as $key => $value) {
            $q->andWhere([$key => $value]);
        }

        $out = [
            'Same Day' => (int)(clone($q))->having(['<=', 'days', 1])->count(),
            '1 week' => (int)(clone($q))->having(['>=', 'days', 2])->andHaving(['<=', 'days', 7])->count(),
            '2 weeks' => (int)(clone($q))->having(['>=', 'days', 8])->andHaving(['<=', 'days', 14])->count(),
            '1 month' => (int)(clone($q))->having(['>=', 'days', 15])->andHaving(['<=', 'days', 31])->count(),
            '3-6 months' => (int)(clone($q))->having(['>=', 'days', 94])->andHaving([
                '<=',
                'days',
                186
            ])->count(),
            '6+ months' => (int)(clone($q))->having(['>=', 'days', 187])->count(),
        ];

        return $out;
    }

    private function prepareData($data)
    {
        $out = [];

        foreach ($data as $item) {
            if ($item[collect(explode('.', $this->field))->last()] !== null) {
                $name = static::getFieldsProcessors()[$this->field]($item[collect(explode('.',
                    $this->field))->last()]);
                if ($name === null) {
                    continue;
                }
                $out[$name] = (int)$item['c'];
            }
        }

        $out = collect($out)->filter(function ($item) {
            return $item > 0;
        })->all();
        ksort($out);

        if ($this->field == 'job.status') {
            $tmp = [];
            foreach (collect(JobStatus::getData())->sortBy('order')->all() as $item) {
                if (isset($out[$item['text']])) {
                    $tmp[$item['text']] = $out[$item['text']];
                    unset($out[$item['text']]);
                }
            }
            $out = $tmp + $out;
        }
        if ($this->field == 'request_type_id') {
            $tmp = [];
            foreach (RequestType::find()->where(['processing_unit_id' => $this->processing_unit_id])->orderBy(['sort' => SORT_ASC])->asArray()->all() as $item) {
                if (isset($out[$item['name']])) {
                    $tmp[$item['name']] = $out[$item['name']];
                    unset($out[$item['name']]);
                }
            }
            $out = $tmp + $out;
        }
        return $out;
    }

    public function proceed()
    {
        if ($this->field == 'completion_time_frame') {
            return $this->requestCompletingTimeFrameData();
        }
        return $this->prepareData($this->requestData());
    }
}
