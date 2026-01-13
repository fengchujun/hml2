# 会员编号功能说明

## 功能概述

本次更新为会员系统增加了智能编号生成功能，编号由三部分组成：

**编号格式**：`{区号}{类型}{序号}`

- **区号**：基于手机号归属地的区号（去掉前面的0）
- **类型**：0=普通会员，8=特邀会员
- **序号**：按区号+类型分组自增，补齐到总长度至少8位

**示例**：
- 深圳普通会员第1个：`75500001`（755 + 0 + 0001）
- 北京普通会员第1个：`10000001`（10 + 0 + 00001）
- 深圳特邀会员第1个：`75580001`（755 + 8 + 0001）

## 安装步骤

### 1. 执行数据库迁移

执行以下SQL文件以创建所需的表和字段：

```bash
mysql -u用户名 -p密码 数据库名 < app/install/sql/member_code_upgrade.sql
```

该SQL文件会：
- 在 `ns_member` 表中添加 `area_code` 和 `member_type` 字段
- 创建 `ns_member_code_sequence` 表（编号自增序列）
- 创建 `ns_member_code_history` 表（编号变更历史）

### 2. 配置阿里云API（可选）

编辑 `/config/member.php` 文件，填入你的阿里云市场 AppCode：

```php
return [
    'mobile_location_appcode' => '你的AppCode',
];
```

**获取 AppCode 步骤**：
1. 访问阿里云市场：https://market.aliyun.com/
2. 搜索"手机号码归属地查询"
3. 购买服务（有免费额度）
4. 在"已购买的服务"中查看 AppCode

**注意**：如果不配置 AppCode，系统会使用默认区号 `86`

## 使用方式

### 新会员注册/添加

新会员在注册或后台添加时，如果提供了手机号，系统会自动生成编号：

```php
$member_model = new \app\model\member\Member();
$result = $member_model->addMember([
    'site_id' => 1,
    'mobile' => '13723407177',
    'nickname' => '张三',
    // ... 其他字段
]);
// 会员编号会自动生成并保存
```

### 会员升级为特邀会员

调用 `upgradeMember()` 方法升级会员：

```php
$member_model = new \app\model\member\Member();
$result = $member_model->upgradeMember($member_id, $site_id);

if ($result['code'] >= 0) {
    echo "升级成功！";
    echo "旧编号：" . $result['data']['old_member_code'];
    echo "新编号：" . $result['data']['new_member_code'];
}
```

### 查看编号变更历史

```php
$history_model = new \app\model\member\MemberCodeHistory();
$result = $history_model->getHistoryList($member_id);

foreach ($result['data'] as $history) {
    echo "变更时间：" . date('Y-m-d H:i:s', $history['create_time']);
    echo "旧编号：" . $history['old_member_code'];
    echo "新编号：" . $history['new_member_code'];
    echo "原因：" . $history['change_reason'];
}
```

## 文件变更清单

### 新增文件
- `/app/install/sql/member_code_upgrade.sql` - 数据库迁移文件
- `/app/model/member/MemberCodeSequence.php` - 序列管理模型
- `/app/model/member/MemberCodeHistory.php` - 历史记录模型
- `/config/member.php` - 会员配置文件

### 修改文件
- `/app/model/member/Member.php`
  - 新增 `getAreaCodeByMobile()` - 获取手机号归属地
  - 新增 `generateMemberCode()` - 生成会员编号
  - 新增 `upgradeMember()` - 升级会员
  - 修改 `addMember()` - 集成编号生成
  - 修改 `handleMember()` - 集成编号生成

## 技术特性

- **并发安全**：使用数据库行锁（FOR UPDATE）保证序号唯一性
- **降级友好**：API调用失败时自动使用默认区号86
- **历史追溯**：完整记录编号变更历史
- **多站点隔离**：序号按 site_id 独立自增
- **自动扩展**：当序号超过预留位数时，总长度自动增加

## 注意事项

1. **历史会员**：已有会员不会自动生成编号，只有在升级时才会按新规则生成
2. **手机号必需**：编号生成需要手机号，没有手机号的会员使用旧的随机编号
3. **区号缓存**：会员升级时会优先使用已保存的 `area_code`，避免重复调用API
4. **API超时**：归属地查询设置了5秒超时，失败不影响业务流程

## 编号示例

| 场景 | 区号 | 类型 | 序号 | 生成编号 | 总位数 |
|-----|------|------|------|---------|-------|
| 深圳普通会员#1 | 755 | 0 | 0001 | 75500001 | 8 |
| 深圳普通会员#9999 | 755 | 0 | 9999 | 75509999 | 8 |
| 深圳普通会员#10000 | 755 | 0 | 10000 | 755010000 | 9 |
| 北京普通会员#1 | 10 | 0 | 00001 | 10000001 | 8 |
| 深圳特邀会员#1 | 755 | 8 | 0001 | 75580001 | 8 |
| 无归属地普通会员#1 | 86 | 0 | 00001 | 86000001 | 8 |

## 常见问题

**Q: 如何批量为历史会员生成编号？**
A: 可以编写脚本调用 `generateMemberCode()` 方法批量处理。

**Q: API调用费用高吗？**
A: 阿里云市场提供免费额度，超出后按量计费，通常很便宜。

**Q: 可以自定义编号规则吗？**
A: 可以修改 `generateMemberCode()` 方法中的逻辑。

**Q: 会员降级怎么办？**
A: 可以参考 `upgradeMember()` 方法，编写类似的 `downgradeMember()` 方法。

## 技术支持

如有问题，请联系开发团队或查看代码注释。
