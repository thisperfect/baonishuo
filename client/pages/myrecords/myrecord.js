var config = require('../../config');
var util = require('../../utils/util.js');
Page({
  data:{
    currentTab:0,
    userIcon:'',
    myRecData:{},
    userInfo:{},
  },
  onLoad: function (options) {
    console.log("Windowheight:=" + wx.getSystemInfoSync().windowHeight);
    console.log("Screenheight:=" + wx.getSystemInfoSync().screenHeight)
    var swiperH = wx.getSystemInfoSync().windowHeight - 43;
    this.setData({
      swiperHeight: swiperH
    });

    /**
     * 从缓存中获取用户信息
     */
    console.log("icon =" + wx.getStorageSync("userinfo").avatarUrl);
    this.setData({
      userInfo: wx.getStorageSync("userinfo")
    });
    console.log("userinfo ：");
    console.log(this.data.userInfo);

    /**
     * 调用接口获取数据
     */
    this.getData();


  },

  getData:function(){
    var that = this;
    var data = {};
    data.open_id = this.data.userInfo.openId;
    console.log(data);
    wx.request({
      url: config.service.getMyRecord,
      data:data,
      method:'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success:function(res){
        console.log("获取我的记录数据成功");
        console.log(res.data.data);
        that.setData({
          myRecData:res.data.data
        })
      },
      fail:function(){
        console.log("获取我的记录数据失败");

      }
    })
  },




  bindChange:function(event){
    console.log("currentTAB =" + event.detail.current);
    this.setData({
      currentTab:event.detail.current
    });
  },
  onSendCurrent:function(event){
    var pos = event.currentTarget.dataset.pos;
    this.setData({
      currentTab: pos
    });
  },
  intoRedPacket:function(event){
    var id = event.currentTarget.dataset.id;
    wx.navigateTo({
      url: '/pages/redpacket/redpacket?id='+id,
    });
  }

})