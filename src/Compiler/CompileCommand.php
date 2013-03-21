<?php namespace Compiler;

use Compiler\Compiler;
use PHPParser_Parser as Parser;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Filesystem\FileNotFoundException;

class CompileCommand extends Command {

	protected $filesystem;

	protected $parser;

	protected $compiler;

	public function __construct(Filesystem $filesystem, Parser $parser, Compiler $compiler)
	{
		$this->filesystem = $filesystem;
		$this->parser = $parser;
		$this->compiler = $compiler;

		parent::__construct();
	}

	protected function configure()
	{
		$this->setName('compile')
		     ->setDescription('Compile a PHP script into E100 Assembly.')
		     ->addArgument('file', InputArgument::REQUIRED, 'Which file or folder do you want to compile?')
		     ->addArgument('output', InputArgument::OPTIONAL, 'The file where the code will be compiled.', 'compiled.txt');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$file = $input->getArgument('file');
		$out  = $input->getArgument('output');

		try
		{
			$output->writeln('<info>Reading file '.$file.'.<info>');

			$source = $this->filesystem->get($file);

			$output->writeln('<info>Parsing file '.$file.'.<info>');

			$code = $this->parser->parse($source);

			$output->writeln('<info>Compiling file '.$file.'.<info>');

			$code = $this->compiler->compile($code);

			$output->writeln('<info>Saving compiled code to '.$out.'.<info>');

			$this->filesystem->put($out, $code);
		}

		catch(\Exception $e)
		{
			$output->writeln('');
			$output->writeln('<error>'.$e->getMessage().'</error>');
		}
	}
}