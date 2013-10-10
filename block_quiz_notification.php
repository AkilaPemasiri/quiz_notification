

<?php

class block_quiz_notification extends block_base { // because this is a block

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

// what happens when the ok is pressed
        if (isset($_POST['ok'])) {
            $userid = $USER->id;        // take the user_id, which is the id of the global variable user
            if ($DB->record_exists('quiz_notification_subs', array('id' => $userid))) { //Check whether the user has already subscribed
                $this->content->text .= get_string('already_subscribed', 'block_quiz_notification');
            } else {
                $facebook_id = $_POST['id_val'];            // take the facebook id
                if (($facebook_id != "Enter the ID here") && ($facebook_id != null)) {      // check whether the entered id is correct
                    $this->quiz_notification_subscribe($userid, $facebook_id);              // call the method to record the subscription
                    $this->content->text .= get_string('subscribed', 'block_quiz_notification');
                } else {
                    $this->content->text .= get_string('Enter_ID', 'block_quiz_notification');  // of the entered password is not valid enter the correct password
                }
            }
        }
        if (isset($_POST['no'])) {  //if someone doesn't want subcribe for the service
            $userid = $USER->id;
            $this->quiz_notification_unsubscribe($userid);
            $this->content->text .= get_string('unsubscribed', 'block_quiz_notification');
        }
    }


    

    public function cron() { // to call the checking process
        global $DB;
        $now = time();
        $instances = $DB->get_records_sql('select * from mdl_quiz');        // get all the quizes
        foreach ($instances as $id) {                                          // to check whether the cron is working... 
            $fp = fopen("C:\Users\akila\Desktop\log.txt", "a");                 // this will act as a log file as well
            if ($fp == false) {
                
            } else {
                $datestamp = date("l jS \of F Y h:i:s A");
                fwrite($fp, $datestamp);
                fclose($fp);
            }
            $starttime = $id->timeopen;           // take the start time of quiz
            $delay = $starttime - $now;

            $gap = $delay / 60;                     // take the gap (in minutes) between the current time and the quiz start time
            if (0 <= $gap && $gap <= 1) {           //if it is less than 1 min
                $courseid = $id->course;
                create_quize_notification($courseid, $starttime);   // create the notification
            } else {

                $courseid = $id->course;                                // these two parts are added for the testing purpose
                $this->create_quize_notification($courseid, $starttime);
            }
        }
    }

    
    
    function connect_to_database() {                            // the function to establish the database connection
        $con = mysql_connect("localhost", "root", "abc123");    // the details of the database 
        if (!$con) {
            die('Could not connect: ' . mysql_error());
        } else {
            //echo "connection established!!!!!!!1";
        }
        mysql_select_db("moodle", $con);
        return $con;                                            // return the connection
    }
    
    
    
    function create_quize_notification($courseid, $starttime) { // create the message 
        ;
             $this->connect_to_database();
        $result = mysql_query("select shortname from mdl_course where id = $courseid");  
        if (!$result) {
            die('Invalid query' . mysql_error());
        }
        $resultcourse = mysql_fetch_array($result);
        $datestamp = date("l jS \of F Y h:i:s A");      // get the current date stamp with the necessary format
        $message = 'There is a quiz in the course ';
        $message .= $resultcourse['shortname'];         // concatenate the relevent data for the message
        $message .= ' on ';
        $message .= $datestamp;
        $this->select_subscribers($message, $courseid);    
    }

    
    
    function quiz_notification_subscribe($userid, $facebook_id) {       // insert the data of the subscribed users
        global $DB;
        //$table = 'quiz_notification_subs';    
        $sub = new stdClass();
        $sub->user_id = $userid;
        $sub->facebook_id = $facebook_id;
        $result = $DB->insert_record('quiz_notification_subs', $sub);   // insert the new tuple to the database table
    }

    
    
    function quiz_notification_unsubscribe($userid) {                   // delete entirs when a user is unsubs cribed
        global $DB;
        if ($DB->record_exists('quiz_notification_subs', array("userid" => $userid))) {
            $DB->delete_records('quiz_notification_subs', array("userid" => $userid));  // delete the tuple
            return true;
        }
        return true;
    }

    
    
    function select_subscribers($message, $course_id) {
       
        global $DB;
        $context = context_course::instance($course_id);        // get the context of the course that is available at access API
        $enrolled_users = get_enrolled_users($context);         // calling function that has been implemented by access API
        $subscribed_users = $DB->get_records_sql('select * from mdl_quiz_notification_subs');
        foreach ($enrolled_users as $enuser) {          // to send the message to each user who is enrolled
            foreach ($subscribed_users as $subuser) {
                if ($enuser->id == $subuser->user_id) {
                    $this->send_message($message, $subuser->facebook_id);
                }
            }
        }
    }

    
    
    function send_message($message, $facebook_id) {
     
        $address = $facebook_id;
      //  $address .= '@facebook.com'; in the real code
        $address .= '@gmail.com';
        $to = $address;
        $subject = 'Moodle Message';
        $message = $message;
        $headers = 'From: Moodle@Moodle.com' . "\r\n" .
                'Reply-To: no reply' . "\r\n" .
                'MIME-Version: 1.0' . "\r\n" .
                'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
        mail($to, $subject, $message, $headers);
        if (mail($to, $subject, $message, $headers)) {
            echo "Email sent to ";
            echo $facebook_id;
        }
        else
            echo "Email sending failed";
        echo $facebook_id;
        echo $message;
         $fp = fopen("C:\Users\akila\Desktop\messages.txt", "a");                 // this will act as a log file as well
            if ($fp == false) {
                
            } else {
                $datestamp = date("l jS \of F Y h:i:s A");
                fwrite($fp, $facebook_id);
                fwrite($fp, $message);
                fwrite($fp, '\n');
                fclose($fp);
            }
    }

}
?>
