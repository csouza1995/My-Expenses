<?php

namespace app\models\Traits;

use yii\behaviors\TimestampBehavior;

trait UseTimestamps
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => time(),
            ],
        ];
    }
}
