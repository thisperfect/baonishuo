//app.js
//kizi
//lijun
//123
var qcloud = require('./vendor/wafer2-client-sdk/index')
var config = require('./config')

App({
    globalData:{
      logged:false
    },

    onLaunch: function () {
        qcloud.setLoginUrl(config.service.loginUrl);
        //
        if(wx.getStorageSync("userinfo")){
          this.globalData.logged=true;
          console.log("APP 已经登录");
        }else{
          this.globalData.logged = false;
          console.log("APP 还未登录");

        }
    }
})