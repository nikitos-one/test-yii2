<?php

namespace app\controllers;

use yii\rest\ActiveController;
use app\models\Currency;

class CurrencyController extends ActiveController
{
  public $modelClass = 'app\models\Currency';

  public function actions()
  {
    $actions = parent::actions();

    // Удаляем метод actionIndex(), который определен по умолчанию
    unset($actions['view']);

    return $actions;
  }

  /**
   * Получаем информамацию о валюте по коду
   * @param $code
   * @return array|\yii\db\ActiveRecord[]
   */
  public function actionView($code)
  {
    return Currency::find()->where(['code' => $code])->all();
  }
}
