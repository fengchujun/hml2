// 商品详情业务
import htmlParser from '@/common/js/html-parser';

export default {
	data() {
		return {
			skuId: 0,
			goodsId: 0,
			// 商品详情
			goodsSkuDetail: {
				goods_id: 0,
				goods_service: []
			},
			preview: 0, //是否开启预览，0：不开启，1：开启
			//评价
			contactData: {
				title: '',
				path: '',
				img: ''
			},

			shareQuery: '', // 分享参数
			shareUrl: '', // 分享链接

			source_member: 0, //分享人的id
			chatRoomParams: {}, // 联系客服参数
			isIphoneX: false, //判断手机是否是iphoneX以上
			whetherCollection: 0,
			posterParams: {}, //海报所需参数
			shareImg: '',
			navbarData: {
				title: '',
				topNavColor: "#ffffff",
				topNavBg: false,
				navBarSwitch: true, // 导航栏是否显示
				textNavColor: "#333333",
				moreLink: {
					name: ""
				},
				navStyle: 1,
				bgUrl: '',
				textImgPosLink: 'left'
			},
			goodsFormVal: [],
			fxCouponCalled: false // 标记是否已经调用过分销优惠券接口
		}
	},
	onLoad(data) {
		// #ifdef MP-ALIPAY
		let options = my.getLaunchOptionsSync();
		options.query && Object.assign(data, options.query);
		// #endif

		this.preview = data.preview || 0;
		this.isIphoneX = this.$util.uniappIsIPhoneX();

		if (data.source_member) {
			uni.setStorageSync('source_member', data.source_member);
			this.source_member = data.source_member;
		}
		//记录分享关系
		if (this.storeToken && uni.getStorageSync('source_member')) {
			this.$util.onSourceMember(uni.getStorageSync('source_member'));
		}

		// 处理分销商品信息（模仿source_member的处理方式）
		if (data.source_member) {
			// 尝试获取 goods_id，可能暂时为 0
			let goods_id = this.goodsId || data.goods_id || 0;

			// 先缓存分销信息，即使 goods_id 暂时为 0
			uni.setStorageSync('fx_goods_info', {
				goods_id: goods_id,
				distributor_id: data.source_member,
				timestamp: Date.now()
			});

			// 如果未登录，跳转到登录页，登录后自动返回当前页面
			if (!this.storeToken) {
				// 构建当前页面URL，包含所有参数
				let currentPage = getCurrentPages()[getCurrentPages().length - 1];
				let currentPath = '/' + currentPage.route;
				let queryParams = [];
				for (let key in data) {
					if (data.hasOwnProperty(key)) {
						queryParams.push(key + '=' + data[key]);
					}
				}
				let currentUrl = currentPath + (queryParams.length > 0 ? '?' + queryParams.join('&') : '');

				// 跳转到登录页，传递返回URL
				this.$util.redirectTo('/pages_tool/login/index', {
					back: encodeURIComponent(currentUrl)
				});
				return;
			}

			// 如果已登录且有 goods_id，立即调用后端处理分销商品访问
			if (this.storeToken && goods_id > 0) {
				this.handleDistributorGoodsVisit(data.source_member, goods_id);
				this.fxCouponCalled = true; // 标记已调用
			}
			// 如果 goods_id 为 0，会在 handleGoodsSkuData 中更新并调用 API
		}

		// 小程序扫码进入
		if (data.scene) {
			var sceneParams = decodeURIComponent(data.scene);
			sceneParams = sceneParams.split('&');
			if (sceneParams.length) {
				sceneParams.forEach(item => {
					if (item.indexOf('m') != -1) uni.setStorageSync('source_member', item.split('-')[1]);
					if (item.indexOf('is_test') != -1) uni.setStorageSync('is_test', 1);
				});
			}
		}
	},
	onShow() {
		// 检查是否有分销商品缓存，且用户已登录，且还没有调用过 API
		let fx_goods_info = uni.getStorageSync('fx_goods_info');
		if (fx_goods_info && fx_goods_info.distributor_id && this.storeToken && !this.fxCouponCalled) {
			// 如果商品详情已经加载
			if (this.goodsSkuDetail.goods_id > 0) {
				// 更新缓存的 goods_id（如果需要）
				if (fx_goods_info.goods_id == 0 || fx_goods_info.goods_id != this.goodsSkuDetail.goods_id) {
					fx_goods_info.goods_id = this.goodsSkuDetail.goods_id;
					uni.setStorageSync('fx_goods_info', fx_goods_info);
				}
				// 调用 API 获取优惠券
				this.handleDistributorGoodsVisit(fx_goods_info.distributor_id, this.goodsSkuDetail.goods_id);
				this.fxCouponCalled = true; // 标记已调用
			}
		}
	},
	methods: {
		detailChangeVal(data) {
			this.goodsFormVal = data;
		},
		// 处理商品详情数据
		handleGoodsSkuData() {
			this.navbarData.title = this.goodsSkuDetail.goods_name.length > 9 ? this.goodsSkuDetail.goods_name.substr(0, 9) + "..." : this.goodsSkuDetail.goods_name;
			this.$langConfig.title(this.navbarData.title);
			if (this.goodsSkuDetail.config) {
				this.navbarData.navBarSwitch = this.goodsSkuDetail.config.nav_bar_switch;
			}

			this.whetherCollection = this.goodsSkuDetail.is_collect; // 用户关注商品状态

			// 处理分销商品信息：如果缓存中 goods_id 为 0，现在更新它
			let fx_goods_info = uni.getStorageSync('fx_goods_info');
			if (fx_goods_info && fx_goods_info.distributor_id && this.goodsSkuDetail.goods_id && !this.fxCouponCalled) {
				// 如果缓存的 goods_id 是 0 或与当前不一致，更新它
				if (fx_goods_info.goods_id == 0 || fx_goods_info.goods_id != this.goodsSkuDetail.goods_id) {
					fx_goods_info.goods_id = this.goodsSkuDetail.goods_id;
					uni.setStorageSync('fx_goods_info', fx_goods_info);

					// 如果已登录，调用 API 处理分销访问
					if (this.storeToken) {
						this.handleDistributorGoodsVisit(fx_goods_info.distributor_id, this.goodsSkuDetail.goods_id);
						this.fxCouponCalled = true; // 标记已调用
					}
				}
			}

			this.modifyGoodsInfo();

			// 初始化商品详情视图数据
			if (this.$refs.goodsDetailView) this.$refs.goodsDetailView.init({
				sku_id: this.skuId,
				goods_id: this.goodsSkuDetail.goods_id,
				preview: this.preview,
				source_member: this.source_member,
				posterParams: this.posterParams,
				posterApi: this.posterApi,
				shareUrl: this.shareUrl,
				goodsRoute: this.goodsRoute,
				isVirtual: this.goodsSkuDetail.is_virtual,
				deliveryType: this.goodsSkuDetail.express_type,
				whetherCollection: this.goodsSkuDetail.is_collect,
				evaluateConfig: this.goodsSkuDetail.evaluate_config,
				evaluateList: this.goodsSkuDetail.evaluate_list,
				evaluateCount: this.goodsSkuDetail.evaluate_count,
				goods_class : this.goodsSkuDetail.goods_class,
				sale_store: this.goodsSkuDetail.sale_store
			});

			//媒体
			if (this.goodsSkuDetail.video_url) this.switchMedia = "video";

			if (!Array.isArray(this.goodsSkuDetail.sku_images)) {
				if (this.goodsSkuDetail.sku_images) this.goodsSkuDetail.sku_images = this.goodsSkuDetail.sku_images.split(",");
				else this.goodsSkuDetail.sku_images = [];
			}

			// 多规格时合并主图
			if (this.goodsSkuDetail.goods_spec_format && this.goodsSkuDetail.goods_image) {

				if (!Array.isArray(this.goodsSkuDetail.goods_image)) this.goodsSkuDetail.goods_image = this.goodsSkuDetail.goods_image.split(",");

				this.goodsSkuDetail.sku_images = this.goodsSkuDetail.goods_image.concat(this.goodsSkuDetail.sku_images);
			}

			let maxHeight = '';
			let systemInfo = uni.getSystemInfoSync();
			this.goodsSkuDetail.goods_image_list.forEach((item, index) => {
				if (typeof item.pic_spec == "string")
					item.pic_spec = item.pic_spec.split('*');

				let ratio = item.pic_spec[0] / systemInfo.windowWidth;
				item.pic_spec[0] = item.pic_spec[0] / ratio;
				item.pic_spec[1] = item.pic_spec[1] / ratio;

				if (!maxHeight || maxHeight > item.pic_spec[1]) {
					maxHeight = item.pic_spec[1];
				}
			});
			this.goodsSkuDetail.swiperHeight = maxHeight + 'px';

			this.goodsSkuDetail.unit = this.goodsSkuDetail.unit || "件";

			// 当前商品SKU规格
			if (this.goodsSkuDetail.sku_spec_format) this.goodsSkuDetail.sku_spec_format = JSON.parse(this.goodsSkuDetail.sku_spec_format);

			// 商品属性
			if (this.goodsSkuDetail.goods_attr_format) {
				let goods_attr_format = JSON.parse(this.goodsSkuDetail.goods_attr_format);
				this.goodsSkuDetail.goods_attr_format = this.$util.unique(goods_attr_format, "attr_id");
				for (var i = 0; i < this.goodsSkuDetail.goods_attr_format.length; i++) {
					for (var j = 0; j < goods_attr_format.length; j++) {
						if (this.goodsSkuDetail.goods_attr_format[i].attr_id == goods_attr_format[j].attr_id && this.goodsSkuDetail.goods_attr_format[i].attr_value_id != goods_attr_format[j].attr_value_id) {
							this.goodsSkuDetail.goods_attr_format[i].attr_value_name += "、" + goods_attr_format[j].attr_value_name;
						}
					}
				}
			}

			// 商品SKU格式
			if (this.goodsSkuDetail.goods_spec_format) this.goodsSkuDetail.goods_spec_format = JSON.parse(this.goodsSkuDetail.goods_spec_format);

			// 商品详情
			// if (this.goodsSkuDetail.goods_content) this.goodsSkuDetail.goods_content = htmlParser(this.goodsSkuDetail.goods_content);

			//商品服务
			if (this.goodsSkuDetail.goods_service) {
				for (let i in this.goodsSkuDetail.goods_service) {
					this.goodsSkuDetail.goods_service[i]['icon'] = this.goodsSkuDetail.goods_service[i]['icon'] ? JSON.parse(this.goodsSkuDetail.goods_service[i]['icon']) : '';
				}
			}

			this.contactData = {
				title: this.goodsSkuDetail.sku_name,
				path: this.shareUrl,
				img: this.$util.img(this.goodsSkuDetail.sku_image, {
					size: 'big'
				})
			};
			if (this.$refs.goodsPromotion) this.$refs.goodsPromotion.refresh(this.goodsSkuDetail.goods_promotion);

			this.setPublicShare();
			// if (this.goodsRoute != '/pages/goods/detail') this.setPublicShare();

			this.getBarrageData();
			if (this.addonIsExist.form) {
				this.getGoodsForm();
			}
		},
		/**
		 * 刷新商品详情数据
		 * @param {Object} data
		 */
		refreshGoodsSkuDetail(data) {
			this.goodsSkuDetail = Object.assign({}, this.goodsSkuDetail, data);
			if (this.$refs.goodsPromotion) this.$refs.goodsPromotion.refresh(this.goodsSkuDetail.goods_promotion);
			if (this.$refs.goodsDetailView) {

				// 初始化商品详情视图数据
				this.goodsSkuDetail.unit = this.goodsSkuDetail.unit || "件";

				// 解决轮播图数量不一致时，切换到第一个
				if (this.swiperCurrent > this.goodsSkuDetail.sku_images.length) {
					this.swiperAutoplay = true;
					this.swiperCurrent = 1;
					setTimeout(() => {
						this.swiperAutoplay = false;
					}, 40);
				}

			}
			this.navbarData.title = this.goodsSkuDetail.goods_name.length > 9 ? this.goodsSkuDetail.goods_name.substr(0, 9) + "..." : this.goodsSkuDetail.goods_name;
			this.$langConfig.title(this.navbarData.title);

			if (this.goodsSkuDetail.membercard) {
				this.membercard = this.goodsSkuDetail.membercard;
			}
		},
		goodsDetailViewInit() {
			// 初始化商品详情视图数据
			if (this.$refs.goodsDetailView) this.$refs.goodsDetailView.init({
				sku_id: this.skuId,
				goods_id: this.goodsSkuDetail.goods_id,
				preview: this.preview,
				source_member: this.source_member,
				posterParams: this.posterParams,
				posterApi: this.posterApi,
				shareUrl: this.shareUrl,
				goodsRoute: this.goodsRoute,
				isVirtual: this.goodsSkuDetail.is_virtual,
				deliveryType: this.goodsSkuDetail.express_type,
				whetherCollection: this.goodsSkuDetail.is_collect,
				evaluateConfig: this.goodsSkuDetail.evaluate_config,
				evaluateList: this.goodsSkuDetail.evaluate_list,
				evaluateCount: this.goodsSkuDetail.evaluate_count
			});
		},
		goHome() {
			if (this.preview) return; // 开启预览，禁止任何操作和跳转
			this.$util.redirectTo('/pages/index/index');
		},
		goCart() {
			if (this.preview) return; // 开启预览，禁止任何操作和跳转
			this.$util.redirectTo('/pages/goods/cart');
		},
		//-------------------------------------关注-------------------------------------
		//更新商品信息
		modifyGoodsInfo() {
			if (this.preview) return; // 开启预览，禁止任何操作和跳转
			//更新商品点击量
			this.$api.sendRequest({
				url: "/api/goods/modifyclicks",
				data: {
					sku_id: this.skuId
				},
				success: res => {
				}
			});

			//添加足迹
			this.$api.sendRequest({
				url: "/api/goodsbrowse/add",
				data: {
					goods_id: this.goodsSkuDetail.goods_id,
					sku_id: this.skuId
				},
				success: res => {
				}
			});
		},
		//-------------------------------------关注-------------------------------------
		async editCollection() {
			if (this.$refs.goodsDetailView) {
				this.whetherCollection = await this.$refs.goodsDetailView.collection();
			}
		},
		openSharePopup() {
			if (this.$refs.goodsDetailView) {
				this.$refs.goodsDetailView.openSharePopup();
			}
		},
		//弹幕
		getBarrageData() {
			this.$api.sendRequest({
				url: '/api/goods/goodsbarrage',
				data: {
					goods_id: this.goodsSkuDetail.goods_id
				},
				success: res => {
					if (res.code == 0 && res.data) {
						let barrageData = [];
						for (let i in res.data.list) {
							if (res.data.list[i]['title']) {
								let title = res.data.list[i]['title'].substr(0, 1) + '*' + res.data.list[i]['title'].substr(res.data.list[i]['title'].length - 1, 1)
								barrageData.push({
									img: res.data.list[i]['img'] ? res.data.list[i]['img'] : this.$util.getDefaultImage().head,
									title: title + '已下单'
								});
							}
						}
						this.goodsSkuDetail.barrageData = barrageData;
					}
				}
			});
		},
		/**
		 * 设置公众号分享
		 */
		setPublicShare() {
			let shareUrl = this.$config.h5Domain + this.shareUrl;
			if (this.memberInfo && this.memberInfo.member_id) shareUrl += '&source_member=' + this.memberInfo.member_id;
			var store_info = this.$store.state.globalStoreInfo;
			if (store_info) shareUrl+= '&store_id=' + store_info.store_id;
			this.$util.setPublicShare({
				title: this.goodsSkuDetail.goods_name,
				desc: '',
				link: shareUrl,
				imgUrl: typeof this.goodsSkuDetail.goods_image == 'object' ? this.goodsSkuDetail.goods_image[0] : this.goodsSkuDetail.goods_image.split(',')[0]
			})
		},
		/**
		 * 获取商品表单
		 */
		getGoodsForm() {
			this.$api.sendRequest({
				url: "/form/api/form/goodsform",
				data: {
					goods_id: this.goodsSkuDetail.goods_id
				},
				success: res => {
					if (res.code == 0 && res.data) this.$set(this.goodsSkuDetail, 'goods_form', res.data);
				}
			});
		},
		/**
		 * 处理分销商品访问（用户点击分销链接访问商品时调用）
		 */
		handleDistributorGoodsVisit(distributor_id, goods_id) {
			this.$api.sendRequest({
				url: '/api/goods/handleDistributorVisit',
				data: {
					distributor_id: distributor_id,
					goods_id: goods_id
				},
				success: res => {
					// 处理成功，可能返回已发放的优惠券信息
					if (res.code >= 0 && res.data && res.data.coupon_sent) {
						this.$util.showToast({
							title: '已获得优惠券'
						});
					}
				}
			});
		}
	},
	/**
	 * 自定义分享内容
	 * @param {Object} res
	 */
	onShareAppMessage(res) {
		var path = this.shareUrl;
		var store_info = this.$store.state.globalStoreInfo;
		if (store_info) path+= '&store_id=' + store_info.store_id;
		if (this.memberInfo && this.memberInfo.member_id) path += '&source_member=' + this.memberInfo.member_id;
		return {
			title: this.goodsSkuDetail.sku_name,
			imageUrl: this.shareImg ? this.$util.img(this.shareImg) : this.$util.img(this.goodsSkuDetail.sku_image, {
				size: 'big'
			}),
			path: path,
			success: res => {
			},
			fail: res => {
			}
		};
	},
	// 分享到微信朋友圈
	// #ifdef MP-WEIXIN
	onShareTimeline() {
		let query = this.shareQuery;
		var store_info = this.$store.state.globalStoreInfo;
		if (store_info) query+= '&store_id=' + store_info.store_id;
		if (this.memberInfo && this.memberInfo.member_id) query += '&source_member=' + this.memberInfo.member_id;
		return {
			title: this.goodsSkuDetail.sku_name,
			query: query,
			imageUrl: this.$util.img(this.goodsSkuDetail.sku_image, {
				size: 'big'
			})
		};
	}
	// #endif
}