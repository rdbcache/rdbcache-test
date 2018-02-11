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

class TestEmployeesController extends TestController
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
        $condition = ['dept_no' => 'd001'];

        // get expected from database
        //
        $expected_array = $this->dbFind($condition, $table);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $query = '';
        foreach ($condition as $key => $value) {
            if ($query != '') $query .= '&';
            $query .= urlencode($key) . '=' . urlencode($value);
        }

        $api = '/v1/get/*/'.$table.'?'.$query;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $key = $response->data['key'];

        if ($response->data['data'] != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
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

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionEmployees() {

        $this->setup(__FUNCTION__);

        $this->empdb_init();

        $table = 'employees';
        $condition = ['emp_no' => 10001];

        // get expected from database
        //
        $expected_array = $this->dbFind($condition, $table);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $query = '';
        foreach ($condition as $key => $value) {
            if ($query != '') $query .= '&';
            $query .= urlencode($key) . '=' . urlencode($value);
        }

        $api = '/v1/get/*/'.$table.'?'.$query;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $key = $response->data['key'];

        if ($response->data['data'] != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
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

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionSalaries() {

        $this->setup(__FUNCTION__);

        $this->empdb_init();

        $table = 'salaries';
        $condition = ['emp_no' => 10001, 'from_date' => '1986-06-26'];

        // get expected from database
        //
        $expected_array = $this->dbFind($condition, $table);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $query = '';
        foreach ($condition as $key => $value) {
            if ($query != '') $query .= '&';
            $query .= urlencode($key) . '=' . urlencode($value);
        }

        $api = '/v1/get/*/'.$table.'?'.$query;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $key = $response->data['key'];

        if ($response->data['data'] != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
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

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionTitles() {

        $this->setup(__FUNCTION__);

        $this->empdb_init();

        $table = 'titles';
        $condition = ['emp_no' => 10001, 'from_date' => '1986-06-26'];

        // get expected from database
        //
        $expected_array = $this->dbFind($condition, $table);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $query = '';
        foreach ($condition as $key => $value) {
            if ($query != '') $query .= '&';
            $query .= urlencode($key) . '=' . urlencode($value);
        }

        $api = '/v1/get/*/'.$table.'?'.$query;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $key = $response->data['key'];

        if ($response->data['data'] != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
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

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionDeptEmp() {

        $this->setup(__FUNCTION__);

        $this->empdb_init();

        $table = 'dept_emp';
        $condition = ['emp_no' => 10001, 'dept_no' => 'd005'];

        // get expected from database
        //
        $expected_array = $this->dbFind($condition, $table);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $query = '';
        foreach ($condition as $key => $value) {
            if ($query != '') $query .= '&';
            $query .= urlencode($key) . '=' . urlencode($value);
        }

        $api = '/v1/get/*/'.$table.'?'.$query;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $key = $response->data['key'];

        if ($response->data['data'] != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
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

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionDeptManager() {

        $this->setup(__FUNCTION__);

        $this->empdb_init();

        $table = 'dept_manager';
        $condition = ['emp_no' => 110039, 'dept_no' => 'd001'];

        // get expected from database
        //
        $expected_array = $this->dbFind($condition, $table);

        if ($expected_array == null) {
            $this->NotTestable("data not found in database");
            return;
        }

        $this->DatabaseOK();

        $query = '';
        foreach ($condition as $key => $value) {
            if ($query != '') $query .= '&';
            $query .= urlencode($key) . '=' . urlencode($value);
        }

        $api = '/v1/get/*/'.$table.'?'.$query;
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();
        
        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $key = $response->data['key'];

        if ($response->data['data'] != $expected_array) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
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

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }
}