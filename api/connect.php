<?php

class Database
{
    private $host   = '';
    private $user   = '';
    private $pass   = '';
    private $dbname = '';

    private $dbh;
    private $error;
    private $stmt;
    private $data;

    private const SQL_CHK_USERNAME = 'SELECT * FROM `users` WHERE `username` = ?';
    private const SQL_INS_USER     = 'INSERT INTO users(username, password,image,imagetype) VALUES (?, ?,?,?)';
    private const SQL_INS_MSG      = "INSERT INTO comments(user_id, username,content) VALUES (?, ?,?)";
    private const SQL_INS_MSGFILE  = "INSERT INTO comments(user_id, username, content,file) VALUES (?,?, ?,?)";
    private const SQL_DEL_MSG      = "DELETE FROM `comments` WHERE (`id` = ? AND `user_id` = ?)";
    private const SQL_CHK_MSG      = "SELECT id,username,content,file FROM `comments` WHERE `id` = ?";
    private const SQL_CHK_OMSG     = "SELECT id,username,content,file FROM `comments`";
    private const SQL_UPD_TITLE    = "UPDATE `titles` SET  `title`= ? WHERE `id` = '3'";
    private const SQL_CHK_TITLE    = "SELECT * FROM `titles` WHERE `id` = '3'";

    public function __construct()
    {
        // Set DSN
        $dsn     = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
        );

        // Create a new PDO instanace
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } // Catch any errors
         catch (PDOException $e) {
            $this->error = $e->getMessage();
        }
    }

    // Prepare statement with query
    public function query($query)
    {
        switch ($query) {
            case 'SQL_CHK_USERNAME':
                $queue = $this::SQL_CHK_USERNAME;
                break;
            case 'SQL_INS_USER':
                $queue = $this::SQL_INS_USER;
                break;
            case 'SQL_INS_MSG':
                $queue = $this::SQL_INS_MSG;
                break;
            case 'SQL_INS_MSGFILE':
                $queue = $this::SQL_INS_MSGFILE;
                break;
            case 'SQL_DEL_MSG':
                $queue = $this::SQL_DEL_MSG;
                break;
            case 'SQL_CHK_MSG':
                $queue = $this::SQL_CHK_MSG;
                break;

            case 'SQL_CHK_OMSG':
                $queue = $this::SQL_CHK_OMSG;
                break;

            case 'SQL_UPD_TITLE':
                $queue = $this::SQL_UPD_TITLE;
                break;
            case 'SQL_CHK_TITLE':
                $queue = $this::SQL_CHK_TITLE;
                break;
            default:
                return;
        }
        $this->stmt = $this->dbh->prepare($queue);
    }

    // Bind values
    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    // Execute the prepared statement
    public function execute()
    {
        return $this->stmt->execute($this->data);
    }

    // Get result set as array of objects
    public function resultset()
    {
        $this->execute($this->data);
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Get single record as object
    public function single()
    {
        $this->execute($this->data);
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    // Get record row count
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

    // Returns the last inserted ID
    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }

    public function setData($data)
    {
        return $this->data = $data;
    }

    public function sqlExec($data, $sql)
    {
        $this->query($sql);
        $this->setData($data);

        return $this->resultset();

    }

}
