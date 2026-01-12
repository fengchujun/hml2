<?php
/**
 * 会员编号生成模型
 */

namespace app\model\member;

use app\model\BaseModel;
use app\service\PhoneAreaService;
use think\facade\Db;

class MemberCode extends BaseModel
{
    /**
     * 生成会员编号
     * @param string $mobile 手机号
     * @param int $member_level 会员等级（1=普通会员，2=特邀会员）
     * @return string 会员编号
     */
    public function generateMemberCode($mobile, $member_level)
    {
        // 1. 获取区号
        $area_code = PhoneAreaService::getAreaCode($mobile);

        // 2. 获取等级标识
        $level_flag = $this->getLevelFlag($member_level);

        // 3. 获取并递增序号（使用事务+行锁保证并发安全）
        $seq = $this->getNextSequence($area_code, $level_flag);

        // 4. 组装编号
        $member_code = $this->formatMemberCode($area_code, $level_flag, $seq);

        return $member_code;
    }

    /**
     * 获取等级标识
     * @param int $member_level 会员等级
     * @return int 等级标识（0=普通会员，8=特邀会员）
     */
    private function getLevelFlag($member_level)
    {
        return ($member_level == 1) ? 0 : 8;
    }

    /**
     * 获取并递增序号（带行锁，保证并发安全）
     * @param string $area_code 区号
     * @param int $level_flag 等级标识
     * @return int 序号
     */
    private function getNextSequence($area_code, $level_flag)
    {
        Db::startTrans();
        try {
            // 使用 FOR UPDATE 行锁
            $record = Db::table('member_code_sequence')
                ->where('area_code', $area_code)
                ->where('level_flag', $level_flag)
                ->lock(true)
                ->find();

            $current_time = time();

            if (!$record) {
                // 首次创建记录
                $seq = 1;
                Db::table('member_code_sequence')->insert([
                    'area_code' => $area_code,
                    'level_flag' => $level_flag,
                    'current_seq' => 1,
                    'create_time' => $current_time,
                    'update_time' => $current_time
                ]);
            } else {
                // 递增序号
                $seq = $record['current_seq'] + 1;
                Db::table('member_code_sequence')
                    ->where('id', $record['id'])
                    ->update([
                        'current_seq' => $seq,
                        'update_time' => $current_time
                    ]);
            }

            Db::commit();
            return $seq;
        } catch (\Exception $e) {
            Db::rollback();
            throw $e;
        }
    }

    /**
     * 组装会员编号
     * @param string $area_code 区号
     * @param int $level_flag 等级标识
     * @param int $seq 序号
     * @return string 会员编号
     */
    private function formatMemberCode($area_code, $level_flag, $seq)
    {
        // 总位数至少8位
        // 后缀位数 = 8 - 区号位数 - 1
        $suffix_length = 8 - strlen($area_code) - 1;

        // 如果计算出的后缀位数小于1，则使用默认值1
        if ($suffix_length < 1) {
            $suffix_length = 1;
        }

        // 补0到指定位数
        $suffix = str_pad($seq, $suffix_length, '0', STR_PAD_LEFT);

        // 组装：区号 + 等级标识 + 后缀
        return $area_code . $level_flag . $suffix;
    }

    /**
     * 检查会员编号是否存在
     * @param string $member_code 会员编号
     * @return bool
     */
    public function checkMemberCodeExists($member_code)
    {
        $count = Db::table('member')
            ->where('member_code', $member_code)
            ->count();
        return $count > 0;
    }

    /**
     * 更新会员编号
     * @param int $member_id 会员ID
     * @param string $member_code 会员编号
     * @return bool
     */
    public function updateMemberCode($member_id, $member_code)
    {
        return Db::table('member')
            ->where('member_id', $member_id)
            ->update(['member_code' => $member_code]) !== false;
    }
}
