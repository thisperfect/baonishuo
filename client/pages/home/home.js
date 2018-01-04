var qcloud = require('../../vendor/wafer2-client-sdk/index');
var config = require('../../config');
var util = require('../../utils/util.js');
Page({
  data:{
    userInfo: {},
    logged: false,
    takeSession: false,
    requestResult: ''
  },
  onLoad:function(options){
    this.login();
   
  },

  // 用户登录示例
  login: function () {
    if (this.data.logged) return

    util.showBusy('正在登录')
    var that = this

    // 调用登录接口
    qcloud.login({
      success(result) {
        if (result) {
          util.showSuccess('登录成功')
          console.log("userinfo =" + result);
          that.setData({
            userInfo: result,
            logged: true
          });

          that.log();
        } else {
          // 如果不是首次登录，不会返回用户信息，请求用户信息接口获取
          qcloud.request({
            url: config.service.requestUrl,
            login: true,
            success(result) {
              util.showSuccess('登录成功')
              that.setData({
                userInfo: result.data.data,
                logged: true
              });

              that.log();
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
  log:function(){
    for (var key in this.data.userInfo) {
      console.log("key =" + key+" value ="+this.data.userInfo[key]);
    }
    /**
    * 将用户信息保存到缓存里面
    */
    wx.setStorageSync("userinfo", this.data.userInfo);
  },
  /**
   * 提交语音口令
   */
  formSubmit:function(e){
    console.log(e.detail);
    e.detail.value["open_id"] = this.data.userInfo.openId;
    e.detail.value["commission"] = parseFloat(e.detail.value.reward)*0.02;
    //e.detail.value["skey"] ="4d518b1e52d2fa01398b26e7db905d83374880f9";
    // var data={

    // };
    // for(var key in e.detail.value){
    //   data[key]=e.detail.value[key];
    // }
    // data[open_id] = this.data.userInfo.openId;
    // data[]
    console.log(e.detail.value);
    wx.request({
      url: config.service.createVoiceComment,
      data: e.detail.value,
      method:'POST',
      header:{
        'content-type': 'application/x-www-form-urlencoded'
      },
      success:function(res){
        console.log(res.data);
      }
      
    })
  },

  intoMyRecord:function(event){
    wx.navigateTo({
      url: '/pages/myrecords/myrecord',
    })
  },
  intoRemaining:function(event){
    wx.navigateTo({
      url: '/pages/remaining/remaining',
    })
  },
  intoQuestion:function(event){
    wx.navigateTo({
      url: '/pages/question/question',
    })
  },
  submit_voice:function(event){
    wx.navigateTo({
      url: '/pages/redpacket/redpacket',
    })
  }
})