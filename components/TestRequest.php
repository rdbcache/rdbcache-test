<?php
/**
 * @link http://rdbcache.com/
 * @copyright Copyright (c) 2017-2018 Sam Wen
 * @license http://rdbcache.com/license/
 */

namespace app\components;

use Yii;
use yii\httpclient\Request;

class TestRequest extends Request {

    protected $server;

    protected $server_port;
    
    public function init() {

        $this->server = Yii::$app->params['rdbcache_server'];
        $this->server_port = Yii::$app->params['rdbcache_port'];
    }

    public function setApi($api) {
        $this->setUrl('http://'.$this->server.':'.$this->server_port . $api);
        return $this;
    }
}