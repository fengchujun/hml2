# 分销功能实现说明文档

## 📋 项目概述

本文档记录了茶叶商城小程序的分销功能实现详情，包括：
- 分销员权限管理
- 分销商品配置
- 优惠券自动发放
- 佣金结算
- 仓库管理

---

## ✅ 已完成的核心功能

### 1. 数据库迁移

**文件位置：** `/database_migration_distribution.sql`

**包含内容：**
- 会员表增加字段：`fx_level`（分销等级）、`warehouse_id`（仓库ID）
- 商品表增加9个分销配置字段（3个等级 × 3个配置）
- 订单表增加字段：`distributor_id`、`commission_amount`、`commission_settled`、`warehouse_id`
- 管理员表增加：`warehouse_id`
- 新建表：`hml_member_source_goods`（会员分销商品访问记录）
- 新建表：`hml_warehouse`（仓库表）

**执行方法：**
```bash
mysql -u用户名 -p数据库名 < database_migration_distribution.sql
```

---

### 2. 后端核心模型

#### 2.1 会员分销商品访问模型

**文件：** `/app/model/member/MemberSourceGoods.php`

**核心方法：**
- `getRecord()` - 获取会员对某商品的访问记录
- `checkPermission()` - 检查会员是否对商品有权限
- `createRecord()` - 创建商品访问记录
- `sendFirstCoupon()` - 发放首次优惠券
- `sendCompleteCoupon()` - 发放完成优惠券
- `checkCouponExpired()` - 检查优惠券是否过期

#### 2.2 仓库管理模型

**文件：** `/app/model/warehouse/Warehouse.php`

**核心方法：**
- `getWarehouseList()` - 获取仓库列表
- `getWarehouseInfo()` - 获取仓库详情
- `addWarehouse()` - 添加仓库
- `editWarehouse()` - 编辑仓库
- `deleteWarehouse()` - 删除仓库
- `updateStatus()` - 修改仓库状态
- `getAvailableWarehouses()` - 获取可用仓库列表

---

### 3. 订单权限控制

**文件：** `/app/model/order/OrderCreate.php`

**修改内容：**

#### 3.1 权限控制逻辑（304-319行）
```php
// brand_id=1 只有特邀会员(member_level=2)或分销员(member_level=6)可以购买
if ($v['brand_id'] == 1 && !in_array($member_level, [2, 6])) {
    // 检查是否通过分销链接访问过该商品（获得永久权限）
    $has_permission = model('member_source_goods')->checkPermission($this->member_id, $v['goods_id']);
    if (!$has_permission) {
        $this->setError(1, '商品【' . $v['goods_name'] . '】仅限特邀会员购买');
    }
}

// brand_id=2 普通会员不能购买（除非通过分销链接访问过）
if ($v['brand_id'] == 2 && $member_level == 1) {
    $has_permission = model('member_source_goods')->checkPermission($this->member_id, $v['goods_id']);
    if (!$has_permission) {
        $this->setError(1, '商品【' . $v['goods_name'] . '】已售罄');
    }
}
```

#### 3.2 佣金计算和仓库关联（新增方法）
```php
// 在create()方法中调用
$commission_data = $this->calculateDistributionCommission();
$order_insert_data['distributor_id'] = $commission_data['distributor_id'];
$order_insert_data['commission_amount'] = $commission_data['commission_amount'];
$order_insert_data['commission_settled'] = 0;
$order_insert_data['warehouse_id'] = $commission_data['warehouse_id'];
```

**佣金计算逻辑：**
- 遍历订单商品，查询是否通过分销员访问
- 根据分销员等级获取对应商品的佣金配置
- 佣金 = 单品佣金 × 数量
- 获取分销员的仓库ID并关联到订单

---

### 4. 分销链接访问API

**文件：** `/app/api/controller/Goods.php`

**新增方法：** `handleDistributorVisit()`

**功能：** 处理用户点击分销链接访问商品的逻辑

**业务流程：**
1. 验证分销员身份（member_level=6）
2. 检查访问者的推荐人
   - 如果已有推荐人且不是当前分销员 → 不操作
   - 如果没有推荐人 → 设置推荐人
   - 如果推荐人就是当前分销员 → 继续
3. 创建或更新商品访问记录
4. 发放首次优惠券（如果需要）

---

### 5. 注册逻辑修改

**文件：** `/app/model/member/Register.php`

**修改位置：** 第190行之后

**功能：** 注册时处理缓存的分销商品信息

```php
// 处理分销商品信息（从前端传入）
if (isset($data['fx_goods_info'])) {
    $fx_goods_info = is_string($data['fx_goods_info']) ? json_decode($data['fx_goods_info'], true) : $data['fx_goods_info'];
    if ($fx_goods_info && isset($fx_goods_info['goods_id']) && isset($fx_goods_info['distributor_id'])) {
        // 创建商品访问记录
        model('member_source_goods')->createRecordOnRegister(...);
        // 发放首次优惠券
        model('member_source_goods')->sendFirstCoupon(...);
    }
}
```

