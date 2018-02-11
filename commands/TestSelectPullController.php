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

class TestSelectPullController extends TestController
{
    public function actionIndex() {

        $start_id = $this->getTestStartAtId();

        $this->actionDepartments();

        $this->actionEmployees();

        $this->actionSalaries();

        $this->actionTitles();

        $this->actionDeptEmp();

        $this->actionDeptManager();

        $stop_id = $this->getTestStopAtId();
        $this->summary($start_id, $stop_id);
    }

    public function actionDepartments() {

        $this->setup(__FUNCTION__);

        $this->empdb_init();

        $table = 'departments';
        $limit = 1024;

        // get expected from database
        //
        $expected_array = $this->dbFindAll($table, $limit);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $api = '/v1/select/'.$table.'?limit='.$limit;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data = $response->data['data'];
        if ( count($data) != count($expected_array)) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data count");
            return;
        }

        $i = 0;
        foreach ($data as $key => $value) {
            $expected = $expected_array[$i++];
            if ($expected != $value) {
                $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
                return;
            }
        }

        $this->HttpOK($response);

        $keys = array_keys($data);

        // check redis
        //
        foreach ($keys as $key) {

            $expected = $data[$key];

            $value = $this->waitForRedisHashUpdate($key, $table, $expected);

            if ($value != $expected) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
                return;
            }
        }

        $this-> RedisOK();

        // get by keys only
        //
        $api = '/v1/pull/';
        $response = $this->createRequest()
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($keys)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data2 = $response->data['data'];
        if ($data2 != $data) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionEmployees() {

        $this->setup(__FUNCTION__);

        $this->empdb_init();

        $table = 'employees';
        $limit = 1024;

        // get expected from database
        //
        $expected_array = $this->dbFindAll($table, $limit);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $api = '/v1/select/'.$table.'?limit='.$limit;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data = $response->data['data'];
        if ( count($data) != count($expected_array)) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data count");
            return;
        }

        $i = 0;
        foreach ($data as $key => $value) {
            $expected = $expected_array[$i++];
            if ($expected != $value) {
                $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
                return;
            }
        }

        $this->HttpOK($response);

        $keys = array_keys($data);

        // check redis
        //
        foreach ($keys as $key) {

            $expected = $data[$key];

            $value = $this->waitForRedisHashUpdate($key, $table, $expected);

            if ($value != $expected) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
                return;
            }
        }

        $this-> RedisOK();

        // get by keys only
        //
        $api = '/v1/pull/';
        $response = $this->createRequest()
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($keys)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data2 = $response->data['data'];
        if ($data2 != $data) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionSalaries() {

        $this->setup(__FUNCTION__);

        $this->empdb_init();

        $table = 'salaries';
        $limit = 1024;

        // get expected from database
        //
        $expected_array = $this->dbFindAll($table, $limit);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $api = '/v1/select/'.$table.'?limit='.$limit;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data = $response->data['data'];
        if ( count($data) != count($expected_array)) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data count");
            return;
        }

        $i = 0;
        foreach ($data as $key => $value) {
            $expected = $expected_array[$i++];
            if ($expected != $value) {
                $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
                return;
            }
        }

        $this->HttpOK($response);

        $keys = array_keys($data);

        // check redis
        //
        foreach ($keys as $key) {

            $expected = $data[$key];

            $value = $this->waitForRedisHashUpdate($key, $table, $expected);

            if ($value != $expected) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
                return;
            }
        }

        $this-> RedisOK();

        // get by keys only
        //
        $api = '/v1/pull/';
        $response = $this->createRequest()
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($keys)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data2 = $response->data['data'];
        if ($data2 != $data) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionTitles() {

        $this->setup(__FUNCTION__);

        $this->empdb_init();

        $table = 'titles';
        $limit = 1024;

        // get expected from database
        //
        $expected_array = $this->dbFindAll($table, $limit);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $api = '/v1/select/'.$table.'?limit='.$limit;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data = $response->data['data'];
        if ( count($data) != count($expected_array)) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data count");
            return;
        }

        $i = 0;
        foreach ($data as $key => $value) {
            $expected = $expected_array[$i++];
            if ($expected != $value) {
                $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
                return;
            }
        }

        $this->HttpOK($response);

        $keys = array_keys($data);

        // check redis
        //
        foreach ($keys as $key) {

            $expected = $data[$key];

            $value = $this->waitForRedisHashUpdate($key, $table, $expected);

            if ($value != $expected) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
                return;
            }
        }

        $this-> RedisOK();

        // get by keys only
        //
        $api = '/v1/pull/';
        $response = $this->createRequest()
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($keys)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data2 = $response->data['data'];
        if ($data2 != $data) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionDeptManager() {

        $this->setup(__FUNCTION__);

        $this->empdb_init();

        $table = 'dept_manager';
        $limit = 1024;

        // get expected from database
        //
        $expected_array = $this->dbFindAll($table, $limit);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $api = '/v1/select/'.$table.'?limit='.$limit;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data = $response->data['data'];
        if ( count($data) != count($expected_array)) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data count");
            return;
        }

        $i = 0;
        foreach ($data as $key => $value) {
            $expected = $expected_array[$i++];
            if ($expected != $value) {
                $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
                return;
            }
        }

        $this->HttpOK($response);

        $keys = array_keys($data);

        // check redis
        //
        foreach ($keys as $key) {

            $expected = $data[$key];

            $value = $this->waitForRedisHashUpdate($key, $table, $expected);

            if ($value != $expected) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
                return;
            }
        }

        $this-> RedisOK();

        // get by keys only
        //
        $api = '/v1/pull/';
        $response = $this->createRequest()
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($keys)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data2 = $response->data['data'];
        if ($data2 != $data) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionDeptEmp() {

        $this->setup(__FUNCTION__);

        $this->empdb_init();

        $table = 'dept_emp';
        $limit = 1024;

        // get expected from database
        //
        $expected_array = $this->dbFindAll($table, $limit);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $api = '/v1/select/'.$table.'?limit='.$limit;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data = $response->data['data'];
        if ( count($data) != count($expected_array)) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data count");
            return;
        }

        $i = 0;
        foreach ($data as $key => $value) {
            $expected = $expected_array[$i++];
            if ($expected != $value) {
                $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
                return;
            }
        }

        $this->HttpOK($response);

        $keys = array_keys($data);

        // check redis
        //
        foreach ($keys as $key) {

            $expected = $data[$key];

            $value = $this->waitForRedisHashUpdate($key, $table, $expected);

            if ($value != $expected) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed, redis data not match");
                return;
            }
        }

        $this-> RedisOK();

        // get by keys only
        //
        $api = '/v1/pull/';
        $response = $this->createRequest()
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($keys)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data2 = $response->data['data'];
        if ($data2 != $data) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }
}
