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
    protected $_tasks = array();
    
    /**
     * @access protected
     * @var array
     */
    protected $_processes = array();
    
    /**
     * @access protected
     * @var callable
     */
    protected $_onStart;
    
    /**
     * @access protected
     * @var callable
     */
    protected $_onRestart;
    
    /**
     * @access protected
     * @var callable
     */
    protected $_onCrash;
    
    /**
     * @access protected
     * @var \Zepi\Core\Utils\Helper\CliHelper
     */
    protected $_cliHelper;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Core\Utils\Helper\CliHelper $cliHelper
     */
    public function __construct(CliHelper $cliHelper)
    {
        $this->_cliHelper = $cliHelper;
    }
    
    /**
     * Sets the start callback of the process manager
     *
     * @access public
     * @param callable $callback
     */
    public function onStart(callable $callback)
    {
        $this->_onStart = $callback;
    }
    
    /**
     * Sets the restart callback of the process manager
     *
     * @access public
     * @param callable $callback
     */
    public function onRestart(callable $callback)
    {
        $this->_onRestart = $callback;
    }
    
    /**
     * Sets the crash callback of the process manager
     *
     * @access public
     * @param callable $callback
     */
    public function onCrash(callable $callback)
    {
        $this->_onCrash = $callback;
    }
    
    /**
     * Adds a task to the process manager
     *
     * @access public
     * @param Task $task
     */
    public function addTask(Task $task)
    {
        $this->_tasks[] = $task;
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
        $this->_cliHelper->writeTimeLine('Setting up signal handlers...');
        declare(ticks = 1);
    
        pcntl_signal(SIGTERM, array($this, 'shutdownProcesses'));
        pcntl_signal(SIGINT, array($this, 'shutdownProcesses'));
        pcntl_signal(SIG_IGN, array($this, 'shutdownProcesses'));
        pcntl_signal(SIGHUP, array($this, 'shutdownProcesses'));
    
        pcntl_signal(SIGCHLD, array($this, 'startCrashedProcess'));
    
        /**
         * Starting processes
         */
        $this->_cliHelper->writeTimeLine('Starting processes...');
        foreach ($this->_tasks as $task) {
            for ($i = 0; $i < $task->getInstances(); $i++) {
                $this->startProcess($task);
            }
        }
    
        /**
         * Monitoring processes and restart after the specified time
         */
        $this->_cliHelper->writeTimeLine('Monitoring processes...');
        while (true) {
            foreach ($this->_processes as $process) {
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
        foreach ($this->_processes as $pid => $process) {
            unset($this->_processes[$pid]);
    
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
    
        if ($pid === -1) {
            $this->_cliHelper->writeTimeLine('Could not fork!');
            exit;
        } elseif ($pid) {
            $process = new Process($task, $pid);
            $this->_processes[$pid] = $process;
    
            if ($this->_onStart !== null) {
                call_user_func($this->_onStart, $process);
            }
        } else {
            $this->_executeTask($task);
        }
    }
    
    /**
     * Restarts a process if the runtime is reached
     *
     * @param Process $oldProcess
     */
    public function restartProcess(Process $oldProcess)
    {
        unset($this->_processes[$oldProcess->getPid()]);
        posix_kill($oldProcess->getPid(), SIGUSR1);
    
        $pid = pcntl_fork();
    
        if ($pid === -1) {
            $this->_cliHelper->writeTimeLine('Could not fork!');
            exit;
        } elseif ($pid) {
            $newProcess = new Process($oldProcess->getTask(), $pid);
            $this->_processes[$pid] = $newProcess;
    
            if ($this->_onRestart !== null) {
                call_user_func($this->_onRestart, $oldProcess, $newProcess);
            }
        } else {
            $this->_executeTask($oldProcess->getTask());
        }
    }
    
    /**
     * Starts a crashed process again
     *
     * @param integer $signo
     */
    public function startCrashedProcess($signo)
    {
        $oldPid = pcntl_waitpid(-1, $status, WNOHANG);
    
        if ($oldPid === -1 || !isset($this->_processes[$oldPid])) {
            return;
        }
    
        $oldProcess = $this->_processes[$oldPid];
        unset($this->_processes[$oldPid]);
    
        $pid = pcntl_fork();
    
        if ($pid === -1) {
            $this->_cliHelper->writeTimeLine('Could not fork!');
            exit;
        } elseif ($pid) {
            $newProcess = new Process($oldProcess->getTask(), $pid);
            $this->_processes[$pid] = $newProcess;
    
            if ($this->_onCrash !== null) {
                call_user_func($this->_onStart, $oldProcess, $newProcess);
            }
        } else {
            $this->_executeTask($oldProcess->getTask());
        }
    }
    
    /**
     * Starts a task
     *
     * @param \Zepi\Core\Utils\Entity\Task $task
     * @return Process
     */
    protected function _executeTask(Task $task)
    {
        call_user_func($task->getCallback(), $task);
    }
}
