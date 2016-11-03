<?php
/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 zepi
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

/**
 * Creates and manages threads for different callbacks.
 * 
 * @package Zepi\Core\Utils
 * @subpackage Helper
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */

namespace Zepi\Core\Utils\Helper;

use \Zepi\Core\Utils\Entity\Process;
use \Zepi\Core\Utils\Entity\Task;

/**
 * Creates and manages threads for different callbacks.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */
class ThreadHelper
{
    /**
     * @access protected
     * @var array
     */
    protected $tasks = array();
    
    /**
     * @access protected
     * @var array
     */
    protected $processes = array();
    
    /**
     * @access protected
     * @var callable
     */
    protected $onStart;
    
    /**
     * @access protected
     * @var callable
     */
    protected $onRestart;
    
    /**
     * @access protected
     * @var callable
     */
    protected $onCrash;
    
    /**
     * @access protected
     * @var \Zepi\Core\Utils\Helper\CliHelper
     */
    protected $cliHelper;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Core\Utils\Helper\CliHelper $cliHelper
     */
    public function __construct(CliHelper $cliHelper)
    {
        $this->cliHelper = $cliHelper;
    }
    
    /**
     * Sets the start callback of the process manager
     *
     * @access public
     * @param callable $callback
     */
    public function onStart(callable $callback)
    {
        $this->onStart = $callback;
    }
    
    /**
     * Sets the restart callback of the process manager
     *
     * @access public
     * @param callable $callback
     */
    public function onRestart(callable $callback)
    {
        $this->onRestart = $callback;
    }
    
    /**
     * Sets the crash callback of the process manager
     *
     * @access public
     * @param callable $callback
     */
    public function onCrash(callable $callback)
    {
        $this->onCrash = $callback;
    }
    
    /**
     * Adds a task to the process manager
     *
     * @access public
     * @param Task $task
     */
    public function addTask(Task $task)
    {
        $this->tasks[] = $task;
    }
    
    /**
     * Starting and monitoring the tasks
     * 
     * @access public
     */
    public function doWork()
    {
        /**
         * Installing signal handlers
         */
        $this->cliHelper->writeTimeLine('Setting up signal handlers...');
        declare(ticks = 1);
    
        pcntl_signal(SIGTERM, array($this, 'shutdownProcesses'));
        pcntl_signal(SIGINT, array($this, 'shutdownProcesses'));
        pcntl_signal(SIG_IGN, array($this, 'shutdownProcesses'));
        pcntl_signal(SIGHUP, array($this, 'shutdownProcesses'));
    
        pcntl_signal(SIGCHLD, array($this, 'startCrashedProcess'));
    
        /**
         * Starting processes
         */
        $this->cliHelper->writeTimeLine('Starting processes...');
        foreach ($this->tasks as $task) {
            for ($i = 0; $i < $task->getInstances(); $i++) {
                $this->startProcess($task);
            }
        }
    
        /**
         * Monitoring processes and restart after the specified time
         */
        $this->cliHelper->writeTimeLine('Monitoring processes...');
        while (true) {
            foreach ($this->processes as $process) {
                /**
                 * If runtime for one process is reached, kill him and restart
                 */
                if ($process->getRestartTime() < time()) {
                    $this->restartProcess($process);
                }
            }
    
            sleep(1);
        }
    }
    
    /**
     * Shutdown the processes when the process manager should killed
     */
    public function shutdownProcesses()
    {
        foreach ($this->processes as $pid => $process) {
            unset($this->processes[$pid]);
    
            posix_kill($pid, SIGUSR1);
        }
    
        exit;
    }
    
    /**
     * Starts a new process of the given task
     *
     * @param \Zepi\Core\Utils\Entity\Task $task
     */
    public function startProcess(Task $task)
    {
        $pid = pcntl_fork();
        $this->startFork($pid, $this->onStart, $task);
    }
    
    /**
     * Restarts a process if the runtime is reached
     *
     * @param Process $oldProcess
     */
    public function restartProcess(Process $oldProcess)
    {
        unset($this->processes[$oldProcess->getPid()]);
        posix_kill($oldProcess->getPid(), SIGUSR1);
    
        $pid = pcntl_fork();
        $this->startFork($pid, $this->onRestart, $oldProcess->getTask(), $oldProcess);
    }
    
    /**
     * Starts a crashed process again
     */
    public function startCrashedProcess()
    {
        $oldPid = pcntl_waitpid(-1, $status, WNOHANG);
    
        if ($oldPid === -1 || !isset($this->processes[$oldPid])) {
            return;
        }
    
        $oldProcess = $this->processes[$oldPid];
        unset($this->processes[$oldPid]);
    
        $pid = pcntl_fork();
        $this->startFork($pid, $this->onCrash, $oldProcess->getTask(), $oldProcess);
    }
    
    /**
     * Starts the work of the fork
     * 
     * @param integer $pid
     * @param callable $callback
     * @param \Zepi\Core\Utils\Entity\Task $task
     * @param \Zepi\Core\Utils\Entity\Process $oldProcess
     */
    protected function startFork($pid, $callback, Task $task, Process $oldProcess = null)
    {
        if ($pid === -1) {
            $this->cliHelper->writeTimeLine('Could not fork!');
            exit;
        } elseif ($pid) {
            $newProcess = new Process($task, $pid);
            $this->processes[$pid] = $newProcess;
        
            if ($callback !== null) {
                if ($oldProcess !== null) {
                    call_user_func($callback, $oldProcess, $newProcess);
                } else {
                    call_user_func($callback, $newProcess);
                }
            }
        } else {
            $this->executeTask($task);
        }
    }
    
    /**
     * Starts a task
     *
     * @param \Zepi\Core\Utils\Entity\Task $task
     * @return Process
     */
    protected function executeTask(Task $task)
    {
        call_user_func($task->getCallback(), $task);
    }
}
