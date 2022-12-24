<?php
    
    require_once("Models/ProductTrait.php");
    require_once("Models/CategoryTrait.php");
    require_once("Models/CustomerTrait.php");
    require_once("Models/BlogTrait.php");
    class Home extends Controllers{
        use ProductTrait, CategoryTrait, CustomerTrait,BlogTrait;
        public function __construct(){
            session_start();
            parent::__construct();
        }

        public function home(){
            $company = getCompanyInfo();
            $data['page_tag'] = $company['name'];
            $data['page_title'] = $company['name'];
            $data['slider'] = $this->getRecCategoriesT(6);
            $data['category1'] = $this->getCategoriesShowT("4,5,6");
            $data['category2'] = $this->getCategoriesShowT("7,8,9");
            $data['products'] = $this->getProductsT(8);
            $data['popProducts'] = $this->getPopularProductsT(4);
            $data['recPosts'] = $this->getRecentPostsT(3);
            $data['page_name'] = "home";
            $this->views->getView($this,"home",$data);
        }

    }
?>