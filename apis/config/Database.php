<?php
class Database {
    //Db Parameters
    private $hostName = 'localhost';
    private $dbname = 'weizledb';
    private $username = 'root';
    private $password = '';
    private $pdo;

    //Start Connection
    public function __construct(){
        $this->pdo = null;
        
        try{
            $this->pdo = new PDO("mysql:host=$this->hostName;dbname=$this->dbname;",

            $this->username, $this->password);

            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $e){
            echo "Error : " . $e->getMessage;
        }
    }

    //Dynamic Query Builder
    public function query($query,$parameters = '',$limit = ''){
        try{
            $run_query = $this->pdo->prepare($query);
            $run_query->setFetchMode(PDO:: FETCH_OBJ);
            if(!empty($limit)){
              foreach($limit as $key => $value){ 
                $run_query->bindValue("$key", $value, PDO::PARAM_INT); 
              }
            }
            if(!empty($parameters)) {
              foreach($parameters as $key => $value){ 
                $run_query->bindValue("$key", $value); 
              }
            }
            if ($run_query->execute()) {
              return $run_query;
            } else {
              return false;
            }
        }catch(PDOException $ex){
            echo "There Is Error In : ".$ex->getMessage();
        }
    }

    //Dynamic SELECT Query
    public function select($table,$parameters = "",$order = ""){
        $where = "";
        $order_by = "";
        if(!empty($order)){
        $order_by = "order by 1 $order";
        }
        if(!empty($parameters)){
          $i = 1;
          $where = "Where ";
          $values = [];
          foreach($parameters as $key => $value){
            if($i > 1){$where .= " AND ";}
            $where .= "$key=:$key";
            $values[":$key"] = $value;
            $i++;
          }
        }
        try{
          $run_query = $this->pdo->prepare("select * from $table $where $order_by");
          $run_query->setFetchMode(PDO:: FETCH_OBJ);
          if(!empty($parameters)){
            if($run_query->execute($values)){ return $run_query; }
          }else{
            if($run_query->execute()){ return $run_query; }
          }
        }catch(PDOException $ex){
          echo "There Is Error In : " . $ex->getMessage();
        }
    }
    
    //Dynamic INSERT Query
    public function insert($table,$parameters = ""){
        if(!empty($parameters)){
          $i = 1;
          $count = count($parameters);
          $fields = ""; $placeholders = ""; $values = [];
          foreach($parameters as $key => $value){
            $fields .= "$key";
            $placeholders .= ":$key";
            $values[":$key"] = $value;
            if($i < $count ) { $fields .=","; $placeholders .=","; } 
            $i++;
          }
        }
        try{
          $run_query = $this->pdo->prepare("insert into $table ($fields) values ($placeholders)");
          $run_query->setFetchMode(PDO:: FETCH_OBJ);
          if($run_query->execute($values)){ return $run_query; }
        }catch(PDOException $ex){
          echo "There Is Error In : " . $ex->getMessage();
        }
    }
    
    //Dynamic UPDATE Query
    public function update($table,$parameters,$where_p = ""){
        $i = 1;
        $count = count($parameters);
        $fields = ""; $values = [];
        foreach($parameters as $key => $value){
          $fields .= "$key=:$key";
          $values[":$key"] = $value;
          if($i < $count ) { $fields .=","; } 
          $i++;
        }
        $where = "";
        if(!empty($where_p)){
        $i = 1;
        $where = "where ";
        foreach($where_p as $key => $value){
          if($i > 1){$where .= " AND ";}
          $where .= "$key=:w_$key";
          $values[":w_$key"] = $value;
          $i++;
        }
        }
        try{
          $run_query = $this->pdo->prepare("update $table set $fields $where");
          $run_query->setFetchMode(PDO:: FETCH_OBJ);
          if($run_query->execute($values)){ return $run_query; }
        }catch(PDOException $ex){
          echo "There Is Error In : " . $ex->getMessage();
        }
    }
    
    //Dynamic DELETE Query
    public function delete($table,$parameters=''){
        $i = 1;
        $where = "";
        $values = [];
        if(!empty($parameters)){
            $i = 1;
            $where = "where ";
            foreach($parameters as $key => $value){
            if($i > 1){$where .= " AND ";}
            $where .= "$key=:$key";
            $values[":$key"] = $value;
            $i++;
            }
        }
        try{
          $run_query = $this->pdo->prepare("delete from $table $where");
          $run_query->setFetchMode(PDO:: FETCH_OBJ);
          if(!empty($parameters)){
          if($run_query->execute($values)){ return $run_query; }
          }else{
            if($run_query->execute()){ return $run_query; }
          }
        }catch(PDOException $ex){
          echo "There Is Error In : " . $ex->getMessage();
        }
    }

    //Dynamic COUNT Query
    public function count($table,$parameters = ""){
        $where = "";
        if(!empty($parameters)){
          $i = 1;
          $where = "Where ";
          $values = [];
          foreach($parameters as $key => $value){
            if($i > 1){$where .= " AND ";}
            $where .= "$key=:$key";
            $values[":$key"] = $value;
            $i++;
          }
        }
        try{
          $run_query = $this->pdo->prepare("select * from $table $where");
          $run_query->setFetchMode(PDO:: FETCH_OBJ);
          if(!empty($parameters)){
          $run_query->execute($values);
          }else{ $run_query->execute(); }
          return $run_query->rowCount();
        }catch(PDOException $ex){
          echo "There Is Error In : " . $ex->getMessage();
        }
    }

}
?>