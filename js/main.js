// javascript版本 show_id
function show_id()
{
    // var跟let :宣告變數
    var user_id = document.getElementById("u_id").value;

    // user_id 不等於空值，有資料  (!= :不等於)
    if(user_id != ""){

    // inline:同一行 block:換行
    // document.getElementById("idmsg").style.display = "inline";
        document.getElementById("idmsg").style.display = "block";

    // innerHTML 寫入html
        document.getElementById("idmsg"). innerHTML = "<font color='#ff0000'>" + user_id +"</font>";
    }
    else{
        document.getElementById("idmsg").style.display = "none";
    }
}

// 第二版本 Jquery版 show_id2
function show_id2(){
    var user_id = $("#u_id").val();
    if(user_id !=""){

        $("#idmsg").show();
        $("#idmsg").html("<font color='#ff0000'>" + user_id + "</font>");
    }
    else{
        $("#idmsg").hide();
    }
}