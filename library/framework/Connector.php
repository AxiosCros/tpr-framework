<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    :  http://hanxv.cn
 * @datetime: 2018/6/7 11:30
 */

namespace tpr\framework;

use tpr\db\core\Query;
use tpr\db\Db;

/**
 * Class Db.
 *
 * @method Query  table(string $table)                                                                                                   static 指定数据表（含前缀）
 * @method Query  name(string $name)                                                                                                     static 指定数据表（不含前缀）
 * @method Query  where(mixed $field, string $op = null, mixed $condition = null)                                                        static 查询条件
 * @method Query  join(mixed $join, mixed $condition = null, string $type = 'INNER')                                                     static JOIN查询
 * @method Query  union(mixed $union, boolean $all = false)                                                                              static UNION查询
 * @method Query  limit(mixed $offset, integer $length = null)                                                                           static 查询LIMIT
 * @method Query  order(mixed $field, string $order = null)                                                                              static 查询ORDER
 * @method Query  cache(mixed $key = null, integer $expire = null)                                                                       static 设置查询缓存
 * @method mixed  value(string $field)                                                                                                   static 获取某个字段的值
 * @method array  column(string $field, string $key = '')                                                                                static 获取某个列的值
 * @method Query  view(mixed $join, mixed $field = null, mixed $on = null, string $type = 'INNER')                                       static 视图查询
 * @method mixed  find(mixed $data = null)                                                                                               static 查询单个记录
 * @method mixed  select(mixed $data = null)                                                                                             static 查询多个记录
 * @method int    insert(array $data, boolean $replace = false, boolean $getLastInsID = false, string $sequence = null)                  static 插入一条记录
 * @method int    insertGetId(array $data, boolean $replace = false, string $sequence = null)                                            static 插入一条记录并返回自增ID
 * @method int    insertAll(array $dataSet)                                                                                              static 插入多条记录
 * @method int    update(array $data)                                                                                                    static 更新记录
 * @method int    delete(mixed $data = null)                                                                                             static 删除记录
 * @method bool   chunk(integer $count, callable $callback, string $column = null)                                                       static 分块获取数据
 * @method mixed  query(string $sql, array $bind = [], boolean $master = false, bool $pdo = false)                                       static SQL查询
 * @method int    execute(string $sql, array $bind = [], boolean $fetch = false, boolean $getLastInsID = false, string $sequence = null) static SQL执行
 * @method mixed  transaction(callable $callback)                                                                                        static 执行数据库事务
 * @method void   startTrans()                                                                                                           static 启动事务
 * @method void   commit()                                                                                                               static 用于非自动提交状态下面的查询提交
 * @method void   rollback()                                                                                                             static 事务回滚
 * @method bool   batchQuery(array $sqlArray)                                                                                            static 批处理执行SQL语句
 * @method string quote(string $str)                                                                                                     static SQL指令安全过滤
 * @method string getLastInsID($sequence = null)                                                                                         static 获取最近插入的ID
 */
class Connector
{
    protected static $connect;

    protected static $instance;

    public static function __callStatic($method, $params)
    {
        if (null === self::$instance || !isset(self::$instance[self::$connect])) {
            self::$instance[self::$connect] = Db::model(self::$connect);
            if (null === self::$instance[self::$connect]) {
                $config                         = Config::get(self::$connect);
                self::$instance[self::$connect] = Db::connect($config, self::$connect);
            }
        }

        return \call_user_func_array([self::$instance[self::$connect], $method], $params);
    }
}
