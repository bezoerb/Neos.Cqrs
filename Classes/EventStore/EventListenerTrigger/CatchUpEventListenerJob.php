<?php
declare(strict_types=1);
namespace Neos\EventSourcing\EventStore\EventListenerTrigger;

/*
 * This file is part of the Neos.EventSourcing package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Flowpack\JobQueue\Common\Job\JobInterface;
use Flowpack\JobQueue\Common\Queue\Message;
use Flowpack\JobQueue\Common\Queue\QueueInterface;
use Neos\EventSourcing\EventListener\EventListenerInterface;
use Neos\EventSourcing\EventListener\EventListenerInvoker;
use Neos\EventSourcing\EventListener\Exception\EventCouldNotBeAppliedException;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;

final class CatchUpEventListenerJob implements JobInterface
{

    /**
     * @var string
     */
    protected $listenerClassName;

    /**
     * @Flow\Inject
     * @var EventListenerInvoker
     */
    protected $eventListenerInvoker;

    /**
     * @Flow\Inject
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    public function __construct(string $listenerClassName)
    {
        $this->listenerClassName = $listenerClassName;
    }

    /**
     * @param QueueInterface $queue
     * @param Message $message
     * @return bool
     * @throws EventCouldNotBeAppliedException
     */
    public function execute(QueueInterface $queue, Message $message): bool
    {
        /** @var EventListenerInterface $listener */
        $listener = $this->objectManager->get($this->listenerClassName);
        $this->eventListenerInvoker->catchUp($listener);
        return true;
    }

    public function getLabel(): string
    {
        return sprintf('Catch up event listener "%s"', $this->listenerClassName);
    }
}
