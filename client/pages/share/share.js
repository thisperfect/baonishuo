var config = require('../../config');
var util = require('../../utils/util.js');
Page({
  data:{
    userInfo: {},
    packet_info: {},
    id:'',
  },
  onLoad:function(options){
    //拿到红包的id 发红的头像 和口令
    var id = options.id;
    this.data.id=id;
    var command = options.command;
    var avatar = options.avatar;
    console.log("id=" + id + "  command=" + command + " avatar=" + avatar);
    this.setData({
      command: command,
      avatar: avatar
    })

    //添加用户信息
    this.setData({
      userInfo: wx.getStorageSync("userinfo")
    });
  },
  onShareAppMessage: function (res) {
    if (res.from === 'button') {
      console.log(res.target);
    }
    return {
      title: '这是转发红包,有本事别打开',
      path: '/pages/redpacket/redpacket?id=' + this.data.id,
      success: function (res) {
        console.log("转发成功");
        for (var key in res.shareTickets) {
          console.log("key =" + key + "  value =" + res.shareTickets[key])
        }
        util.showSuccess("转发成功");
      },
      fail: function (res) {
        console.log("转发失败")
      }
    }
  }
})