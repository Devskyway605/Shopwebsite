<?php
header("Content-Type: application/json; charset=utf-8");

if (isset($_FILES['photo']['name']) && $_FILES['photo']['name'] !== "") {
    $file = $_FILES['photo'];

    $allowedTypes = ['image/jpeg', 'image/png'];
    if (in_array($file['type'], $allowedTypes)) {
        $filename = date("YmdHis") . "_" . $file['name'];
        $location = '../img/member/' . $filename;

        if (!is_dir('../img/member/')) {
            mkdir('../img/member/', 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $location)) {
            echo json_encode([
                "state" => true,
                "message" => "檔案上傳成功!",
                "filename" => $filename,
                "type" => $file['type'],
                "size" => $file['size']
            ]);
        } else {
            echo json_encode([
                "state" => false,
                "message" => "檔案移動失敗!",
                "error" => $file['error']
            ]);
        }
    } else {
        echo json_encode([
            "state" => false,
            "message" => "檔案格式錯誤，僅支援 JPEG 或 PNG"
        ]);
    }
} else {
    echo json_encode([
        "state" => false,
        "message" => "未接收到圖片檔案"
    ]);
}
