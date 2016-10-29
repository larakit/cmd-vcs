<?php
namespace Larakit\Cmdvcs;

use File;
use Illuminate\Console\Command;

class CommandVcs extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'larakit:vcs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда проверки версии файлов.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    protected $touched_git = [];

    protected $git_types   = [
        'M'  => 'Изменено',
        'A'  => 'Добавлено',
        'D'  => 'Удалено',
        '??' => 'Вне ревизии'
    ];
    protected $touched_svn = [];

    protected $svn_types = [
        'M' => 'Изменено',
        'A' => '<info>Добавлено</info>',
        '?' => '<comment>Вне ревизии</comment>',
        'D' => '<error>Удалено</error>',
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $path         = base_path('vendor');
        $vendor_paths = \File::directories($path);
        $path         = rtrim(rtrim($path, '\\'), '/');

        $this->info('Сканируем установленные пакеты на наличие папок .git и .svn');

        $check_directories = [];

        foreach ($vendor_paths as $vendor_path) {
            $package_paths = File::directories($vendor_path);
            foreach ($package_paths as $package_path) {
                $check_directories[] = (string)$package_path;
            }
        }


        $progress = new \Symfony\Component\Console\Helper\ProgressBar($this->output, sizeof($check_directories));
        $progress->setFormat('debug');
        $progress->start();
        foreach ($check_directories as $directory) {
            $progress->advance();
            $is_git = $this->checkGit($directory);
            if (!$is_git) {
                $this->checkSvn($directory);
            }
        }
        $progress->finish();
        $this->info('');
        $this->info('Сканирование завершено.');
        $table     = [];
        $separator = [
            '-',
            '-',
            '-',
            '-',
        ];
        if ($this->touched_git) {
            $this->info('Имеются изменные файлы GIT: ');
            foreach ($this->touched_git as $package_path => $files) {
                $package = larasafepath($package_path);
                foreach ($files as $file => $type) {
                    $table[] = [
                        'GIT',
                        $package,
                        larasafepath($file),
                        \Illuminate\Support\Arr::get($this->git_types, $type, $type)
                    ];
                }
                $table[] = $separator;
            }
        }
        if ($this->touched_svn) {
            $this->info('Имеются изменные файлы SVN: ');
            foreach ($this->touched_svn as $package_path => $files) {
                $package = larasafepath($package_path);
                foreach ($files as $file => $type) {
                    $table[] = [
                        'SVN',
                        $package,
                        trim(str_replace($package, '', larasafepath($file)), '/'),
                        \Illuminate\Support\Arr::get($this->svn_types, $type, $type)
                    ];
                }
                $table[] = $separator;
            }
        }


        if ($table) {
            unset($table[sizeof($table) - 1]);
            $this->info('');
            $this->table(
                [
                    'VCS',
                    'package',
                    'file',
                    'type'
                ],
                $table
            );
        }

    }

    protected function checkGit($path) {
        $git_dir = $path . '/.git';
        if (is_dir($git_dir)) {
            $command = 'git --work-tree=' . $path . ' --git-dir=' . $git_dir . ' status -s';
            exec($command, $result, $ret_val);
            if ($result) {
                foreach ($result as $r) {
                    $r = trim($r);
                    list($type, $file) = explode(' ', $r);
                    $this->touched_git[$path][$file] = $type;
                }
            }

            return true;
        }

        return false;
    }

    protected function checkSvn($path) {
        $svn_dir = $path . '/.svn';
        if (is_dir($svn_dir)) {
            $command = 'svn status ' . $path;
            exec($command, $result, $ret_val);
            if ($result) {
                foreach ($result as $r) {
                    $r    = trim($r);
                    $info = explode(' ', $r);
                    $type = array_shift($info);
                    if ('>' != $type) {
                        $file                            = trim(implode('', $info));
                        $this->touched_svn[$path][$file] = $type;
                    }
                }
            }
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments() {
        return [
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions() {
        return [
        ];
    }

}
