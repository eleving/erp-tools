<?php

declare(strict_types=1);

namespace Common\Tool;

/**
 * Simple FTP client class.
 *
 * Contains methods to:
 *  - create connection (ftp, ssl-ftp)
 *  - close connection
 *  - get connection
 *  - login
 *  - upload file from string
 *  - upload file from path
 *  - get the current directory name
 *  - change to the parent directory
 *  - change the current directory
 *  - remove a directory
 *  - create a directory
 *  - __call method to forward to native FTP functions.
 *    Example: "$ftpSimpleClient->delete($filePath)" will execute "ftp_delete($this->conn, $filePath)" function
 *
 * Usage example:
 *      $ftp = new \Common\Tool\FtpSimpleClient();
 *      $ftp->createConnection($host, $ssl, $port)->login($user, $pass)->chdir($directory);
 *      $ftp->putFromPath($filePath);
 *      $ftp->delete(basename($filePath));
 *      $ftp->closeConnection();
 */
class FtpSimpleClient
{

    /**
     * @var string $error Default exception error message mask
     */
    protected string $error = 'FTP Error: %s!';

    /**
     * @var resource $conn Connection with the server
     */
    protected $conn;

    /**
     * Class constructor.
     *
     * @access public
     * @param boolean $ignore_user_abort Ignore user abort, true by default
     * @throws \Exception If ftp extension is not loaded.
     */
    public function __construct(bool $ignore_user_abort = true)
    {
        if (!extension_loaded('ftp')) {
            throw new \Exception(sprintf($this->error, 'FTP extension is not loaded'));
        }

        ignore_user_abort($ignore_user_abort);
    }

    /**
     * Class destructor.
     *
     * @access public
     * @return void
     */
    public function __destruct()
    {
        $this->closeConnection();
    }

    /**
     * Overwrite maximum execution time limit.
     *
     * @access public
     * @param mixed $time Max execution time, unlimited by default
     * @return \Common\Tool\FtpSimpleClient
     */
    public function setMaxExecutionTimeLimit($time = 0)
    {
        if (null !== $time) {
            set_time_limit($time);
        }

        return $this;
    }

    /**
     * Overwrite maximum memory limit.
     *
     * @access public
     * @param mixed $memory
     * @return \Common\Tool\FtpSimpleClient
     */
    public function setMaxMemoryLimit($memory = null)
    {
        if (null !== $memory) {
            ini_set('memory_limit', $memory);
        }

        return $this;
    }

    /**
     * Opens a FTP or SSL-FTP connection.
     * Sets up stream resource on success or FALSE on error.
     *
     * @access public
     * @param string $host
     * @param boolean $ssl
     * @param int $port
     * @param int $timeout
     * @return \Common\Tool\FtpSimpleClient
     * @throws \Exception on failure
     */
    public function createConnection($host, $ssl = false, $port = 21, $timeout = 90)
    {
        if ($ssl === true) {
            $this->conn = ftp_ssl_connect($host, $port, $timeout);
        } else {
            $this->conn = ftp_connect($host, $port, $timeout);
        }

        if (!$this->conn) {
            throw new \Exception(sprintf($this->error, 'Can not connect'));
        }

        return $this;
    }

    /**
     * Close the active FTP or SSL-FTP connection.
     *
     * @access public
     */
    public function closeConnection()
    {
        if ($this->conn) {
            ftp_close($this->conn);
        }
    }

    /**
     * Get current active FTP or SSL-FTP connection.
     *
     * @access public
     * @return $conn resource
     */
    public function getConnection()
    {
        return $this->conn;
    }

    /**
     * Log in to an FTP connection
     *
     * @access public
     * @param string $username
     * @param string $password
     * @return \Common\Tool\FtpSimpleClient
     * @throws \Exception on failure
     */
    public function login($username = 'anonymous', $password = '')
    {
        $result = ftp_login($this->conn, $username, $password);
        if ($result === false) {
            throw new \Exception(sprintf($this->error, 'Login incorrect'));
        }

        return $this;
    }

