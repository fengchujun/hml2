<template>
	<view :style="value.pageStyle">
		<view class="container" :style="warpCss">
			<!-- 会员信息 -->
			<view class="common-wrap info-wrap" :class="[`data-style-${value.style}`]">
				<view class="member-info" :style="memberInfoStyle">
					<view class="info-wrap" :style="infoStyle" v-if="memberInfo">
						<view class="headimg" @click="getWxAuth">
							<image :src="memberInfo.headimg ? $util.img(memberInfo.headimg) : $util.getDefaultImage().head" mode="widthFix" @error="memberInfo.headimg = $util.getDefaultImage().head" />
						</view>
						<view class="info">
							<!-- #ifdef MP -->
							<block v-if="(memberInfo.nickname.indexOf('u_') != -1 && memberInfo.nickname == memberInfo.username) || memberInfo.nickname == memberInfo.mobile">
								<view class="nickname">
									<text class="name" @click="getWxAuth">点击授权头像昵称</text>
								</view>
							</block>
							<view class="nickname" v-else>
								<text class="name" @click="getWxAuth">{{ memberInfo.nickname }}</text>
								<view class="member-level"
									v-if="(value.style == 1 || value.style == 2) && memberInfo.member_level"
									@click="redirectBeforeAuth(memberInfo.member_level_type ? '/pages_tool/member/card' : '/pages_tool/member/level')">
									<text class="icondiy icon-system-huangguan"></text>
									<text class="level-name">{{ memberInfo.member_level_name }}</text>
								</view>
							</view>
							<view class="member-level" v-if="value.style == 3 && memberInfo.member_level"
								@click="redirectBeforeAuth(memberInfo.member_level_type ? '/pages_tool/member/card' : '/pages_tool/member/level')">
								<image :src="$util.img('public/uniapp/member/supervip_icon.png')" mode="widthFix" class="level-icon" />
								<view class="level-name">{{ memberInfo.member_level_name }}</view>
							</view>
							<view class="member-level-wrapper" v-if="value.style == 4 && memberInfo.member_level">
								<view class="member-level"
									@click="redirectBeforeAuth(memberInfo.member_level_type ? '/pages_tool/member/card' : '/pages_tool/member/level')">
									<image :src="$util.img('app/component/view/member_info/img/style_4_vip_tag.png')" mode="widthFix" class="level-icon" />
									<text class="level-name">{{ memberInfo.member_level_name }}</text>
									<text class="arrow-down">︾</text>
								</view>
								<view class="member-code-display" v-if="memberInfo.member_code">
									<text class="code-text">卡号 NO.{{ memberInfo.member_code }}</text>
								</view>
							</view>
							<!-- #endif -->

							<!-- #ifdef H5 -->
							<block v-if="$util.isWeiXin() && ((memberInfo.nickname.indexOf('u_') != -1 && memberInfo.nickname == memberInfo.username) || memberInfo.nickname == memberInfo.mobile) ">
								<view class="nickname">
									<text class="name" @click="getWxAuth">点击获取微信头像</text>
								</view>
							</block>
							<view class="nickname" v-else>
								<text class="name">{{ memberInfo.nickname }}</text>
								<view class="member-level" v-if="(value.style == 1 || value.style == 2) && memberInfo.member_level"
									@click="redirectBeforeAuth(memberInfo.member_level_type ? '/pages_tool/member/card' : '/pages_tool/member/level')">
									<text class="icondiy icon-system-huangguan"></text>
									<text class="level-name">{{ memberInfo.member_level_name }}</text>
								</view>
							</view>
							<view class="member-level" v-if="value.style == 3 && memberInfo.member_level"
								@click="redirectBeforeAuth(memberInfo.member_level_type ? '/pages_tool/member/card' : '/pages_tool/member/level')">
								<image :src="$util.img('public/uniapp/member/supervip_icon.png')" mode="widthFix" class="level-icon" />
								<view class="level-name">{{ memberInfo.member_level_name }}</view>
							</view>
							<view class="member-level-wrapper" v-if="value.style == 4 && memberInfo.member_level">
								<view class="member-level"
									@click="redirectBeforeAuth(memberInfo.member_level_type ? '/pages_tool/member/card' : '/pages_tool/member/level')">
									<image :src="$util.img('app/component/view/member_info/img/style_4_vip_tag.png')" mode="widthFix" class="level-icon" />
									<text class="level-name">{{ memberInfo.member_level_name }}</text>
									<text class="arrow-down">︾</text>
								</view>
								<view class="member-code-display" v-if="memberInfo.member_code">
									<text class="code-text">卡号 NO.{{ memberInfo.member_code }}</text>
								</view>
							</view>
							<!-- #endif -->
						</view>
						<!-- <text class="iconfont icon-qrcode member-code" @click="showMemberQrcode"></text> -->
						<text class="iconfont icon-shezhi user-info" @click="$util.redirectTo('/pages_tool/member/info')"></text>
					</view>

					<view class="info-wrap" v-else :style="infoStyle" @click="redirect('/pages/member/index')">
						<view class="headimg">
							<image :src="$util.getDefaultImage().head" mode="widthFix"></image>
						</view>
						<view class="info">
							<view class="nickname"><text class="name">登录/注册</text></view>
							<view class="desc">点击登录 享受更多精彩信息</view>
						</view>
						<text class="iconfont icon-qrcode member-code"></text>
					</view>

					<view class="account-info" v-show="value.style == 1 || value.style == 3" :style="{ 'margin-left': parseInt(value.infoMargin) * 2 + 'rpx', 'margin-right': parseInt(value.infoMargin) * 2 + 'rpx' }">
						<view class="account-item" @click="redirect('/pages_tool/member/balance')">
							<view class="value price-font">{{ memberInfo ? (parseFloat(memberInfo.balance) + parseFloat(memberInfo.balance_money)).toFixed(2) : '--' }}</view>
							<view class="title">余额</view>
						</view>
						<view class="solid"></view>
						<view class="account-item" @click="redirect('/pages_tool/member/point')">
							<view class="value price-font">{{ memberInfo ? parseFloat(memberInfo.point) : '--' }}</view>
							<view class="title">积分</view>
						</view>
						<view class="solid"></view>
						<view class="account-item" @click="redirect('/pages_tool/member/coupon')">
							<view class="value price-font">{{ memberInfo && memberInfo.coupon_num != undefined ? memberInfo.coupon_num : '--' }}</view>
							<view class="title">优惠券</view>
						</view>
					</view>

					<view class="super-member" v-if="superMember && (value.style == 1 || value.style == 2 || value.style == 3)" :style="superMemberStyle">
						<block v-if="value.style == 1 || value.style == 2">
							<view class="super-info">
								<text class="icondiy icon-system-huangguan"></text>
								<text>超级会员</text>
							</view>
							<view class="super-text">
								<text class="see" v-if="memberInfo && memberInfo.member_level_type" @click="redirectBeforeAuth('/pages_tool/member/card')">查看特权</text>
								<text class="see" v-else @click="redirectBeforeAuth('/pages_tool/member/card_buy')">会员可享更多权益</text>
								<text class="iconfont icon-right"></text>
							</view>
						</block>
						<block v-if="value.style == 3">
							<view class="super-info">
								<image :src="$util.img('public/uniapp/member/open_member.png')" class="title" mode="heightFix" />
								<view class="desc">开通可享更多权益</view>
							</view>
							<view class="super-text">
								<text class="see" v-if="memberInfo && memberInfo.member_level_type" @click="redirectBeforeAuth('/pages_tool/member/card')">查看特权</text>
								<text class="see" v-else @click="redirectBeforeAuth('/pages_tool/member/card_buy')">立即开通</text>
							</view>
						</block>
					</view>

					<view class="member-info-style4" v-show="value.style == 4">
						<view class="super-member" v-if="superMember" :style="superMemberStyle">
							<view class="super-info" >
								<image :src="$util.img('app/component/view/member_info/img/style_4_vip_huangguan.png')" class="title" mode="widthFix" />
								<view  v-if="memberInfo" class="desc">您是尊贵的{{ memberInfo.member_level_name }}</view>
								<view  v-else class="desc">您还未登陆</view>
							</view>
							<!-- <view class="super-text" :class="{ 'more' : memberInfo && memberInfo.member_level_type }">
								<text class="see" v-if="memberInfo && memberInfo.member_level_type" @click="redirectBeforeAuth('/pages_tool/member/card')">查看更多权益</text>
								<text class="see" v-else @click="redirectBeforeAuth('/pages_tool/member/card_buy')">立即开通</text>
							</view> -->
						</view>

						<!-- 保级进度 - 仅特邀会员显示 -->
						<view class="preserve-section" v-if="memberInfo && memberInfo.member_type == 8 && preserveInfo">
							<view class="preserve-title">
								<text class="icon">🏆</text>
								<text>保级进度</text>
							</view>
							<view class="preserve-progress">
								<view class="progress-bar">
									<view class="progress-fill" :style="{width: preserveInfo.preserve_progress + '%'}"></view>
								</view>
								<view class="progress-text">
									<text>已消费：¥{{ preserveInfo.year_consumption }}</text>
									<text>目标：¥{{ preserveInfo.preserve_target }}</text>
								</view>
								<view class="progress-tip" v-if="preserveInfo.need_amount > 0">
									还需消费 <text class="highlight">¥{{ preserveInfo.need_amount }}</text> 即可保级
								</view>
								<view class="progress-tip success" v-else>
									恭喜！已达到保级标准
								</view>
							</view>
						</view>
						<view class="account-info" :style="{ 'margin-left': parseInt(value.infoMargin) * 2 + 'rpx', 'margin-right': parseInt(value.infoMargin) * 2 + 'rpx' }">
							<view class="account-item" @click="redirect('/pages_tool/member/balance')">
								<view class="value price-font">
									{{ memberInfo ? (parseFloat(memberInfo.balance) + parseFloat(memberInfo.balance_money)).toFixed(2) : '--' }}
								</view>
								<view class="title">余额</view>
							</view>
							<view class="solid"></view>
							<view class="account-item" @click="redirect('/pages_tool/member/point')">
								<view class="value price-font">{{ memberInfo ? parseFloat(memberInfo.point) : '--' }}</view>
								<view class="title">积分</view>
							</view>
							<view class="solid"></view>
							<view class="account-item" @click="redirect('/pages_tool/member/coupon')">
								<view class="value price-font">
									{{ memberInfo && memberInfo.coupon_num != undefined ? memberInfo.coupon_num : '--' }}
								</view>
								<view class="title">优惠券</view>
							</view>
						</view>
						<view class="style4-other">
							<view class="style4-btn-wrap">
								<view @click="redirect('/pages_tool/recharge/list')" class="recharge-btn">余额充值</view>
								<ns-contact>
									<view class="kefu-btn">专属顾问</view>
								</ns-contact>
							</view>
							<view class="code">
								<image @click="redirect('/pages_tool/store/payment_qrcode')" :src="$util.img('app/component/view/member_info/img/style_4_code.png')" mode="aspectFill" />
							</view>
						</view>
					</view>
				</view>

				<view class="account-info" v-show="value.style == 2" :style="{ 'margin-left': parseInt(value.infoMargin) * 2 + 'rpx', 'margin-right': parseInt(value.infoMargin) * 2 + 'rpx' }">
					<view class="account-item" @click="redirect('/pages_tool/member/balance')">
						<view class="value price-font">
							{{ memberInfo ? (parseFloat(memberInfo.balance) + parseFloat(memberInfo.balance_money)).toFixed(2) : '--' }}
						</view>
						<view class="title">余额</view>
					</view>
					<view class="solid"></view>
					<view class="account-item" @click="redirect('/pages_tool/member/point')">
						<view class="value price-font">{{ memberInfo ? parseFloat(memberInfo.point) : '--' }}</view>
						<view class="title">积分</view>
					</view>
					<view class="solid"></view>
					<view class="account-item" @click="redirect('/pages_tool/member/coupon')">
						<view class="value price-font">
							{{ memberInfo && memberInfo.coupon_num != undefined ? memberInfo.coupon_num : '--' }}
						</view>
						<view class="title">优惠券</view>
					</view>
				</view>
			</view>

			<!-- 会员码 -->
			<uni-popup ref="erWeiPopup" type="center">
				<view class="member-code-popup" v-if="memberCode">
					<view class="popup-top">
						<view class="popup-top-title">
							<view class="iconfont icon-erweima"></view>
							<view class="popup-top-title-txt">会员码</view>
						</view>
						<view class="popup-top-tiao">
							<image :src="$util.img(memberCode.barcode)" />
						</view>
						<view class="popup-top-code">{{ splitFn(memberCode.member_code) }}</view>
						<view class="popup-top-erwei">
							<image :src="$util.img(memberCode.qrcode)" />
						</view>
						<view class="popup-top-text">如遇到扫码失败请将屏幕调至最亮重新扫码</view>
					</view>
					<view class="popup-bottom">
						<text class="iconfont iconfont-delete icon-close-guanbi" @click="closeMemberQrcode"></text>
					</view>
				</view>
			</uni-popup>

			<!-- 完善会员资料 -->
			<view @touchmove.prevent.stop class="member-complete-info-popup">
				<uni-popup ref="completeInfoPopup" type="bottom" :maskClick="false">
					<view class="complete-info-wrap">
						<view class="head">
							<text class="title">获取您的昵称、头像</text>
							<text class="color-tip tips">获取用户头像、昵称完善个人资料，主要用于向用户提供具有辨识度的用户中心界面</text>
							<text class="iconfont icon-close color-tip" @click="cancelCompleteInfo"></text>
						</view>
						<!-- #ifdef MP-WEIXIN -->
						<view class="item-wrap">
							<text class="label">头像</text>
							<button open-type="chooseAvatar" @chooseavatar="onChooseAvatar">
								<image :src="avatarUrl ? avatarUrl : $util.getDefaultImage().head" @error="avatarUrl = $util.getDefaultImage().head" mode="aspectFill" />
								<text class="iconfont icon-right color-tip"></text>
							</button>
						</view>
						<view class="item-wrap">
							<text class="label">昵称</text>
							<input type="nickname" placeholder="请输入昵称" v-model="nickName" @blur="blurNickName" />
						</view>
						<!-- #endif -->
						<!-- #ifdef MP-ALIPAY -->
						<view class="item-wrap">
							<text class="label">头像</text>
							<button open-type="getAuthorize" scope="userInfo" @getAuthorize="aliappGetUserinfo" :plain="true" class="border-0">
								<image :src="avatarUrl ? avatarUrl : $util.getDefaultImage().head" @error="avatarUrl = $util.getDefaultImage().head" mode="aspectFill" />
								<text class="iconfont icon-right color-tip"></text>
							</button>
						</view>
						<view class="item-wrap">
							<text class="label">昵称</text>
							<input type="nickname" placeholder="请输入昵称" v-model="nickName" @blur="blurNickName" />
						</view>
						<!-- #endif -->
						<button type="default" class="save-btn" @click="saveCompleteInfo" :disabled="isDisabled">保存</button>
					</view>
				</uni-popup>
			</view>

			<ns-login ref="login"></ns-login>
		</view>
	</view>
