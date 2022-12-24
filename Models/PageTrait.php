<?php
    require_once("Libraries/Core/Mysql.php");
    trait PageTrait{
        private $con;

        public function getPage(int $id){
            $this->con = new Mysql();
            $sql = "SELECT *,DATE_FORMAT(date_created, '%d/%m/%Y') as datecreated,DATE_FORMAT(date_updated, '%d/%m/%Y') as dateupdated  FROM pages WHERE id = $id";
            $request = $this->con->select($sql);
            return $request;
        }
    }
    
?>