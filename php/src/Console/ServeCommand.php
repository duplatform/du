<?php

namespace Jubayed\A2\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Yaml\Yaml;

class ServeCommand extends Command
{
    /**
     * Filesystem
     * 
     * @var Symfony\Component\Filesystem\Filesystem
     */
    public $filesystem;

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->filesystem = new Filesystem;
        $this->setName('s')->setDescription('Run duplatform.')->addOption('token', 't', InputOption::VALUE_OPTIONAL, 'Set A2 Token', null);
    }

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        if($token = $this->input->getOption('token')){

            $this->output->writeln([ $token ]);
            if($token != "" && trim($token) != ""){
                $this->setToken(trim($token));
                return 0;
            }
        }

        $this->artLine();

        $environmentFile = $this->getManifestPath();
        if(!file_exists( $environmentFile) ){
            $environmentContent = array(
                "APP_NAME" => basename(getcwd()),
                "A2_TECHNOLOGY"=> "",
                "A2_TOKEN"=> $this->getToken(),
                "API_SERVER"=> "https://duplatform.herokuapp.com/api"
            );

            $this->saveManifest($environmentContent);
        }

        $hasEnvironment = file_exists($environmentFile);

        $this->output->writeln(['','<info>Starting A2 development server:</info> http://127.0.0.1:8011/', '']);
        $process = $this->startProcess($hasEnvironment);

        while ($process->isRunning()) {
            // waiting for process to finish
            if ($hasEnvironment) {
                clearstatcache(false, $environmentFile);
            }
            usleep(500 * 1000);
        }

        echo $process->getOutput();
        
        return 0;
    }

    /**
     * Start a new server process.
     *
     * @param  bool  $hasEnvironment
     * @return \Symfony\Component\Process\Process
     */
    protected function startProcess($hasEnvironment)
    {
        $process = new Process($this->serverCommand());

        $process->start(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        return $process;
    }

    /**
     * Get the full server command.
     *
     * @return array
     */
    protected function serverCommand()
    {
        return [
            (new PhpExecutableFinder)->find(false),
            '-S',
            '127.0.0.1:8011',
            __DIR__. '/../../index.php',
        ];
    }


    /**
     * Art line
     */
    public function artLine()
    {
        $this->output->writeln('<comment>
 ____  _____ _____ __    _____ _____ _____ _____ _____ _____ 
|    \|  |  |  _  |  |  |  _  |_   _|   __|     | __  |     |
|  |  |  |  |   __|  |__|     | | | |   __|  |  |    -| | | |
|____/|_____|__|  |_____|__|__| |_| |__|  |_____|__|__|_|_|_|</comment>
                                                    <info>By Jubayed</info>'
       );
    }

    /**
     * Get duplatform token 
     * 
     * @return string
     */
    public function getToken()
    {
        $helper = $this->getHelper('question');

        $question = new Question("A2-TOKEN?");
        $token = $helper->ask($this->input, $this->output, $question);

        if ($token == "" || $token == null) {
            $this->getToken($this->input, $this->output);
        }

        $this->output->writeln("");
        return str_replace([" ", "\r", "\n"], '', $token);
    }

    /**
     * Set new token
     * 
     * @param string $token
     * @return bool
     */
    public function setToken($token = null )
    {
        if($token){
            $data = $this->getManifest();
            $data['A2_TOKEN'] = $token;

            $this->saveManifest((array)$data);
            $this->output->writeln(['Token successfully changed.']);
        }else {
            $token = $this->getToken();
            $this->setToken($token);
        }

        return true;
    }

    /**
     * Get Manifest data
     * 
     * @return array
     */
    public function getManifest()
    {
        return Yaml::parseFile($this->getManifestPath());
    }

    /**
     * Save Envirement content
     * 
     * @param array $data
     * @return void
     */
    private function saveManifest(array $data): void
    {
        $this->filesystem->dumpFile(
            $this->getManifestPath(),
            Yaml::dump($data)
        );
    }

    /**
     * Get manifest path
     * 
     * @return string 
     */
    private function getManifestPath()
    {
        return a2_path('manifest.yaml');
    }
}
