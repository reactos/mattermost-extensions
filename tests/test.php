<?php

$src_dir = dirname(dirname(__FILE__))."/src/";
$config = require($src_dir . 'config.php');

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
$gh_url = "https://github.com/reactos/reactos/commit/";
$rg_url = "https://git.reactos.org/?p=reactos.git;a=commit;h=";
$pr_url = "https://github.com/reactos/";

$tests = array(
    array(
        'name' => 'Jira',
        'expect' => array('text' => 'Invalid token'),
        'post' => array(),
    ),
    array(
        'name' => 'Jira',
        'expect' => array('text' => 'Invalid issue ID'),
        'post' => array('token' => $config['jira_token']),
    ),
    array(
        'name' => 'Jira',
        'expect' => array('text' => 'Invalid issue ID'),
        'post' => array('token' => $config['jira_token'], 'text' => 'a'),
    ),
    array(
        'name' => 'Jira',
        'expect' => array('text' => 'Invalid issue ID'),
        'post' => array('token' => $config['jira_token'], 'text' => 'CORE-'),
    ),
    array(
        'name' => 'Jira',
        'expect' => array('text' => '@ requested `/jira ROSBE-1`: https://jira.reactos.org/browse/ROSBE-1'),
        'post' => array('token' => $config['jira_token'], 'text' => 'ROSBE-1'),
    ),
    array(
        'name' => 'Jira',
        'expect' => array('text' => '@ requested `/jira CORE-1`: https://jira.reactos.org/browse/CORE-1'),
        'post' => array('token' => $config['jira_token'], 'text' => 'CORE-1'),
    ),
    array(
        'name' => 'Jira',
        'expect' => array('text' => '@test requested `/jira CORE-1`: https://jira.reactos.org/browse/CORE-1'),
        'post' => array('token' => $config['jira_token'], 'text' => 'CORE-1', 'user_name' => 'test'),
    ),

    array(
        'name' => 'Translate',
        'expect' => array('text' => 'Invalid token'),
        'post' => array(),
    ),
    array(
        'name' => 'Translate',
        'expect' => array('text' => 'Invalid command'),
        'post' => array('token' => $config['translate_token'][0]),
    ),
    array(
        'name' => 'Translate',
        'expect' => array('text' => 'Not a number'),
        'post' => array('token' => $config['translate_token'][0]),
        'get' => array('cmd' => 'error'),
    ),
    array(
        'name' => 'Translate',
        'expect' => array('text' => 'Not a number'),
        'post' => array('token' => $config['translate_token'][0], 'text' => 'a'),
        'get' => array('cmd' => 'error'),
    ),
    array(
        'name' => 'Translate',
        'expect' => array('text' => "@ requested `/error 0`:\n\tERROR_SUCCESS\n\tSTATUS_SUCCESS\n\tSTATUS_WAIT_0\n\tS_OK"),
        'post' => array('token' => $config['translate_token'][0], 'text' => '0'),
        'get' => array('cmd' => 'error'),
    ),
    array(
        'name' => 'Translate',
        'expect' => array('text' => "@ requested `/error 0x0`:\n\tERROR_SUCCESS\n\tSTATUS_SUCCESS\n\tSTATUS_WAIT_0\n\tS_OK"),
        'post' => array('token' => $config['translate_token'][0], 'text' => '0x0'),
        'get' => array('cmd' => 'error'),
    ),
    array(
        'name' => 'Translate',
        'expect' => array('text' => "@test requested `/error 0xa`:\n\tERROR_BAD_ENVIRONMENT\n\tSTATUS_WAIT_0 + 10"),
        'post' => array('token' => $config['translate_token'][0], 'text' => '0xa', 'user_name' => 'test'),
        'get' => array('cmd' => 'error'),
    ),
    array(
        'name' => 'Translate',
        'expect' => array('text' => "Invalid command"),
        'post' => array('token' => $config['translate_token'][0], 'text' => '0', 'user_name' => 'test'),
        'get' => array('cmd' => 'x'),
    ),
    array(
        'name' => 'Translate',
        'expect' => array('text' => "@test requested `/wm 0`:\n\tWM_NULL"),
        'post' => array('token' => $config['translate_token'][0], 'text' => '0', 'user_name' => 'test'),
        'get' => array('cmd' => 'wm'),
    ),
    array(
        'name' => 'Translate',
        'expect' => array('text' => "1111111 not found"),
        'post' => array('token' => $config['translate_token'][0], 'text' => '1111111', 'user_name' => 'test'),
        'get' => array('cmd' => 'wm'),
    ),

    array(
        'name' => 'Git',
        'expect' => array('text' => "Invalid token"),
        'post' => array(),
    ),
    array(
        'name' => 'Git',
        'expect' => array('text' => "Invalid commit hash"),
        'post' => array('token' => $config['git_token']),
    ),
    array(
        'name' => 'Git',
        'expect' => array('text' => "Invalid commit hash"),
        'post' => array('token' => $config['git_token'], 'text' => 'g'),
    ),
    array(
        'name' => 'Git',
        'expect' => array('text' => "Invalid commit hash"),
        'post' => array('token' => $config['git_token'], 'text' => 'g123456'),
    ),
    array(
        'name' => 'Git',
        'expect' => array('text' => "@ requested `/git g1234567`:\n" . $gh_url . "1234567\n" . $rg_url . "1234567"),
        'post' => array('token' => $config['git_token'], 'text' => 'g1234567'),
    ),
    array(
        'name' => 'Git',
        'expect' => array('text' => "@ requested `/git AAAAAAA`:\n" . $gh_url . "AAAAAAA\n" . $rg_url . "AAAAAAA"),
        'post' => array('token' => $config['git_token'], 'text' => 'AAAAAAA'),
    ),


    array(
        'name' => 'Pr',
        'expect' => array('text' => "Invalid token"),
        'post' => array(),
    ),
    array(
        'name' => 'Pr',
        'expect' => array('text' => "Invalid PR number"),
        'post' => array('token' => $config['pr_token']),
    ),
    array(
        'name' => 'Pr',
        'expect' => array('text' => "Invalid PR number"),
        'post' => array('token' => $config['pr_token'], 'text' => '#'),
    ),
    array(
        'name' => 'Pr',
        'expect' => array('text' => "Invalid PR number"),
        'post' => array('token' => $config['pr_token'], 'text' => '#a'),
    ),
    array(
        'name' => 'Pr',
        'expect' => array('text' => "@ requested `/pr #1`:\n" . $pr_url . "reactos/pull/" . "1"),
        'post' => array('token' => $config['pr_token'], 'text' => '#1'),
    ),
    array(
        'name' => 'Pr',
        'expect' => array('text' => "@ requested `/pr rapps-db#333333`:\n" . $pr_url . "rapps-db/pull/" . "333333"),
        'post' => array('token' => $config['pr_token'], 'text' => 'rapps-db#333333'),
    ),
    array(
        'name' => 'Pr',
        'expect' => array('text' => "@ requested `/pr buildbot_config#123456789`:\n" . $pr_url . "buildbot_config/pull/" . "123456789"),
        'post' => array('token' => $config['pr_token'], 'text' => 'buildbot_config#123456789'),
    ),
    array(
        'name' => 'Pr',
        'expect' => array('text' => "@ requested `/pr Qemu_GUI#1`:\n" . $pr_url . "Qemu_GUI/pull/" . "1"),
        'post' => array('token' => $config['pr_token'], 'text' => 'Qemu_GUI#1'),
    ),
    array(
        'name' => 'Pr',
        'expect' => array('text' => "@ requested `/pr mESSAGE_tRANSLATOR#1`:\n" . $pr_url . "Message_Translator/pull/" . "1"),
        'post' => array('token' => $config['pr_token'], 'text' => 'mESSAGE_tRANSLATOR#1'),
    ),
);

define("mattermost_plugin_test", 1);
require_once($src_dir . 'jira.php');
require_once($src_dir . 'translate.php');


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
