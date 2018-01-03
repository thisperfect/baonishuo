var commentData = require("../../data/data.js");
Page({
  data:{

  },
  onLoad:function(options){
    this.setData({
      commentList: commentData.commentList
    });
  },
  ToShare:function(event){
    wx.navigateTo({
      url: '/pages/share/share',
    })
  }
})