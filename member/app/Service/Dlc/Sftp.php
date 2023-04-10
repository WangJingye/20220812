<?php namespace App\Service\Dlc;

class Sftp
{
    private $conn;
    private $sftp;
    public function __construct()
    {
        $host = config('dlc.sftp.host');
        $port = config('dlc.sftp.port');
        $user = config('dlc.sftp.user');
        $pwd = config('dlc.sftp.pwd');
        $this->conn = ssh2_connect($host, $port);
        if (!ssh2_auth_password($this->conn, $user, $pwd)) {
            throw new \Exception('账号或密码错误');
        }
        if (!$this->sftp = ssh2_sftp($this->conn)) {
            throw new \Exception('sftp连接失败');
        }
    }

    public function download($remote, $local)
    {
        return copy("ssh2.sftp://{$this->sftp}".$remote, $local);
    }

    public function unlink($file)
    {
        return unlink("ssh2.sftp://{$this->sftp}".$file);
    }

    public function upload($local,$remote)
    {
        return copy($local,"ssh2.sftp://{$this->sftp}".$remote);
    }

    public function scan($remote){
        $dirHandle = opendir("ssh2.sftp://{$this->sftp}" . $remote);
        $all = [];
        while (false !== ($file = readdir($dirHandle))) {
            if ($file != '.' && $file != '..') {
                $all[] = $file;
            }
        }
        return $all;
    }
}