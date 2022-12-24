<?php 
    class PostModel extends Mysql{
        private $intIdCategory;
        private $intIdSubCategory;
        private $intIdArticle;
        private $strReference;
		private $strName;
        private $strDescription;
		private $intStatus;
        private $strRoute;
        private $strPicture;

        public function __construct(){
            parent::__construct();
        }
        /*************************Article methods*******************************/
        public function insertArticle(int $intCategory,int $intSubCategory,string $strPicture,string $strName,string $strDescription,int $intStatus,string $route){
            
            $this->intIdCategory = $intCategory;
            $this->intIdSubCategory = $intSubCategory;
			$this->strName = $strName;
            $this->strDescription = $strDescription;
            $this->strPicture = $strPicture;
			$this->intStatus = $intStatus;
			$this->strRoute = $route;

			$return = 0;
			$sql = "SELECT * FROM article WHERE name = '$this->strName'";
			$request = $this->select_all($sql);

			if(empty($request)){ 			
				$query_insert  = "INSERT INTO article(categoryid,subcategoryid,name,picture,description,status,route) VALUES(?,?,?,?,?,?,?)";
	        	$arrData = array($this->intIdCategory,$this->intIdSubCategory,$this->strName,$this->strPicture,$this->strDescription,$this->intStatus,$this->strRoute);
	        	$request_insert = $this->insert($query_insert,$arrData);
	        	$return = $request_insert;
			}else{
				$return = "exist";
			}
	        return $return;
		}
        public function updateArticle(int $idArticle, int $intCategory, int $intSubCategory, string $strPicture, string $strName, string $strDescription,int $intStatus,string $route){
            $this->intIdArticle = $idArticle;
            $this->intIdCategory = $intCategory;
            $this->intIdSubCategory = $intSubCategory;
			$this->strName = $strName;
            $this->strDescription = $strDescription;
            $this->strPicture = $strPicture;
			$this->intStatus = $intStatus;
			$this->strRoute = $route;

			$sql = "SELECT * FROM article WHERE name = '{$this->strName}' AND idarticle != $this->intIdArticle";
			$request = $this->select_all($sql);

			if(empty($request)){
                $sql = "UPDATE article SET categoryid=?,subcategoryid=?,name=?,picture=?,description=?,status=?,route=?,date_updated=NOW() WHERE idarticle = $this->intIdArticle";
                $arrData = array($this->intIdCategory,$this->intIdSubCategory,$this->strName,$this->strPicture,$this->strDescription,$this->intStatus,$this->strRoute);
				$request = $this->update($sql,$arrData);
			}else{
				$request = "exist";
			}
			return $request;
		}
        public function deleteArticle($id){
            $this->intIdArticle = $id;
            $sql = "DELETE FROM article WHERE idarticle = $this->intIdArticle";
            $request = $this->delete($sql);
            return $request;
        }
        public function selectArticles(){
            $sql = "SELECT 
                a.idarticle,
                a.categoryid,
                a.subcategoryid,
                a.name,
                a.description,
                a.status,
                a.route,
                c.idcategory,
                c.name as category,
                s.idsubcategory,
                s.categoryid,
                s.name as subcategory,
                DATE_FORMAT(a.date_created, '%d/%m/%Y') as date,
                DATE_FORMAT(a.date_updated, '%d/%m/%Y') as dateupdated
            FROM article a
            INNER JOIN blogcategory c, blogsubcategory s
            WHERE c.idcategory = a.categoryid AND c.idcategory = s.categoryid AND a.subcategoryid = s.idsubcategory
            ORDER BY a.idarticle DESC
            ";
            $request = $this->select_all($sql);
            return $request;
        }
        public function selectArticle($id){
            $this->intIdArticle = $id;
            $sql = "SELECT 
            a.idarticle,
            a.categoryid,
            a.subcategoryid,
            a.name,
            a.picture,
            a.description,
            a.status,
            a.route,
            c.idcategory,
            c.name as category,
            s.idsubcategory,
            s.categoryid,
            s.name as subcategory,
            DATE_FORMAT(a.date_created, '%d/%m/%Y') as date,
            DATE_FORMAT(a.date_updated, '%d/%m/%Y') as dateupdated
            FROM article a
            INNER JOIN blogcategory c, blogsubcategory s
            WHERE c.idcategory = a.categoryid AND c.idcategory = s.categoryid AND a.subcategoryid = s.idsubcategory
            AND a.idarticle = $this->intIdArticle";

            $request = $this->select($sql);
            return $request;
        }
        public function search($search){
            $sql="SELECT 
            a.idarticle,
            a.categoryid,
            a.subcategoryid,
            a.name,
            a.description,
            a.status,
            a.route,
            c.idcategory,
            c.name as category,
            s.idsubcategory,
            s.categoryid,
            s.name as subcategory,
            DATE_FORMAT(a.date_created, '%d/%m/%Y') as date,
            DATE_FORMAT(a.date_updated, '%d/%m/%Y') as dateupdated
            FROM article a
            INNER JOIN blogcategory c, blogsubcategory s
            WHERE c.idcategory = a.categoryid AND c.idcategory = s.categoryid AND a.subcategoryid = s.idsubcategory AND
            a.name LIKE  '%$search%' || c.idcategory = a.categoryid AND c.idcategory = s.categoryid AND a.subcategoryid = s.idsubcategory AND
            c.name LIKE  '%$search%' || c.idcategory = a.categoryid AND c.idcategory = s.categoryid AND a.subcategoryid = s.idsubcategory AND
            s.name LIKE '%$search%'
            ";
            $request = $this->select_all($sql);
            return $request;
        }
        public function sort($sort){
            $option="DESC";
            if($sort == 2){
                $option = " ASC"; 
            }
            $sql="SELECT 
            a.idarticle,
            a.categoryid,
            a.subcategoryid,
            a.name,
            a.description,
            a.status,
            a.route,
            c.idcategory,
            c.name as category,
            s.idsubcategory,
            s.categoryid,
            s.name as subcategory,
            DATE_FORMAT(a.date_created, '%d/%m/%Y') as date,
            DATE_FORMAT(a.date_updated, '%d/%m/%Y') as dateupdated
            FROM article a
            INNER JOIN blogcategory c, blogsubcategory s
            WHERE c.idcategory = a.categoryid AND c.idcategory = s.categoryid AND a.subcategoryid = s.idsubcategory ORDER BY a.idarticle $option";
            $request = $this->select_all($sql);
            return $request;
        }
        /*************************Category methods*******************************/
        public function insertCategory(string $strName, string $strRoute){

			$this->strName = $strName;
			$this->strRoute = $strRoute;
			$return = 0;

			$sql = "SELECT * FROM blogcategory WHERE 
					name = '{$this->strName}'";
			$request = $this->select_all($sql);

			if(empty($request)){ 
				$query_insert  = "INSERT INTO blogcategory(name,route) VALUES(?,?)";
	        	$arrData = array($this->strName,$this->strRoute);
	        	$request_insert = $this->insert($query_insert,$arrData);
	        	$return = $request_insert;
			}else{
				$return = "exist";
			}
	        return $return;
		}
        public function updateCategory(int $intIdCategory,string $strName,string $strRoute){
            $this->intIdCategory = $intIdCategory;
            $this->strName = $strName;

			$sql = "SELECT * FROM blogcategory WHERE name = '{$this->strName}' AND idcategory != $this->intIdCategory";
			$request = $this->select_all($sql);

			if(empty($request)){

                $sql = "UPDATE blogcategory SET name=?, route=? WHERE idcategory = $this->intIdCategory";
                $arrData = array($this->strName,$this->strRoute);
				$request = $this->update($sql,$arrData);
			}else{
				$request = "exist";
			}
			return $request;
		
		}
        public function deleteCategory($id){
            $this->intIdCategory = $id;
            $sql = "SELECT * FROM blogsubcategory WHERE categoryid = $this->intIdCategory";
            $request = $this->select_all($sql);
            $return = "";
            if(empty($request)){
                $sql = "DELETE FROM blogcategory WHERE idcategory = $this->intIdCategory";
                $return = $this->delete($sql);
            }else{
                $return="exist";
            }
            return $return;
        }
        public function selectCategories(){
            $sql = "SELECT * FROM blogcategory ORDER BY idcategory DESC";       
            $request = $this->select_all($sql);
            return $request;
        }
        public function selectCategory($id){
            $this->intIdCategory = $id;
            $sql = "SELECT * FROM blogcategory WHERE idcategory = $this->intIdCategory";
            $request = $this->select($sql);
            return $request;
        }
        public function searchc($search){
            $sql = "SELECT * FROM blogcategory WHERE name LIKE '%$search%'";
            $request = $this->select_all($sql);
            return $request;
        }
        public function sortc($sort){
            $option="DESC";
            if($sort == 2){
                $option = " ASC"; 
            }
            $sql = "SELECT * FROM blogcategory ORDER BY idcategory $option ";
            $request = $this->select_all($sql);
            return $request;
        }
        /*************************SubCategory methods*******************************/
        public function insertSubCategory(int $intIdCategory ,string $strName,string $strRoute){
            $this->intIdCategory = $intIdCategory;
			$this->strName = $strName;
			$this->strRoute = $strRoute;

			$return = 0;
			$sql = "SELECT * FROM blogsubcategory WHERE name = '{$this->strName}' AND categoryid = $this->intIdCategory";
			$request = $this->select_all($sql);
			if(empty($request)){
				$query_insert  = "INSERT INTO blogsubcategory(categoryid,name,route) VALUES(?,?,?)";  
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

			$sql = "SELECT * FROM blogsubcategory WHERE name = '{$this->strName}' AND idsubcategory != $this->intIdSubCategory AND categoryid = $this->intIdCategory";
			$request = $this->select_all($sql);

			if(empty($request)){

                $sql = "UPDATE blogsubcategory SET categoryid=?,name=?, route=? WHERE idsubcategory = $this->intIdSubCategory";
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
            $sql="SELECT * FROM article WHERE subcategoryid = $id";
            $request = $this->select_all($sql);
            $return="";
            if(empty($request)){
                $sql = "DELETE FROM blogsubcategory WHERE idsubcategory = $this->intIdSubCategory";
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
                    FROM blogsubcategory s
                    INNER JOIN blogcategory c
                    ON c.idcategory = s.categoryid
                    ORDER BY idsubcategory DESC";       
            $request = $this->select_all($sql);
            return $request;
        }
        public function selectSubCategory($id){
            $this->intIdSubCategory = $id;
            $sql = "SELECT * FROM blogsubcategory WHERE idsubcategory = $this->intIdSubCategory";
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
                    FROM blogsubcategory s
                    INNER JOIN blogcategory c
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
                    FROM blogsubcategory s
                    INNER JOIN blogcategory c
                    ON c.idcategory = s.categoryid 
                    ORDER BY idsubcategory $option ";
            $request = $this->select_all($sql);
            return $request;
        }
    }
?>