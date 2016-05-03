<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-11-22
 * Time: 23:14
 */

namespace ScayTrase\SmsDeliveryBundle\Features\Context;

use PHPUnit_Framework_Assert;
use ScayTrase\Behat\Context\RawSymfonyContext;
use ScayTrase\SmsDeliveryBundle\DataCollector\MessageDeliveryDataCollector;
use ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface;

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
        PHPUnit_Framework_Assert::assertCount(1, $collector->getData());
        $packages = $collector->getData();
        /** @var ShortMessageInterface $message */
        $message = $packages[0]->getMessage();
        PHPUnit_Framework_Assert::assertEquals('disabled', $packages[0]->getStatus());
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
