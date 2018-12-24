$(function () {
    /*自定义form表单发送事件*/
    $("#form").submit(function (e) {
        /*阻止input跳转*/
        var e = e || window.event;
        if (e.preventDefault) {
            e.preventDefault();
        } else {
            window.event.returnValue = false;//IE
        }
        /*获取数据变量*/
        var to = $("#to").val();
        var subject = $("#subject").val();
        var msg = $("#msg").val();
        /*发送ajax*/
        $.ajax({
            type: "POST",
            url: 'index.php',
            dataType: "json",
            data: {
                "to": to,
                "subject": subject,
                "msg": msg
            },
            success: function (data) {
                //做有意义的事
                if (data.status) {
                    alert("发送成功：" + data.errmsg);
                    setTimeout("history.go(0)", 1000);
                } else {
                    alert("发送失败：" + data.errmsg);
                }
            },
            error: function (jqXHR) {
                if (jqXHR.status != 200) {
                    alert("发生错误：" + jqXHR.status);
                }
            }
        });
    });
});