<?php

class block_quiz_notification extends block_base {

    public function init() {
        $this->title = get_string('quiz_notification', 'block_quiz_notification');
    }

    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        //content of the block which is to be displayed
        $this->content = new stdClass;
        $this->content->text = get_string('wantservice', 'block_quiz_notification');
        $this->content->text .= '<form id="form1" name="form1" method="post" action="">';
        $this->content->text .= '<table width="120" border="0"><tr>';
        $this->content->text .= '<td width= "120"><input type = "display" name= "adress_label" value="' . get_string('view', 'block_quiz_notification') . '" readonly align="left"/></td></tr>';
    $this->content->text .= '<tr><td width= "120"><input type = "display" name= "adress_label" value="' . get_string('view1', 'block_quiz_notification') . '"a align="left"/></td>';
        $this->content->text .= ' </tr></table>';
        
    //     $this->content->text .=      </table>';
        $this->content->text .= '<tr>';
        $this->content->text .= '<td width="60"><input type="submit" name="ok" id="button" value="' . get_string('yes', 'block_quiz_notification') . '" a align="left"/></td>';
        $this->content->text .= '<td width="60"><input type="submit" name="no" id="button" value="' . get_string('no', 'block_quiz_notification') . '" a align="right"/></td>';
        $this->content->text .= '</tr> </table>';
        $this->content->text .= '</form>';
    }

    public function cron() { // cron exe seems to be not working
        global $DB;
        $now = time();

        //  echo $now;
        $instances = $DB->get_records_sql('select * from mdl_quiz');
        // $result = mysql_query("select quiz.id,course,name,timeopen from quiz where cou");
        foreach ($instances as $id) {
            $fp = fopen("C:\Users\akila\Desktop\myTextttt.txt", "a");


            if ($fp == false) {
                echo 'oh fp is false';
            } else {
                fwrite($fp, "akila");
                fclose($fp);
            }


            //testing
        }
        $starttime1 = $id->timeopen;
        $starttime = $starttime1 - 19800;
        $delay = $starttime - $now;

        $gap = $delay / 60;
        if (0 <= $gap && $gap <= 1) {
            $courseid = $id->course;
            create_quize_notification($courseid, $starttime);
//                $courseid = $id->course;
//                $result = mysql_query("select shortname from course where course.id = $courseid");
//                if (!$result) {
//                    die('Invalid query' . mysql_error());
//                }
//                echo $result;
        } else {
            $courseid = $id->course;
            create_quize_notification($courseid, $starttime);
//********************************** this was done to testing purpose****************************** at the real time nothing within else*?
//           se['shortname'];
        }
        //  echo $result->fullname;     $courseid = $id->course;
//               
//                echo $resultcourse['shortname'];
    }

    //  echo $result->fullname;
}

function create_quize_notification($courseid, $starttime) {

    $con = mysql_connect("localhost", "root", "abc123");
    if (!$con) {
        die('Could not connect: ' . mysql_error());
    }
    mysql_select_db("moodle", $con);

    $result = mysql_query("select shortname from mdl_course where id = $courseid");
    if (!$result) {
        die('Invalid query' . mysql_error());
    }
    $resultcourse = mysql_fetch_array($result);
    echo $resultcourse['shortname'];
    $datestamp = date("l jS \of F Y h:i:s A");


    $message = 'There is a quiz in the course $courseid at $datestamp';
    echo $message;
//                echo $courseid ;
//                echo 'at';
//                echo $datestamp;
//                $id = 'akila';
//                $arr = array('id'=> $id,'message'=> $message );
//                
//                $url = 'http://localhost/intermediateFile.php';
//   
//    $ch=curl_init($url);
//    $data_string = urlencode(json_encode($arr));
//    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//    curl_setopt($ch, CURLOPT_POSTFIELDS, array("message"=>$data_string));
//
//
//    $result = curl_exec($ch);
//    curl_close($ch);
//
//    echo $result;
//    echo 'End of jason';
}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
