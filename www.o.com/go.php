<?php

// 函数定义
function getTerminalType() {
    $userAgent =$_SERVER['HTTP_USER_AGENT'];
    $terminalType = '';
    if (preg_match('/Mobile|Android/i', $userAgent)) {
        $terminalType = "移动设备终端";
    } elseif (preg_match('/Windows/i', $userAgent)) {
        $terminalType = "电脑终端";
    } elseif (preg_match('/Mac/i', $userAgent)) {
        $terminalType = "苹果电脑终端";
    } elseif (preg_match('/iPad|iPhone/i', $userAgent)) {
        $terminalType = "苹果平板或手机终端";
    } elseif (preg_match('/Android|BlackBerry|iPhone|Windows Phone/i', $userAgent)) {
        $terminalType = "其他移动设备终端";
    } else {
        $terminalType = "未知终端类型";
    }
    return $terminalType;
}

function getExternalIP() {
    $ip_address = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip_address =$_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip_address =$_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
        $ip_address =$_SERVER['HTTP_X_FORWARDED'];
    } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ip_address =$_SERVER['HTTP_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
        $ip_address =$_SERVER['HTTP_FORWARDED'];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip_address =$_SERVER['REMOTE_ADDR'];
    }
    return $ip_address;
}

function readFileContent($filename) {
    if (file_exists($filename)) {
        $str = file_get_contents($filename);
        $str_encoding = mb_convert_encoding($str, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
        return $str_encoding;
    }
    return '';
}

function saveContent($data,$filename) {
    file_put_contents($filename,$data, FILE_APPEND);
}

// 读取文件内容并转换编码
$str = file_get_contents('id.txt') . "\n";$str .= file_get_contents('id2.txt');
$str_encoding = mb_convert_encoding($str, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
$array = explode("\n",$str_encoding);

// 过滤数组并随机选择一个值
$filteredArray = array_filter($array, function($value) {
    return $value !== null &&$value !== "";
});

if (!empty($filteredArray)) {
    $randomIndex = array_rand($filteredArray); // 随机选择一个索引
    $wsurl =$filteredArray[$randomIndex]; // 获取随机选择的URL

    // 检查前缀并去除
    if (strpos($wsurl, 'wa.me/') === 0) {
        $zalo = substr($wsurl, 6); // 去除'wa.me/'前缀
        // 构建WhatsApp消息链接
        $whatsappLink = "https://api.whatsapp.com/send?phone=" . urlencode($zalo) . "&text=" . urlencode("Tahun 2024 akan menjadi era kemakmuran bagi bangsa Indonesia. Jika Anda berusia di atas 22 tahun dan memiliki kartu bank, hubungi saya sekarang dan jalani kehidupan impian Anda");
    } else {
        // 如果没有前缀，构建普通HTTPS链接
        $whatsappLink = "https://" .$wsurl;
    }
}

// 记录访问数据
if (isset($whatsappLink)) {
    $ip = getExternalIP();
    $currentDate = date('Ymd') . ".txt";
    $currentTime = date('Y-m-d H:i:s');
    $terminalType = getTerminalType();

    // 检查链接是否是WhatsApp链接，并提取电话号码
    if (strpos($whatsappLink, 'https://api.whatsapp.com/send?phone=') === 0) {
        $phoneNumber = substr($whatsappLink, 30); // 提取电话号码
        $phoneNumber = strtok($phoneNumber, '&'); // 去除后面的查询参数（如果存在）
        $phoneNumber = str_replace('phone=', '',$phoneNumber); // 去除"phone="部分
    } else {
        $phoneNumber =$whatsappLink; // 如果不是WhatsApp链接，直接使用整个URL
    }

    // 保存访问记录
    $userData =$currentTime . "----" . $ip . "----" .$terminalType . "----" . $phoneNumber . "\n";
    saveContent($userData,$currentDate);

    // 更新访问次数
//    $tj = readFileContent('tj.txt');
//    if ($tj === '') {
//        $tj = 1;
//    } else {
//        $tj = intval($tj) + 1;
    }
   // saveContent($tj, 'tj.txt');
//}




// 重定向用户
if (isset($whatsappLink)) {
    header('Location: ' . $whatsappLink);
    exit; // 确保在重定向
    exit; // 确保在重定向后停止执行
}

?>
