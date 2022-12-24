<?php
    require_once("Libraries/Core/Mysql.php");
    trait ReviewTrait{
        private $con;
        private $intIdProduct;
        private $intIdPerson;
        private $intRate;
        private $intIdReview;
        private $strReview;

        public function getRate($id){
            $this->con = new Mysql();
            $sql = "SELECT 
            AVG(rate) as rate, 
            COUNT(rate) as total,
            sum(case when rate = 5 then 1 else 0 end) AS five,
            sum(case when rate = 4 then 1 else 0 end) AS four, 
            sum(case when rate = 3 then 1 else 0 end) AS three, 
            sum(case when rate = 2 then 1 else 0 end) AS two, 
            sum(case when rate = 1 then 1 else 0 end) AS one
            FROM productrate WHERE productid=$id";
            $request = $this->con->select($sql);

            if($request['rate']==null){
                $request['five'] = 0;
                $request['four'] = 0;
                $request['three'] = 0;
                $request['two'] = 0;
                $request['one'] = 0;
                $request['rate']=0;
            }
            return $request;
        }
        public function setReviewT($idProduct,$idUser,$strReview,$intRate){
            $this->con = new Mysql();
            $this->intIdProduct = $idProduct;
            $this->intIdPerson = $idUser;
            $this->strReview = $strReview;
            $this->intRate = $intRate;
            $return="";
            $sql = "SELECT * FROM productrate WHERE productid = $this->intIdProduct AND personid = $this->intIdPerson";
            $request = $this->con->select_all($sql);
            if(empty($request)){
                $sql = "INSERT INTO productrate(productid,personid,description,rate) VALUES(?,?,?,?)";
                $arrData = array($this->intIdProduct,$this->intIdPerson,$this->strReview,$this->intRate);
                $request = $this->con->insert($sql,$arrData);
                $return = $request;
            }else{
                $return = array("exists"=>"exists","id"=>$request[0]['id']);
            }
            return $return;
        }
        public function updateReviewT($intIdReview,$idProduct,$idUser,$strReview,$intRate){
            $this->con = new Mysql();
            $this->intIdProduct = $idProduct;
            $this->intIdPerson = $idUser;
            $this->strReview = $strReview;
            $this->intRate = $intRate;
            $this->intIdReview = $intIdReview;

            $sql = "UPDATE productrate SET productid=?,personid=?,description=?,rate=?, date_updated=NOW() WHERE id = $this->intIdReview";
            $arrData = array($this->intIdProduct,$this->intIdPerson,$this->strReview,$this->intRate);
            $request = $this->con->update($sql,$arrData);
            return $request;
        }
        public function getReviewsT($id,$option=null){
            if($option !=null){
                if($option == 1){
                    $option = " ORDER BY r.id DESC";
                }else{
                    $option = " AND r.rate >= 4";
                }
            }else{
                $option = " ORDER BY r.id DESC";
            }
            $this->con = new Mysql();
            $this->intIdProduct = $id;
            $sql = "SELECT 
                    r.id,
                    r.personid,
                    r.description,
                    r.rate,
                    p.idperson,
                    p.image,
                    p.firstname,
                    p.lastname,
                    DATE_FORMAT(r.date, '%d/%m/%Y') as date,
                    DATE_FORMAT(r.date_updated, '%d/%m/%Y') as dateupdated
                    FROM productrate r
                    INNER JOIN person p, product pr
                    WHERE p.idperson = r.personid AND pr.idproduct = r.productid AND pr.idproduct = $this->intIdProduct $option";
            $request = $this->con->select_all($sql);
            //dep($request);exit;
            return $request;
        }
        public function getSearchReviewsT($id,$search){
            $this->con = new Mysql();
            $this->intIdProduct = $id;
            $sql = "SELECT 
                    r.id,
                    r.personid,
                    r.description,
                    r.rate,
                    p.idperson,
                    p.image,
                    p.firstname,
                    p.lastname,
                    DATE_FORMAT(r.date, '%d/%m/%Y') as date,
                    DATE_FORMAT(r.date_updated, '%d/%m/%Y') as dateupdated
                    FROM productrate r
                    INNER JOIN person p, product pr
                    WHERE p.idperson = r.personid AND pr.idproduct = r.productid AND pr.idproduct = $this->intIdProduct AND p.firstname LIKE '%$search%'
                    OR  p.idperson = r.personid AND pr.idproduct = r.productid AND pr.idproduct = $this->intIdProduct AND p.lastname LIKE '%$search%' ORDER BY p.idperson DESC";
            $request = $this->con->select_all($sql);
            return $request;
        }
        public function getReviewT($id){
            $this->con = new Mysql();
            $this->intIdReview = $id;
            
            $sql = "SELECT * FROM productrate WHERE id = $this->intIdReview";
            $request = $this->con->select($sql);
            $request['productid'] = openssl_encrypt($request['productid'],METHOD,KEY);
            return $request;
        }
        public function deleteReviewT($id){
            $this->con = new Mysql();
            $this->intIdReview = $id;
            $sql ="DELETE FROM productrate WHERE id = $this->intIdReview";
            $request = $this->con->delete($sql);
            return $request;
        }
    }
    
?>