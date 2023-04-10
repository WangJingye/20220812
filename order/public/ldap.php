<?php
$host = '10.211.0.6';
$conn = ldap_connect($host) or die('无法连接AD服务器');
ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
if ($conn) {
    $bind = ldap_bind($conn, 'CN=test,OU=IT,OU=ACN,OU=GCN Users,DC=cn,DC=asics,DC=com', 'Init1234');
    if ($bind) {
        echo '验证通过';
    } else {
        echo '登录失败';
    }
    ldap_close($conn);
}




