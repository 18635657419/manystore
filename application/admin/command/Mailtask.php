<?php
namespace app\admin\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use \PhpImap\Mailbox;
class Mailtask extends Command
{
	public $paypal_status = [
		'您无法再使用PayPal开展业务了' => "limited180"
	
	];
	protected function configure()
	{
		$this->setName('Mailtask')->setDescription('mailtask');
	}

	protected function execute(Input $input, Output $output)
	{
		$mailbox = new \PhpImap\Mailbox(
				'{imap.gmail.com:993/imap/ssl}INBOX', // IMAP server and mailbox folder
				'vhaferl.service@gmail.com', // Username for the before configured mailbox
				'service123.',// Password for the before configured username
				__DIR__, // Directory, where attachments will be saved (optional)
				'UTF-8' // Server encoding (optional)
				);
		

		try {
				// Get all emails (messages)
					// PHP.net imap_search criteria: http://php.net/manual/en/function.imap-search.php
						$mail_ids = $mailbox->searchMailbox('UNSEEN');
		} catch(PhpImap\Exceptions\ConnectionException $ex) {
				echo "IMAP connection failed: " . $ex->getMessage();
					die();
		}
	
		foreach ($mail_ids as $mail_id) {
			echo "+------ P A R S I N G ------+\n";

			$email = $mailbox->getMail(
					$mail_id, // ID of the email, you want to get
					false // Do NOT mark emails as seen (optional)
					);

			$fromemail = (string) $email->fromAddress;
			$subject = (string) $email->subject;
			foreach($this->paypal_status as $keyword =>  $status){
				if(strpos($subject, $keyword) !==  false){
					$fromemail = "test@gmail.com";
					Db::table("ppaccount")->where("ppaccount", $fromemail)->update(['status' => 'limited180']);
				}
				
			}
			 $mailbox->markMailAsRead($mail_id);



		}

		 $mailbox->disconnect();


	}
}
