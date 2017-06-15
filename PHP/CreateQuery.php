<?php 
class CreateQuery
{
    public $globalQuery='SELECT DISTINCT STRAIGHT_JOIN SQL_CALC_FOUND_ROWS';
    public $foundRows;

    private $placeholder;
    private $strSelect;
    private $strFrom;
    private $strJoin;
    private $strWhere;
    private $strBetween;
    private $strIn;
    private $strGroup; 
    private $strOrder;
    private $strLimit;



    public function getSelect(){
        return $this->strSelect;
    }
    public function getFrom(){
        return $this->strFrom;
    }
    public function getJoin(){
        return $this->strJoin;
    }
    public function getWhere(){
        return $this->strWhere;
    }
    public function getBetween(){
        return $this->strBetween;
    }
    public function getIn(){
        return $this->strIn;
    }
    public function getGroup(){
        return $this->strGroup;
    }

    public function getOrder(){
        return $this->strOrder;
    }

    public function getLimit(){
        return $this->strLimit;
    }

    public function getPlaceholder(){
        return $this->placeholder;
    }



    /**
     * @param  var, 
     * @return $this
     */
    public function select($var='*',$DISTINCT=true,$STRAIGHT_JOIN=true,$SQL_CALC_FOUND_ROWS=true){
        $str = $this->globalQuery;

        $str = $DISTINCT===false ? str_replace('DISTINCT', ' ', $str ) : $str;
        $str = $STRAIGHT_JOIN===false ? str_replace('STRAIGHT_JOIN', ' ', $str ) : $str;
        $str = $SQL_CALC_FOUND_ROWS===false ? str_replace('SQL_CALC_FOUND_ROWS', ' ', $str ) : $str;


        if (count($var)>0) {
            if (count($var)==1) {
                if (is_array($var)) {
                    $str .=' '.$var[0].' ';
                }elseif(is_string($var)){
                    $str .=' '.$var.' ';
                }
                
            }else{
                $i=0;
                foreach ($var as $col) {
                    $str .=' '.$col.' ';
                    $i++;
                    if ($i<count($var)) {
                        $str .=',';
                    }
                }
                unset($col);
            }
            
        }
        $this->globalQuery = $str;
        $this->strSelect = $str;
        return $this;
    }


    /**
     * @param  string
     * @return $this
     */
    public function from($table){
        $str = 'FROM `'.trim($table).'` ';
        $this->globalQuery .=$str;
        $this->strFrom = $str;
        return $this;
    }

    /**
     * @param  array
     * @return $this
     */
    public function leftJoin($var=[]){
        $str='';
        if (count($var)>0) {
            foreach ($var as $table => $params) {
                $str .= 'LEFT JOIN  '.$table.' ON '.$params.' ';
            }
            unset($params);
        }
        $this->globalQuery .=$str;
        $this->strJoin = $str;
        return $this;

    }

    /**
     * @param  array
     * @return $this
     */
    public function where($var=[]){
        $sign = '=';
        $cond = '';
        $data=[];
        if (count($var)==1) {
            $cond .= ' WHERE ';
            foreach ($var as $col => $value) {
                if (is_array($value)) {
                    $sign = $value[0];
                    $value= $value[1];
                }else{
                    $sign = '=';
                }
                if ($sign=='IS NULL' || $sign=='IS NOT NULL') {
                    $cond .=''.$col.' '.$sign.' ';
                }else{
                    $cond .=''.$col.' '.$sign.'?'/*.$col*/;
                    $data[]=$value;
                }
            }
        }
        unset($value);
        if (count($var)>1) {
            $i=0;
            $cond .= ' WHERE ';
            foreach ($var as $col => $value) {
                if (is_array($value)) {
                    $sign = $value[0];
                    $value= $value[1];
                }else{
                    $sign = '=';
                }
                if ($sign=='IS NULL' || $sign=='IS NOT NULL') {
                    $cond .=''.$col.' '.$sign.' ';
                }else{
                    $cond .=''.$col.' '.$sign.'?'/*.$col*/;
                    $data[]=$value;
                }
                
                $i++;
                if ($i<count($var)) {
                    $cond .=' AND ';
                }
            }
        }
        unset($var);
        unset($value);
        $this->placeholder = $data;
        $this->strWhere = $cond;
        $this->globalQuery .=$cond;
        return $this;
    }


    /**
     * @param  array
     * @return $this
     */
    public function between($var=[]){
        $str='';
        $cond = strlen($this->strWhere)>0 ? ' AND ' : ' WHERE ';
        foreach ($var as $table => $val) {
            $str .= $cond.' '.$table.' BETWEEN '.$val[0]. ' AND '.$val[1];
        }
        unset($var);
        unset($val);
        $this->strBetween = $str;
        $this->globalQuery .=$str;
        return $this;

    }

