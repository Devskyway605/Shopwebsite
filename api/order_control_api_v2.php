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

function add_order()
{
    $input = get_jsoninput();
    if(isset($input["userId"] && $input["productId"] && $input["quantity"]))
    {
        
    }     
}