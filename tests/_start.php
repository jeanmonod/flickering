<?php
use Flickering\Flickering;
use Flickering\Method;
use Flickering\Request;
use Flickering\OAuth\User;
use Flickering\OAuth\Consumer;

abstract class FlickeringTests extends PHPUnit_Framework_TestCase
{
  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// DUMMIES /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  protected function getDummyFlickering()
  {
    return new Flickering('foo', 'bar');
  }

  protected function getDummyMethod()
  {
    return new Method($this->getDummyFlickering(), 'foobar', array('foo' => 'bar'));
  }

  protected function getDummyUser()
  {
    return new User(array(
      'credentials' => array(
        'token' => 'foo',
        'secret' => 'bar')));
  }

  protected function getDummyRequest($parameters = array(), $config = null)
  {
    $user     = $this->getDummyUser();
    $consumer = new Consumer('foo', 'bar');
    $cache    = $this->getCache();
    if (!$config) $config = $this->getConfig();

    return new Request($parameters, $consumer, $user, $cache, $config);
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// CONTAINER ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  protected function getCache()
  {
    $cache = Mockery::mock('Illuminate\Cache\FileStore');
    $cache->shouldReceive('remember')->andReturnUsing(function($name, $time, $closure) {
      return $closure();
    });

    return $cache;
  }

  protected function getConfig()
  {
    $config = Mockery::mock('Illuminate\Config\Repository');
    $config->shouldReceive('get')->with('config.cache.cache_requests')->andReturn(true);
    $config->shouldReceive('get')->with('config.cache.lifetime')->andReturn(20);

    return $config;
  }

  protected function getSession()
  {
    $session = Mockery::mock('Session');
    $session->shouldReceive('get')->with('flickering_oauth_user')->andReturn($this->getDummyUser());
    $session->shouldReceive('has');

    return $session;
  }

}