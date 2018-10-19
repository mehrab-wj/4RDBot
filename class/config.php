<?php

class DatabaseConnection {
  public $host = "localhost";
  public $username = "username";
  public $password = "password";
  public $dbname = "database_name";
  public $con;
  public $select_db;
  public $from_id = 0;
  function __construct($from_id = 0) {
    $this->con = mysqli_connect($this->host, $this->username, $this->password);
    if (!$this->con){
        die("Database Connection Failed :( <br>" . mysqli_error($this->con));
    }
    $this->select_db = mysqli_select_db($this->con, $this->dbname);
    if (!$this->select_db){
        die("Database Selection Failed :! <br>" . mysqli_error($this->con));
    }
    mysqli_query($this->con,"SET NAMES 'utf8mb4'");
    mysqli_query($this->con,"SET CHARACTER SET 'utf8mb4'");
    mysqli_query($this->con,"SET character_set_connection = 'utf8mb4'");
    $this->from_id = $from_id;
  }
  function secure_str($str) { return mysqli_real_escape_string($this->con,strip_tags($str)); }

  function insert_order($order_id,$trans_id,$amount,$order_count ,$post_link,$order_type,$for_user = 0) {
    $query = mysqli_query($this->con,"INSERT INTO `orders` (`id`, `amount`, `order_id`, `jap_order_id`, `order_count`, `post_link`, `order_type`, `trans_id`,`for_user`, `payment_status`)
    VALUES (NULL, '$amount', '$order_id', '0', '$order_count', '$post_link', '$order_type', '$trans_id','$for_user', 'waiting'); ");
  }
  function update_user_field($user_id,$field,$value) {
    mysqli_query($this->con,"UPDATE `users` SET `$field` = '$value' WHERE `users`.`user_id` = '$user_id'; ");
    return true;
  }

  function get_option($key,$value = "not set") {
    $search_query = mysqli_query($this->con,"SELECT * FROM `options` WHERE `tag` = '$key'");
    if (mysqli_num_rows($search_query) >= 1) { return mysqli_fetch_assoc($search_query)['value']; }
    else { return $value; }
  }
  function set_option($tag,$value = 'NULL') {
    $search_query = mysqli_query($this->con,"SELECT * FROM `options` WHERE `tag` = '$tag'");
    if (mysqli_num_rows($search_query) == 0) {
      $insert_query = mysqli_query($this->con,"INSERT INTO `options` (`id`,`tag`,`value`) VALUES (NULL,'$tag','$value')");
    }
    else {
      $edit_query = mysqli_query($this->con,"UPDATE `options` SET `value` = '$value' WHERE `options`.`tag` = '$tag'; ");
    }
  }

}

 ?>
