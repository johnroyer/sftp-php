<?php

namespace Zeroplex\Sftp;

class Sftp
{
    private $host = '';
    private $port = 22;
    private $timeout = 10; // in seconds
    private $username = '';
    private $password = '';
    private $connection;
    private $sftpResource;

    public function __construct($host, $port, $timeoutInSecond = 10)
    {
        $this->host = $host;

        if (!is_int($port)) {
            throw new \InvalidArgumentException('Port must be an integer');
        }
        if (1 > $port || 65535 < $port)  {
            throw new \InvalidArgumentException('Port must be between 1 and 65535');
        }
        $this->port = $port;

        if (!is_int($timeoutInSecond)) {
            throw new \InvalidArgumentException('Timeout must be an integer');
        }
        $this->timeout = $timeoutInSecond;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

    public function login($username, $password)
    {
        // update timeout
        ini_set('default_socket_timeout', $this->timeout);

        if (0 == strlen($username)) {
            throw new \InvalidArgumentException('Username cannot be empty');
        }
        $this->username = $username;
        $this->password = $password;

        // connect to server
        $this->connection = ssh2_connect($this->host, $this->port);
        if (false === $this->connection) {
            throw new \Exception('failed to connect to ' . $this->host . ' on port ' . $this->port);
        }

        // login
        $loginReslut = ssh2_auth_password($this->connection, $this->username, $this->password);
        if (false === $loginReslut) {
            throw new \Exception('failed to login');
        }

        $this->sftpResource = ssh2_sftp($this->connection);
        if (false === $this->sftpResource) {
            throw new \Exception('failed to open sftp');
        }
    }

    public function isLoggedIn()
    {
        return null !== $this->sftpResource;
    }

    public function pwd()
    {
        if (!$this->isLoggedIn()) {
            throw new \Exception('you need to login first');
        }

        return ssh2_sftp_realpath($this->sftpResource, '.');
    }

    public function get($remoteFilePath)
    {
        if (!$this->isLoggedIn()) {
            throw new \Exception('you need to login first');
        }

        if (!$this->isFileExists($remoteFilePath)) {
            throw new \Exception('File does not exist');
        }

        $fp = @fopen('ssh2.sftp://' . intval($this->sftpResource) . $remoteFilePath, 'r');
        if (false === $fp) {
            throw new \Exception('failed to open remote file');
        }

        $content = stream_get_contents($fp);
        if (false === $content) {
            throw new \Exception('failed to read file content');
        }

        return $content;
    }

    public function isFileExists($filePath)
    {
        if (!$this->isLoggedIn()) {
            throw new \Exception('you need to login first');
        }

        $fileInfo = ssh2_sftp_stat($this->sftpResource, $filePath);

        if (false === $fileInfo) {
            return false;
        }
        return true;
    }

    public function put($remoteFile, $content)
    {
        if (!$this->isLoggedIn()) {
            throw new \Exception('you need to login first');
        }

        $fp = @fopen('ssh2.sftp://' . intval($this->sftpResource) . $remoteFile, 'w');
        if (false === $fp) {
            throw new \Exception('failed to open remote file');
        }

        $writeResult = fwrite($fp, $content);
        if (false === $writeResult) {
            throw new \Exception('failed to write file content');
        }
    }

    public function __destruct()
    {
        if (!empty($this->sftpResource)) {
            $this->sftpResource = null;
        }

        if (!empty($this->connection)) {
            ssh2_disconnect($this->connection);
        }
    }
}
