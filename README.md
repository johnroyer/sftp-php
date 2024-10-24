# Introduction

SFTP library use ssh2 libary connect to target server. This PHP library simulate command `sftp`.

# Example

use `user` as `pass`，connect to `my.ssh.server` on port `22`：

```php
$sftp = new \Zeroplex\Sftp\Sftp('my.ssh.server');
$sftp->login('user', 'pass');
```

download file `tmp/sftp.log`：

```php
$fileContent = $sftp->get('tmp/sftp.log');
```

upload file `avatar.png` to remote `tmp/avatar.png`:

```php
$fileContent = file_get_contents('avatar.png');

$sftp->put('tmp/avatar.png', $fileContent);
```