</template>

<script>
	let menuButtonInfo = {};
	// 如果是小程序，获取右上角胶囊的尺寸信息，避免导航栏右侧内容与胶囊重叠(支付宝小程序非本API，尚未兼容)
	// #ifdef MP-WEIXIN || MP-BAIDU || MP-TOUTIAO || MP-QQ
	menuButtonInfo = uni.getMenuButtonBoundingClientRect();
	// #endif
	// 自定义会员中心——会员信息
	import nsContact from '@/components/ns-contact/ns-contact.vue';
	export default {
		name: 'diy-member-info',
		props: {
			value: {
				type: Object,
				default: () => {
					return {};
				}
			},
			global: {
				type: Object,
				default: () => {
					return {};
				}
			}
		},
		components: {
			nsContact
		},
		data() {
			return {
				info: null,
				superMember: null,
				memberCode: null,
				avatarUrl: '', // 头像预览
				headImg: '', // 头像保存
				nickName: '',
				completeInfoCallback: null,
				menuButtonInfo: menuButtonInfo,
				preserveInfo: null // 保级进度信息
			};
		},
		options: {
			styleIsolation: 'shared'
		},
		created() {
			this.init(false);
		},
		watch: {
			storeToken(nVal, oVal) {
				this.init();
			},
			// 组件刷新监听
			componentRefresh: function(nval) {
				this.init();
			}
		},
		computed: {
			memberInfoStyle() {
				let style = {},
					img = '',
					backSize = 'contain';
				if (this.global.navBarSwitch == false) {
					// #ifdef MP
					// this.menuButtonInfo.height + this.menuButtonInfo.top ==>是胶囊按钮到顶部的距离，因为后台有可能设置margin.top,所以要减去
					style['padding-top'] = this.menuButtonInfo.height + this.menuButtonInfo.top - this.value.margin.top + 'px';
					// #endif
				}
				if (this.value.style == 4) {
					img = this.$util.img('app/component/view/member_info/img/style_4_bg.png');
					backSize = 'cover';
				} else if (this.value.style != 3) {
					img = this.$util.img('public/static/img/diy_view/member_info_bg.png');
				}

				if (this.value.theme == 'default') {
					style.background = `url('${img}') no-repeat bottom / ${backSize}, var(--base-color)`;
				} else {
					style.background = `url('${img}') no-repeat bottom / ${backSize},linear-gradient(${this.value.gradientAngle}deg, ${this.value.bgColorStart} 0%, ${this.value.bgColorEnd} 100%)`;
				}

				return this.$util.objToStyle(style);
			},
			infoStyle() {
				let style = {};

				if (this.value.style == 4) {

					if (this.superMember) {
						style['padding-bottom'] = '276rpx';
					} else {
						style['padding-bottom'] = '166rpx';
					}
				}

				return this.$util.objToStyle(style);
			},
			superMemberStyle() {
				let style = {
					'margin-left': parseInt(this.value.infoMargin) * 2 + 'rpx ',
					'margin-right': parseInt(this.value.infoMargin) * 2 + 'rpx '
				};
				if (this.value.style == 3) {
					style.background = `#292f45 url(` + this.$util.img('public/uniapp/member/supervip_bg.png') + `) no-repeat bottom / 100% 100%`;
				} else if (this.value.style == 4) {
					style = {};
					style.background = `url(` + this.$util.img('app/component/view/member_info/img/super_vip_bg_4.png') + `) no-repeat bottom / contain`;
				} else {
					style.background = `url('` + this.$util.img('public/static/img/diy_view/super_member_bg.png') + `') no-repeat bottom / 100% 100%, linear-gradient(107deg, ` + this.themeStyle.super_member.super_member_start_bg + ` 0%, ` + this.themeStyle.super_member.super_member_end_bg + ` 100%)`;
				}
				return this.$util.objToStyle(style);
			},
			warpCss() {
				var obj = '';
				obj += 'background-color:' + this.value.componentBgColor + ';';
				if (this.value.componentAngle == 'round') {
					obj += 'border-top-left-radius:' + this.value.topAroundRadius * 2 + 'rpx;';
					obj += 'border-top-right-radius:' + this.value.topAroundRadius * 2 + 'rpx;';
					obj += 'border-bottom-left-radius:' + this.value.bottomAroundRadius * 2 + 'rpx;';
					obj += 'border-bottom-right-radius:' + this.value.bottomAroundRadius * 2 + 'rpx;';
				}
				return obj;
			},
			isDisabled() {
				if (this.nickName.length > 0) return false;
				return true;
			}
		},
		methods: {
			// isRefresh 是否刷新会员数据，true：刷新，false：不刷新
			init(isRefresh = true) {
				if (isRefresh) {
					if (this.storeToken) this.getMemberInfo();
					else this.$store.commit('setMemberInfo', '');
				} else if (this.memberInfo) {
					this.headImg = this.memberInfo.headimg;
					this.nickName = this.memberInfo.nickname;
					this.avatarUrl = this.headImg ? this.$util.img(this.headImg) : this.$util.getDefaultImage().head;

					/*
						小程序用户头像昵称获取规则调整公告
						https://developers.weixin.qq.com/community/develop/doc/00022c683e8a80b29bed2142b56c01
						用于处理昵称将统一返回 “微信用户”
					*/
					if (this.nickName == '微信用户') {
						this.openCompleteInfoPop();
					}
					this.getCouponNum();
				}
				this.getMemberCardInfo();
				// 加载保级进度（仅特邀会员）
				if (this.memberInfo && this.memberInfo.member_type == 8) {
					this.getPreserveInfo();
				}
			},
			/**
			 * 查询会员信息
			 */
			getMemberInfo() {
				this.$api.sendRequest({
					url: '/api/member/info',
					data: {},
					success: res => {
						if (res.code == 0) {
							this.info = res.data;
							if (this.info == null) {
								this.$store.commit('setToken', '');
								this.$store.commit('setMemberInfo', '');
								this.$store.dispatch('emptyCart');
								// uni.removeStorageSync('authInfo');
								return;
							}

							this.headImg = this.info.headimg;
							this.nickName = this.info.nickname;
							this.avatarUrl = this.headImg ? this.$util.img(this.headImg) : this.$util.getDefaultImage().head;

							/*
								小程序用户头像昵称获取规则调整公告
								https://developers.weixin.qq.com/community/develop/doc/00022c683e8a80b29bed2142b56c01
								用于处理昵称将统一返回 “微信用户”
							*/
							if (this.nickName == '微信用户') {
								this.openCompleteInfoPop();
							}
							this.getCouponNum();
						}
					}
				});
			},
			/**
			 * 查询优惠券数量
			 */
			getCouponNum() {
				this.$api.sendRequest({
					url: '/coupon/api/coupon/num',
					success: res => {
						if (res.code == 0) {
							if (this.info) {
								// 二次刷新数据
								this.info.coupon_num = res.data;
								this.$store.commit('setMemberInfo', this.info);
							} else {
								// 第一次赋值
								this.memberInfo.coupon_num = res.data;
								this.$forceUpdate();
								this.$store.commit('setMemberInfo', this.memberInfo);
							}
						}
					}
				});
			},
			/**
			 * 查询超级会员信息
			 */
			getMemberCardInfo() {
				this.$api.sendRequest({
					url: '/supermember/api/membercard/firstcard',
					success: res => {
						if (res.code == 0 && res.data) {
							this.superMember = res.data;
						}
					}
				});
			},
			/**
			 * 查询保级进度信息
			 */
			getPreserveInfo() {
				this.$api.sendRequest({
					url: '/api/membervip/getPromoteStats',
					success: res => {
						if (res.code >= 0 && res.data) {
							this.preserveInfo = res.data.preserve_info;
						}
					}
				});
			},
			/**
			 * 跳转
			 * @param {Object} url
			 */
			redirect(url) {
				if (!this.storeToken) {
					// this.$refs.login.open(url);
					if (url) this.$util.redirectTo('/pages_tool/login/index', {
						back: encodeURIComponent(url)
					});
					else this.$util.redirectTo('/pages_tool/login/index');
				} else {
					this.$util.redirectTo(url);
				}
			},
			/**
			 * 显示会员码
			 */
			showMemberQrcode() {
				if (!this.memberInfo.mobile && !this.memberInfo.member_code) {
					uni.showModal({
						title: '提示',
						content: '使用会员码需先绑定手机号，是否绑定手机号？',
						success: res => {
							if (res.confirm) {
								// #ifdef MP-WEIXIN
								this.$util.redirectTo('/pages_tool/member/info_edit', {
									action: 'bind_mobile'
								});
								// #endif
								// #ifndef MP-WEIXIN
								this.$util.redirectTo('/pages_tool/member/info_edit', {
									action: 'mobile'
								});
								// #endif
							}
						}
					});
					return;
				}
				if (this.memberCode) {
					this.$refs.erWeiPopup.open();
				}
				this.$api.sendRequest({
					url: '/api/member/membereqrcode',
					data: {
						page: ''
					},
					success: res => {
						if (res.code == 0) {
							let time = new Date().getTime();
							this.memberCode = {
								barcode: res.bar_code + '?rand=' + time,
								qrcode: res.data.path + '?rand=' + time,
								member_code: res.member_code
							};
							this.$refs.erWeiPopup.open();
						}
					}
				});
			},
			/**
			 * 关闭会员码
			 */
			closeMemberQrcode() {
				this.$refs.erWeiPopup.close();
			},
			/**
			 * 跳转之前需先进行授权
			 * @param {Object} url
			 */
			redirectBeforeAuth(url) {
				if (!this.storeToken) {
					this.$refs.login.open('/pages/member/index');
					return;
				}

				// #ifdef MP
				if ((this.memberInfo.nickname.indexOf('u_') != -1 && this.memberInfo.nickname == this.memberInfo.username) || this.memberInfo.nickname == this.memberInfo.mobile) {
					this.getWxAuth(() => {
						this.$util.redirectTo(url);
					});
				} else {
					this.$util.redirectTo(url);
				}
				// #endif

				// #ifdef H5
				if (this.$util.isWeiXin() && ((this.memberInfo.nickname.indexOf('u_') != -1 && this.memberInfo.nickname == this.memberInfo.username) || this.memberInfo.nickname == this.memberInfo.mobile)) {
					this.getWxAuth();
				} else {
					this.$util.redirectTo(url);
				}
				// #endif
			},
			/**
			 * 获取微信授权
			 */
			getWxAuth(callback) {
				// #ifdef MP
				this.openCompleteInfoPop(() => {
					typeof callback == 'function' && callback();
				});
				// #endif

				// #ifdef H5
				if (this.$util.isWeiXin()) {
					this.$api.sendRequest({
						url: '/wechat/api/wechat/authcode',
						data: {
							scopes: 'snsapi_userinfo',
							redirect_url: this.$config.h5Domain + '/pages/member/index'
						},
						success: res => {
							if (res.code >= 0) {
								location.href = res.data;
							}
						}
					});
				}
				// #endif
			},
			/**
			 * 修改昵称
			 * @param {Object} nickName
			 */
			modifyNickname(nickName) {
				this.$api.sendRequest({
					url: '/api/member/modifynickname',
					data: {
						nickname: nickName
					},
					success: res => {
						if (res.code == 0) {
							this.memberInfo.nickname = nickName;
							this.$store.commit('setMemberInfo', this.memberInfo);
						}
					}
				});
			},
			/**
			 * 修改头像
			 */
			modifyHeadimg(headimg) {
				this.$api.sendRequest({
					url: '/api/member/modifyheadimg',
					data: {
						headimg: headimg
					},
					success: res => {
						if (res.code == 0) {
							this.memberInfo.headimg = headimg;
							this.$store.commit('setMemberInfo', this.memberInfo);
						}
					}
				});
			},
			openCompleteInfoPop(callback) {
				this.$refs.completeInfoPopup.open(() => {
					this.$store.commit('setBottomNavHidden', false); //显示底部导航
				});
				this.$store.commit('setBottomNavHidden', true); // 隐藏底部导航
				this.completeInfoCallback = callback;
			},
			saveCompleteInfo() {
				if (this.nickName.length == 0) {
					this.$util.showToast({
						title: '请输入昵称'
					});
					return;
				}

				this.modifyNickname(this.nickName);
				this.modifyHeadimg(this.headImg);
				this.$refs.completeInfoPopup.close();
				this.$store.commit('setBottomNavHidden', false); // 显示底部导航
				typeof this.completeInfoCallback == 'function' && this.completeInfoCallback();
			},
			cancelCompleteInfo() {
				this.$refs.completeInfoPopup.close();
				this.$store.commit('setBottomNavHidden', false); // 显示底部导航
			},
			blurNickName(e) {
				if (e.detail.value) this.nickName = e.detail.value;
			},
			onChooseAvatar(e) {
				this.avatarUrl = e.detail.avatarUrl;
				uni.getFileSystemManager().readFile({
					filePath: this.avatarUrl, //选择图片返回的相对路径
					encoding: 'base64', //编码格式
					success: res => {
						let base64 = 'data:image/jpeg;base64,' + res.data; //不加上这串字符，在页面无法显示的哦

						this.$api.uploadBase64({
							base64,
							success: res => {
								if (res.code == 0) {
									this.headImg = res.data.pic_path;
								} else {
									this.$util.showToast({
										title: res.message
									});
								}
							},
							fail: () => {
								this.$util.showToast({
									title: '上传失败'
								});
							}
						})
					}
				});
			},
			splitFn(str) {
				return str.replace(/(?=(\d{4})+$)/g, ' ');
			},
			// #ifdef MP-ALIPAY
			aliappGetUserinfo() {
				my.getOpenUserInfo({
					success: (res) => {
						let userInfo = JSON.parse(res.response).response
						if (userInfo.code && userInfo.code == '10000') {
							if (userInfo.avatar) {
								this.avatarUrl = userInfo.avatar;
								this.$api.pullImage({
									path: this.avatarUrl,
									success: res => {
										if (res.code == 0) {
											this.headImg = res.data.pic_path;
										} else {
											this.$util.showToast({
												title: res.message
											});
										}
									},
									fail: () => {
										this.$util.showToast({
											title: '头像拉取失败'
										});
									}
								})
							}
							this.nickName = userInfo.nickName
						} else {
							this.$util.showToast({
								title: userInfo.subMsg
							})
						}
					},
					fail: (err) => {
						this.$util.showToast({
							title: err.subMsg
						})
					}
				});
			}
			// #endif
		}
	};
