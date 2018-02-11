rdbcache Tests
--------------

rdbcache test suite is built on top of PHP Yii2 framework. It requires php 5.4.0+ and Yii2 framework.

Setup Environment Variables:
----------------------------

Please replace the values with the proper ones for your environment.

export RDBCACHE_SERVER=localhost

export RDBCACHE_PORT=8181

export REDIS_SERVER=localhost

export DATABASE_NAME=testdb

export DATABASE_SERVER=localhost

export DB_USER_NAME=dbuser

export DB_USER_PASS=rdbcache

Run Tests
---------

To run the whole test suite:

./rdbcache_test

A previous test result has been saved in file rdbcache-test-result.txt.

You also can run individual as followings:

./yii test-get

./yii test-get2

./yii test-set

./yii test-set2

./yii test-put

./yii test-getset

./yii test-minus-expire

./yii test-pull

./yii test-push

./yii test-delkey

./yii test-delall

./yii test-employees

./yii test-select

./yii test-select-pull

./yii test-insert

./yii test-insert-pull

