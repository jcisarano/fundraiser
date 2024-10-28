<?php

class Email {
    const WRAPPER_FILE = "../emails/wrapper.txt";
    const EMAIL_FILE_PATH = "../emails/";
    /*
     * @param $cFilePath - path to text file where email lives
     * @param $cWrapper - text for header/footer of email. Must have a single %s
     *      somewhere where the body will be added. If no wrapper is needed, 
     *      pass in NULL
     * @param - Optional args passed in will be added to the body using sprintf,
     *      so number of tokens in body must match extra arg count.
     */
    public static function BuildEmail( $cFilePath, $cWrapper ) {
        if( !file_exists( $cFilePath ))
            return NULL;

        $cSubject = "";
        $cBody = "";

        //this churns out an array of lines from the text file
        $raw = file( $cFilePath );
        
        if( $raw )
        {
            //email subject line will always be the first line of the text file
            $cSubject = $raw[0];

            //allows for a comment in the first line followed by a colon
            $cSubject = explode(":", $cSubject, 2 );
            if( count( $cSubject ) > 1 )
                $cSubject = $cSubject[1];
            else
                $cSubject = $cSubject[0];

            //body is the rest of the email, reassemble it
            $cBody = implode(PHP_EOL,array_splice($raw,1));

            //wrapper must be ready to rock
            //  with only a single token waiting for the body text
            if( $cWrapper != NULL ) {
                $cBody = sprintf($cWrapper, $cBody);
            }

            //check for params to attach to the 
            $iArgCount = func_num_args();
            
            if( $iArgCount > 2 )
            {
                $args = func_get_args();
                $args = array_slice( $args, 2 );
                $cBody = vsprintf( $cBody, $args );
            }
        }

        return array( "subject"=>$cSubject, "body"=>$cBody );
    }

    /*Sends mail using Mandrill*/
    public static function MandrillInstantSend( $pDB, EmailData $mailData, 
        $contentHtml, $contentText, $subject )
    {
        require_once( "mailchimp/Mandrill.php" );
        
        if( is_null( $mailData->email ) || $mailData->email == "" )
        {
            error_log( "MandrillInstantSend Fail: No email address given");
            return false;
        }
        
        $mandrill = new Mandrill( $mailData->apiKey );
        
        $message = array(
                'html' => $contentHtml,
                'text' => $contentText,
                'subject' => $subject,
                'from_email' => $mailData->replyEmail,
                'from_name' => $mailData->replyName,
                'to' => array(
                    array(
                        'email' => $mailData->email,
                        'name' =>  "{$mailData->firstName} {$mailData->lastName}",
                        'type' => 'to'
                    )
                )
            );
        
        $async = false; //enables background mode for bulk email
        $ip_pool = ''; //if you have a dedicated ip pool with mandrill
        $send_at = ''; //schedule a time to send email, only for prepaid accounts


        $bSuccess = true;
        $extStatus = "";
        try
        {
            $result = $mandrill->messages->send( $message, $async, $ip_pool, $send_at );

            $extStatus .= $result[0]["status"];

            if( isset( $result[0]["reject_reason"] ) && $result[0]["reject_reason"] != "" )
            {
                error_log( "Mandrill email rejected addr:{$mailData->email}, reason: " 
                    . $result[0]["reject_reason"] );

                $extStatus .= " : {$result[0]["reject_reason"]}";

                //TODO: Attempt backup send depending on Mandrill error?
                $bSuccess = false;
            }
        }
        catch(Mandrill_Error $e) 
        {
            error_log( 'A mandrill error occurred: ' 
                . get_class($e) . ' - ' . $e->getMessage() );

            //backup attempt?
            /*
            self::SendPhpMail( 
                $mailData->email, 
                $subject, 
                $contentHtml, 
                $mailData->replyEmail 
            );
            */

            $bSuccess = false;
        }

        self::LogEmailSent( $pDB, $mailData->email, $extStatus, $contentHtml );

        return $bSuccess;
    }

    public static function LoadEmailTemplate( $cFilePath, $cWrapper = NULL )
    {
        $cSubject = "";
        $cBody = "";
        if( file_exists( $cFilePath ))
        {
            $data = file( $cFilePath );
            $cSubject = $data[0];
            $cSubject = explode(":", $cSubject, 2 );
            if( count( $cSubject ) > 1 )
                $cSubject = $cSubject[1];
            else
                $cSubject = $cSubject[0];		
                
            $cBody = implode(PHP_EOL,array_splice($data,1));
        }

        if( $cWrapper != NULL ) {
            $cBody = sprintf( $cWrapper, $cBody );
        }

        return array("subject"=>$cSubject, "body"=>$body);
    }

    //
    // Backup email sent using PHP mail - Use for internal mail
    //      or only as last resort if external mail fails
    public static function SendPhpMail( $recipients, $cSubject, $cMsg, $cFrom )
    {
        $sHeaders = "From: {$cFrom}\r\n";
        $sHeaders .= "Content-type: text/html\r\n";

        $recipientArray = array();
        if( !is_array( $recipients ))
            $recipientArray[] = $recipients;
        else
            $recipientArray = $recipients;

        $result = array();
        foreach( $recipientArray as $to )
        {
            if( mail($to, $cSubject, $cMsg, $sHeaders ) )
            {
                $result[] = "Message successfully sent to {$to}";
            }
        }

        return count( $result ) > 0 ? $result : false;
    }

    function LogEmailSent( $pDB, $sEmailAddress, $sExternalStatus, $sContent ) {
        $cQuery = "INSERT INTO EmailLog (address,externalStatus,content) VALUES ('%s','%s','%s')";
        $pResult = $pDB->execute_query($cQuery, $sEmailAddress, 
            $sExternalStatus, $sContent);
        $pDB->free_result( $pResult );
    }
}

//
// Container to make it easier to pass data into the mail class
//
class EmailData {
    public $email;
    public $firstName;
    public $lastName;
    public $zip;
    public $replyEmail;
    public $replyName;
    public $apiKey;
}