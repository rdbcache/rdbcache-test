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

class TestPullController extends TestController
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
        $table = null;

        // prepare
        //
        foreach ($keys as $key) {

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

            $expected_arrays[$key] = $response->data['data'];

            $this->HttpOK($response);
        }
        echo "expected: ".json_encode($expected_arrays)."\n";

        //pull
        $api = '/v1/pull/';
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

        if ($data != $expected_arrays) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionWithTable() {

        $this->setup(__FUNCTION__);

        $ids = [1, 2, 3];
        $keys = [];
        $expected_arrays = [];
        $table = 'tb1';

        // get expected from database
        //
        foreach ($ids as $id) {

            $expected_array = $this->dbFind($id, $table);

            if ($expected_array == null) {
                $this->NotTestable("data not found in database");
                return;
            }

            $this->DatabaseOK();

            $key = 'my-hash-key-'.$id;
            array_push($keys, $key);
            $expected_arrays[$key] = $expected_array;

            $this->DatabaseOK();
        }

        // prepare
        //
        foreach ($ids as $id) {

            $key = 'my-hash-key-'.$id;
            
            $api = '/v1/get/'.$key.'/'.$table.'?id='.$id;
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

        //pull
        $api = '/v1/pull/';
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

        if ($data != $expected_arrays) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }

    public function actionMix() {

        $this->setup(__FUNCTION__);

        $keys = ['id1', 'id2'];
        $expected_arrays = [];

        // prepare
        //
        foreach ($keys as $key) {

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

            $expected_arrays[$key] = $response->data['data'];
            $this->HttpOK($response);
        }

        $ids = [2, 3];
        $table = 'tb1';

        // prepare
        //
        foreach ($ids as $id) {

            $key = 'my-hash-key-'.$id;
            array_push($keys, $key);

            $api = '/v1/get/'.$key.'/'.$table.'?id='.$id;
            $response = $this->createRequest()
                ->setMethod('get')
                ->setApi($api)
                ->send();

            if (!$response->isOk) {
                $this->failed(basename(__FILE__)."@".__LINE__.": Failed!  " . $response->data['message']);
                return;
            }

            $expected_arrays[$key] = $response->data['data'];

            $this->HttpOK($response);
        }

        //pull
        $api = '/v1/pull/';
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
        if ($data != $expected_arrays) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        if ($data != $expected_arrays) {
            $this->failed(basename(__FILE__)."@".__LINE__.": incorrect data");
            return;
        }

        $this->HttpOK($response);

        $this->VerifyOK(basename(__FILE__)."@".__LINE__);
    }
}