    /**
     * Uploads a file to the server from a string
     *
     * @access public
     * @param string $remote_file
     * @param string $content
     * @return \Common\Tool\FtpSimpleClient
     * @throws \Exception on failure
     */
    public function putFromString($remote_file, $content)
    {
        $handle = fopen('php://temp', 'w');
        fwrite($handle, $content);
        rewind($handle);
        if (@ftp_fput($this->conn, $remote_file, $handle, FTP_BINARY)) {
            return $this;
        }

        throw new \Exception(sprintf($this->error,
            'Unable to put the file "' . $remote_file . '"'
        ));
    }

    /**
     * Uploads a file to the server
     *
     * @access public
     * @param string $local_file
     * @return \Common\Tool\FtpSimpleClient
     * @throws \Exception on failure
     */
    public function putFromPath($local_file)
    {
        $remote_file = basename($local_file);
        $handle = fopen($local_file, 'r');
        if (@ftp_fput($this->conn, $remote_file, $handle, FTP_BINARY)) {
            rewind($handle);
            return $this;
        }

        throw new \Exception(sprintf($this->error,
            'Unable to put the remote file from the local file "' . $local_file . '"'
        ));
    }

    /**
     * Get the current directory name
     *
     * @access public
     * @return string $result Current directory name
     * @throws \Exception on failure
     */
    public function pwd()
    {
        $result = ftp_pwd($this->conn);
        if ($result === false) {
            throw new \Exception(sprintf($this->error,
                'Unable to resolve the current directory'
            ));
        }

        return $result;
    }

    /**
     * Changes to the parent directory
     *
     * @access public
     * @return \Common\Tool\FtpSimpleClient
     * @throws \Exception on failure
     */
    public function cdup()
    {
        $result = ftp_cdup($this->conn);
        if ($result === false) {
            throw new \Exception(sprintf($this->error,
                'Unable to get parent folder'
            ));
        }

        return $this;
    }

    /**
     * Changes the current directory
     *
     * @access public
     * @param string $directory
     * @return \Common\Tool\FtpSimpleClient
     * @throws \Exception on failure
     */
    public function chdir($directory)
    {
        $result = @ftp_chdir($this->conn, $directory);
        if ($result === false) {
            throw new \Exception(sprintf($this->error,
                'Unable to change the current directory'
            ));
        }

        return $this;
    }

    /**
     * Removes a directory
     *
     * @access public
     * @param string $directory Directory
     * @return \Common\Tool\FtpSimpleClient
     * @throws \Exception on failure
     */
    public function rmdir($directory)
    {
        $result = ftp_rmdir($this->conn, $directory);
        if ($result === false) {
            throw new \Exception(sprintf($this->error,
                'Unable to remove directory'
            ));
        }

        return $this;
    }

    /**
     * Creates a directory
     *
     * @access public
     * @param string $directory Directory
     * @return string Newly created directory name
     * @throws \Exception on failure
     */
    public function mkdir($directory)
    {
        $result = ftp_mkdir($this->conn, $directory);
        if ($result === false) {
            throw new \Exception(sprintf($this->error,
                'Unable to create directory'
            ));
        }

        return $result;
    }

    /**
     * Check if a directory exist.
     *
     * @access public
     * @param string $directory
     * @return boolean
     * @throws \Exception on failure
     */
    public function isDir($directory)
    {
        $result = false;

        // Get current dir
        $pwd = ftp_pwd($this->conn);
        if ($pwd === false) {
            throw new \Exception(sprintf($this->error,
                'Unable to resolve the current directory'
            ));
        }

        // Check if needed directory exists
        if (@ftp_chdir($this->conn, $directory)) {
            $result = true;
        }

        // Set up back currnet directory
        ftp_chdir($this->conn, $pwd);

        return $result;
    }

    /**
     * Forward the method call to FTP functions.
     * Note: set up function name without native "ftp_" prefix
     *
     * @access public
     * @param string $function Function name
     * @param array $arguments
     * @return mixed Return value of the callback
     * @throws \Exception When the function is not valid
     */
    public function __call($function, array $arguments)
    {
        $function = 'ftp_' . $function;

        if (function_exists($function)) {
            array_unshift($arguments, $this->conn);
            return call_user_func_array($function, $arguments);
        }

        throw new \Exception(sprintf($this->error,
            "{$function} is not a valid FTP function"
        ));
    }

}
