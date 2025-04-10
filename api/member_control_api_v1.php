<?php
header("Access-Control-Allow-Origin : *");
const DB_SERVER = "localhost"; //const:公有變數,不會再做改變
const DB_USERNAME = "owner01";
const DB_PASSWORD = "123456";
const DB_NAME = "project";

// 建立連線
function create_connection()
{
    $conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if (!$conn) {
        echo json_encode(["state" => false, "message" => "連線失敗!"]);
        exit;
    }
    return $conn;
}

// 取得JSON訊息
function get_jsoninput()
{
    $data = file_get_contents("php://input");
    return json_decode($data, true);
}

// 回復JSON格式訊息
// state:狀態(成功或失敗) message:訊息內容 data:回傳資料(可有可無)
function respond($state, $message, $data = null)
{
    echo json_encode(["state" => $state, "message" => $message, "data" => $data]);
}

// 確認帳號是否存在
// input: {"username" : "owner01"}
// {"state" : true, "message" : "帳號不存在可使用"}
// {"state" : false, "message" : "帳號已存在,不可使用"}
// {"state" : false, "message" : "欄位錯誤"}
// {"state" : false, "message" : "欄位不得為空白"}
function checkuni_username()
{
    $input = get_jsoninput();

    if (isset($input["username"])) {
        $p_username = trim($input["username"]);
        if ($p_username) {
            $conn = create_connection();
            $sql = $conn->prepare("SELECT Username FROM member WHERE Username = ?");
            $sql->bind_param("s", $p_username);
            $sql->execute();

            $result = $sql->get_result();

            if ($result->num_rows === 1) {
                // 帳號已存在
                respond(false, "帳號已不可使用");
            } else {
                // 帳號不存在
                respond(true, "帳號尚未註冊，可以使用");
            }
        } else {
            respond(false, "欄位錯誤");
        }
    } else {
        respond(false, "欄位不得為空");
    }
}

