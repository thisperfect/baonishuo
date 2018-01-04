Page({
  data:{
    currentTab:0,
    userIcon:'',
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
      userIcon: wx.getStorageSync("userinfo").avatarUrl
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
  }

})