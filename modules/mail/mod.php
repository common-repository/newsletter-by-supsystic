<?php
class mailNbs extends moduleNbs {
	private $_smtpMailer = null;
	private $_sendMailMailer = null;
	private $_sendgridMailer = null;

	public function send($to, $subject, $message, $params = array()) {
		$type = frameNbs::_()->getModule('options')->get('mail_send_engine');
		$res = false;
		switch($type) {
			case 'smtp':
				$res = $this->sendSmtpMail( $to, $subject, $message, $params );
				break;
			case 'sendmail':
				$res = $this->sendSendMailMail( $to, $subject, $message, $params );
				break;
			case 'wp_mail': default:
				$res = $this->sendWpMail( $to, $subject, $message, $params );
				if(!$res) {
					// Sometimes it return false, but email was sent, and in such cases
					// - in errors array there are only one - empty string - value.
					// Let's count this for now as Success sending
					$mailErrors = array_filter( $this->getMailErrors() );
					if(empty($mailErrors)) {
						$res = true;
					}
				}
				break;
		}
		return $res;
	}
	private function _getSmtpMailer() {
		if(!$this->_smtpMailer) {
			$this->_connectPhpMailer();
			$this->_smtpMailer = new PHPMailer();  // create a new object
			$this->_smtpMailer->IsSMTP(); // enable SMTP
			$this->_smtpMailer->Debugoutput = array($this, 'pushPhpMailerError');
			$this->_smtpMailer->SMTPDebug = 1;  // debugging: 1 = errors and messages, 2 = messages only
			$this->_smtpMailer->SMTPAuth = true;  // authentication enabled
			$smtpSecure = frameNbs::_()->getModule('options')->get('smtp_secure');
			if(!empty($smtpSecure)) {
				$this->_smtpMailer->SMTPSecure = $smtpSecure; // secure transfer enabled REQUIRED for GMail
			}
			$this->_smtpMailer->Host = trim(frameNbs::_()->getModule('options')->get('smtp_host'));
			$this->_smtpMailer->Port = trim(frameNbs::_()->getModule('options')->get('smtp_port'));
			$this->_smtpMailer->Username = trim(frameNbs::_()->getModule('options')->get('smtp_login'));
			$this->_smtpMailer->Password = trim(frameNbs::_()->getModule('options')->get('smtp_pass'));
			$this->_smtpMailer->CharSet = 'UTF-8';
		}
		return $this->_smtpMailer;
	}
	public function pushPhpMailerError( $errorStr ) {
		if(strpos($errorStr, 'SMTP ERROR') !== false) {
			$this->pushError( $errorStr );
		}
	}
	private function _getSendMailMailer() {
		if(!$this->_sendMailMailer) {
			$this->_connectPhpMailer();
			$this->_sendMailMailer = new PHPMailer();  // create a new object
			$this->_sendMailMailer->isSendmail(); // enable SendMail
			$this->_sendMailMailer->CharSet = 'UTF-8';
			$sendMailPath = trim(frameNbs::_()->getModule('options')->get('sendmail_path'));
			if(!empty($sendMailPath)) {
				$this->_sendMailMailer->Sendmail = $sendMailPath;
			}
		}
		return $this->_sendMailMailer;
	}
	private function _connectPhpMailer() {
      global $wp_version;
				if (!class_exists('PHPMailer', false)) {
					 require_once ( ABSPATH . WPINC . '/PHPMailer/PHPMailer.php' );
					 require_once ( ABSPATH . WPINC . '/PHPMailer/Exception.php' );
				}
  }
	private function _parseParamsData( $params ) {
		if(!isset($params['from_email']) || !isset($params['from_name'])) {
			if(!isset($params['from_email'])) {
				$params['from_email'] = get_bloginfo('admin_email');
			}
			if(!isset($params['from_name'])) {
				$params['from_name'] = wp_specialchars_decode(get_bloginfo('name'));
			}
		}
		return $params;
	}
	public function sendSendMailMail( $to, $subject, $message, $params ) {
		$this->_getSendMailMailer();
		$params = $this->_parseParamsData( $params );
		if(isset($params['from_email']) && isset($params['from_name'])) {
			$this->_sendMailMailer->setFrom($params['from_email'], $params['from_name']);
		}
		if(isset($params['reply_to_name']) || isset($params['reply_to_email'])) {
			$this->_sendMailMailer->addReplyTo($params['reply_to_name'], $params['reply_to_email']);
        }
		if(isset($params['return_path_email']) && !empty($params['return_path_email'])) {
			$this->_sendMailMailer->ReturnPath = $params['return_path_email'];
        }
		$this->_sendMailMailer->Subject = $subject;
		$this->_sendMailMailer->addAddress($to);
		if(frameNbs::_()->getModule('options')->get('disable_email_html_type')) {
			$this->_sendMailMailer->Body = $message;
		} else {
			$this->_sendMailMailer->msgHTML( $message );
		}
		if($this->_sendMailMailer->send()) {
			return true;
		} else {
			$this->pushError( 'Mail error: '.$this->_sendMailMailer->ErrorInfo );
		}
		return false;
	}
	public function sendSmtpMail( $to, $subject, $message, $params ) {
		$this->_getSmtpMailer();
		// Clear all prev. data - to not collect them
		$this->_smtpMailer->clearAddresses();
		$this->_smtpMailer->clearReplyTos();
		$this->_smtpMailer->clearAllRecipients();
		$this->_smtpMailer->clearAttachments();
		$this->_smtpMailer->clearCustomHeaders();

		$params = $this->_parseParamsData( $params );
		if(isset($params['from_email']) && isset($params['from_name'])) {
			$this->_smtpMailer->setFrom($params['from_email'], $params['from_name']);
		}
		if(isset($params['reply_to_name']) || isset($params['reply_to_email'])) {
			$this->_smtpMailer->addReplyTo($params['reply_to_name'], $params['reply_to_email']);
        }
		if(isset($params['return_path_email']) && !empty($params['return_path_email'])) {
			$this->_smtpMailer->ReturnPath = $params['return_path_email'];
        }
		$this->_smtpMailer->Subject = $subject;
		$this->_smtpMailer->addAddress($to);
		if(frameNbs::_()->getModule('options')->get('disable_email_html_type')) {
			$this->_smtpMailer->Body = $message;
		} else {
			$this->_smtpMailer->msgHTML( $message );
		}
		$res = false;
		switch(trim(frameNbs::_()->getModule('options')->get('smtp_host'))) {
			/*case 'smtp.sendgrid.net':
				$this->_getSendgridMailer();
				$res = $this->_sendgridMailer->sendMail( $this->_smtpMailer );
				break;*/
			default:
				$res = $this->_smtpMailer->send();
				break;
		}
		if($res) {
			return true;
		} else {
			$this->pushError( $this->_getSmtpMailErrors() );
		}
		return false;
	}
	/*private function _getSendgridMailer() {
		if(!$this->_sendgridMailer) {
			if(!class_exists('acymailingSendgrid')) {
				require_once( $this->getModDir(). 'engines'. DS. 'class.sendgrid.php');
			}
			$this->_sendgridMailer = new acymailingSendgrid();
			$this->_sendgridMailer->Username = trim(frameNbs::_()->getModule('options')->get('smtp_login'));
			$this->_sendgridMailer->Password = trim(frameNbs::_()->getModule('options')->get('smtp_pass'));
		}
		return $this->_sendgridMailer;
	}*/
	private function _getSmtpMailErrors() {
		switch(trim(frameNbs::_()->getModule('options')->get('smtp_host'))) {
			/*case 'smtp.sendgrid.net':
				$this->_getSendgridMailer();
				return $this->_sendgridMailer->error;*/
			default:
				return 'Mail error: '.$this->_smtpMailer->ErrorInfo;
		}
	}
	public function sendWpMail($to, $subject, $message, $params = array()) {
		$headersArr = array();
		$eol = "\r\n";
		$params = $this->_parseParamsData( $params );
        if(isset($params['from_name']) || isset($params['from_email'])) {
			$header = 'From: ';
			if(isset($params['from_name']) && isset($params['from_email'])) {
				$header .= $params['from_name']. ' <'. $params['from_email']. '>';
			} else {
				$header .= isset($params['from_name']) ? $params['from_name'] : $params['from_email'];
			}
            $headersArr[] = $header;
        }
		if(isset($params['reply_to_name']) || isset($params['reply_to_email'])) {
			$header = 'Reply-To: ';
			if(isset($params['reply_to_name']) && isset($params['reply_to_email'])) {
				$header .= $params['reply_to_name']. ' <'. $params['reply_to_email']. '>';
			} else {
				$header .= isset($params['reply_to_name']) ? $params['reply_to_name'] : $params['reply_to_email'];
			}
            $headersArr[] = $header;
        }
		if(isset($params['return_path_email']) && !empty($params['return_path_email'])) {
			$header = 'Return-Path: '. $params['return_path_email'];
            $headersArr[] = $header;
        }

		if(isset($params['add_headers']) && !empty($params['add_headers'])) {
			$headersArr = array_merge($headersArr, $params['add_headers']);
		}
		if(!function_exists('wp_mail'))
			frameNbs::_()->loadPlugins();
		if(!frameNbs::_()->getModule('options')->get('disable_email_html_type')) {
			add_filter('wp_mail_content_type', array($this, 'mailContentType'));
		}

		$attach = null;
		if(isset($params['attach']) && !empty($params['attach'])) {
			$attach = $params['attach'];
		}
		$message = stripslashes( str_replace('\\\\', '', $message ) );	// Some symbols can be with slashes - let's make sure that there wil be no such cases here
		if(empty($attach)) {
			$result = wp_mail($to, $subject, $message, implode($eol, $headersArr));
		} else {
			$result = wp_mail($to, $subject, $message, implode($eol, $headersArr), $attach);
		}
		if(!frameNbs::_()->getModule('options')->get('disable_email_html_type')) {
			remove_filter('wp_mail_content_type', array($this, 'mailContentType'));
		}

		return $result;
	}
	/*public function send($to, $subject, $message, $fromName = '', $fromEmail = '', $replyToName = '', $replyToEmail = '', $additionalHeaders = array(), $additionalParameters = array()) {

	}*/
	public function getMailErrors() {
		global $ts_mail_errors;
		global $phpmailer;
		$type = frameNbs::_()->getModule('options')->get('mail_send_engine');
		switch($type) {
			case 'smtp': case 'sendmail':
				return $this->getErrors();
				break;
			case 'wp_mail': default:
				// Clear prev. send errors at first
				$ts_mail_errors = array();

				// Let's try to get errors about mail sending from WP
				if (!isset($ts_mail_errors)) $ts_mail_errors = array();
				if (isset($phpmailer)) {
					$ts_mail_errors[] = $phpmailer->ErrorInfo;
				}
				if(empty($ts_mail_errors)) {
					$ts_mail_errors[] = __('Cannot send email - problem with send server', NBS_LANG_CODE);
				}
				return $ts_mail_errors;
				break;
		}
	}
	public function mailContentType($contentType) {
		$contentType = 'text/html';
        return $contentType;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function addOptions($opts) {
		$opts[ $this->getCode() ] = array(
			'label' => __('Mail', NBS_LANG_CODE),
			'opts' => array(
				'mail_function_work' => array('label' => __('Mail function tested and work', NBS_LANG_CODE), 'desc' => ''),
				'notify_email' => array('label' => __('Notify Email', NBS_LANG_CODE), 'desc' => __('Email address used for all email notifications from plugin', NBS_LANG_CODE), 'html' => 'text', 'def' => get_option('admin_email')),
			),
		);
		return $opts;
	}
}
