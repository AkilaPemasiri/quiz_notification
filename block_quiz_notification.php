<?php
class block_quiz_notification extends block_base{
    public function init() {
        $this->title = get_string('quiz_notification', 'block_quiz_notification');
        
    }
    public function get_content() {
       if ($this->content !== null) {
    return $this->content;
  }
 
 $this->content         =  new stdClass;
    $this->content->text   = 'The content of our SimpleHTML block!';
    $this->content->footer = 'Footer here...';
 
    return $this->content;
    }
    public function cron(){
        $fp = fopen("my.txt", "wb");
        
        if($fp == FALSE){
           echo 'Hey, i didnt write to file';    
        }else {
            fwrite($fp, "aa");
            fclose($fp);
             echo 'Hey, i wrote to file';
            
        }
            
         echo 'Hey, my cron script is running';
        
    }
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
