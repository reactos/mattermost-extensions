<?php
header('Content-Type: application/json');

$arg = isset($_POST['text']) ? $_POST['text'] : '';
$token = isset($_POST['token']) ? $_POST['token'] : '';
$user = isset($_POST['user_name']) ? $_POST['user_name'] : '';

$response_type = 'ephemeral';
$response_text = 'Invalid token';

$configs = include('config.php');

if ($token == $configs->jira_token)
{
    if (preg_match('/^(ARWINSS|CORE|ROSBE|ONLINE|ROSTESTS|ROSAPPS)-[0-9]+$/i', $arg))
    {
        $response_text = "Url requested by $user: https://jira.reactos.org/browse/$arg";
        $response_type = "in_channel";
    }
    else
    {
        $response_text = "Invalid issue ID";
    }
}

?>
{
    "response_type": "<?php echo $response_type; ?>",
    "text": "<?php echo $response_text; ?>"
}
