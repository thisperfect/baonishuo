Page({
  data:{
    currentTab:0,
  },
  onLoad: function (options) {
    console.log("Windowheight:=" + wx.getSystemInfoSync().windowHeight);
    console.log("Screenheight:=" + wx.getSystemInfoSync().screenHeight)
    var swiperH = wx.getSystemInfoSync().windowHeight - 43;
    this.setData({
      swiperHeight: swiperH
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