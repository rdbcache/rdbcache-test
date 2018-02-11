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

class TestSelectController extends TestController
{
    public function actionIndex() {

        $start_id = $this->getTestStartAtId();

        $this->actionSimple();

        $this->actionWithTable();

        $this->actionWithTable1();

        $this->actionWithTable2();

        $this->actionWithTable3();

        $stop_id = $this->getTestStopAtId();
        $this->summary($start_id, $stop_id);
    }

    public function actionSimple() {

        $this->setup(__FUNCTION__);

         //select
        $api = '/v1/select?key=id1&key=id2&key=id3';
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data = $response->data['data'];

        $this->HttpOK($response);

        // check database
        //
        $condition = ['id' => ['id1', 'id2', 'id3']];
        $records = $this->dbFindAll(null, 256, $condition);

        if ($records == null || count($data) != count($records)) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - " . $keys[$i]);
            return;
        }

        for ($i = 0; $i < count($records); $i++) {
            $record = $records[$i];
            $key = $record['id'];
            $value = $record['value'];
            if (empty($data[$key])) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find data  - $key");
                return;
            }
            $dataValue = $data[$key];
            if ($value != $dataValue) {
                $this->failed(basename(__FILE__)."@".__LINE__.": data not match for $key");
                return;
            }
        }
        
        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTable() {

        $this->setup(__FUNCTION__);
        $table = 'tb1';

         //select
        $api = '/v1/select/'.$table.'?'.urlencode('id>=3');
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data = $response->data['data'];
        $values = array_values($data);
        
        $this->HttpOK($response);

        // check database
        //
        $condition = ['>=', 'id', 3];
        $records = $this->dbFindAll($table, 256, $condition);

        if ($records == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - " . $keys[$i]);
            return;
        }

        if ($records != $values) {
            $this->failed(basename(__FILE__)."@".__LINE__.": data not match");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTable1() {

        $this->setup(__FUNCTION__);

        $table = 'tb1';

         //select
        $api = '/v1/select/'.$table.'?id_GE_3';
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data = $response->data['data'];
        $values = array_values($data);

        $this->HttpOK($response);

        // check database
        //
        $condition = ['>=', 'id', 3];
        $records = $this->dbFindAll($table, 256, $condition);

        if ($records == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - " . $keys[$i]);
            return;
        }

        if ($records != $values) {
            $this->failed(basename(__FILE__)."@".__LINE__.": data not match");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTable2() {

        $this->setup(__FUNCTION__);

        $table = 'tb1';

         //select
        $api = '/v1/select/'.$table.'?id_LT_3';
        $response = $this->createRequest(true)
            ->setMethod('get')
            ->setApi($api)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data = $response->data['data'];
        $values = array_values($data);

        $this->HttpOK($response);

        // check database
        //
        $condition = ['<', 'id', 3];
        $records = $this->dbFindAll($table, 256, $condition);

        if ($records == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - " . $keys[$i]);
            return;
        }

        if ($records != $values) {
            $this->failed(basename(__FILE__)."@".__LINE__.": data not match");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTable3() {

        $this->setup(__FUNCTION__);

        $table = 'tb1';
        $keys = ["aaa", "bbb", "ccc"];
        
         //select
        $api = '/v1/select/'.$table.'?id_GT_0';
        $response = $this->createRequest(true)
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setMethod('post')
            ->setApi($api)
            ->setData($keys)
            ->send();

        if (!$response->isOk) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
            return;
        }

        $data = $response->data['data'];
        $values = array_values($data);

        $this->HttpOK($response);

        // check database
        //
        $condition = ['>', 'id', 0];
        $records = $this->dbFindAll($table, 3, $condition);

        if ($records == null) {
            $this->failed(basename(__FILE__)."@".__LINE__.": Failed to find record  - " . $keys[$i]);
            return;
        }

        if ($records != $values) {
            $this->failed(basename(__FILE__)."@".__LINE__.": data not match");
            return;
        }

        $this->DatabaseOK();

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }
}
