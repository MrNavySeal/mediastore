<?php 
    class CategoryModel extends Mysql{
        private $intIdCategory;
        private $intIdSubCategory;
		private $strName;
        private $strRoute;
        private $strPhoto;
        private $strDescription;

        public function __construct(){
            parent::__construct();
        }
        /*************************Category methods*******************************/
        public function insertCategory(string $photo,string $strName, string $strDescription, string $strRoute){

			$this->strName = $strName;
			$this->strRoute = $strRoute;
            $this->strDescription = $strDescription;
            $this->strPhoto = $photo;
			$return = 0;

			$sql = "SELECT * FROM category WHERE 
					name = '{$this->strName}'";
			$request = $this->select_all($sql);

			if(empty($request))
			{ 
				$query_insert  = "INSERT INTO category(picture,name,description,route) 
								  VALUES(?,?,?,?)";
	        	$arrData = array(
                    $this->strPhoto,
                    $this->strName,
                    $this->strDescription,
                    $this->strRoute
        		);
	        	$request_insert = $this->insert($query_insert,$arrData);
	        	$return = $request_insert;
			}else{
				$return = "exist";
			}
	        return $return;
		}
        public function updateCategory(int $intIdCategory,string $photo, string $strName, string $strDescription,string $strRoute){
            $this->intIdCategory = $intIdCategory;
            $this->strName = $strName;
            $this->strDescription = $strDescription;
			$this->strRoute = $strRoute;
            $this->strPhoto = $photo;
            

			$sql = "SELECT * FROM category WHERE name = '{$this->strName}' AND idcategory != $this->intIdCategory";
			$request = $this->select_all($sql);

			if(empty($request)){

                $sql = "UPDATE category SET picture=?, name=?,description=?, route=? WHERE idcategory = $this->intIdCategory";
                $arrData = array(
                    $this->strPhoto,
                    $this->strName,
                    $this->strDescription,
                    $this->strRoute
                );
				$request = $this->update($sql,$arrData);
			}else{
				$request = "exist";
			}
			return $request;
		
		}
        public function deleteCategory($id){
            $this->intIdCategory = $id;
            $sql = "SELECT * FROM subcategory WHERE categoryid = $this->intIdCategory";
            $request = $this->select_all($sql);
            $return = "";
            if(empty($request)){
                $sql = "DELETE FROM category WHERE idcategory = $this->intIdCategory";
                $return = $this->delete($sql);
            }else{
                $return="exist";
            }
            return $return;
        }
        public function selectCategories(){
            $sql = "SELECT * FROM category ORDER BY idcategory DESC";       
            $request = $this->select_all($sql);
            return $request;
        }
        public function selectCategory($id){
            $this->intIdCategory = $id;
            $sql = "SELECT * FROM category WHERE idcategory = $this->intIdCategory";
            $request = $this->select($sql);
            return $request;
        }
        public function search($search){
            $sql = "SELECT * FROM category WHERE name LIKE '%$search%'";
            $request = $this->select_all($sql);
            return $request;
        }
        public function sort($sort){
            $option="DESC";
            if($sort == 2){
                $option = " ASC"; 
            }
            $sql = "SELECT * FROM category ORDER BY idcategory $option ";
            $request = $this->select_all($sql);
            return $request;
        }
        /*************************SubCategory methods*******************************/
        public function insertSubCategory(int $intIdCategory ,string $strName,string $strRoute){
            $this->intIdCategory = $intIdCategory;
			$this->strName = $strName;
			$this->strRoute = $strRoute;

			$return = 0;
			$sql = "SELECT * FROM subcategory WHERE name = '{$this->strName}' AND categoryid = $this->intIdCategory";
			$request = $this->select_all($sql);
			if(empty($request)){
				$query_insert  = "INSERT INTO subcategory(categoryid,name,route) VALUES(?,?,?)";  
	        	$arrData = array(
                    $this->intIdCategory,
                    $this->strName,
                    $this->strRoute
        		);
	        	$request_insert = $this->insert($query_insert,$arrData);
	        	$return = $request_insert;
			}else{
				$return = "exist";
			}
	        return $return;
		}
        public function updateSubCategory(int $intIdSubCategory,int $intIdCategory, string $strName,string $strRoute){
            $this->intIdSubCategory = $intIdSubCategory;
            $this->intIdCategory = $intIdCategory;
            $this->strName = $strName;
			$this->strRoute = $strRoute;

			$sql = "SELECT * FROM subcategory WHERE name = '{$this->strName}' AND idsubcategory != $this->intIdSubCategory AND categoryid = $this->intIdCategory";
			$request = $this->select_all($sql);

			if(empty($request)){

                $sql = "UPDATE subcategory SET categoryid=?,name=?, route=? WHERE idsubcategory = $this->intIdSubCategory";
                $arrData = array(
                    $this->intIdCategory,
                    $this->strName,
                    $this->strRoute
                );
				$request = $this->update($sql,$arrData);
			}else{
				$request = "exist";
			}
			return $request;
		
		}
        public function deleteSubCategory($id){
            $this->intIdSubCategory = $id;
            $sql="SELECT * FROM product WHERE subcategoryid = $id";
            $request = $this->select_all($sql);
            $return="";
            if(empty($request)){
                $sql = "DELETE FROM subcategory WHERE idsubcategory = $this->intIdSubCategory";
                $request = $this->delete($sql);
                $return = $request;
            }else{
                $return ="exist";
            }
            return $return;
        }
        public function selectSubCategories(){
            $sql = "SELECT  
                    s.idsubcategory,
                    s.name,
                    s.categoryid,
                    c.idcategory,
                    c.name as category
                    FROM subcategory s
                    INNER JOIN category c
                    ON c.idcategory = s.categoryid
                    ORDER BY idsubcategory DESC";       
            $request = $this->select_all($sql);
            return $request;
        }
        public function selectSubCategory($id){
            $this->intIdSubCategory = $id;
            $sql = "SELECT * FROM subcategory WHERE idsubcategory = $this->intIdSubCategory";
            $request = $this->select($sql);
            return $request;
        }
        public function searchs($search){
            $sql = "SELECT  
                    s.idsubcategory,
                    s.name,
                    s.categoryid,
                    c.idcategory,
                    c.name as category
                    FROM subcategory s
                    INNER JOIN category c
                    ON c.idcategory = s.categoryid
                    WHERE s.name LIKE '%$search%' || c.name LIKE '%$search%'
                    ORDER BY idsubcategory DESC
                    ";
            $request = $this->select_all($sql);
            return $request;
        }
        public function sorts($sort){
            $option="DESC";
            if($sort == 2){
                $option = " ASC"; 
            }
            $sql = "SELECT  
                    s.idsubcategory,
                    s.name,
                    s.categoryid,
                    c.idcategory,
                    c.name as category
                    FROM subcategory s
                    INNER JOIN category c
                    ON c.idcategory = s.categoryid 
                    ORDER BY idsubcategory $option ";
            $request = $this->select_all($sql);
            return $request;
        }
    }
?>