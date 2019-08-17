<?php

class DATABASE
{
  private $mysql;
  private $sql;

  private $host = 'localhost';
  private $user = 'root';
  private $password = '';
  private $db_name = 'bad_day_rkn';

  function __construct() {
		$this->mysql = mysqli_connect($this->host, $this->user, $this->password, $this->db_name) or die('Database connection error');
		mysqli_set_charset($this->mysql, 'utf8');
	}

  static function getInstance() {
  if (self::$_instance === null) {
  	self::$_instance = new self();
  }
  return self::$_instance;
  }

    // main functions

  public function add_msg($link, $text, $created, $lifetime)
  {
    $text = quotemeta($text);
    $patterns = array('/</','/>/');
    $text = preg_replace($patterns, '', $text);
    $sql = "INSERT INTO `messages` (`link`, `text`, `created`, `lifetime`) VALUES ('$link', '$text', '$created', '$lifetime')";
    $result = mysqli_query($this->mysql, $sql);
  }

  public function load_msg($link)
  {
    $sql = "SELECT * FROM `messages` WHERE `link` LIKE '$link'";
    $result = mysqli_query($this->mysql, $sql);
    $data = mysqli_fetch_assoc($result);

    return $data['text'];
  }

  public function delete_msg($link)
  {
    $sql = "DELETE FROM `messages` WHERE `link` = '$link'";
    $result = mysqli_query($this->mysql, $sql);
  }

  public function load_all()
  {
    $sql = "SELECT * FROM `messages`";
    $result = mysqli_query($this->mysql, $sql);

    // v1.3: check table emptyness
    $test = mysqli_fetch_assoc($result);
    if ( empty($test) ) { return null; }

    while ($row = mysqli_fetch_assoc($result))
    {
        $data[] = $row;
    }
    $data = array_reverse($data);

    return $data;
  }
}

?>
