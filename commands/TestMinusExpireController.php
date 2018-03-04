<?php
/**
 * @link http://rdbcache.com/
 * @copyright Copyright (c) 2017-2018 Sam Wen
 * @license http://rdbcache.com/license/
 */

namespace app\commands;

use Yii;
use app\components\TestController;

use app\models\Tb1;

class TestMinusExpireController extends TestController
{
    public function actionIndex() {

        $start_id = $this->getTestStartAtId();

        $this->actionWithTableAndExpire();

        $stop_id = $this->getTestStopAtId();
        $this->summary($start_id, $stop_id);
    }

    public function actionWithTableAndExpire() {

        $this->setup(__FUNCTION__);

        $this->flushLocalCache();

        $expire = "-3";
        $table = 'tb1';

        // get expected from database
        //
        $expected_array = $this->dbFind(1, $table);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $key = 'my-hash-key-minus-expire';
        $api = '/v1/get/'.$key.'/'.$expire.'/'.$table.'?id=1';
        
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }
        
        $resp_key = $response->data['key'];
        if ($resp_key != $key) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
        }

        $data = $response->data['data'];

        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
        }

        $this->HttpOK($response);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, $expected_array);

        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
            return;
        }

        $this-> RedisOK();

        // get by key only
        //
        $api = '/v1/get/'.$key;
        $response = $this->createRequest()
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $resp_key = $response->data['key'];
        if ($resp_key != $key) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
            return;
        }
        $data = $response->data['data'];

        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $record = Tb1::findOne(1);
        $record->name = 'new mike';
        
        for ($i = 0; $i < 5; $i++) {
            
            $record->age = 1 + $i;
            $record->save(false);
        
            $expected_array = $record->attributes;
        
            echo "\nsleep 4 seconds\n"; sleep(4); 

            // check redis
            //
            $data = $this->waitForRedisHashUpdate($key, $table, $expected_array);

            if ($data != $expected_array) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
                return;
            }

            $this-> RedisOK();

            // get by key only
            //
            $api = '/v1/get/'.$key;
            $response = $this->createRequest()
                ->setMethod('get')
                ->setApi($api)
                ->send();

            if (!$response->isOk) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
                return;
            }

            $resp_key = $response->data['key'];
            if ($resp_key != $key) {
                $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
                return;
            }
            $data = $response->data['data'];

            if ($data != $expected_array) {
                $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
                return;
            }

            $this->HttpOK($response);
        }

        // stop updating
        $api = '/v1/get/'.$key.'/0/tb1?id=1';
        
        $response = $this->createRequest()
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $this->HttpOK($response);

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }
}
