<?php
    // echo $_FILES['file']['name'].'<br>';
    // echo $_FILES['file']['type'].'<br>';
    // echo $_FILES['file']['tmp_name'].'<br>';
    // echo $_FILES['file']['size'].'<br>';
    // echo $_FILES['file']['error'].'<br>'; 
    
    if(isset($_FILES['file']['name']) && $_FILES['file']['name'] !=""){
        if($_FILES['file']['type'] == 'image/jpeg' || $_FILES['file']['type'] == 'image/png'){
            // 檔案(圖片)上傳至伺服器(後端主機)

            $filename = date("YmdHis")."_".$_FILES['file']['name'];
            // 先設定路徑
            $location = '../img/member/' . $filename;
            if(move_uploaded_file($_FILES['file']['tmp_name'], $location)){
                $datainformation = array();
                $datainformation["state"] = true;
                $datainformation["message"] = "檔案上傳成功!"; 
                $datainformation["name"] = $_FILES['file']['name'];
                $datainformation["location"] = $location; 
                $datainformation["type"] = $_FILES['file']['type']; 
                $datainformation["tmp_name"] = $_FILES['file']['tmp_name'];
                $datainformation["size"] = $_FILES['file']['size']; 
                $datainformation["error"] = $_FILES['file']['error'];
                
                echo json_encode($datainformation);
            }else{
                $errorinfo = array();
                $errorinfo["state"] = false;
                $errorinfo["message"] = "檔案上傳失敗!";
                $errorinfo["error"] = $_FILES['file']['error'];

                echo json_encode($errorinfo);
            }
            
        }else{
            echo '{"state" : false, "message" : "上傳格式不為jpeg or png!"}';
        }
    }else{
        echo '{"state" : false, "message" : "檔案不存在!"}';
    }
?>