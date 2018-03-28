<?php
namespace app\widgets;

use app\assets\DatePickerAsset;
use app\assets\DatePickerLanguageAsset;
use yii\helpers\Json;

class DatePicker extends \dosamigos\datepicker\DatePicker
{
    /**
     * Registers required script for the plugin to work as DatePicker
     */
    public function registerClientScript()
    {
        $js = [];
        $view = $this->getView();

        // @codeCoverageIgnoreStart
        if ($this->language !== null) {
            $this->clientOptions['language'] = $this->language;
            DatePickerLanguageAsset::register($view)->js[] = 'bootstrap-datepicker.' . $this->language . '.min.js';
        } else {
            DatePickerAsset::register($view);
        }
        // @codeCoverageIgnoreEnd

        $id = $this->options['id'];
        $selector = ";jQuery('#$id')";

        if ($this->addon || $this->inline) {
            $selector .= ".parent()";
        }

        $options = !empty($this->clientOptions) ? Json::encode($this->clientOptions) : '';

        if ($this->inline) {
            $this->clientEvents['changeDate'] = "function (e){ jQuery('#$id').val(e.format());}";
        }

        $js[] = "$selector.datepicker($options);";

        if (!empty($this->clientEvents)) {
            foreach ($this->clientEvents as $event => $handler) {
                $js[] = "$selector.on('$event', $handler);";
            }
        }
        $view->registerJs(implode("\n", $js));
    }

}
