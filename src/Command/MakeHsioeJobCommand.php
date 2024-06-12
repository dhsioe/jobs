<?php

namespace Hsioe\Jobs\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webman\Console\Util;


class MakeHsioeJobCommand extends Command
{
    protected static $defaultName = 'hsioe-job:create';
    protected static $defaultDescription = '生成HsioeJob任务类';
    
    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, '任务名称');
    }
    
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $output->writeln("Make HsioeJobs $name");
        
        $path = '';
        $namespace = 'app\\jobs';
        if ($pos = strrpos($name, DIRECTORY_SEPARATOR)) {
            $path = substr($name, 0, $pos + 1);
            $name = substr($name, $pos + 1);
            $namespace .= '\\' . str_replace(DIRECTORY_SEPARATOR, '\\', trim($path, DIRECTORY_SEPARATOR));
        }
        $class = Util::nameToClass($name);
        
        $file = app_path() . "/jobs/{$path}$class.php";
        $this->createJobs($namespace, $class, $file);
        
        return self::SUCCESS;
    }
    
    /**
     * @param $namespace
     * @param $class
     * @param $file
     * @return void
     */
    protected function createJobs($namespace, $class, $file)
    {
        $path = pathinfo($file, PATHINFO_DIRNAME);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $controller_content = <<<EOF
<?php

namespace $namespace;

use Hsioe\\Jobs\\HsioeJobsAbstract;

class $class extends HsioeJobsAbstract
{
    public function execute(): void
    {
        // TODO 执行异步逻辑
    }
    
}

EOF;
        file_put_contents($file, $controller_content);
    }
    
}
