Page({


  intoMyRecord:function(event){
    wx.navigateTo({
      url: '/pages/myrecords/myrecord',
    })
  },
  intoRemaining:function(event){
    wx.navigateTo({
      url: '/pages/remaining/remaining',
    })
  }
})