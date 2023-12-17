<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use app\models\Currency;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\httpclient\Client;

class DataController extends Controller
{
    /**
     * Команда для добавления данных с сайта http://www.cbr.ru/ в таблицы "Currency"
     * @return int Exit code
     */
    public function actionUpdate()
    {
      // Url для получения даннных
      $client = new Client(['baseUrl' => 'http://www.cbr.ru/scripts/XML_daily.asp']);

      $response = $client->createRequest()
        ->setFormat(Client::FORMAT_XML)
        ->send();
      if ($response->isOk) {
        // Записываем предварительный массив с данными
        $prepareData = $response->data;

        // Подготавливаем данные для записи в БД
        $data = array_map(function($el){
          return [
            'name' => $el['Name'],
            'code' => mb_strtolower($el['CharCode']),
            'rate' => floatval(str_replace(',', '.', $el['Value']))
          ];
        }, end($prepareData));
      }

      // Запись данных
      $transaction = Yii::$app->db->beginTransaction();
      try {
        foreach ($data as $row) {
          $model = Currency::find()->where(['code' => $row['code']])->one();
          if($model) {
            $model->rate = $row['rate'];
          } else {
            $model = new Currency();
            $model->attributes = $row;
          }
          $model->save();
        }
        $transaction->commit();
        echo "Данные успешно добавлены!";
        return ExitCode::OK;
      } catch(\Exception $e) {
        $transaction->rollBack();
        echo "Ошибка добавления данных!";
        throw $e;
        return ExitCode::IOERR;
      } catch(\Throwable $e) {
        $transaction->rollBack();
        echo "Ошибка добавления данных!";
        return ExitCode::IOERR;
      }
    }
}
