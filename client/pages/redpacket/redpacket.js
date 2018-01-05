var commentData = require("../../data/data.js");
var config = require('../../config');
var util = require('../../utils/util.js');
var qcloud = require('../../vendor/wafer2-client-sdk/index');
var app = getApp();
Page({
  data: {
    userInfo: {},
    detail_info: {},
  },
  onLoad: function (options) {
    this.setData({
      commentList: commentData.commentList
    });
    //目前将redpacket_id设置为14作为测试专用id
    console.log("redpacket_id =" + options.id);
    /**
     * 判断用户是否登录，避免在从红包转发入口进入时，领取红包缺少此用户的登录信息openid等
     */
    if (app.globalData.logged) {
      //添加用户信息
      this.setData({
        userInfo: wx.getStorageSync("userinfo")
      });
      //获取数据
      this.getData(options.id);
    } else {
      //未登录
      this.login(options.id);
    }

  },

  getData: function (id) {
    var that = this;
    var data = {};
    data.packet_id = id;
    data.open_id = this.data.userInfo.openId;
    console.log(data);
    wx.request({
      url: config.service.getVoiceDetail,
      data: data,
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
        console.log(res);
        //绑定数据
        that.setData({
          detail_info: res.data.data
        })
      },
      fail: function () {
        console.log("redpacket fail")
      }
    });
  },

  ToShare: function (event) {
    var avatar = this.data.detail_info.packet.user_info.avatarUrl;
    //var name = this.detail_info.packet.user_info.nickName;
    var command = this.data.detail_info.packet.command;
    var id = this.data.detail_info.packet.packet_id;
    wx.navigateTo({
      url: '/pages/share/share?avatar=' + avatar + '&command=' + command + '&id=' + id,
    })
  },

  // 用户登录示例
  login: function (id) {
    util.showBusy('正在登录')
    var that = this
    // 调用登录接口
    qcloud.login({
      success(result) {
        if (result) {
          util.showSuccess('登录成功')
          console.log("first to login");
          console.log(result);
          console.log("userinfo =" + result);
          //
          qcloud.request({
            url: config.service.requestUrl,
            login: true,
            success(result) {
              util.showSuccess('登录成功');
              console.log(result.data.data);
              that.setData({
                userInfo: result.data.data,
              });
              that.log(id);
            },
            fail(error) {
              util.showModel('请求失败', error)
              console.log('request fail', error)
            }
          })
        } else {
          // 如果不是首次登录，不会返回用户信息，请求用户信息接口获取
          qcloud.request({
            url: config.service.requestUrl,
            login: true,
            success(result) {
              util.showSuccess('登录成功');
              console.log("not first to login");
              console.log(result.data.data);
              that.setData({
                userInfo: result.data.data,
              });
              that.log(id);
            },
            fail(error) {
              util.showModel('请求失败', error)
              console.log('request fail', error)
            }
          })
        }
      },

      fail(error) {
        util.showModel('登录失败', error)
        console.log('登录失败', error)
      }
    })
  },

  /**
   * 用来打印用户信息,并保存用户信息
   */
  log: function (id) {
    for (var key in this.data.userInfo) {
      console.log("key =" + key + " value =" + this.data.userInfo[key]);
    }
    /**
    * 将用户信息保存到缓存里面
    */
    wx.setStorageSync("userinfo", this.data.userInfo);

    /**
   * 当从转发红包处进来是，先登录，获取用户信息，再开始获取此页面的信息
   * 当前方法则为 获取此页面的信息
   */
    app.globalData.logged = true;
    this.getData(id);
  },

  /**
   * 再去发红包
   */
  distributeRedPacket: function (event) {
    wx:wx.reLaunch({
      url: '/pages/home/home',
      success: function(res) {},
      fail: function(res) {},
      complete: function(res) {},
    })
  }


})