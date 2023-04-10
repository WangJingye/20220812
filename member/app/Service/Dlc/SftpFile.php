<?php namespace App\Service\Dlc;

class SftpFile
{
    private $conn;
    private $sftp;
    private $dir;
    public function __construct()
    {
        $host = config('dlc.sftp_file.host');
        $port = config('dlc.sftp_file.port');
        $user = config('dlc.sftp_file.user');
        $pwd = config('dlc.sftp_file.pwd');
        $this->dir = config('dlc.sftp_file.dir');
        $this->conn = ssh2_connect($host, $port);
        if (!ssh2_auth_password($this->conn, $user, $pwd)) {
            throw new \Exception('账号或密码错误');
        }
        if (!$this->sftp = ssh2_sftp($this->conn)) {
            throw new \Exception('sftp连接失败');
        }
    }

    public function is_file($remote)
    {
        $remote = "{$this->dir}/{$remote}";
        return is_file("ssh2.sftp://{$this->sftp}".$remote)?1:0;
    }

    public function download($remote, $local)
    {
        $remote = "{$this->dir}/{$remote}";
        return copy("ssh2.sftp://{$this->sftp}".$remote, $local);
    }

    public function unlink($file)
    {
        return unlink("ssh2.sftp://{$this->sftp}".$file);
    }

    public function upload($local,$remote)
    {
        $remote = "{$this->dir}/{$remote}";
        return copy($local,"ssh2.sftp://{$this->sftp}".$remote);
    }

    public function scan($remote){
        $remote = "{$this->dir}/{$remote}";
        $dirHandle = opendir("ssh2.sftp://{$this->sftp}".$remote);
        $all = [];
        while (false !== ($file = readdir($dirHandle))) {
            if ($file != '.' && $file != '..') {
                $all[] = $file;
            }
        }
        return $all;
    }
}