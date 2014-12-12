<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-11-22
 * Time: 23:14
 */

namespace ScayTrase\Utils\SMSDeliveryBundle\Features\Context;

use PHPUnit_Framework_Assert;
use ScayTrase\Behat\Context\RawSymfonyContext;
use ScayTrase\Utils\SMSDeliveryBundle\DataCollector\MessageDeliveryDataCollector;
use ScayTrase\Utils\SMSDeliveryBundle\Service\ShortMessageInterface;

class FeatureContext extends RawSymfonyContext
{
    private $code = null;

    /**
     * @Then /^I should receive SMS for "([^"]*)"$/
     * @param $phoneNumber
     */
    public function iShouldReceiveSMSFor($phoneNumber)
    {
        $profiler = $this->getSymfonyProfile();

        /** @var MessageDeliveryDataCollector $collector */
        $collector = $profiler->getCollector('sms_delivery.data_collector');
        PHPUnit_Framework_Assert::assertEquals(1, $collector->getMessageCount());
        $messages = $collector->getRecords();
        /** @var ShortMessageInterface $message */
        $message = $messages[0]['message'];
        PHPUnit_Framework_Assert::assertEquals('disabled', $messages[0]['status']);
        PHPUnit_Framework_Assert::assertNotEmpty($message->getBody());
        PHPUnit_Framework_Assert::assertEquals($phoneNumber, $message->getRecipient());
        preg_match('/\d{6}/', $message->getBody(), $matches);
        PHPUnit_Framework_Assert::assertCount(1, $matches);
        $this->code = $matches[0];
    }

    /**
     * @Given /^I fill in "([^"]*)" with code from SMS$/
     * @param $field
     */
    public function iFillInWithCodeFromSMS($field)
    {
        PHPUnit_Framework_Assert::assertNotNull($this->code, 'Code was not obtained');
        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($this->code);
        $this->getSession()->getPage()->fillField($field, $value);
    }

    /**
     * Returns fixed step argument (with \\" replaced back to ").
     *
     * @param string $argument
     *
     * @return string
     */
    protected function fixStepArgument($argument)
    {
        return str_replace('\\"', '"', $argument);
    }

}
