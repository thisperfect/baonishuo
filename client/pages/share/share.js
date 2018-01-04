var config = require('../../config');
var util = require('../../utils/util.js');
Page({
  data:{
    userInfo: {},
  },
  onLoad:function(options){
    //目前将opeid_id设置为14作为测试专用id   
    console.log("redpacket_id =" + options.id);
    //添加用户信息
    this.setData({
      userInfo: wx.getStorageSync("userinfo")
    });
    var data={};
    data.packet_id="14";
    data.open_id=this.data.userInfo.openId;
    console.log(data);
    wx.request({
      url: config.service.getVoiceDetail,
      data: data,
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success:function(res){
        console.log(res)
      },
      fail:function(){
        console.log("share fail")
      }
    });


  }
})