<?php

namespace app\modules\reports\services;

use app\helpers\AlexBond;
use app\models\CustomFormField;
use app\models\enums\CustomFormFieldType;
use app\models\JobCustomFields;
use app\models\RequestType;

class CustomFieldReportService
{
    private $requestType;
    private $label;

    private $dateFrom;
    private $dateTo;

    private $additionalFilters;

    public function __construct(
        RequestType $requestType,
        string $label,
        \DateTime $dateFrom,
        \DateTime $dateTo,
        $additionalFilters = []
    )
    {
        $this->requestType = $requestType;
        $this->label = $label;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->additionalFilters = $additionalFilters;
    }

    private function requestData()
    {
        $additional = [];
        $params = [];
        $i = 1;
        foreach ($this->additionalFilters as $key => $value) {
            $params[':addFilter_' . $i] = $value;
            $additional[] = $key . '= :addFilter_' . $i;
            $i++;
        }
        if (count($additional) > 0) {
            $additional = 'AND ' . implode(' AND ', $additional);
        } else {
            $additional = '';
        }
        return \Yii::$app->db->createCommand('SELECT
	cf.`value`,
	count(cf.`value`)
FROM
	job_custom_fields cf
	INNER JOIN job job ON job.id = cf.job_id 
	AND job.request_type_id = ' . $this->requestType->id . ' 
	AND job.created_at >= \'' . $this->dateFrom->format('Y-m-d') . '\'
	AND job.created_at <= \'' . $this->dateTo->format('Y-m-d') . '\'
	' . $additional . '
WHERE
	cf.label = \'' . $this->label . '\' 
GROUP BY
	cf.`value`', $params)->queryAll(\PDO::FETCH_NUM);
    }

    private function prepareData($data)
    {
        $out = [];

        $type = null;
        $typeModel = CustomFormField::find()->where([
            'form_id' => $this->requestType->custom_form_id,
            'label' => $this->label
        ])->one();
        if ($typeModel) {
            $type = $typeModel->type;
        }

        foreach ($data as $item) {
            if ($item[0] == '' || $item[0] == '""') {
                continue;
            }
            $out[$item[0]] = (int)$item[1];
        }


        $col = collect($out);
        if (AlexBond::isJson(key($out))) {
            $tmp = [];
            $col->each(function ($item, $key) use (&$tmp, $type) {
                $decoded = json_decode($key);
                if ($type == CustomFormFieldType::CHECKBOX) {
                    if ($key != 1) {
                        $key = 'No';
                    } else {
                        $key = 'Yes';
                    }
                }
                if (is_array($decoded)) {
                    foreach ($decoded as $i) {
                        if (isset($tmp[$i])) {
                            $tmp[$i] += $item;
                        } else {
                            $tmp[$i] = $item;
                        }
                    }
                } else {
                    if (isset($tmp[$key])) {
                        $tmp[$key] += $item;
                    } else {
                        $tmp[$key] = $item;
                    }
                }
            });
            $out = $tmp;
        }
        $out = collect($out)->filter(function ($item) {
            return $item > 0;
        })->all();
        ksort($out);

        if ($typeModel->type == CustomFormFieldType::CHECKBOX_LIST || $typeModel->type == CustomFormFieldType::SELECT) {
            $options = $typeModel->getOptions();

            $tmp = [];

            foreach ($options as $key => $item) {
                if (isset($out[$item])) {
                    $tmp[$item] = $out[$item];
                    unset($out[$item]);
                }
            }

            $out = $tmp + $out;
        }
        return $out;
    }

    public function proceed()
    {
        return $this->prepareData($this->requestData());
    }
}
