# WebSocket 接口

## 地址：
ws://ws.sxisa.org:10240

## 用法：
请参照`index.html`的Js代码。

## Message指令：
`{::List}` 获取当前在线列表。

## 接口格式(Sample)：
1. 通信接口：
``` json
{
	"user" : {
		"id" : "1",
		"ip" : "192.168.0.1"
	},
	"status" : "connect/disconnect/sendmsg",
	"msg" : "the message which send"
}
```
2. 在线列表：
``` json
{
	"list" : [{
		"id" : "1",
		"ip" : "192.168.0.1"
	},
	{
		"id" : "2",
		"ip" : "192.168.0.2"
	}]
}
```
[view sample →](http://ws.sxisa.org)
