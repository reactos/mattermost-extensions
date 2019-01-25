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
);

define("mattermost_plugin_test", 1);
require_once("./jira.php");

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
            $test_result .= "Expected '$value' for '$key', got '$result[$key]'<br />";
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
    echo "<td>$test_result</td>";
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
