<?php
/*
 * Copyright (c) 2017-2018, Sam Wen <sam underscore wen at yahoo dot com>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in the
 *     documentation and/or other materials provided with the distribution.
 *   * Neither the name of rdbcache nor the names of its contributors may be used
 *     to endorse or promote products derived from this software without
 *     specific prior written permission.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 */

namespace app\commands;

use Yii;
use app\components\TestController;

class TestSetController extends TestController
{

    public function actionIndex() {

        $start_id = $this->getTestStartAtId();

        $this->actionSimple();

        $this->actionSimpleWithJsonValue();

        $this->actionSimpleWithGeneratedId();

        $this->actionWithTable();

        $this->actionWithTableAndGeneratedId();

        $stop_id = $this->getTestStopAtId();
        $this->summary($start_id, $stop_id);
    }

    public function actionSimple() {

        $this->setup(__FUNCTION__);

        $key = 'my-key';
        $value = 'my-value';
        $expected_value = $value;
        $table = null;

        $api = '/v1/set/'.$key.'/'.$value;
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
            return;
        }

        $this->HttpOK($response);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, $expected_value);
        
        if ($data != $expected_value) {
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
        if ($data != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check database
        //
        $record = $this->dbWaitFind(['id' => $key, 'type' => 'data']);
        
        if ($record == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - $key");
            return;
        }
        if ($record != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect value");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionSimpleWithJsonValue() {

        $this->setup(__FUNCTION__);

        $key = 'my-json-key';
        $value = ['name'=> 'mike','age'=> 25, 'title'=>'engineer'];
        $expected_value = $value;
        $table = null;

        $api = '/v1/set/'.$key;
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($value)
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

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, $expected_value);
        
        if ($data != $expected_value) {
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
        if ($data != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check database
        //
        $record = $this->dbWaitFind(['id' => $key, 'type' => 'data']);
        
        if ($record == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - $key");
            return;
        }
        if ($record != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect value");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionSimpleWithGeneratedId() {

        $this->setup(__FUNCTION__);

        $key = '*';
        $value = 'my-value';
        $expected_value = $value;
        $table = null;

        $api = '/v1/set/'.$key.'/'.$value;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }
        
        $resp_key = $response->data['key'];
        if ($resp_key == $key) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect key");
            return;
        }

        $key = $resp_key;

        $this->HttpOK($response);

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, $expected_value);

        if ($data != $expected_value) {
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
        if ($data != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check database
        //
        $record = $this->dbWaitFind(['id' => $key, 'type' => 'data']);
        
        if ($record == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - $key");
            return;
        }
        if ($record != $expected_value) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect value");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTable()
    {

        $this->setup(__FUNCTION__);

        $key = 'my-key123';
        $value = ["name" => "mike", "age" => "20"];
        $expected_array = $value;
        $table = 'tb1';

        $api = '/v1/set/'.$key.'/'.$table;
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($value)
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

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, 'data');

        if (empty($data)) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not found");
            return;
        }
        if (empty($data['id'])) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, missing id in redis data ");
            return;
        }
        $id = $data['id'];
        unset($data['id']);
        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, incorrect redis data");
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

        if (empty($data['id'])) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data missing id");
            return;
        }

        unset($data['id']);

        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check database
        //
        $record = $this->dbWaitFind($id, 'tb1');

        if ($record == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Record not found");
            return;
        }

        unset($record['id']);

        if ($record != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Record data not match");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTableAndGeneratedId()
    {

        $this->setup(__FUNCTION__);

        $key = '*';
        $value = ["name" => "mike", "age" => "20"];
        $expected_array = $value;
        $table = 'tb1';

        $api = '/v1/set/'.$key.'/'.$table;
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($value)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $resp_key = $response->data['key'];

        $this->HttpOK($response);

        $key = $resp_key;

        // check redis
        //
        $data = $this->waitForRedisHashUpdate($key, $table, 'data');

        if (empty($data)) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not found");
            return;
        }
        if (empty($data['id'])) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, missing id in redis data ");
            return;
        }
        unset($data['id']);
        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed, incorrect redis data");
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

        if (empty($data['id'])) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data missing id");
            return;
        }
        $id = $data['id'];
        unset($data['id']);

        if ($data != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        // check database
        //
        $record = $this->dbWaitFind($id, 'tb1');

        if ($record == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Record not found");
            return;
        }

        unset($record['id']);

        if ($record != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Record data not match");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }
}