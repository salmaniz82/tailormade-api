<?php 


class DbConnection {
  // Hold the class instance.
  private static $instance = null;
  public $conn;
   
  // The db connection is established in the private constructor.
  private function __construct()
  {
      
      $this->conn = new mysqli(SERVER, USER, PASSWORD, DATABASE);


      if (mysqli_connect_errno())
      {
          echo "Failed to connect to MySQL: " . mysqli_connect_error();
      }

      $this->conn->set_charset("utf8");

      $this->setConnectionTimeZone();
        
  }
  
  public static function getInstance()
  {

    if(!self::$instance)
    {
    
      self::$instance = new DbConnection();


    }
   
    return self::$instance;

  }


  public function setConnectionTimeZone()
    {

        $now = new DateTime();
        $mins = $now->getOffset() / 60;
        $sgn = ($mins < 0 ? -1 : 1);
        $mins = abs($mins);
        $hrs = floor($mins / 60);
        $mins -= $hrs * 60;
        $offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);
        $this->conn->query("SET time_zone='{$offset}'");

    }
  





}