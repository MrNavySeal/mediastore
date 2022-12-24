<?php
    require_once("Models/PageTrait.php");
    class About extends Controllers{
        use PageTrait;
        public function __construct(){
            session_start();
            parent::__construct();
        }

        public function about(){
            $company=getCompanyInfo();
            $data['page_tag'] = "Nosotros | ".$company['name'];
			$data['page_title'] = "Nosotros | ".$company['name'];
			$data['page_name'] = "nosotros";
            $data['page'] = $this->getPage(1);
            $this->views->getView($this,"about",$data);
        }
    }
?>