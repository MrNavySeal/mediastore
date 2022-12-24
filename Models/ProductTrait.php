<?php
    require_once("Libraries/Core/Mysql.php");
    trait ProductTrait{
        private $con;
        private $intIdProduct;

        public function getProductsSlider($cant){
            $this->con=new Mysql();
            $sql = "SELECT 
                p.idproduct,
                p.categoryid,
                p.subcategoryid,
                p.reference,
                p.name,
                p.description,
                p.price,
                p.discount,
                p.description,
                p.stock,
                p.status,
                p.route,
                c.idcategory,
                c.name as category,
                c.route as routec,
                s.idsubcategory,
                s.categoryid,
                s.name as subcategory
            FROM product p
            INNER JOIN category c, subcategory s
            WHERE c.idcategory = p.categoryid AND c.idcategory = s.categoryid AND p.subcategoryid = s.idsubcategory AND p.discount != 0 AND p.status =1
            ORDER BY p.idproduct DESC LIMIT $cant
            ";
            $request = $this->con->select_all($sql);
            if(count($request)> 0){
                for ($i=0; $i < count($request); $i++) { 
                    $request[$i]['priceDiscount'] =  formatNum($request[$i]['price']-($request[$i]['price']*($request[$i]['discount']/100)));
                    $request[$i]['price'] = formatNum($request[$i]['price']);
                    $idProduct = $request[$i]['idproduct'];
                    $sqlImg = "SELECT * FROM productimage WHERE productid = $idProduct";
                    $requestImg =  $this->con->select_all($sqlImg);
                    if(count($requestImg)>0){
                        $request[$i]['url'] = media()."/images/uploads/".$requestImg[0]['name'];
                        $request[$i]['image'] = $requestImg[0]['name'];
                    }else{
                        $request[$i]['image'] = media()."/images/uploads/image.png";
                    }
                }
            }
            return $request;
        }
        public function getProductsT($cant){
            if($cant !=""){
                $cant = " LIMIT $cant";
            }
            $this->con=new Mysql();
            $sql = "SELECT 
                p.idproduct,
                p.categoryid,
                p.subcategoryid,
                p.reference,
                p.name,
                p.description,
                p.price,
                p.discount,
                p.description,
                p.stock,
                p.status,
                p.route,
                c.idcategory,
                c.name as category,
                c.route as routec,
                s.idsubcategory,
                s.categoryid,
                s.name as subcategory
            FROM product p
            INNER JOIN category c, subcategory s
            WHERE c.idcategory = p.categoryid AND c.idcategory = s.categoryid AND p.subcategoryid = s.idsubcategory AND p.status = 1
            ORDER BY p.idproduct DESC $cant
            ";
            $request = $this->con->select_all($sql);
            if(count($request)> 0){
                for ($i=0; $i < count($request); $i++) { 

                    $request[$i]['priceDiscount'] =  $request[$i]['price']-($request[$i]['price']*($request[$i]['discount']*0.01));
                    $request[$i]['price'] = $request[$i]['price'];
                    $request[$i]['favorite'] = 0;

                    $idProduct = $request[$i]['idproduct'];

                    if(isset($_SESSION['login'])){
                        $idUser = $_SESSION['idUser'];
                        $sqlFavorite = "SELECT * FROM wishlist WHERE productid = $idProduct AND personid = $idUser";
                        $requestFavorite = $this->con->select($sqlFavorite);
                        if(!empty($requestFavorite)){
                            $request[$i]['favorite'] = $requestFavorite['status'];
                        }
                    }
                    $sqlRate = "SELECT AVG(rate) as rate FROM productrate WHERE productid = $idProduct";
                    $sqlImg = "SELECT * FROM productimage WHERE productid = $idProduct";
                    $requestImg =  $this->con->select_all($sqlImg);
                    $requestRate =  $this->con->select($sqlRate);
                    $request[$i]['rate'] = $requestRate['rate'];

                    if(count($requestImg)>0){
                        $request[$i]['url'] = media()."/images/uploads/".$requestImg[0]['name'];
                        $request[$i]['image'] = $requestImg[0]['name'];
                    }else{
                        $request[$i]['image'] = media()."/images/uploads/image.png";
                    }
                }
            }
            //dep($request);exit;
            return $request;
        }
        public function getProductsRandT($cant){
            if($cant !=""){
                $cant = " LIMIT $cant";
            }
            $this->con=new Mysql();
            $sql = "SELECT 
                p.idproduct,
                p.categoryid,
                p.subcategoryid,
                p.reference,
                p.name,
                p.description,
                p.price,
                p.discount,
                p.description,
                p.stock,
                p.status,
                p.route,
                c.idcategory,
                c.name as category,
                c.route as routec,
                s.idsubcategory,
                s.categoryid,
                s.name as subcategory
            FROM product p
            INNER JOIN category c, subcategory s
            WHERE c.idcategory = p.categoryid AND c.idcategory = s.categoryid AND p.subcategoryid = s.idsubcategory AND p.status = 1
            ORDER BY RAND() $cant
            ";
            $request = $this->con->select_all($sql);
            if(count($request)> 0){
                for ($i=0; $i < count($request); $i++) { 

                    $request[$i]['priceDiscount'] =  $request[$i]['price']-($request[$i]['price']*($request[$i]['discount']*0.01));
                    $request[$i]['price'] = $request[$i]['price'];
                    $request[$i]['favorite'] = 0;

                    $idProduct = $request[$i]['idproduct'];

                    if(isset($_SESSION['login'])){
                        $idUser = $_SESSION['idUser'];
                        $sqlFavorite = "SELECT * FROM wishlist WHERE productid = $idProduct AND personid = $idUser";
                        $requestFavorite = $this->con->select($sqlFavorite);
                        if(!empty($requestFavorite)){
                            $request[$i]['favorite'] = $requestFavorite['status'];
                        }
                    }
                    $sqlRate = "SELECT AVG(rate) as rate FROM productrate WHERE productid = $idProduct";
                    $sqlImg = "SELECT * FROM productimage WHERE productid = $idProduct";
                    $requestImg =  $this->con->select_all($sqlImg);
                    $requestRate =  $this->con->select($sqlRate);
                    $request[$i]['rate'] = $requestRate['rate'];

                    if(count($requestImg)>0){
                        $request[$i]['url'] = media()."/images/uploads/".$requestImg[0]['name'];
                        $request[$i]['image'] = $requestImg[0]['name'];
                    }else{
                        $request[$i]['image'] = media()."/images/uploads/image.png";
                    }
                }
            }
            //dep($request);exit;
            return $request;
        }
        public function getTotalProductsT(string $category,string $subcategory){
            $option="";
            $this->con=new Mysql();
            if($subcategory!=""){
                $option=" AND c.route = '$category' AND s.route = '$subcategory'";
            }else{
                $option=" AND c.route = '$category'";
            }
            $sql = "SELECT COUNT(p.idproduct) as total 
                    FROM product p
                    INNER JOIN category c, subcategory s
                    WHERE c.idcategory = p.categoryid AND c.idcategory = s.categoryid AND p.subcategoryid = s.idsubcategory AND p.status = 1 $option";
            $request = $this->con->select($sql);
            return $request;
        }
        public function getProductsCategoryT(string $category,string $subcategory){
            $option="";
            if($subcategory!=""){
                $option=" AND c.route = '$category' AND s.route = '$subcategory'";
            }else{
                $option=" AND c.route = '$category'";
            }

            $this->con=new Mysql();
            $sql = "SELECT 
                p.idproduct,
                p.categoryid,
                p.subcategoryid,
                p.reference,
                p.name,
                p.description,
                p.price,
                p.discount,
                p.description,
                p.stock,
                p.status,
                p.route,
                c.idcategory,
                c.name as category,
                c.route as routec,
                s.idsubcategory,
                s.categoryid,
                s.name as subcategory,
                s.route as routes
            FROM product p
            INNER JOIN category c, subcategory s
            WHERE c.idcategory = p.categoryid AND c.idcategory = s.categoryid AND p.subcategoryid = s.idsubcategory AND p.status = 1 $option
            ORDER BY p.idproduct
            ";
            $request = $this->con->select_all($sql);
            if(count($request)> 0){
                for ($i=0; $i < count($request); $i++) { 

                    $request[$i]['priceDiscount'] =  $request[$i]['price']-($request[$i]['price']*($request[$i]['discount']*0.01));
                    $request[$i]['price'] = $request[$i]['price'];
                    $request[$i]['favorite'] = 0;

                    $idProduct = $request[$i]['idproduct'];

                    if(isset($_SESSION['login'])){
                        $idUser = $_SESSION['idUser'];
                        $sqlFavorite = "SELECT * FROM wishlist WHERE productid = $idProduct AND personid = $idUser";
                        $requestFavorite = $this->con->select($sqlFavorite);
                        if(!empty($requestFavorite)){
                            $request[$i]['favorite'] = $requestFavorite['status'];
                        }
                    }
                    $sqlRate = "SELECT AVG(rate) as rate FROM productrate WHERE productid = $idProduct";
                    $sqlImg = "SELECT * FROM productimage WHERE productid = $idProduct";
                    $requestImg =  $this->con->select_all($sqlImg);
                    $requestRate =  $this->con->select($sqlRate);
                    $request[$i]['rate'] = $requestRate['rate'];

                    if(count($requestImg)>0){
                        $request[$i]['url'] = media()."/images/uploads/".$requestImg[0]['name'];
                        $request[$i]['image'] = $requestImg[0]['name'];
                    }else{
                        $request[$i]['image'] = media()."/images/uploads/image.png";
                    }
                }
            }
            //dep($request);exit;
            return $request;
        }
        public function getPopularProductsT($cant){
            if($cant !=""){
                $cant = " LIMIT $cant";
            }
            $this->con=new Mysql();
            $sql = "SELECT 
                p.idproduct,
                p.categoryid,
                p.subcategoryid,
                p.reference,
                p.name,
                p.description,
                p.price,
                p.discount,
                p.description,
                p.stock,
                p.status,
                p.route,
                c.idcategory,
                c.name as category,
                c.route as routec,
                s.idsubcategory,
                s.categoryid,
                s.name as subcategory
            FROM product p
            INNER JOIN category c, subcategory s
            WHERE c.idcategory = p.categoryid AND c.idcategory = s.categoryid AND p.subcategoryid = s.idsubcategory AND p.status = 1
            ORDER BY p.idproduct
            ";
            //dep($sql);exit;
            $request = $this->con->select_all($sql);
            $arrProducts = array();
            //dep($request);exit;
            if(count($request)){
                for ($i=0; $i < count($request); $i++) { 

                    $request[$i]['priceDiscount'] =  $request[$i]['price']-($request[$i]['price']*($request[$i]['discount']*0.01));
                    $request[$i]['price'] = $request[$i]['price'];
                    $request[$i]['favorite'] = 0;

                    $idProduct = $request[$i]['idproduct'];

                    if(isset($_SESSION['login'])){
                        $idUser = $_SESSION['idUser'];
                        $sqlFavorite = "SELECT * FROM wishlist WHERE productid = $idProduct AND personid = $idUser";
                        $requestFavorite = $this->con->select($sqlFavorite);
                        if(!empty($requestFavorite)){
                            $request[$i]['favorite'] = $requestFavorite['status'];
                        }
                    }
                    $sqlRate = "SELECT AVG(rate) as rate FROM productrate WHERE productid = $idProduct";
                    $sqlImg = "SELECT * FROM productimage WHERE productid = $idProduct";
                    $requestImg =  $this->con->select_all($sqlImg);
                    $requestRate =  $this->con->select($sqlRate);
                    $request[$i]['rate'] = $requestRate['rate'] !="" ? $requestRate['rate'] : 0;

                    if(count($requestImg)>0){
                        $request[$i]['url'] = media()."/images/uploads/".$requestImg[0]['name'];
                        $request[$i]['image'] = $requestImg[0]['name'];
                    }else{
                        $request[$i]['image'] = media()."/images/uploads/image.png";
                    }
                    if($request[$i]['rate']>=4){
                        array_push($arrProducts,$request[$i]);
                    }
                    
                }
            }
            return $arrProducts;
        }
        public function getProductT(int $idProduct){
            $this->con=new Mysql();
            $this->intIdProduct = $idProduct;
            $sql = "SELECT 
                p.idproduct,
                p.categoryid,
                p.subcategoryid,
                p.reference,
                p.name,
                p.shortdescription,
                p.description,
                p.price,
                p.discount,
                p.stock,
                p.status,
                p.route,
                c.idcategory,
                c.name as category,
                c.route as routec,
                s.idsubcategory,
                s.categoryid,
                s.name as subcategory
            FROM product p
            INNER JOIN category c, subcategory s
            WHERE c.idcategory = p.categoryid AND c.idcategory = s.categoryid AND p.subcategoryid = s.idsubcategory 
            AND p.idproduct = $this->intIdProduct";

            $request = $this->con->select($sql);
            $request['favorite'] = 0;

            if(isset($_SESSION['login'])){
                $idUser = $_SESSION['idUser'];
                $sqlFavorite = "SELECT * FROM wishlist WHERE productid = $this->intIdProduct AND personid = $idUser";
                $requestFavorite = $this->con->select($sqlFavorite);
                if(!empty($requestFavorite)){
                    $request['favorite'] = $requestFavorite['status'];
                }
            }

            $sqlRate = "SELECT AVG(rate) as rate, COUNT(rate) as total FROM productrate WHERE productid = $idProduct HAVING rate IS NOT NULL";
            $requestRate =  $this->con->select_all($sqlRate);
            $request['rate'] = $requestRate;

            $sqlImg = "SELECT * FROM productimage WHERE productid = $this->intIdProduct";
            $requestImg = $this->con->select_all($sqlImg);

            if(count($requestImg)){
                for ($i=0; $i < count($requestImg); $i++) { 
                    $request['image'][$i] = array("url"=>media()."/images/uploads/".$requestImg[$i]['name'],"name"=>$requestImg[$i]['name']);
                }
            }
            //dep($request);exit;
            return $request;
        }
        public function getProductPageT(string $route){
            $this->con=new Mysql();
            $sql = "SELECT 
                p.idproduct,
                p.categoryid,
                p.subcategoryid,
                p.reference,
                p.name,
                p.shortdescription,
                p.description,
                p.price,
                p.discount,
                p.stock,
                p.status,
                p.route,
                c.idcategory,
                c.name as category,
                c.route as routec,
                s.idsubcategory,
                s.categoryid,
                s.name as subcategory
            FROM product p
            INNER JOIN category c, subcategory s
            WHERE c.idcategory = p.categoryid AND c.idcategory = s.categoryid AND p.subcategoryid = s.idsubcategory AND p.status = 1
            AND p.route = '$route'";

            $request = $this->con->select($sql);
            if(!empty($request)){
                $request['priceDiscount'] =  $request['price']-($request['price']*($request['discount']*0.01));
                $request['price'] = $request['price'];
                $request['favorite'] = 0;
                $idProduct =$request['idproduct'];
    
                if(isset($_SESSION['login'])){
                    $idUser = $_SESSION['idUser'];
                    $sqlFavorite = "SELECT * FROM wishlist WHERE productid = $idProduct AND personid = $idUser";
                    $requestFavorite = $this->con->select($sqlFavorite);
                    if(!empty($requestFavorite)){
                        $request['favorite'] = $requestFavorite['status'];
                    }
                }
    
                $sqlRate = "SELECT AVG(rate) as rate, COUNT(rate) as total FROM productrate WHERE productid = $idProduct HAVING rate IS NOT NULL";
                $requestRate =  $this->con->select($sqlRate);
                //dep($requestRate);exit;
                if(!empty($requestRate)){
                    $request['rate'] = number_format($requestRate['rate'],1);
                    $request['reviews'] = $requestRate['total'];
                }else{
                    $request['rate'] = number_format(0,1);
                    $request['reviews'] = 0;
                }
                
                $sqlImg = "SELECT * FROM productimage WHERE productid = $idProduct";
                $requestImg = $this->con->select_all($sqlImg);
    
                if(count($requestImg)){
                    for ($i=0; $i < count($requestImg); $i++) { 
                        $request['image'][$i] = array("url"=>media()."/images/uploads/".$requestImg[$i]['name'],"name"=>$requestImg[$i]['name']);
                    }
                }
            }
            return $request;
        }
        public function getProductsFavorites($id){
            $this->con=new Mysql();
            $sql = "SELECT 
                p.idproduct,
                p.categoryid,
                p.subcategoryid,
                p.reference,
                p.name,
                p.description,
                p.price,
                p.discount,
                p.description,
                p.stock,
                p.status,
                p.route,
                c.idcategory,
                c.name as category,
                c.route as routec,
                s.idsubcategory,
                s.categoryid,
                s.name as subcategory
            FROM product p
            INNER JOIN category c, subcategory s, wishlist w
            WHERE c.idcategory = p.categoryid AND c.idcategory = s.categoryid AND p.subcategoryid = s.idsubcategory AND p.status = 1
            AND p.idproduct = w.productid AND w.personid = $id AND w.status = 1";
            $request = $this->con->select_all($sql);
            if(count($request)> 0){
                for ($i=0; $i < count($request); $i++) { 

                    $request[$i]['priceDiscount'] =  $request[$i]['price']-($request[$i]['price']*($request[$i]['discount']*0.01));
                    $request[$i]['price'] = $request[$i]['price'];
                    $request[$i]['favorite'] = 0;

                    $idProduct = $request[$i]['idproduct'];

                    $sqlImg = "SELECT * FROM productimage WHERE productid = $idProduct";
                    $requestImg =  $this->con->select_all($sqlImg);

                    if(count($requestImg)>0){
                        $request[$i]['url'] = media()."/images/uploads/".$requestImg[0]['name'];
                        $request[$i]['image'] = $requestImg[0]['name'];
                    }else{
                        $request[$i]['image'] = media()."/images/uploads/image.png";
                    }
                }
            }
            return $request;
        }
        public function addWishListT($idProduct,$idUser){
            $this->con = new Mysql();
            $sql = "SELECT * FROM wishlist WHERE productid = $idProduct AND personid = $idUser";
            $request = $this->con->select_all($sql);
            $return ="";
            if(empty($request)){
                $sql = "INSERT INTO wishlist (productid,personid,status) VALUE(?,?,?)";
                $array = array($idProduct,$idUser,1);
                $request = $this->con->insert($sql,$array);
                $return =$request;
            }else{
                $return ="exists";
            }
            return $return;
        }
        public function delWishListT($idProduct,$idUser){
            $this->con = new Mysql();
            $sql = "DELETE FROM wishlist WHERE productid=$idProduct AND personid = $idUser";
            $request = $this->con->delete($sql);
            return $request;
        }
        public function getProductsSearchT($search){
            $this->con=new Mysql();
            $sql = "SELECT 
                p.idproduct,
                p.categoryid,
                p.subcategoryid,
                p.reference,
                p.name,
                p.description,
                p.price,
                p.discount,
                p.description,
                p.stock,
                p.status,
                p.route,
                c.idcategory,
                c.name as category,
                c.route as routec,
                s.idsubcategory,
                s.categoryid,
                s.name as subcategory
            FROM product p
            INNER JOIN category c, subcategory s
            WHERE c.idcategory = p.categoryid AND c.idcategory = s.categoryid AND p.subcategoryid = s.idsubcategory AND
            p.name LIKE  '%$search%' AND p.status = 1 || c.idcategory = p.categoryid AND c.idcategory = s.categoryid AND p.subcategoryid = s.idsubcategory AND
            c.name LIKE  '%$search%' AND p.status = 1 || c.idcategory = p.categoryid AND c.idcategory = s.categoryid AND p.subcategoryid = s.idsubcategory AND
            s.name LIKE '%$search%' AND p.status = 1
            ";
            $request = $this->con->select_all($sql);
            if(count($request)> 0){
                for ($i=0; $i < count($request); $i++) { 

                    $request[$i]['priceDiscount'] =  $request[$i]['price']-($request[$i]['price']*($request[$i]['discount']*0.01));
                    $request[$i]['price'] = $request[$i]['price'];

                    $idProduct = $request[$i]['idproduct'];

                    $sqlRate = "SELECT AVG(rate) as rate FROM productrate WHERE productid = $idProduct";
                    $sqlImg = "SELECT * FROM productimage WHERE productid = $idProduct";
                    $requestImg =  $this->con->select_all($sqlImg);
                    $requestRate =  $this->con->select($sqlRate);
                    $request[$i]['rate'] = $requestRate['rate'];

                    if(count($requestImg)>0){
                        $request[$i]['url'] = media()."/images/uploads/".$requestImg[0]['name'];
                        $request[$i]['image'] = $requestImg[0]['name'];
                    }else{
                        $request[$i]['image'] = media()."/images/uploads/image.png";
                    }
                }
            }
            //dep($request);exit;
            return $request;
        }
    }
    
?>