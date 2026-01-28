<?php
/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2019-2029 杭州牛之云科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * =========================================================
 */

namespace app\model\warehouse;

use app\model\BaseModel;
use think\facade\Db;

/**
 * 仓库管理
 */
class Warehouse extends BaseModel
{
    /**
     * 获取仓库列表
     * @param array $condition 查询条件
     * @param string $field 查询字段
     * @param string $order 排序
     * @param int $limit 分页大小
     * @return array
     */
    public function getWarehouseList($condition = [], $field = '*', $order = 'create_time desc', $limit = 0)
    {
        $list = $this->where($condition)->field($field)->order($order);
        if ($limit) {
            $list = $list->paginate([
                'list_rows' => $limit,
                'query' => request()->param()
            ]);
        } else {
            $list = $list->select();
        }
        return $list;
    }

    /**
     * 获取仓库详情
     * @param int $warehouse_id 仓库ID
     * @param string $field 查询字段
     * @return array|null
     */
    public function getWarehouseInfo($warehouse_id, $field = '*')
    {
        $info = $this->where([['warehouse_id', '=', $warehouse_id]])->field($field)->find();
        return $info ? $info->toArray() : null;
    }

    /**
     * 添加仓库
     * @param array $data 仓库数据
     * @return int|string
     */
    public function addWarehouse($data)
    {
        $data['create_time'] = time();
        $data['update_time'] = time();

        // 如果未传入site_id，从request中获取
        if (!isset($data['site_id'])) {
            $data['site_id'] = request()->siteId();
        }

        return $this->insertGetId($data);
    }

    /**
     * 编辑仓库
     * @param int $warehouse_id 仓库ID
     * @param array $data 更新数据
     * @return int
     */
    public function editWarehouse($warehouse_id, $data)
    {
        $data['update_time'] = time();

        return $this->where([['warehouse_id', '=', $warehouse_id]])->update($data);
    }

    /**
     * 删除仓库
     * @param int $warehouse_id 仓库ID
     * @return int
     */
    public function deleteWarehouse($warehouse_id)
    {
        // 检查是否有会员或管理员关联此仓库
        $member_count = model('member')->where([['warehouse_id', '=', $warehouse_id]])->count();
        if ($member_count > 0) {
            return $this->error('', '该仓库下有会员关联，无法删除');
        }

        $user_count = model('user')->where([['warehouse_id', '=', $warehouse_id]])->count();
        if ($user_count > 0) {
            return $this->error('', '该仓库下有管理员关联，无法删除');
        }

        return $this->where([['warehouse_id', '=', $warehouse_id]])->delete();
    }

    /**
     * 修改仓库状态
     * @param int $warehouse_id 仓库ID
     * @param int $status 状态 1启用 0禁用
     * @return int
     */
    public function updateStatus($warehouse_id, $status)
    {
        return $this->where([['warehouse_id', '=', $warehouse_id]])->update([
            'status' => $status,
            'update_time' => time()
        ]);
    }

    /**
     * 获取可用仓库列表（状态=1）
     * @param int $site_id 站点ID
     * @return array
     */
    public function getAvailableWarehouses($site_id = 0)
    {
        $condition = [['status', '=', 1]];

        if ($site_id) {
            $condition[] = ['site_id', '=', $site_id];
        }

        return $this->where($condition)
            ->field('warehouse_id, warehouse_name, warehouse_code')
            ->order('create_time desc')
            ->select()
            ->toArray();
    }
}
