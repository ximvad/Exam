<?php
namespace PHPPeru\Test\Exam;

use PHPPeru\Exam\SimpleExam;

/**
 * Test class for SimpleExam.
 * Generated by PHPUnit on 2012-03-09 at 21:22:33.
 */
class SimpleExamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SimpleExam
     */
    protected $exam;
    
    protected $triggeredEvents = array();

    /**
     * Sets up the exam to be used
     */
    protected function setUp()
    {
        $this->exam = new SimpleExam();
    }

    /**
     * @covers PHPPeru\Exam\SimpleExam::start
     * @covers PHPPeru\Exam\SimpleExam::isStarted
     */
    public function testStart()
    {
        $this->exam->start();
        $this->assertTrue($this->exam->isStarted());
        $this->assertFalse($this->exam->isNew());
        $this->assertFalse($this->exam->isAborted());
        $this->assertFalse($this->exam->isCompleted());
    }

    /**
     * @covers PHPPeru\Exam\SimpleExam::abort
     * @covers PHPPeru\Exam\SimpleExam::isAborted
     */
    public function testAbort()
    {
        $this->exam->start();
        $this->exam->abort();
        $this->assertTrue($this->exam->isAborted());
        $this->assertFalse($this->exam->isNew());
        $this->assertFalse($this->exam->isStarted());
        $this->assertFalse($this->exam->isCompleted());
    }

    /**
     * @covers PHPPeru\Exam\SimpleExam::complete
     * @covers PHPPeru\Exam\SimpleExam::isCompleted
     * @todo Implement testComplete().
     */
    public function testComplete()
    {
        $this->exam->start();
        $this->exam->complete();
        $this->assertTrue($this->exam->isCompleted());
        $this->assertFalse($this->exam->isNew());
        $this->assertFalse($this->exam->isStarted());
        $this->assertFalse($this->exam->isAborted());
    }

    /**
     * Checks that newly created exams are marked as new 
     *
     * @covers PHPPeru\Exam\SimpleExam::isNew
     */
    public function testIsNew()
    {
        $this->assertTrue($this->exam->isNew());
        $this->assertFalse($this->exam->isStarted());
        $this->assertFalse($this->exam->isAborted());
        $this->assertFalse($this->exam->isCompleted());
    }
    

    /**
     * Checks that newly created exams have an associated event dispatcher 
     *
     * @covers PHPPeru\Exam\SimpleExam::isNew
     */
    public function testGetEventDispatcher()
    {
        $this->assertInstanceOf(
            'Symfony\Component\EventDispatcher\EventDispatcherInterface',
            $this->exam->getEventDispatcher()
        );
    }
    
    /**
     * Checks that events regarding the lifecycle are triggered correctly and
     * in the correct order when starting and completing an exam.
     */
    public function testEventsAreDispatchedDuringCompleteLifecycle()
    {
        $startListener = $this->getMock('stdClass', array('startCallback'));
        $startListener
            ->expects($this->once())
            ->method('startCallback')
            ->with($this->isInstanceOf('PHPPeru\Exam\Event'));
        $completeListener = $this->getMock('stdClass', array('completeCallback'));
        $completeListener
            ->expects($this->once())
            ->method('completeCallback')
            ->with($this->isInstanceOf('PHPPeru\Exam\Event'));
        $abortListener = $this->getMock('stdClass', array('abortCallback'));
        $abortListener
            ->expects($this->never())
            ->method('abortCallback')
            ->with($this->isInstanceOf('PHPPeru\Exam\Event'));
        $evd = $this->exam->getEventDispatcher();
        $evd->addListener('start', array($startListener, 'startCallback'));
        $evd->addListener('complete', array($completeListener, 'completeCallback'));
        $evd->addListener('abort', array($abortListener, 'abortCallback'));
        $this->exam->start();
        $this->exam->complete();
    }
}