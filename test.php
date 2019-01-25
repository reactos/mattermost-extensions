<?php

$config = require('./config.php');


$tests = array(
    array(
        'name' => 'Jira',
        'post' => array(),
        'expect' => array('text' => 'Invalid token')
    ),
    array(
        'name' => 'Jira',
        'post' => array('token' => $config['jira_token']),
        'expect' => array('text' => 'Invalid issue ID')
    ),
    array(
        'name' => 'Jira',
        'post' => array('token' => $config['jira_token'], 'text' => 'a'),
        'expect' => array('text' => 'Invalid issue ID')
    ),
    array(
        'name' => 'Jira',
        'post' => array('token' => $config['jira_token'], 'text' => 'CORE-'),
        'expect' => array('text' => 'Invalid issue ID')
    ),
    array(
        'name' => 'Jira',
        'post' => array('token' => $config['jira_token'], 'text' => 'ROSBE-1'),
        'expect' => array('text' => 'Url requested by : https://jira.reactos.org/browse/ROSBE-1')
    ),
    array(
        'name' => 'Jira',
        'post' => array('token' => $config['jira_token'], 'text' => 'CORE-1'),
        'expect' => array('text' => 'Url requested by : https://jira.reactos.org/browse/CORE-1')
    ),
    array(
        'name' => 'Jira',
        'post' => array('token' => $config['jira_token'], 'text' => 'CORE-1', 'user_name' => 'test'),
        'expect' => array('text' => 'Url requested by test: https://jira.reactos.org/browse/CORE-1')
    ),

    array(
        'name' => 'Translate',
        'post' => array(),
        'expect' => array('text' => 'Invalid token')
    ),
    array(
        'name' => 'Translate',
        'post' => array('token' => $config['translate_token'][0]),
        'expect' => array('text' => 'Invalid command')
    ),
    array(
        'name' => 'Translate',
        'post' => array('token' => $config['translate_token'][0]),
        'get' => array('cmd' => 'error'),
        'expect' => array('text' => 'Not a number')
    ),
    array(
        'name' => 'Translate',
        'post' => array('token' => $config['translate_token'][0], 'text' => 'a'),
        'get' => array('cmd' => 'error'),
        'expect' => array('text' => 'Not a number')
    ),
    array(
        'name' => 'Translate',
        'post' => array('token' => $config['translate_token'][0], 'text' => '0'),
        'get' => array('cmd' => 'error'),
        'expect' => array('text' => "@, 0 could be:\n\tERROR_SUCCESS\n\tSTATUS_SUCCESS\n\tSTATUS_WAIT_0\n\tS_OK")
    ),
    array(
        'name' => 'Translate',
        'post' => array('token' => $config['translate_token'][0], 'text' => '0x0'),
        'get' => array('cmd' => 'error'),
        'expect' => array('text' => "@, 0x0 could be:\n\tERROR_SUCCESS\n\tSTATUS_SUCCESS\n\tSTATUS_WAIT_0\n\tS_OK")
    ),
    array(
        'name' => 'Translate',
        'post' => array('token' => $config['translate_token'][0], 'text' => '0xa', 'user_name' => 'test'),
        'get' => array('cmd' => 'error'),
        'expect' => array('text' => "@test, 0xa could be:\n\tERROR_BAD_ENVIRONMENT\n\tSTATUS_WAIT_0 + 10")
    ),
    array(
        'name' => 'Translate',
        'post' => array('token' => $config['translate_token'][0], 'text' => '0', 'user_name' => 'test'),
        'get' => array('cmd' => 'x'),
        'expect' => array('text' => "Invalid command")
    ),
    array(
        'name' => 'Translate',
        'post' => array('token' => $config['translate_token'][0], 'text' => '0', 'user_name' => 'test'),
        'get' => array('cmd' => 'wm'),
        'expect' => array('text' => "@test, 0 could be:\n\tWM_NULL")
    ),
);

define("mattermost_plugin_test", 1);
require_once("./jira.php");
require_once("./translate.php");

function test_object($args)
{
    $name = $args['name'];
    $expect = $args['expect'];

    $test_post = $args['post'];
    if (isset($args['get']))
        $test_get = $args['get'];
    else
        $test_get = '';

    $obj = new $name($test_post, $test_get);
    $result = $obj->run();
    $diff = array_diff_assoc($expect, $result);

    if (empty($diff))
    {
        $color = 'green';
        $test_result = 'Ok';
    }
    else
    {
        $color = 'red';
        $test_result = "";
        foreach($diff as $key => $value)
        {
            $test_result .= "Wrong result for $key:\n";
            $test_result .= "Expected '$value'\n";
            $test_result .= "Got      '$result[$key]'\n";
        }
    }
    echo "<tr bgcolor='$color'>";
    echo "<td>$name</td>";
    if (isset($test_post['token']))
    {
        $test_post['token'] = '* MAGIC TOKEN *';
    }
    echo "<td>post = " . print_r($test_post, true) . "<br />";
    echo "get = " . print_r($test_get, true) . "</td>";
    echo "<td><pre>$test_result</pre></td>";
    echo "</tr>";
}

?>
<html>
<head></head>
<body>
<table border="1">
    <tr>
        <th>Object</th>
        <th>Input</th>
        <th>Result</th>
    </tr>
<?php

foreach($tests as $test)
{
    test_object($test);
}

?>
</table>
</body>
</html>
