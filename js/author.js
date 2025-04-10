(function () {

    function getCookie(cname) {
        let name = cname + "=";
        let ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    // 檢查uid是否存在,若沒有則導入登入畫面
    if (!getCookie("UID01")) {
        Swal.fire({
            title: "請先登入會員!",
            showDenyButton: false,
            showCancelButton: false,
            confirmButtonText: "確認",
            denyButtonText: `Don't save`,
            allowOutsideClick: false
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                location.href = "index.html";
            }
        });

        return;
    } 

    // 若uid存在,傳送至後端API執行驗證
    var JSONdata = {};
    JSONdata["uid01"] = getCookie("UID01");
    // console.log(JSON.stringify(JSONdata));

    $.ajax({
        type: "POST",
        url: "member_control_api_v1.php?action=checkuid",
        data: JSON.stringify(JSONdata),
        dataType: "json",
        success: showdata_checkuid,
        error: function () {
            console.log(state, "api錯誤");
        }
    });

    //確認uid是否合法
    if (getCookie("UID01")) {
        //將uid01傳遞至後端API執行驗證
        //input {"uid01" : "c5e6c37a"}
        var JSONdata = {};
        JSONdata["uid01"] = getCookie("UID01");
        // console.log(JSON.stringify(JSONdata));

        $.ajax({
            type: "POST",
            url: "member_control_api_v1.php?action=checkuid",
            data: JSON.stringify(JSONdata),
            dataType: "json",
            success: showdata_checkuid,
            error: function () {
                console.log(state, "api錯誤");
            }
        });
    } else {
        Swal.fire({
            title: "請先登入會員!",
            showDenyButton: false,
            showCancelButton: false,
            confirmButtonText: "確認",
            denyButtonText: `Don't save`,
            allowOutsideClick: false
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                location.href = "index.html";
            }
        });
    }

    // 檢查uid function
    function showdata_checkuid(data) {
        console.log(data.data.Role);
        if (data.state) {

            // 會員權限分級
            if (data.data.Role === "M") {
                // 顯示控制台按鈕
                $("#manager_function").removeClass("d-none");
                $("#normal_member_function").removeClass("d-none");
            } else if (data.data.Role == "N") {
                // 顯示會員功能
                $("#normal_member_function").removeClass("d-none");
            }

            // loginModal消失
            $("#loginModal").modal("hide");

            //顯示頭像歡迎訊息
            $("#navbar_username_showtext").removeClass("d-none");
            $("#navbar_memberPhoto").attr("src", "img/member/" + data.data.Photo);
            $("#navbar_username_text").text(data.data.RealName);

            //隱藏註冊與登入按鈕
            $("#navbar_btn_login").addClass("d-none");
            $("#navbar_username_text").removeClass("d-none");

            //顯示登出按鈕
            $("#navbar_btn_logout").removeClass("d-none");



        }
    }

})();