</script>
<style lang="scss">
	// 增加超出隐藏，是为样式四进行占位【样式四是有定位部分】，定位部分也可以设置颜色
	.container {
		overflow: hidden;
	}

	.common-wrap {
		width: 100%;
		box-sizing: border-box;
	}

	// 会员信息
	.member-info {
		.info-wrap {
			padding: 44rpx 30rpx;
			display: flex;
			align-items: center;

			view {
				color: var(--btn-text-color);
			}

			.info {
				flex: 1;
				width: 0;
				padding-right: 20rpx;
				overflow: hidden;
			}
		}

		.headimg {
			width: 120rpx;
			height: 120rpx;
			overflow: hidden;
			border-radius: 50%;
			margin-right: 20rpx;

			image {
				width: 100%;
			}
		}

		.nickname {
			font-weight: bold;
			white-space: nowrap;
			margin-bottom: 0;
			display: flex;
			align-items: center;
			flex-wrap: wrap;
		}

		.name {
			overflow: hidden;
			text-overflow: ellipsis;
			white-space: nowrap;
			font-size: 38rpx;
			font-weight: 600;
		}

		.mobile {
			font-size: 26rpx;
			line-height: 1.5;
		}

		.desc {
			font-size: 24rpx;
		}

		.member-code,
		.user-info {
			font-size: 40rpx;
			margin-left: 20rpx;
			line-height: 1;
			color: var(--btn-text-color);
		}

		.member-level {
			font-size: 24rpx;
			background: linear-gradient(107deg, #7c7878 0%, #201a18 100%);
			color: #f7c774 !important;
			line-height: 40rpx;
			height: 40rpx;
			border-radius: 4rpx;
			padding: 0 16rpx;
			margin-left: 20rpx;
			display: flex;
			align-items: center;

			.icondiy {
				line-height: 1;
				font-size: 24rpx;
				margin-right: 10rpx;
			}

			&.auth {
				margin-left: 0;
			}

			.level-name {
				max-width: 240rpx;
				xword-break: break-all;
				text-overflow: ellipsis;
				overflow: hidden;
				display: -webkit-box;
				-webkit-line-clamp: 1;
				-webkit-box-orient: vertical;
				white-space: break-spaces;
			}
		}

		.super-member {
			display: flex;
			align-items: center;
			justify-content: space-between;
			height: 120rpx;
			border-top-left-radius: 10rpx;
			border-top-right-radius: 10rpx;
			background: linear-gradient(107deg, #7c7878 0%, #201a18 100%);
			padding: 30rpx 40rpx;
			box-sizing: border-box;

			.icon-huangguan {
				margin-right: 10rpx;
				font-size: 40rpx;
			}

			.super-info {
				flex: 1;
				width: 0;
				font-size: 36rpx;
				color: var(--super-member-start-text-color);
				// background-image: linear-gradient(90deg, var(--super-member-start-text-color) 0%, var(--super-member-end-text-color) 100%);
				// -webkit-background-clip: text;
				// -webkit-text-fill-color: transparent;
				display: flex;
				align-items: center;

				.icondiy {
					margin-right: 10rpx;
				}
			}

			.see {
				line-height: 1;
				font-size: 30rpx;
			}

			.icon-right {
				font-size: 28rpx;
				margin-left: 10rpx;
			}
		}
	}

	.account-info {
		display: flex;
		padding: 40rpx 0;
		align-items: center;
		justify-content: center;

		view {
			color: #fff;
		}

		.solid {
			height: 70rpx;
			width: 2rpx;
			background: #fff;
			border-radius: 2rpx;
		}

		.account-item {
			flex: 1;
			text-align: center;

			.value {
				font-size: 34rpx;
				font-weight: bold !important;
				margin-bottom: 4rpx;
				line-height: 1.3;
			}

			.title {
				font-size: 26rpx;
			}
		}
	}

	.data-style-1 {
		.account-info {
			padding: 20rpx 0;
		}

		.super-member {
			height: 100rpx;
		}

		.super-text {
			background-image: linear-gradient(90deg, #ffdba6 0%, #ffebca 49%, #f7c774 100%);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			display: flex;
			align-items: center;
		}
	}

	.data-style-2 {
		.member-info {
			border-radius: 0 0 100% 100%/0 0 70rpx 70rpx;
			overflow: hidden;

			.member-level {
				background: linear-gradient(107deg, #fadcb5 0%, #f6bd74 100%);
				color: #8d4b16 !important;
			}
		}

		.account-info {
			background: #fff;
			margin: 20rpx 0 0 0;
			color: #333;
			border-radius: 18rpx;

			.solid {
				background: #f2f2f2;
			}

			.account-item {
				.value {
					color: #000000;
				}

				.title {
					color: #666666;
				}
			}
		}

		.super-member {
			color: #8d4b16;
			background: linear-gradient(107deg, #fadcb5 0%, #f6bd74 100%);
			border-top-left-radius: 18rpx;
			border-top-right-radius: 18rpx;

			.super-info {
				color: var(--super-member-start-text-color);
				// background-image: linear-gradient(90deg, var(--super-member-start-text-color) 0%, var(--super-member-end-text-color) 100%);
			}

			.see {
				color: var(--super-member-start-text-color);
			}

			.icon-right {
				color: var(--super-member-start-text-color);
			}
		}
	}

	.data-style-3 {
		.info-wrap view {
			color: #282c38;
		}

		.member-code,
		.user-info {
			color: #282c38;
			font-weight: bold;
		}

		.member-code {
			font-size: 36rpx;
		}

		.super-member {
			border-radius: 22rpx;

			.super-text {
				.see {
					width: 160rpx;
					height: 56rpx;
					line-height: 56rpx;
					background: #e3c377;
					border-radius: 56rpx;
					color: #77413e;
					text-align: center;
					font-weight: bold;
					font-size: 24rpx;
					display: block;
				}
			}

			.super-info {
				flex-direction: column;
				align-items: normal;

				.title {
					height: 36rpx;
					width: auto;
					margin-bottom: 14rpx;
				}

				.desc {
					color: #e3c377;
					line-height: 1;
				}
			}
		}

		.account-info {
			.solid {
				background: none;
			}
		}

		.account-item {
			.value {
				color: #282c38;
				font-size: 44rpx;
			}

			.title {
				color: #aab0ba;
			}
		}

		.member-level {
			background: none;
			padding: 0;
			margin: 0;
			height: auto;
			display: flex;
			align-items: center;

			.level-icon {
				width: 40rpx;
				height: auto;
				will-change: transform;
			}

			.level-name {
				height: 36rpx;
				line-height: 36rpx;
				padding: 0 20rpx;
				color: #8d4b16;
				background: #f8cf9a;
				font-size: 24rpx;
				margin-left: 10rpx;
				border-radius: 36rpx;
				border-bottom-left-radius: 0;
				border-top-left-radius: 40rpx;
				font-weight: bold;
			}
		}
	}

	.data-style-4 {
		position: relative;

		.info-wrap {
			padding-bottom: 276rpx;
			margin-bottom: 226rpx;

			view {
				color: #282c38;
			}

			.info {
				display: flex;
				flex-direction: column;
				align-items: baseline;

				.name,
				.desc {
					color: #fff;
				}
			}
		}

		.member-code,
		.user-info {
			color: #fff;
			font-weight: bold;
			font-size: 36rpx;
		}

		.member-level-wrapper {
			margin: 10rpx 0 0;
			display: flex;
			flex-direction: column;
			align-items: flex-start;

			.member-level {
				background: #474758;
				padding: 0;
				height: auto;
				border-radius: 10px;
				display: flex;
				align-items: center;

				.level-icon {
					width: 40rpx;
					height: auto;
					vertical-align: middle;
					will-change: transform;
				}

				.level-name {
					padding: 0 6rpx 0 6rpx;
					color: #ddc095;
					font-size: 24rpx;
				}

				.arrow-down {
					padding: 0 10rpx 0 0;
					color: #ddc095;
					font-size: 20rpx;
					opacity: 0.8;
				}
			}

			.member-code-display {
				margin-top: 8rpx;
				font-size: 22rpx;
				color: rgba(255, 255, 255, 0.8);

				.code-text {
					letter-spacing: 1rpx;
				}
			}
		}

		.member-info-style4 {
			position: absolute;
			bottom: -226rpx;
			left: 24rpx;
			right: 24rpx;
			padding: 30rpx;
			background-color: #fff;
			border-radius: 16rpx;

			.account-info {
				padding: 50rpx 0;

				.account-item {
					.value {
						color: #282c38;
						font-size: 38rpx;
					}

					.title {
						color: #666666;
						font-size: $font-size-tag;
					}
				}
			}

			.super-member {
				border-radius: 22rpx;
				height: 120rpx;
				line-height: 100rpx;
				padding: 20rpx;

				.super-info {
					display: flex;
					align-items: center;

					.title {
						width: 80rpx;
						height: auto;
						will-change: transform;
						margin-right: 20rpx;
					}

					.desc {
						font-size: 30rpx;
						color: #333;
						font-weight: bold;
					}
				}

				.super-text {
					display: flex;
					align-items: center;
					justify-content: center;
					background-color: #333;
					border-radius: 26rpx;
					width: 138rpx;
					height: 52rpx;

					&.more {
						width: 180rpx;
					}

					.see {
						color: #f6dcad;
						font-size: $font-size-goods-tag;
					}
				}
			}

			.style4-other {
				display: flex;
				justify-content: space-between;
				padding: 0 10rpx;

				.style4-btn-wrap {
					flex: 1;
					display: flex;

					.recharge-btn,
					.kefu-btn {
						margin: 0;
						width: 200rpx;
						height: 80rpx;
						line-height: 84rpx;
						border-radius: 44rpx;
						text-align: center;
						border: 2rpx solid transparent;
					}

					.recharge-btn {
						margin-right: 30rpx;
						background-color: $base-color;
						color: #fff;
					}

					.kefu-btn {
						color: $base-color;
						border-color: $base-color;
					}

					.contact-wrap {
						flex: 1;
					}
				}

				.code {
					width: 80rpx;
					height: 80rpx;
					padding: 20rpx;
					background-color: $base-color;
					border-radius: 50%;
					box-sizing: border-box;

					image {
						width: 40rpx;
						height: 40rpx;
					}
				}
			}

			/* 保级进度样式 */
			.preserve-section {
				background: rgba(207, 175, 112, 0.1);
				border: 1rpx solid #cfaf70;
				border-radius: 16rpx;
				padding: 20rpx;
				margin: 20rpx 24rpx;

				.preserve-title {
					display: flex;
					align-items: center;
					font-size: 28rpx;
					font-weight: bold;
					margin-bottom: 20rpx;
					color: #333;

					.icon {
						margin-right: 10rpx;
						font-size: 32rpx;
					}
				}

				.preserve-progress {
					.progress-bar {
						height: 16rpx;
						background: rgba(207, 175, 112, 0.2);
						border-radius: 8rpx;
						overflow: hidden;
						margin-bottom: 15rpx;

						.progress-fill {
							height: 100%;
							background: #cfaf70;
							border-radius: 8rpx;
							transition: width 0.3s;
						}
					}

					.progress-text {
						display: flex;
						justify-content: space-between;
						font-size: 24rpx;
						color: #666;
						margin-bottom: 10rpx;
					}

					.progress-tip {
						font-size: 24rpx;
						text-align: center;
						color: #666;
						margin-top: 10rpx;

						.highlight {
							color: #cfaf70;
							font-weight: bold;
						}

						&.success {
							color: #4ade80;
						}
					}
				}
			}
		}
	}

	.member-code-popup {
		width: 100%;
		min-height: 900rpx;
		background: none;

		.popup-top {
			padding: 40rpx;
			box-sizing: border-box;
			width: 100%;
			height: 800rpx;
			background: #ffffff;
			border-radius: 12rpx;

			.popup-top-title {
				display: flex;
				align-items: center;
				margin-bottom: 60rpx;

				.popup-top-title-txt {
					font-size: 30rpx;
					margin-left: 16rpx;
				}
			}

			.popup-top-tiao {
				width: 480rpx;
				height: 130rpx;
				overflow: hidden;
				margin: 0 auto 20rpx;

				image {
					width: 480rpx;
					height: 160rpx;
					max-height: unset !important;
				}
			}

			.popup-top-code {
				text-align: center;
				color: #000;
				font-size: 28rpx;
				margin: 0 auto 20rpx;
				line-height: 1;
			}

			.popup-top-tiaoJie {
				text-align: center;
				font-size: 28rpx;
				color: rgb(153, 153, 153);
				margin-bottom: 40rpx;
			}

			.popup-top-erwei {
				width: 254rpx;
				height: 254rpx;
				margin: 0 auto 20rpx;

				image {
					width: 100%;
					height: 100%;
				}
			}

			.popup-top-shauxin {
				width: 350rpx;
				height: 80rpx;
				border-radius: $border-radius;
				background: rgb(245, 249, 247);
				margin: 0 auto;
				font-size: 30rpx;
				text-align: center;
				display: flex;
				align-items: center;
				justify-content: center;

				.iconfont {
					margin-left: 20rpx;
				}
			}

			.popup-top-text {
				text-align: center;
				font-size: 18rpx;
				color: rgb(153, 153, 153);
				margin-top: 30rpx;
			}
		}

		.popup-bottom {
			display: flex;
			align-items: center;
			justify-content: center;
			background: none !important;

			.iconfont-delete {
				font-size: 60rpx;
				margin-top: 20rpx;
				color: white;
			}
		}
	}

	/deep/ .uni-popup__wrapper.uni-custom .uni-popup__wrapper-box {
		background: none !important;
	}

	/deep/ .member-info-style4 .uni-popup__wrapper.uni-custom .uni-popup__wrapper-box {
		background: #fff !important;
	}

	.member-code-popup .popup-top {
		height: auto;
	}

	.member-complete-info-popup {
		.complete-info-wrap {
			background: #fff;
			padding: 50rpx 40rpx 40rpx;
			padding-bottom: calc(40rpx + constant(safe-area-inset-bottom));
			padding-bottom: calc(40rpx + env(safe-area-inset-bottom));

			.head {
				position: relative;
				border-bottom: 2rpx solid $color-line;
				padding-bottom: 20rpx;

				.title {
					font-size: $font-size-toolbar;
					display: block;
				}

				.tips {
					font-size: $font-size-base;
					display: block;
				}

				.iconfont {
					position: absolute;
					right: 0;
					top: -30rpx;
					display: inline-block;
					width: 56rpx;
					height: 56rpx;
					line-height: 56rpx;
					text-align: right;
					font-size: $font-size-toolbar;
					font-weight: bold;
				}
			}

			.item-wrap {
				border-bottom: 2rpx solid $color-line;
				display: flex;
				align-items: center;
				padding: 20rpx 0;

				.label {
					font-size: $font-size-toolbar;
					margin-right: 40rpx;
				}

				button {
					background: transparent;
					margin: 0;
					padding: 0;
					border-radius: 0;
					flex: 1;
					text-align: left;
					display: flex;
					align-items: center;
					font-size: $font-size-toolbar;
					border: none;

					image {
						width: 100rpx;
						height: 100rpx;
						border-radius: 10rpx;
						overflow: hidden;
					}
				}

				.iconfont {
					flex: 1;
					text-align: right;
				}

				input {
					flex: 1;
					height: 80rpx;
					box-sizing: border-box;
					font-size: $font-size-toolbar;
				}
			}

			.save-btn {
				width: 280rpx;
				height: 90rpx;
				line-height: 90rpx;
				background-color: #07c160;
				color: #fff;
				margin: 40rpx auto 20rpx;
			}
		}
	}
</style>
<style scoped>
	.member-complete-info-popup /deep/ .uni-popup__wrapper.bottom,
	.member-complete-info-popup /deep/ .uni-popup__wrapper.bottom .uni-popup__wrapper-box {
		border-top-left-radius: 30rpx !important;
		border-top-right-radius: 30rpx !important;
	}
</style>