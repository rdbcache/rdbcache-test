delete from rdbcache_kv_pair where type in ('data', 'info', 'query');

delete from tb1;

delete from tb2;

delete from tb3;


insert into rdbcache_kv_pair (id, type, value) values("id1", "data", "value1");
insert into rdbcache_kv_pair (id, type, value) values("id2", "data", "{\"f1\":\"v21\"}");
insert into rdbcache_kv_pair (id, type, value) values("id3", "data", "{\"f1\":\"v31\"}");
insert into rdbcache_kv_pair (id, type, value) values("id4", "data", "{\"f1\":\"v41\",\"f2\":\"v42\",\"f3\":null}");

insert into tb1 (id, name, age) values(1, "name11", 10);
insert into tb1 (id, name, age) values(2, "name12", 21);
insert into tb1 (id, name, age) values(3, "name13", 32);
insert into tb1 (id, name, age) values(4, "name14", null);
insert into tb1 (id, name, age) values(5, null, 22);

insert into tb2 (id, name, dob) values("id21", "name21", "1977-01-01");
insert into tb2 (id, name, dob) values("id22", "name22", "2010-03-19");
insert into tb2 (id, name, dob) values("id23", "name23", null);
insert into tb2 (id, name, dob) values("id24", null, "2017-01-01");
insert into tb2 (id, name, dob) values("id25", null, null);

insert into tb3 (name, dv, fv, iv, bv, t1v, t2v, t3v, t4v, t5v) values("name31", 3.3210, 3.4321, 321, true, "22:41:21", "2008-09-21", "2008-09-21 22:41:21", "2008-09-21 22:41:21", 2008);
insert into tb3 (name, dv, fv, iv, bv, t1v, t2v, t3v, t4v, t5v) values("name32", null, 4.4321, 4321, 0, "23:41:21", "2018-09-23", "2008-08-01 23:41:21", "2008-09-21 22:41:21", 2018);