// 會員註冊
// input:{"username" : "owner01", "password" : "123456", "name":"xxx", "gender":"x","email" : "xxx@gmail..com", "city":"xxx", "region":"xxx"}
// {"state" : true, "message" : "註冊成功"}
// {"state" : false, "message" : "新增失敗與錯誤相關訊息"}
// {"state" : false, "message" : "欄位錯誤"}
// {"state" : false, "message" : "欄位不得為空白"}
function member_register()
{
    $input = get_jsoninput();

    if (isset($input["username"], $input["password"], $input["photo"], $input["name"], $input["gender"], $input["email"], $input["city"], $input["region"])) {

        $p_username = $input["username"];
        $p_password = password_hash(trim($input["password"]), PASSWORD_DEFAULT);
        $p_photo = trim($input["photo"]);
        $p_name = trim($input["name"]);
        $p_gender = trim($input["gender"]);
        $p_email = trim($input["email"]);
        $p_city = trim($input["city"]);
        $p_region = trim($input["region"]);

        if ($p_username && $p_password && $p_name && $p_gender && $p_email && $p_city && $p_region) {
            $conn = create_connection();

            $stmt = $conn->prepare("INSERT INTO member (Username, Password, Photo, RealName, Gender, Email, City, Region) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $p_username, $p_password, $p_photo, $p_name, $p_gender, $p_email, $p_city, $p_region);
            // $regist_data = $stmt->execute();

            try {
                if($stmt->execute()){
                    $sql = $conn->prepare("SELECT * FROM member WHERE Username = ?");
                    $sql->bind_param("s", $p_username);
                    $sql->execute();
                    $result = $sql->get_result();

                    if ($result->num_rows === 1) {
                        
                            //比對成功
                            //產生UID並更新至資料庫
                            $uid01 = substr(hash('sha256', time()), 10, 4) . substr(bin2hex(random_bytes(16)), 4, 4);
                            $update_stmt = $conn->prepare("UPDATE member SET UID01 = ? WHERE Username = ?");
                            $update_stmt->bind_param('ss', $uid01, $p_username);
                            if ($update_stmt->execute()) {
                                // unset($row["Password"]);
                                //取得登入的使用者資訊
                                $user_stmt = $conn->prepare("SELECT * FROM member WHERE Username = ?");
                                $user_stmt->bind_param("s", $p_username); //一定要傳遞變數
                                $user_stmt->execute();
                                $user_data = $user_stmt->get_result()->fetch_assoc();
                                unset($user_data["Password"], $user_data["Createtime"]);
                                respond(true, "登入成功", $user_data);
                            } else {
                                respond(false, "登入失敗, UID更新失敗");
                            }
                        
                    } else {
                        respond(false, "登入失敗, 該帳號不存在");
                    }
                }else{
                    respond(false, "註冊失敗");
                }
            } catch (Exception $e) {
                respond(false, "資料寫入失敗");
            }

            $stmt->close();
            $conn->close();
        } else {
            respond(false, "欄位不得為空");
        }
    } else {
        respond(false, "欄位錯誤");
    }
}

//會員登入
// {"username" : "owner01", "password" : "123456"}
// {"state" : true, "message" : "登入成功", "data" : "使用者資訊"}
// {"state" : false, "message" : "登入失敗與相關錯誤訊息"}
// {"state" : false, "message" : "欄位錯誤"}
// {"state" : false, "message" : "欄位不得為空"}
function member_login()
{
    $input = get_jsoninput();
    if (isset($input["username"], $input["password"])) {
        $p_username = trim($input["username"]);
        $p_password = trim($input["password"]);

        if ($p_username && $p_password) {
            $conn = create_connection();

            $sql = $conn->prepare("SELECT * FROM member WHERE Username = ?");
            $sql->bind_param("s", $p_username);
            $sql->execute();
            $result = $sql->get_result();

            if ($result->num_rows === 1) {
                //抓取密碼執行password_verify比對
                $row = $result->fetch_assoc();
                if (password_verify($p_password, $row["Password"])) {
                    //比對成功
                    //產生UID並更新至資料庫
                    $uid01 = substr(hash('sha256', time()), 10, 4) . substr(bin2hex(random_bytes(16)), 4, 4);
                    $update_stmt = $conn->prepare("UPDATE member SET UID01 = ? WHERE Username = ?");
                    $update_stmt->bind_param('ss', $uid01, $p_username);
                    if ($update_stmt->execute()) {
                        // unset($row["Password"]);
                        //取得登入的使用者資訊
                        $user_stmt = $conn->prepare("SELECT * FROM member WHERE Username = ?");
                        $user_stmt->bind_param("s", $p_username); //一定要傳遞變數
                        $user_stmt->execute();
                        $user_data = $user_stmt->get_result()->fetch_assoc();
                        unset($user_data["Password"], $user_data["Createtime"]);
                        respond(true, "登入成功", $user_data);
                    } else {
                        respond(false, "登入失敗, UID更新失敗");
                    }
                } else {
                    //比對失敗
                    respond(false, "登入失敗, 密碼錯誤");
                }
            } else {
                respond(false, "登入失敗, 該帳號不存在");
            }
            $sql->close();
            $conn->close();
        } else {
            respond(false, "登入失敗, 欄位不得為空");
        }
    } else {
        respond(false, "登入失敗, 欄位錯誤");
    }
}

// check UID01驗證
// input: {"uid01" : "owner01"}
// {"state" : true, "message" : "驗證成功"}
// {"state" : false, "message" : "驗證登入失敗與錯誤相關訊息"}
// {"state" : false, "message" : "欄位錯誤"}
// {"state" : false, "message" : "欄位不得為空白"}
function member_checkuid()
{
    $input = get_jsoninput();
    if (isset($input["uid01"])) {
        $p_uid01 = trim($input["uid01"]);
        if ($p_uid01) {
            $conn = create_connection();
            if (!$conn) {
                respond(false, "連線失敗!");
                exit;
            }

            $sql = $conn->prepare("SELECT * FROM member WHERE UID01 = ?");
            $sql->bind_param("s", $p_uid01);
            $sql->execute();
            $result = $sql->get_result();

            if ($result->num_rows === 1) {
                // 驗證成功
                $userdata = $result->fetch_assoc();
                unset($userdata["Password"]);
                respond(true, "驗證成功", $userdata);
            } else {
                respond(false, "驗證失敗");
            }
            $conn->close();
            $sql->close();
        } else {
            respond(false, "欄位不得為空白");
        }
    } else {
        respond(false, "欄位錯誤");
    }
}

// 取得所有會員資料
// input: none
// {"state" : true, "message" : "取得所有會員資料成功", "data" : "所有使用者資訊"}
// {"state" : false, "message" : "取得所有會員資料失敗"}
// {"state" : false, "message" : "欄位錯誤"}
// {"state" : false, "message" : "欄位不得為空白"}
function member_getdata()
{
    $conn = create_connection();
    $sql = $conn->prepare("SELECT * FROM member ORDER BY ID DESC");
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $mydata = array();
        while ($row = $result->fetch_assoc()) {
            unset($row["UID01"]);
            $mydata[] = $row;
        }
        respond(true, "取得會員資料成功", $mydata);
    } else {
        respond(false, "查無資料");
    }
    $conn->close();
    $sql->close();
}

