var question_database=[
    {
      pos:0,
      title:"包你说 - 常见问题",
      detail:"你可以设置一个带奖励的语音口令，好友说对口令才能领到奖励"
    },
    {
      pos: 0,
      title: "我支付了但是没有发出去",
      detail: "请在主页的【我的记录】中找到相应的记录，点击进入详情后点击【去转发】可以把口令转发给好友或群，你也可以生成朋友圈分享图后发朋友圈"
    },
    {
      pos: 0,
      title: "好友可以转发我的口令吗",
      detail: "可以的，您分享给好友或者转发到微信群的语音口令，其他好友均可再次转发"
    },
    {
      pos: 0,
      title: "发口令会收取服务费吗",
      detail: "发语音口令会收取2%的服务费"
    }, {
      pos: 0,
      title: "未领取的金额怎样处理",
      detail: "未领取的金额将于24小时后退至包你说小程序余额；同时，未领取金额的服务费也将全部退回"
    },
     {
      pos: 0,
      title: "如何提现到微信钱包",
      detail: "在主页的【余额提现】或详情页的【去提现】均可以跳转至余额提现页面进行提现，提现金额每次至少1元，每天至多提现3次"
    },
     {
       pos: 0,
       title: "提现会收取服务费吗？多久到账",
       detail: "提现不收取服务费；申请提现后会在1-5个工作日内转账到您的微信钱包"
     },
     {
       pos: 0,
       title: "如何联系客服？(客服在线时间：9:00-23:00)",
       detail: "您可以点击本页面下方的绿色按钮来联系我们的在线客服；\n您也可以拨打我们的客服电话：020-29052945"
     }

]
var comment_database=[
  {
    name:'我会放光啊',
    time:"8''",
    price:'1.00元',
    date:'12月24日 17：02'

  },
  {
    name: 'kizi',
    time: "7''",
    price: '2.00元',
    date: '12月24日 17：02'

  },
  {
    name: 'lijun',
    time: "6''",
    price: '6.00元',
    date: '12月24日 17：02'

  },
  {
    name: 'whj',
    time: "8''",
    price: '5.00元',
    date: '12月24日 17：02'

  },
  {
    name: 'cml',
    time: "8''",
    price: '4.00元',
    date: '12月24日 17：02'

  }

]

module.exports = {
  questionList: question_database,
  commentList: comment_database
}