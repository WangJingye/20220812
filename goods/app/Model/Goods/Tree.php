<?php

namespace App\Model\Goods;

class Tree
{
    /**
     * 获取全部数据.
     *
     * @param string $type  tree获取树形结构 level获取层级结构
     * @param string $order 排序方式
     *
     * @return array 结构数据
     */
    public function getTreeData($data, $type = 'tree', $name = 'name', $child = 'id', $parent = 'pid')
    {
        // 获取树形或者结构数据
        if ('tree' == $type) {
            $data = \App\Lib\Data::tree($data, $name, $child, $parent);
        } elseif ($type = 'level') {
            $data = \App\Lib\Data::channelLevel($data, 0, '&nbsp;', $child, $parent);
        }

        return $data;
    }
}