---

### 6. 订单完成优惠券发放

**文件：** `/app/event/order/OrderComplete.php`

**新增方法：** `sendDistributionCompleteCoupon()`

**触发时机：** 订单状态变更为"已完成"（order_status=10）

**功能：**
- 查询订单中的所有商品
- 检查每个商品是否有分销记录
- 如果有，发放对应等级的完成优惠券

---

### 7. 小程序前端修改

#### 7.1 商品详情页

**文件：** `/uniapp2/common/js/goods_detail_base.js`

**修改内容：**

**onLoad方法（56-85行）：**
```javascript
// 处理分销商品信息（模仿source_member的处理方式）
if (data.source_member && (this.skuId || data.sku_id)) {
    // 保存分销商品信息到本地缓存
    let goods_id = this.goodsId || data.goods_id || 0;
    uni.setStorageSync('fx_goods_info', {
        goods_id: goods_id,
        distributor_id: data.source_member,
        timestamp: Date.now()
    });

    // 如果已登录，调用后端处理分销商品访问
    if (this.storeToken && goods_id > 0) {
        this.handleDistributorGoodsVisit(data.source_member, goods_id);
    }
}
```

**新增方法：** `handleDistributorGoodsVisit()`
- 调用后端API `/api/goods/handleDistributorVisit`
- 处理优惠券发放结果

#### 7.2 注册页面

**文件：** `/uniapp2/pages_tool/login/register.vue`

**修改位置：** 第266行之后

```javascript
// 发送分销商品信息
if (uni.getStorageSync('fx_goods_info')) {
    data.fx_goods_info = JSON.stringify(uni.getStorageSync('fx_goods_info'));
}
```

---

### 8. 后台订单列表仓库过滤

**文件：** `/app/shop/controller/Order.php`

**修改位置：** lists()方法，第129行之后

**功能：** 管理员只能看到自己仓库的订单

```php
// 仓库过滤：如果管理员配置了仓库ID，只显示该仓库的订单
$admin_info = model('user')->getInfo([['uid', '=', $this->uid]], 'warehouse_id');
if ($admin_info && $admin_info['warehouse_id'] > 0) {
    $condition[] = [ 'a.warehouse_id', '=', $admin_info['warehouse_id'] ];
}
```

---

## 🔧 需要手动完成的后台管理功能

由于后台管理涉及前端页面开发，以下功能需要您手动实现：

### 1. 仓库管理页面（CRUD）

**建议位置：** `/app/shop/controller/Warehouse.php`

**需要实现的方法：**
```php
// 仓库列表
public function lists() {
    // 调用 model('warehouse')->getWarehouseList()
}

// 添加仓库
public function add() {
    // 调用 model('warehouse')->addWarehouse()
}

// 编辑仓库
public function edit() {
    // 调用 model('warehouse')->editWarehouse()
}

// 删除仓库
public function delete() {
    // 调用 model('warehouse')->deleteWarehouse()
}
```

**前端页面位置：** `/app/shop/view/warehouse/` （需要创建）

---

### 2. 商品编辑页面 - 分销配置

**文件：** `/app/shop/controller/Goods.php` 的 `edit()` 方法

**需要添加的字段：**

在商品编辑表单中增加"分销配置"Tab，包含以下字段：

| 分销等级 | 字段 | 说明 |
|---------|------|------|
| **1级分销** | `fx_level1_first_coupon` | 首次优惠券ID（下拉选择） |
| | `fx_level1_complete_coupon` | 完成优惠券ID（下拉选择） |
| | `fx_level1_commission` | 佣金金额（数字输入） |
| **2级分销** | `fx_level2_first_coupon` | 首次优惠券ID |
| | `fx_level2_complete_coupon` | 完成优惠券ID |
| | `fx_level2_commission` | 佣金金额 |
| **3级分销** | `fx_level3_first_coupon` | 首次优惠券ID |
| | `fx_level3_complete_coupon` | 完成优惠券ID |
| | `fx_level3_commission` | 佣金金额 |

**优惠券下拉列表数据源：**
```php
// 获取所有可用的优惠券类型
$coupon_list = model('coupon_type')->where([['status', '=', 1]])->select();
```

---

### 3. 佣金管理页面

**建议位置：** `/app/shop/controller/Commission.php` （需要创建）

**需要实现的功能：**

#### 3.1 佣金订单列表
```php
public function lists() {
    $condition = [
        ['commission_amount', '>', 0] // 只显示有佣金的订单
    ];

    // 可选筛选条件
    $distributor_id = input('distributor_id', 0); // 分销员ID
    $settled = input('settled', ''); // 结算状态

    if ($distributor_id) {
        $condition[] = ['distributor_id', '=', $distributor_id];
    }

    if ($settled !== '') {
        $condition[] = ['commission_settled', '=', $settled];
    }

    $list = model('order')->getPageList($condition, ...);
}
```

