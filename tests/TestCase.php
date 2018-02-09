<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests;

use Geocoder\Model\Address;
use League\Geotools\Coordinate\CoordinateInterface;
use League\Geotools\Coordinate\Ellipsoid;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * @return ProviderAggregator
     */
    protected function getStubGeocoder()
    {
        $stub = $this
            ->getMockBuilder('\Geocoder\ProviderAggregator')
            ->disableOriginalConstructor()
            ->getMock();

        return $stub;
    }

    /**
     * @param array $providers
     * @param array $data
     *
     * @return ProviderAggregator
     */
    protected function getMockGeocoderReturns(array $providers, array $data = array())
    {
        $addresses = new AddressCollection();

        if (!empty($data)) {
            $addresses = new AddressCollection([Address::createFromArray($data)]);
        }

        $mock = $this->getMockBuilder('\Geocoder\ProviderAggregator')->getMock();
        $mock
            ->expects($this->any())
            ->method('getProviders')
            ->will($this->returnValue($providers));
        $mock
            ->expects($this->any())
            ->method('using')
            ->will($this->returnSelf());
        $mock
            ->expects($this->any())
            ->method('geocode')
            ->will($this->returnValue($addresses));
        $mock
            ->expects($this->any())
            ->method('reverse')
            ->will($this->returnValue($addresses));

        return $mock;
    }

    /**
     * @param array $providers
     *
     * @return ProviderAggregator
     */
    protected function getMockGeocoderThrowException(array $providers)
    {
        $mock = $this->createMock('\Geocoder\ProviderAggregator');
        $mock
            ->expects($this->once())
            ->method('getProviders')
            ->will($this->returnValue($providers));
        $mock
            ->expects($this->any())
            ->method('using')
            ->will($this->returnSelf());
        $mock
            ->expects($this->any())
            ->method('geocode')
            ->will($this->throwException(new \Exception));
        $mock
            ->expects($this->any())
            ->method('reverse')
            ->will($this->throwException(new \Exception));

        return $mock;
    }

    /**
     * @param float|null $lat
     * @param float|null $lng
     * @return CoordinateInterface
     */
    protected function getStubCoordinate($lat = null, $lng = null)
    {
        $stub = $this
            ->getMockBuilder('\League\Geotools\Coordinate\CoordinateInterface')
            ->disableOriginalConstructor()
            ->getMock();

        if (null !== $lat) {
            $stub->method('getLatitude')->willReturn($lat);
        }
        if (null !== $lng) {
            $stub->method('getLongitude')->willReturn($lng);
        }

        return $stub;
    }

    /**
     * @param array $coordinate
     * @param Ellipsoid $ellipsoid
     *
     * @return CoordinateInterface
     */
    protected function getMockCoordinateReturns(array $coordinate, Ellipsoid $ellipsoid = null)
    {
        $mock = $this->createMock('\League\Geotools\Coordinate\CoordinateInterface');
        $mock
            ->expects($this->any())
            ->method('getLatitude')
            ->will($this->returnValue($coordinate[0]));
        $mock
            ->expects($this->any())
            ->method('getLongitude')
            ->will($this->returnValue($coordinate[1]));

        if ($ellipsoid) {
            $mock
                ->expects($this->atLeastOnce())
                ->method('getEllipsoid')
                ->will($this->returnValue($ellipsoid));
        }

        return $mock;
    }

	/**
     * Create an address object for testing
     *
     * @param array $data
     * @return Address|null
     */
    protected function createAddress(array $data)
    {
        return Address::createFromArray($data);
    }

	/**
     * Create an empty address object
     *
     * @return Address|null
     */
    protected function createEmptyAddress()
    {
        return $this->createAddress([]);
    }
}
