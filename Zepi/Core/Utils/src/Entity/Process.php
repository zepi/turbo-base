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
 * Process
 * 
 * @package Zepi\Core\Utils
 * @subpackage Entity
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */

namespace Zepi\Core\Utils\Entity;

/**
 * Process
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */
class Process
{
    /**
     * @access protected
     * @var \Zepi\Core\Utils\Entity\Task
     */
    protected $task;
    
    /**
     * @access protected
     * @var integer
     */
    protected $pid;
    
    /**
     * @access protected
     * @var integer
     */
    protected $startTime;
    
    /**
     * @access protected
     * @var integer
     */
    protected $restartTime;
    
    /**
     * Constructs the entity
     * 
     * @access public
     * @param \Zepi\Core\Utils\Entity\Task $task
     * @param integer $pid
     */
    public function __construct(Task $task, $pid)
    {
        $this->task = $task;
        $this->pid = $pid;
        
        $this->startTime = time();
        $this->restartTime = time() + $task->getResetTime() + rand(1, 5);
    }
    
    /**
     * Returns the task
     * 
     * @access public
     * @return \Zepi\Core\Utils\Task
     */
    public function getTask()
    {
        return $this->task;
    }
    
    /**
     * Returns the pid of the process
     * 
     * @access public
     * @return integer
     */
    public function getPid()
    {
        return $this->pid;
    }
    
    /**
     * Returns the start time of the process
     * 
     * @access public
     * @return integer
     */
    public function getStartTime()
    {
        return $this->startTime;
    }
    
    /**
     * Returns the restart time of the process
     * 
     * @access public
     * @return integer
     */
    public function getRestartTime()
    {
        return $this->restartTime;
    }
}