#### 3.2 批量标记已结算
```php
public function settle() {
    $order_ids = input('order_ids/a', []);

    model('order')->where([['order_id', 'in', $order_ids]])->update([
        'commission_settled' => 1
    ]);
}
```

**前端页面需要显示：**
- 订单号
- 分销员信息
- 商品信息
- 佣金金额
- 结算状态
- 操作按钮（标记已结算）

---

### 4. 会员管理页面 - 分销员配置

**文件：** `/app/shop/controller/Member.php` 的 `edit()` 方法

**需要添加的字段：**
- `fx_level`：分销等级（下拉选择：0/1/2/3）
- `warehouse_id`：所属仓库（下拉选择，数据来自仓库表）

**仓库下拉列表数据源：**
```php
$warehouse_list = model('warehouse')->getAvailableWarehouses($this->site_id);
```

---

### 5. 管理员管理页面 - 仓库配置

**文件：** `/app/shop/controller/User.php` （或管理员管理相关controller）

**需要添加的字段：**
- `warehouse_id`：管理员所属仓库（下拉选择）

**功能说明：**
- 配置了仓库ID的管理员只能看到该仓库的订单
- 未配置仓库ID的管理员可以看到所有订单

---

## 📊 完整业务流程图

### 流程1：用户点击分销链接

```
用户点击分销链接（URL带source_member参数）
    ↓
小程序onLoad接收参数
    ↓
缓存到uni.setStorageSync('fx_goods_info')
    ↓
【已登录】→ 调用API /api/goods/handleDistributorVisit
    ├─ 检查推荐人关系
    ├─ 创建/更新访问记录
    └─ 发放首次优惠券

【未登录】→ 注册后处理
    ├─ 读取缓存 fx_goods_info
    ├─ 创建访问记录
    └─ 发放首次优惠券
```

### 流程2：订单创建

```
用户下单
    ↓
OrderCreate::create()
    ├─ 权限检查（brand_id + member_source_goods表）
    ├─ 计算佣金（遍历商品，累加佣金）
    ├─ 获取分销员仓库ID
    └─ 写入订单表
        ├─ distributor_id
        ├─ commission_amount
        ├─ commission_settled = 0
        └─ warehouse_id
```

### 流程3：订单完成

```
订单状态变更为"已完成"（order_status=10）
    ↓
触发 OrderComplete事件
    ├─ 发放消费积分（原有逻辑）
    └─ 发放分销完成优惠券（新增）
        └─ 遍历订单商品
            └─ 查询member_source_goods表
                └─ 如果有记录，发放完成优惠券
```

---

## 🔍 测试建议

### 1. 权限测试
- [ ] member_level=6 可以购买brand_id=1的商品
- [ ] member_level=1 通过分销链接访问后可以购买brand_id=1的商品
- [ ] member_level=1 未通过分销链接不能购买brand_id=1的商品

### 2. 优惠券测试
- [ ] 首次点击分销链接，成功发放首次优惠券
- [ ] 优惠券过期后再次点击，重新发放
- [ ] 订单完成后，成功发放完成优惠券

### 3. 佣金测试
- [ ] 单商品订单，佣金计算正确
- [ ] 多商品订单，佣金累加正确
- [ ] 佣金 = 商品佣金 × 数量

### 4. 仓库测试
- [ ] 配置了仓库ID的管理员只能看到对应仓库的订单
- [ ] 未配置仓库ID的管理员可以看到所有订单
- [ ] 订单正确关联到分销员的仓库

---

## 📝 注意事项

1. **数据库迁移必须先执行**，否则会报字段不存在错误

2. **优惠券插件依赖**：确保系统已安装 `addon/coupon` 插件

3. **权限是永久的**：用户通过分销链接访问商品后，获得的权限是永久的

4. **佣金是固定金额**：不受优惠券、满减等影响

5. **仓库ID可以为0**：表示未分配仓库（普通订单或未配置的分销员订单）

6. **分销员必须是member_level=6**：其他等级不能作为分销员

---

## 🚀 部署步骤

1. **执行数据库迁移**
   ```bash
   mysql -u用户名 -p数据库名 < database_migration_distribution.sql
   ```

2. **清理缓存**
   ```bash
   rm -rf runtime/cache/*
   ```

3. **测试后端API**
   - 访问 `/api/goods/handleDistributorVisit` 测试分销链接访问
   - 创建测试订单，检查佣金计算

4. **小程序重新编译**
   - 使用微信开发者工具重新编译小程序
   - 测试分销链接分享功能

5. **完成后台管理页面**
   - 参照上述"需要手动完成的后台管理功能"部分
   - 创建仓库管理、商品分销配置、佣金管理页面

---

## 📞 技术支持

如有问题，请检查：
1. 数据库字段是否正确添加
2. 模型文件是否正确加载
3. 日志文件 `runtime/log/` 查看错误信息

---

**文档生成时间：** 2026-01-21
**实现版本：** v1.0
