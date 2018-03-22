<?php
/**
 * @link http://rdbcache.com/
 * @copyright Copyright (c) 2017-2018 Sam Wen
 * @license http://rdbcache.com/license/
 */

namespace app\commands;

use Yii;
use app\components\TestController;

class TestPushController extends TestController
{
    public function actionIndex() {

        $start_id = $this->getTestStartAtId();

        $this->actionSimple();

        $this->actionWithTable();

        $this->actionMix();
        
        $stop_id = $this->getTestStopAtId();
        $this->summary($start_id, $stop_id);
    }

    public function actionSimple() {

        $this->setup(__FUNCTION__);

        $keys = ['id1', 'id2', 'id3'];
        $expected_arrays = [];
        
        // prepare
        //
        foreach ($keys as $key) {

            $api = '/rdbcache/v1/get/'.$key;
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

            $expected_arrays[$key] = $response->data['data'];
            $this->HttpOK($response);
        }

        $values_array = [
            'id1' => [ 'f' => 'new_value'],
            'id2' => [ 'f11' => 'new_f', 'f22' => 12 ],
            'id3' => [ 'fff' => 'aa', 'a' => 13 ],
        ];

        //push
        $api = '/rdbcache/v1/push/';
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

        $data = $response->data['data'];

        if ($data != array_keys($expected_arrays)) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTable() {

        $this->setup(__FUNCTION__);

        $values_array = [
            ['name' => 'new_name001', 'age' => 11],
            ['name' => 'new_name002', 'age' => 12 ],
            ['name' => 'new_name003', 'age' => 13 ],
        ];
        $table = 'tb1';

         //insert
        $api = '/rdbcache/v1/save/'.$table;
        $response = $this->createRequest()
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

        $values_array = [
            'push_aaa' => ['name' => 'new_name011', 'age' => 21],
            'push_bbb' => ['name' => 'new_name012', 'age' => 22 ],
            'push_ccc' => ['name' => 'new_name013', 'age' => 23 ],
        ];
        
         //push
        $api = '/rdbcache/v1/push/'.$table;
        $response = $this->createRequest()
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($values_array)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data = $response->data['data'];

        if ($data != array_keys($values_array)) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionMix() {

        $this->setup(__FUNCTION__);

        $keys = ['id2', 'id3'];

        // prepare data
        //
        foreach ($keys as $key) {

            $api = '/rdbcache/v1/get/'.$key;
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

            $this->HttpOK($response);
        }

        $ids = [2, 3];
        $table = 'tb1';

        // prepare
        //
        foreach ($ids as $id) {

            $key = 'my-hash-key-push-'.$id;
            array_push($keys, $key);

            $api = '/rdbcache/v1/get/'.$key.'/'.$table.'?id='.$id;
            $response = $this->createRequest()
                ->setMethod('get')
                ->setApi($api)
                ->send();

            if (!$response->isOk) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
                return;
            }

            $this->HttpOK($response);
        }

        $values_array = [
            'id1' => ['name' => 'new_name011', 'age' => 21],
            'id2' => ['name' => 'new_name012', 'age' => 22 ],
            'my-hash-key-2' => ['name' => 'new_name2222', 'age' => 22 ],
            'my-hash-key-3' => ['name' => 'new_name3333', 'age' => 33 ],
        ];

        //push
        $api = '/rdbcache/v1/push/';
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

        $data = $response->data['data'];

        if ($data != array_keys($values_array)) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);

    }
}
