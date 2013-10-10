<?php

class block_quiz_notification extends block_base {

    public function init() {
        $this->title = get_string('quiz_notification', 'block_quiz_notification');
    }

    public function get_content() {
        global $USER, $CFG, $DB;
        $user = $USER->id; //username of the current user
        $prf = $CFG->prefix;  //perfix of the moodle tables
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
        $this->content->text .= '<tr><td width= "120"><input type = "text" name= "id_val" value="' . get_string('view1', 'block_quiz_notification') . '"a align="left"/></td>';
        $this->content->text .= ' </tr></table>';

        //     $this->content->text .=      </table>';
        $this->content->text .= '<tr>';
        $this->content->text .= '<td width="60"><input type="submit" name="ok" id="button" value="' . get_string('yes', 'block_quiz_notification') . '" a align="left"/></td>';
        $this->content->text .= '<td width="60"><input type="submit" name="no" id="button" value="' . get_string('no', 'block_quiz_notification') . '" a align="right"/></td>';
        $this->content->text .= '</tr> </table>';
        $this->content->text .= '</form>';
///************************************************************///
///************************************************************///
///************************************************************///
        //here onwards did not check the code//

        if (isset($_POST['ok'])) {
            $userid = $USER->id;
            if ($DB->record_exists('quiz_notification_subs', array('id' => $userid))) { //Check whether the user has already subscribed
                $this->content->text .= get_string('already_subscribed', 'block_quiz_notification');
            } else {
                $facebook_id = $_POST['id_val'];
                // echo $facebook_id;
                if (($facebook_id != "Enter the ID here") && ($facebook_id != null)) {

                    $this->quiz_notification_subscribe($userid, $facebook_id);
                   // $this->content->text .= get_string('Enter_ID1', 'block_quiz_notification');
                } else {

                    $this->content->text .= get_string('Enter_ID', 'block_quiz_notification');
                }
            }
        }
///************************************************************///
///************************************************************///
///************************************************************///
    }

    public function cron() { // cron exe seems to be not working
        global $DB;
        $now = time();

        //  echo $now;
        $instances = $DB->get_records_sql('select * from mdl_quiz');
        // $result = mysql_query("select quiz.id,course,name,timeopen from quiz where cou");
        foreach ($instances as $id) {
            $fp = fopen("C:\Users\akila\Desktop\myText.txt", "a");


            if ($fp == false) {
                echo 'fp is not working';
            } else {
                fwrite($fp, time());
                fclose($fp);
            }


            //testing

            $starttime1 = $id->timeopen;
            $starttime = $starttime1 - 19800; // to adjust the system time
            $delay = $starttime - $now;

            $gap = $delay / 60;
            if (0 <= $gap && $gap <= 1) {
                $courseid = $id->course;
                create_quize_notification($courseid, $starttime);
            } else {
                $courseid = $id->course;
                create_quize_notification($courseid, $starttime);
//********************************** this was done to testing purpose****************************** at the real time nothing within else*?
//           se['shortname'];
            }
        }
        //  echo $result->fullname;     $courseid = $id->course;
//               
//                echo $resultcourse['shortname'];
    }

    //  echo $result->fullname;


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

        $datestamp = date("l jS \of F Y h:i:s A");


        $message = 'There is a quiz in the course ';
        echo $message;
        echo $resultcourse['shortname'];
        echo ' at';
        echo $datestamp;
        //  echo $message;
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

//*this function is not tested**//
    function quiz_notification_subscribe($userid, $facebook_id) {
        global $DB;
        $table = 'quiz_notification_subs';
//        if ($DB->record_exists($table, array('user_id' => $userid, 'facebook_id' => $facebook_id))) { // if is not working 
//       
//            return TRUE;
//        }

        $sub = new stdClass();
        $sub->user_id = $userid;
        $sub->facebook_id = $facebook_id;
        $result = $DB->insert_record('quiz_notification_subs', $sub);
    }

}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
