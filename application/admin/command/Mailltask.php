<?php
namespace app\admin\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
class Mailtask extends Command                                                                                                                                                                                                                                                                                                                                                                                                                                                     
{
	protected function configure()
	{
		echo 222;
		$this->setName('test')->setDescription('Here is the remark ');
	}

	protected function execute(Input $input, Output $output)
	{
		$output->writeln("TestCommand:");
	}
}