// 更新會員資料
// input: {"id" : "xx", "email" : "xxxxx"}
// {"state" : true, "message" : "更新資料成功"}
// {"state" : false, "message" : "更新資料失敗"}
// {"state" : false, "message" : "欄位錯誤"}
// {"state" : false, "message" : "欄位不得為空白"}
function member_updatedata()
{
    $input = get_jsoninput();
    if (isset($input["id"], $input["realname"], $input["email"], $input["photo"], $input["city"], $input["region"])) {
        $p_id = trim($input["id"]);
        $p_realname = trim($input["realname"]);
        // $p_gender = trim($input["gender"]);
        $p_email = trim($input["email"]);
        $p_photo = date("YmdHis") . "_" . trim($input["photo"]);
        $p_city = trim($input["city"]);
        $p_region = trim($input["region"]);

        if ($p_id) {
            $conn = create_connection();

            $sql = $conn->prepare("UPDATE member SET Realname = ?, Email = ?, Photo = ?, City = ?, Region = ? WHERE ID = ?");
            $sql->bind_param("sssssi", $p_realname, $p_email, $p_photo, $p_city, $p_region, $p_id);

            if ($sql->execute()) {
                if ($sql->affected_rows === 1) {
                    respond(true, "會員資料更新成功");
                } else {
                    respond(false, "會員資料更新失敗,沒有更新行為");
                }
            } else {
                respond(false, "會員資料更新失敗");
            }
        } else {
            respond(false, "欄位錯誤");
        }
    } else {
        respond(false, "欄位不得為空白");
    }
}

// 刪除會員資料
// input: {"id":"XXX"}
// {"state" : true, "message" : "刪除成功"}
// {"state" : false, "message" : "刪除失敗與錯誤相關訊息"}
function member_delete()
{
    $input = get_jsoninput();

    if (isset($input["id"])) {
        $p_id = trim($input["id"]);

        if ($p_id) {
            $conn = create_connection();
            $sql = $conn->prepare("DELETE FROM member WHERE ID = ?");
            $sql->bind_param("s", $p_id);

            if ($sql->execute()) {
                if ($sql->affected_rows === 1) {
                    respond(true, "會員刪除成功");
                } else {
                    respond(false, "會員刪除失敗");
                }
            } else {
                respond(false, "會員刪除失敗");
            }
            $conn->close();
            $sql->close();
        } else {
            respond(false, "欄位不得為空");
        }
    } else {
        respond(false, "欄位錯誤");
    }
}

// METHOD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_GET['action'] ?? '';
    switch ($action) {
        case 'checkuni':
            checkuni_username();
            break;
        case 'register':
            member_register();
            break;
        case 'login':
            member_login();
            break;
        case 'checkuid':
            member_checkuid();
            break;
        case 'updatedata':
            member_updatedata();
            break;
        default:
            respond(false, "無效的操作");
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    switch ($action) {
        case 'getdata':
            member_getdata();
            break;
        default:
            respond(false, "無效的操作");
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $action = $_GET['action'] ?? '';
    switch ($action) {
        case 'delete':
            member_delete();
            break;
        default:
            respond(false, "無效的操作");
    }
}
