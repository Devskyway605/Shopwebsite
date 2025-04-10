<?php
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


// 確認產品名稱是否存在
function checkuni_productname()
{
    $input = get_jsoninput();

    if (isset($input["name"])) {
        $p_name = trim($input["name"]);
        if ($p_name) {
            $conn = create_connection();
            $sql = $conn->prepare("SELECT Name FROM Products WHERE Name = ?");
            $sql->bind_param("s", $p_name);
            $sql->execute();

            $result = $sql->get_result();

            if ($result->num_rows === 1) {
                // 商品名稱已存在
                respond(false, "商品名稱已存在");
            } else {
                // 帳號不存在
                respond(true, "可使用此商品名");
            }
        } else {
            respond(false, "欄位錯誤");
        }
    } else {
        respond(false, "欄位不得為空");
    }
}
// 新增產品
// input:{"name" : "xxx", "description" : "xxx", "photo": "xxxxx.xxx", "price" : "50", "quantity":"10", "status":"x"}
// {"state" : true, "message" : "註冊成功"}
// {"state" : false, "message" : "新增失敗與錯誤相關訊息"}
// {"state" : false, "message" : "欄位錯誤"}
// {"state" : false, "message" : "欄位不得為空白"}
function product_add()
{
    $input = get_jsoninput();

    if (isset($input["pname"], $input["category"], $input["price"], $input["stock"], $input["description"], $input["status"], $input["photo"])) {

        $p_name = trim($input["pname"]);
        $p_category = $input["category"];
        $p_price = trim($input["price"]);
        $p_stock = trim($input["stock"]);
        $p_description = trim($input["description"]);
        $p_status = trim($input["status"]);
        $p_photo = date("YmdHis") . "_" . trim($input["photo"]);



        if ($p_name && $p_category && $p_description && $p_photo && $p_price && $p_stock) {
            $conn = create_connection();

            $sql = $conn->prepare("INSERT INTO Products (Name, Category, Description, Photo, Price, Stock_quantity, Status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $sql->bind_param("ssssiis", $p_name, $p_category, $p_description, $p_photo, $p_price, $p_stock, $p_status);

            if ($sql->execute()) {
                respond(true, "新增成功");
            } else {
                respond(false, "新增失敗");
            }
            $sql->close();
            $conn->close();
        } else {
            respond(false, "欄位不得為空");
        }
    } else {
        respond(false, "欄位錯誤");
    }
}

// 取得所有產品資料
// input: none
// {"state" : true, "message" : "產品資料取得成功", "data" : "所有使用者資訊"}
// {"state" : false, "message" : "查無資料"}
function product_getdata()
{
    $conn = create_connection();
    $sql = $conn->prepare("SELECT * FROM Products ORDER BY Product_id DESC");
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $mydata = array();
        while ($row = $result->fetch_assoc()) {
            $mydata[] = $row;
        }
        respond(true, "產品資料取得成功", $mydata);
    } else {
        respond(false, "查無資料");
    }
    $conn->close();
    $sql->close();
}

// 修改產品資料
// input:{"name" : "xxx", "description" : "xxx", "photo": "xxxxx.xxx", "price" : "50", "quantity":"10", "status":"x"}
function product_update()
{
    $input = get_jsoninput();

    if (isset($input["id"])) {
        $p_id = trim($input["id"]);
        $p_name = trim($input["name"]);
        $p_category = trim($input["category"]);
        $p_description = trim($input["description"]);
        $p_photo = trim($input["photo"]);
        $p_price = trim($input["price"]);
        $p_quantity = trim($input["stock"]);
        $p_status = trim($input["status"]);

        if ($p_id) {
            $conn = create_connection();

            $sql = $conn->prepare("UPDATE Products SET Name = ?, Category = ?, Description = ?, Photo = ?, Price = ?, Stock_quantity = ?, Status = ? WHERE Product_id = ?");
            $sql->bind_param("ssssiisi", $p_name, $p_category, $p_description, $p_photo, $p_price, $p_quantity, $p_status, $p_id);

            if ($sql->execute()) {
                if ($sql->affected_rows === 1) {
                    respond(true, "產品資訊更新成功");
                } else {
                    respond(true, "產品資訊更新成功");
                }
            } else {
                respond(false, "產品資訊更新失敗");
            }
        } else {
            respond(false, "欄位錯誤");
        }
    } else {
        respond(false, "欄位不得為空");
    }
}

// 刪除產品資料
// input:{"id" : "xxx"}
function product_delete()
{
    $input = get_jsoninput();

    if (isset($input["id"])) {
        $p_id = trim($input["id"]);

        if ($p_id) {
            $conn = create_connection();
            $sql = $conn->prepare("DELETE FROM Products WHERE Product_id = ?");
            $sql->bind_param("s", $p_id);

            if ($sql->execute()) {
                respond(true, "產品刪除成功");
            } else {
                respond(false, "產品刪除失敗");
            }
        } else {
            respond(false, "產品刪除失敗");
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
            checkuni_productname();
            break;
        case 'add':
            product_add();
            break;
        case 'update':
            product_update();
            break;
        default:
            respond(false, "無效的操作");
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    switch ($action) {
        case 'getdata':
            product_getdata();
            break;
        default:
            respond(false, "無效的操作");
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $action = $_GET['action'] ?? '';
    switch ($action) {
        case 'delete':
            product_delete();
            break;
        default:
            respond(false, "無效的操作");
    }
}
