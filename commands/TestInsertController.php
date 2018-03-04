<?php
/**
 * @link http://rdbcache.com/
 * @copyright Copyright (c) 2017-2018 Sam Wen
 * @license http://rdbcache.com/license/
 */

namespace app\commands;

use Yii;
use app\components\TestController;

class TestInsertController extends TestController
{
    public function actionIndex() {

        $start_id = $this->getTestStartAtId();

        $this->actionWithTable();
        
        $this->actionWithTableAndGeneratedId();

        $stop_id = $this->getTestStopAtId();
        $this->summary($start_id, $stop_id);
    }

    public function actionWithTable() {

        $this->setup(__FUNCTION__);

        $this->flushLocalCache();

        $table = 'tb1';

        $values_array = [
            ['key' => 'insert_aaa', 'name' => 'new_name001', 'age' => 11],
            ['key' => 'insert_bbb', 'name' => 'new_name002', 'age' => 12 ],
            ['key' => 'insert_ccc', 'name' => 'new_name003', 'age' => 13 ],
            ['key' => '*', 'name' => 'new_name004', 'age' => 14 ],
        ];
        
         //insert
        $api = '/v1/insert/'.$table;
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($values_array)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $this->HttpOK($response);

        echo "\nwait 1 seconds for sync data\n";
        sleep(1);
        
        // check database
        //
        for ($i = 0; $i < count($values_array); $i++) {

            $condition = $values_array[$i];
            unset($condition['key']);
            $value = $this->dbFind($condition, $table);

            if ($value == null) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - " . $keys[$i]);
                return;
            }
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTableAndGeneratedId() {

        $this->setup(__FUNCTION__);

        $this->flushLocalCache();

        $table = 'tb1';

        $values_array = [
            ['name' => 'new_name001', 'age' => 11 ],
            ['name' => 'new_name002', 'age' => 12 ],
            ['name' => 'new_name003', 'age' => 13 ],
        ];
        
         //insert
        $api = '/v1/insert/'.$table;
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($values_array)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $this->HttpOK($response);

        echo "\nwait 1 seconds for sync data\n";
        sleep(1);

        // check database
        //
        for ($i = 0; $i < count($values_array); $i++) {

            $value = $this->dbFind($values_array[$i], $table);

            if ($value == null) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - $key");
                return;
            }
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }
}
