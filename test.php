<?php

$config = require('./config.php');

// Update our 'config' array inline to hide tokens in case we use a 'real' config
function update_config(&$config)
{
    foreach($config as $key => &$value)
    {
        if (strpos($key, '_token') !== false) {
            if (is_array($value)) {
                $value = array('* MAGIC TOKEN *', '* MAGIC TOKEN2 *');
            } else {
                $value = '* MAGIC TOKEN *';
            }
        }
    }
}

update_config($config);

$tests = array(
    'InvalidToken #1' => array(
        'name' => 'Jira',
        'expect' => array('text' => 'Invalid token'),
        'post' => array(),
    ),
    'InvalidIssueID #1' => array(
        'name' => 'Jira',
        'expect' => array('text' => 'Invalid issue ID'),
        'post' => array('token' => $config['jira_token']),
    ),
    'InvalidIssueID #2' => array(
        'name' => 'Jira',
        'expect' => array('text' => 'Invalid issue ID'),
        'post' => array('token' => $config['jira_token'], 'text' => 'a'),
    ),
    'InvalidIssueID #3' => array(
        'name' => 'Jira',
        'expect' => array('text' => 'Invalid issue ID'),
        'post' => array('token' => $config['jira_token'], 'text' => 'CORE-'),
    ),
    'Valid #1' => array(
        'name' => 'Jira',
        'expect' => array('text' => 'Url requested by : https://jira.reactos.org/browse/ROSBE-1'),
        'post' => array('token' => $config['jira_token'], 'text' => 'ROSBE-1'),
    ),
    'Valid #2' => array(
        'name' => 'Jira',
        'expect' => array('text' => 'Url requested by : https://jira.reactos.org/browse/CORE-1'),
        'post' => array('token' => $config['jira_token'], 'text' => 'CORE-1'),
    ),
    'Valid #3' => array(
        'name' => 'Jira',
        'expect' => array('text' => 'Url requested by test: https://jira.reactos.org/browse/CORE-1'),
        'post' => array('token' => $config['jira_token'], 'text' => 'CORE-1', 'user_name' => 'test'),
    ),

    'InvalidToken #1' => array(
        'name' => 'Translate',
        'expect' => array('text' => 'Invalid token'),
        'post' => array(),
    ),
    'InvalidCommand #1' => array(
        'name' => 'Translate',
        'expect' => array('text' => 'Invalid command'),
        'post' => array('token' => $config['translate_token'][0]),
    ),
    'NotANumber #1' => array(
        'name' => 'Translate',
        'expect' => array('text' => 'Not a number'),
        'post' => array('token' => $config['translate_token'][0]),
        'get' => array('cmd' => 'error'),
    ),
    'NotANumber #2' => array(
        'name' => 'Translate',
        'expect' => array('text' => 'Not a number'),
        'post' => array('token' => $config['translate_token'][0], 'text' => 'a'),
        'get' => array('cmd' => 'error'),
    ),
    'Valid #1' => array(
        'name' => 'Translate',
        'expect' => array('text' => "@, 0 could be:\n\tERROR_SUCCESS\n\tSTATUS_SUCCESS\n\tSTATUS_WAIT_0\n\tS_OK"),
        'post' => array('token' => $config['translate_token'][0], 'text' => '0'),
        'get' => array('cmd' => 'error'),
    ),
    'Valid #2' => array(
        'name' => 'Translate',
        'expect' => array('text' => "@, 0x0 could be:\n\tERROR_SUCCESS\n\tSTATUS_SUCCESS\n\tSTATUS_WAIT_0\n\tS_OK"),
        'post' => array('token' => $config['translate_token'][0], 'text' => '0x0'),
        'get' => array('cmd' => 'error'),
    ),
    'Valid #3' => array(
        'name' => 'Translate',
        'expect' => array('text' => "@test, 0xa could be:\n\tERROR_BAD_ENVIRONMENT\n\tSTATUS_WAIT_0 + 10"),
        'post' => array('token' => $config['translate_token'][0], 'text' => '0xa', 'user_name' => 'test'),
        'get' => array('cmd' => 'error'),
    ),
    'InvalidCommand #2' => array(
        'name' => 'Translate',
        'expect' => array('text' => "Invalid command"),
        'post' => array('token' => $config['translate_token'][0], 'text' => '0', 'user_name' => 'test'),
        'get' => array('cmd' => 'x'),
    ),
    'Valid #3' => array(
        'name' => 'Translate',
        'expect' => array('text' => "@test, 0 could be:\n\tWM_NULL"),
        'post' => array('token' => $config['translate_token'][0], 'text' => '0', 'user_name' => 'test'),
        'get' => array('cmd' => 'wm'),
    ),
);

define("mattermost_plugin_test", 1);
require_once("./jira.php");
require_once("./translate.php");


final class Test extends PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testRun($name, $expect, $post, $get = null)
    {
        $obj = new $name($post, $get);
        // Hide our tokens
        $reflection = new ReflectionClass($name);
        $prop = $reflection->getProperty('config');
        $prop->setAccessible(true);
        $cfg = $prop->getValue($obj);
        \update_config($cfg);
        $prop->setValue($obj, $cfg);

        $result = $obj->run();
        $diff = \array_diff_assoc($expect, $result);

        foreach($diff as $key => $value)
        {
            $this->assertEquals($value, $result[$key]);
        }
        $this->assertEmpty($diff);
    }

    public function dataProvider()
    {
        global $tests;
        return $tests;
    }
}