    /**
     * @param  array
     * @return $this
     */
    public function in($var=[]){
        $cond = strlen($this->strWhere)>0 ? ' AND ' : ' WHERE ';
        $cond = strlen($this->strBetween)>0 ? ' AND ' : ' WHERE ';
        $str = '';
            $s='';
            foreach ($var as $tab => $value) {
                foreach ($value as $index => $val) {
                    $s .=$val.','; 
                }
                unset($value);
                $s = substr($s, 0, -1);
                $str = $cond.$tab.' IN ('.$s.')';
            }   
            unset($var);
            unset($val);


        $this->globalQuery .=$str;
        $this->strIn = $str;
        return $this;

    }

    /**
     * @param  array
     * @return $this
     */
    public  function group($var=[]){
        $str = '';
        if(is_string($var)){
            $str = ' GROUP BY ' . $var;
        }
        if(is_array($var)){
            $str = ' GROUP BY ';
            foreach ($var as $index => $val) {
                $str .=$val.',';
            }
            unset($var);
            unset($val);
            $str = substr($str, 0, -1);
        }

        $this->globalQuery .=$str;
        $this->strGroup = $str;
        return $this;

    }


    /**
     * @param  array, type order
     * @return $this
     */
    public function order($var=[],$type="ASC"){
        $str = '';
        if(is_string($var)){
            $str = ' ORDER BY ' . $var . ' ' .$type;
        }
        if(is_array($var)){
            $str = ' ORDER BY ';
            foreach ($var as $index => $val) {
                $str .=$val.',';
            }
            unset($var);
            unset($val);
            $str = substr($str, 0, -1);
            $str .=' '.$type;
        }

        $this->globalQuery .=$str;
        $this->strOrder = $str;
        return $this;

    }



    /**
     * @param  integer $limit,$page
     * @return $this
     */
    public function limit($limit=5,$page=1){

        $this->limitRequest = $limit;
        $begin_page = ($limit*$page)-$limit;
        if($begin_page<0)
            $begin_page = 0;
        $limit = " LIMIT ".$begin_page.", ".$limit;
        $this->globalQuery .=$limit;
        $this->strLimit = $limit;
        return $this;
    }

    /**
     * @param  integer $other_count,$limit
     * @return $count_page
     */
    public function getCountPageFilter($other_count,$limit=1){
        $count_page =ceil( $other_count / $limit);
        return $count_page;
    }

    /**
     * @param  $count $activ
     * @return $pagination
     */
    public function getPagination($count,$activ,$host='',$type='link'){

       //$count = $this->getCountPageFilter($count,$this->limitRequest);

        if ($type=='link' && $host!=='') {
            $request = $this->pagination->createPaginationLink($count,$activ,$host);
        }elseif($type=='ajax'){
             $request =$this->pagination->createPaginationAjax($count,$activ);
        }

        return $request;
    }



    private function clear(){
            $this->clearQuery();
            $this->clearSelect();
            $this->clearFrom();
            $this->clearJoin();
            $this->clearWhere();
            $this->clearBetween();
            $this->clearIn();
            $this->clearGroup();
            $this->clearOrder();
            $this->clearLimit();
            $this->clearPlacholder();
    }


    private function clearQuery(){
        $this->globalQuery='SELECT DISTINCT STRAIGHT_JOIN SQL_CALC_FOUND_ROWS';
        //unset($this->globalQuery);
        return 1;
    }

    private function clearSelect(){
         $this->strSelect='';
        unset($this->strSelect); 
        return 1;
    }

    private function clearFrom(){
        $this->strFrom='';
        unset($this->strFrom);
        return 1;
    }

    private function clearJoin(){
        $this->strJoin='';
         unset($this->strJoin);
         return 1;
    }

    private function clearWhere(){
        $this->strWhere='';
        unset($this->strWhere);
        return 1;
    }

    private function clearBetween(){
        $this->strBetween='';
        unset($this->strBetween);
        return 1;
    }

    private function clearIn(){
        $this->strIn='';
       unset($this->strIn); 
        return 1;
    }

    private function clearGroup(){
        $this->strGroup='';
        unset($this->strGroup);
        return 1;
    }

    private function clearOrder(){
        $this->strOrder='';
        unset($this->strOrder);
        return 1;
    }

    private function clearLimit(){
        $this->strLimit='';
        unset($this->strLimit);
        return 1;
    }

    private function clearPlacholder(){
        unset($this->placeholder);
        return 1;
    }



    /**
     * @param  $type
     * @return $response
     */
    public function get($type="assoc"){
        $qr = $this->dbQuery($this->globalQuery,$type,$this->placeholder);
        $this->foundRows = $this->getFoundRows();
        $this->clear();
        return $qr;
    }

    /**
     *
     * @return SQL 
     */
    public function getSql(){
        return $this->strSelect.
               $this->strFrom.
               $this->strJoin.
               $this->strWhere.
               $this->strBetween.
               $this->strIn.
               $this->strGroup.
               $this->strOrder.
               $this->strLimit;
    }





}
?>