var commentData = require("../../data/data.js");
var config = require('../../config');
var util = require('../../utils/util.js');
Page({
  data: {
    userInfo: {},
    detail_info:{},
  },
  onLoad: function (options) {
    this.setData({
      commentList: commentData.commentList
    });
    //目前将opeid_id设置为14作为测试专用id   
    console.log("redpacket_id =" + options.id);
    //添加用户信息
    this.setData({
      userInfo: wx.getStorageSync("userinfo")
    });
    //获取数据
    this.getData(options.id);

  },

  getData: function (id) {
    var that=this;
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
          detail_info:res.data.data
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
})