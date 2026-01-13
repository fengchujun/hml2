<?php
/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2019-2029 杭州牛之云科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * =========================================================
 */

namespace app\model\member;

use app\model\BaseModel;
use think\facade\Db;

/**
 * 会员编号变更历史管理
 */
class MemberCodeHistory extends BaseModel
{
    /**
     * 添加变更历史记录
     * @param array $data 变更数据
     * @return array
     */
    public function addHistory($data)
    {
        $data['create_time'] = time();
        $res = Db::name('member_code_history')->insert($data);
        return $this->success($res);
    }

    /**
     * 获取会员的变更历史
     * @param int $member_id 会员ID
     * @param string $field 字段
     * @param string $order 排序
     * @return array
     */
    public function getHistoryList($member_id, $field = '*', $order = 'create_time desc')
    {
        $list = Db::name('member_code_history')
            ->where('member_id', $member_id)
            ->field($field)
            ->order($order)
            ->select()
            ->toArray();

        return $this->success($list);
    }

    /**
     * 获取最近一次变更记录
     * @param int $member_id 会员ID
     * @param string $field 字段
     * @return array
     */
    public function getLastHistory($member_id, $field = '*')
    {
        $info = Db::name('member_code_history')
            ->where('member_id', $member_id)
            ->field($field)
            ->order('create_time desc')
            ->find();

        return $this->success($info);
    }
}
