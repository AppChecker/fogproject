<?php
header('Content-Type: text/event-stream');
$getInterface = function() {
    $dev = trim($_REQUEST['dev'] ? basename(htmlentities($_REQUEST['dev'],ENT_QUOTES,'utf-8')) : 'eth0');
    $interfaces = array_map(function(&$iface) use (&$interfaces) {
        if (trim(file_get_contents(sprintf('/sys/class/net/%s/operstate',$iface))) !== 'up') return;
        return $iface;
    },array_diff(scandir('/sys/class/net'),array('..','.')));
    $interface = preg_grep("#$dev#",(array)$interfaces);
    $dev = @array_shift($interface);
    if (empty($dev)) return array('dev'=>'unknown','rx'=>0,'tx'=>0);
    $rx = trim(file_get_contents(sprintf('/sys/class/net/%s/statistics/rx_bytes',$dev)));
    $tx = trim(file_get_contents(sprintf('/sys/class/net/%s/statistics/tx_bytes',$dev)));
    return array('dev'=>$dev,'rx'=>$rx,'tx'=>$tx);
};
echo json_encode($getInterface());
exit;
