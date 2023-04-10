<?php

/**
 * User: JIWI001
 * Date: 2018/09/11
 * Time: 16:06.
 */

namespace App\Model\Common;

class RedisBase
{
    public $redis;
    public $conn;

    public function __construct()
    {
    }

    public function init($host, $port, $timeout, $auth, $db)
    {
        $this->redis = new \Redis();
        try {
            $this->conn = $this->redis->connect($host, $port, $timeout);
        } catch (\Exception $e) {
            $this->conn = false;
        }
        if (!$this->conn) {
            return false;
        }
        $this->redis->auth($auth);
        $this->_setDb($db);
    }

    /**
     * _setDb.
     *
     * @param string $db
     *
     * @return bool Result
     */
    public function _setDb($db)
    {
        $this->redis->select($db);
    }

    /**
     * _exists.
     *
     * @param string $key
     *
     * @return bool Result
     */
    public function _exists($key)
    {
        if ($this->conn) {
            if ($this->redis->exists($key)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _set.
     *
     * @param string $key
     * @param string $value
     *
     * @return bool Result
     */
    public function _set($key, $value, $expire = '')
    {
        if ($this->conn) {
            if ('' === $expire) {
                if ($this->redis->set($key, $value)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                if ($this->redis->set($key, $value, $expire)) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * _expire.
     *
     * @param string $key
     * @param string $value
     *
     * @return bool Result
     */
    public function _expire($key, $expire = 15 * 60)
    {
        if ($this->conn) {
            if ($this->redis->expire($key, $expire)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _rename.
     *
     * @param string $old
     * @param string $new
     *
     * @return bool Result
     */
    public function _rename($old, $new)
    {
        if ($this->conn) {
            if ($this->redis->rename($old, $new)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _incr.
     *
     * @param string $key
     *
     * @return bool Result
     */
    public function _incr($key)
    {
        if ($this->conn) {
            if ($this->redis->incr($key)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _incrBy.
     *
     * @param string $key
     * @param string $inc
     *
     * @return bool Result
     */
    public function _incrBy($key, $inc)
    {
        if ($this->conn) {
            if ($this->redis->incrby($key, $inc)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _get.
     *
     * @param string $key
     * @param string $value
     *
     * @return bool Result
     */
    public function _get($key)
    {
        if ($this->conn) {
            $re = $this->redis->get($key);
            if (false !== $re) {
                return $re;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _del.
     *
     * @param string $key
     *
     * @return bool Result
     */
    public function _del($key)
    {
        if ($this->conn) {
            if ($this->redis->del($key)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _zadd.
     *
     * @param string $key
     * @param string $score
     * @param string $field
     *
     * @return bool Result
     */
    public function _zadd($key, $score, $field)
    {
        if ($this->conn) {
            if ($this->redis->zadd($key, $score, $field)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _zscore.
     *
     * @param string $key
     * @param string $field
     *
     * @return bool Result
     */
    public function _zscore($key, $field)
    {
        if ($this->conn) {
            $score = $this->redis->zscore($key, $field);
            if (false !== $score) {
                return $score;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _zincrby.
     *
     * @param string $key
     * @param string $score
     * @param string $field
     *
     * @return bool Result
     */
    public function _zincrby($key, $score, $field)
    {
        if ($this->conn) {
            if ($this->redis->zincrby($key, $score, $field)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _zrange.
     *
     * @param string $key
     * @param string $start
     * @param string $end
     *
     * @return bool Result
     */
    public function _zrange($key, $start, $end, $score = false)
    {
        if ($this->conn) {
            $range = $this->redis->zrange($key, $start, $end, $score);
            if (false !== $range) {
                return $range;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _zrevrange.
     *
     * @param string $key
     * @param string $start
     * @param string $end
     *
     * @return bool Result
     */
    public function _zrevrange($key, $start, $end, $score = false)
    {
        if ($this->conn) {
            $range = $this->redis->zrevrange($key, $start, $end, $score);
            if (false !== $range) {
                return $range;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _sadd.
     *
     * @param string $key
     * @param string $score
     *
     * @return bool Result
     */
    public function _sadd($key, $member)
    {
        if ($this->conn) {
            if ($this->redis->sadd($key, $member)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _srem.
     *
     * @param string $key
     * @param string $score
     *
     * @return bool Result
     */
    public function _srem($key, $member)
    {
        if ($this->conn) {
            if ($this->redis->srem($key, $member)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _sismember.
     *
     * @param string $key
     * @param string $score
     *
     * @return bool Result
     */
    public function _sismember($key, $member)
    {
        if ($this->conn) {
            if ($this->redis->sismember($key, $member)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _smembers.
     *
     * @param string $key
     * @param string $score
     *
     * @return bool Result
     */
    public function _smembers($key)
    {
        if ($this->conn) {
            $sget = $this->redis->smembers($key);
            if (false !== $sget) {
                return $sget;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _hset.
     *
     * @param string $key
     * @param string $field
     * @param string $value
     *
     * @return bool Result
     */
    public function _hset($key, $field, $value)
    {
        if ($this->conn) {
            if ($this->redis->hset($key, $field, $value)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _hget.
     *
     * @param string $key
     * @param string $field
     *
     * @return bool Result
     */
    public function _hget($key, $field)
    {
        if ($this->conn) {
            $hget = $this->redis->hget($key, $field);
            if (false !== $hget) {
                return $hget;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _hdel.
     *
     * @param string $key
     * @param string $field
     *
     * @return bool Result
     */
    public function _hdel($key, $field)
    {
        if ($this->conn) {
            if ($this->redis->hdel($key, $field)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _hmset.
     *
     * @param string $key
     * @param array $data
     *
     * @return bool Result
     */
    public function _hmset($key, $data)
    {
        if ($this->conn) {
            if ($this->redis->hmset($key, $data)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _hmget.
     *
     * @param string $key
     * @param array $fields
     *
     * @return bool Result
     */
    public function _hmget($key, $fields)
    {
        if ($this->conn) {
            $hget = $this->redis->hmget($key, $fields);
            if (false !== $hget) {
                return $hget;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _hincrBy.
     *
     * @param string $key
     * @param string $inc
     *
     * @return bool Result
     */
    public function _hincrBy($key, $field, $inc)
    {
        if ($this->conn) {
            if ($this->redis->hincrby($key, $field, $inc)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _zrank.
     *
     * @param string $key
     * @param string $field
     *
     * @return bool Result
     */
    public function _zrank($key, $field)
    {
        if ($this->conn) {
            $rank = $this->redis->zrank($key, $field);
            if (false !== $rank) {
                return $rank;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _zrevrank.
     *
     * @param string $key
     * @param string $field
     *
     * @return bool Result
     */
    public function _zrevrank($key, $field)
    {
        if ($this->conn) {
            $rank = $this->redis->zrevrank($key, $field);
            if (false !== $rank) {
                return $rank;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _zcard.
     *
     * @param string $key
     *
     * @return bool Result
     */
    public function _zcard($key)
    {
        if ($this->conn) {
            if ($count = $this->redis->zcard($key)) {
                return $count;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * _zrem.
     *
     * @param string $key
     * @param string $field
     *
     * @return bool Result
     */
    public function _zrem($key, $field)
    {
        if ($this->conn) {
            if ($this->redis->zrem($key, $field)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
