<?php

/*
 * Google Rank Finder
 * Author : Cem AÇAR
 * İletişim:mceykiii@gmail.com
 * */

/*
 *Bir Tozlu Aynana Bak Bir Lekeli Yüzüne :D
 *
 * */

class GoogleRanker{
    private $googleurul="";
    private $url;
    private $keyword;
    private $page_count;
    private $arama=array();
    private $hata = false;
    public function __construct($url,$keyword,$page_count)
    {
        try{
            $valid_url=$this->valid_url($url);
            if($valid_url){
                $this->url=$url;
            }else{
                $this->hata = true;
                throw new Exception("While type url be carefull ",'001');
            }
        }catch(Exception $e){
            $this->hata = true;
            echo "Error".$e->getMessage()."Error Code:".$e->getCode();
        }
        try{
            $valid_url=$this->valid_keyword($keyword);
            if($valid_url){
                $this->keyword=$this->keydors_prosedurs($keyword);
            }else{
                $this->hata = true;
                throw new Exception("Only permisson for number and also the value is be numner",'002');
            }
        }catch(Exception $e){
            $this->hata = true;
            echo "Hata".$e->getMessage()."Error Code:".$e->getCode();
        }

        try{
            $valid_url=$this->valid_page_count($page_count);
            if($valid_url){
                $this->page_count=$page_count;
            }else{
                $this->hata = true;
                throw new Exception("Only permisson for number and also the value is to be 1-50",'003');
            }
        }catch(Exception $e){
            $this->hata = true;
            echo "Hata".$e->getMessage()."Error Code:".$e->getCode();
        }

        if($this->hata ){
            "Please fill blank with rightly";
        }else{
            $this->googleurul;
        }
    }

    private function keydors_prosedurs($value){

        $split_str=explode(' ',$value);
        $str_clear="";

        if($split_str>0){

            foreach ($split_str as $str){
                $str_clear.=$str."+";
            }
            rtrim($str_clear, '+');
            return $str_clear;

        }else{
            $this->keyword=$value;
        }
    }

    private function get_contents($url)
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public function get_rank(){
        $all=[];
        for($i=0;$i<=$this->page_count;$i++){
            $goole_url="https://www.google.com/search?start=$i&source=hp&ei=WzpMW-fbJeWE6AScypVI&q=$this->keyword&oq=$this->keyword&gs_l=psy-ab.3...1313.2759.0.2899.11.8.0.0.0.0.248.248.2-1.2.0....0...1c.1.64.psy-ab..9.2.507.6..0j35i39k1j0i67k1j0i131k1.259.zdXcDRgEeOY";
            $contents = $this->get_contents($goole_url);
            $regex = '#\<h3 class="r"\>(.+?)\<\/h3\>#s';
            $regex_href='/<a href="(.+)">/';
            $regex_correct_url='/url/';
            preg_match_all ($regex, $contents, $matches);
            foreach ($matches[0] as $value){
                preg_match_all ($regex_href, $value,$deger);
                $correctly_url=preg_match_all($regex_correct_url,$deger[1][0]);
                if($correctly_url){
                    $tam_url=str_replace('/url?q=','',$deger[1]);
                    array_push($all,$tam_url);
                }
            }
        }
        $this->arama=$all;
        $this->arama=$this->single_array($this->arama);
        return $this;
    }

    public function parsing_url(){
        try{
          $result_count=count($this->arama);

          if($result_count>0){
              $new_list=[];
              foreach ($this->arama as $value){
                  $pars=parse_url($value);
                  array_push($new_list,$pars["host"]);
              }
             $this->arama=$this->single_array($new_list);
             return $this;
          }else{
              throw new Exception("before you can not call this method",'005');
          }

        }catch (Exception $e){
            echo "Hata".$e->getMessage()."Error Code:".$e->getCode();
        }
    }

    public function  comparison_key(){

        $rank=1;

        $key=parse_url($this->url);

        $main_url=$key["host"];

        foreach ($this->arama as $value){

             if($main_url==$value){
                 return $rank ;
             }

             $rank++;

        }

        return "There is no result" ;

    }

    private function valid_url($user_url){
        $regex='#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si';
        $result_m=preg_match_all($regex,$user_url);
        if($result_m){
            return true;
        }else{
            return false ;
        }
    }

     private function single_array($arr){
        $new_arr=[];
        foreach($arr as $key){
             if(is_array($key)){
                 $arr1=$this->single_array($key);
                 foreach($arr1 as $k){
                     $new_arr[]=$k;
                 }
             }
             else{
                 $new_arr[]=$key;
             }
        }
        return $new_arr;
    }

    private function valid_page_count($count){

        if(is_numeric($count)){
            if($count>=0 && $count<=50){

                return true ;
            }else{
                return false ;
            }
        }else{
            return false ;
        }
    }
    private function valid_keyword(){
        return true;
    }
}

///Ben Burda örnekleme yaptıkdan sonra
$sapmling=new GoogleRanker('https://www.arabam.com/','araba','10');
echo $sapmling->get_rank()->parsing_url()->comparison_key();



?>
