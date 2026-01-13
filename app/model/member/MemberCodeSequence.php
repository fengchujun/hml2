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
 * 会员编号自增序列管理
 */
class MemberCodeSequence extends BaseModel
{
    /**
     * 获取下一个序号（带事务和行锁）
     * @param int $site_id 站点ID
     * @param string $area_code 区号
     * @param int $member_type 会员类型
     * @return int 序号
     */
    public function getNextSeq($site_id, $area_code, $member_type)
    {
        Db::startTrans();
        try {
            // 使用行锁查询
            $seq = Db::name('member_code_sequence')
                ->where([
                    ['site_id', '=', $site_id],
                    ['area_code', '=', $area_code],
                    ['member_type', '=', $member_type]
                ])
                ->lock(true) // FOR UPDATE
                ->find();

            if (!$seq) {
                // 首次创建该组合的序列
                $current_seq = 1;
                Db::name('member_code_sequence')->insert([
                    'site_id' => $site_id,
                    'area_code' => $area_code,
                    'member_type' => $member_type,
                    'current_seq' => $current_seq,
                    'create_time' => time(),
                    'update_time' => time()
                ]);
            } else {
                // 自增序号
                $current_seq = $seq['current_seq'] + 1;
                Db::name('member_code_sequence')
                    ->where('id', $seq['id'])
                    ->update([
                        'current_seq' => $current_seq,
                        'update_time' => time()
                    ]);
            }

            Db::commit();
            return $current_seq;
        } catch (\Exception $e) {
            Db::rollback();
            throw $e;
        }
    }

    /**
     * 获取当前序号（不自增）
     * @param int $site_id 站点ID
     * @param string $area_code 区号
     * @param int $member_type 会员类型
     * @return int 当前序号
     */
    public function getCurrentSeq($site_id, $area_code, $member_type)
    {
        $seq = Db::name('member_code_sequence')
            ->where([
                ['site_id', '=', $site_id],
                ['area_code', '=', $area_code],
                ['member_type', '=', $member_type]
            ])
            ->value('current_seq');

        return $seq ?: 0;
    }
}
